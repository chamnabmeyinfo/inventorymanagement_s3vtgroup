<?php

namespace App\Services;

use App\Mail\LowStockAlertMail;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LowStockAlertService
{
    /**
     * Get products needing attention (out of stock or below reorder point).
     */
    public function getProductsNeedingAttention(): array
    {
        $threshold = (int) (Setting::get('low_stock_threshold') ?? config('inventory.low_stock_threshold', 5));

        $products = Product::with(['category', 'stock', 'preferredSupplier'])
            ->get()
            ->filter(function ($p) use ($threshold) {
                $qty = $p->stock?->quantity ?? 0;
                $reorderPoint = $p->reorder_point;
                if ($reorderPoint !== null) {
                    return $qty <= $reorderPoint;
                }
                return $qty <= $threshold || ($p->stock?->status ?? '') === 'out_of_stock';
            });

        return [
            'out_of_stock' => $products->filter(fn ($p) => ($p->stock?->quantity ?? 0) <= 0)->values(),
            'low_stock' => $products->filter(fn ($p) => ($p->stock?->quantity ?? 0) > 0)->values(),
        ];
    }

    /**
     * Send email alert(s) to configured recipients.
     */
    public function sendEmailAlerts(array $data): array
    {
        $emails = $this->parseEmails(Setting::get('alert_email') ?? config('inventory.alert_email'));
        if (empty($emails)) {
            return ['sent' => 0, 'failed' => []];
        }

        $results = ['sent' => 0, 'failed' => []];
        $mailable = new LowStockAlertMail($data['out_of_stock'], $data['low_stock']);

        foreach ($emails as $email) {
            try {
                Mail::to(trim($email))->send($mailable);
                $results['sent']++;
            } catch (\Throwable $e) {
                Log::warning('Low stock alert email failed', ['email' => $email, 'error' => $e->getMessage()]);
                $results['failed'][] = $email;
            }
        }

        return $results;
    }

    /**
     * Send Telegram alert(s) to configured chat(s).
     */
    public function sendTelegramAlerts(array $data): array
    {
        $token = Setting::get('telegram_bot_token') ?? config('inventory.telegram_bot_token');
        $chatIds = $this->parseChatIds(Setting::get('telegram_chat_id') ?? config('inventory.telegram_chat_id'));

        if (!$token || empty($chatIds)) {
            return ['sent' => 0, 'failed' => []];
        }

        $text = $this->buildTelegramMessage($data['out_of_stock'], $data['low_stock']);
        $results = ['sent' => 0, 'failed' => []];

        foreach ($chatIds as $chatId) {
            try {
                $response = Http::timeout(10)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => trim($chatId),
                    'text' => $text,
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => true,
                ]);
                if ($response->successful()) {
                    $results['sent']++;
                } else {
                    Log::warning('Telegram alert failed', ['chat_id' => $chatId, 'response' => $response->body()]);
                    $results['failed'][] = $chatId;
                }
            } catch (\Throwable $e) {
                Log::warning('Telegram alert error', ['chat_id' => $chatId, 'error' => $e->getMessage()]);
                $results['failed'][] = $chatId;
            }
        }

        return $results;
    }

    /**
     * Send all configured alerts.
     */
    public function sendAllAlerts(): array
    {
        $data = $this->getProductsNeedingAttention();

        if ($data['out_of_stock']->isEmpty() && $data['low_stock']->isEmpty()) {
            return ['data' => $data, 'email' => ['sent' => 0, 'failed' => []], 'telegram' => ['sent' => 0, 'failed' => []]];
        }

        return [
            'data' => $data,
            'email' => $this->sendEmailAlerts($data),
            'telegram' => $this->sendTelegramAlerts($data),
        ];
    }

    /**
     * Build HTML-formatted Telegram message.
     */
    public function buildTelegramMessage(Collection $outOfStock, Collection $lowStock): string
    {
        $lines = ["<b>⚠️ S3VT Inventory – Low Stock Alert</b>\n"];
        $adminUrl = config('app.url') . '/admin';

        if ($outOfStock->isNotEmpty()) {
            $lines[] = "<b>OUT OF STOCK (" . $outOfStock->count() . "):</b>";
            foreach ($outOfStock->take(20) as $p) {
                $lines[] = "• " . $this->escapeTelegram($p->sku) . " " . $this->escapeTelegram($p->name);
            }
            if ($outOfStock->count() > 20) {
                $lines[] = "<i>... and " . ($outOfStock->count() - 20) . " more</i>";
            }
            $lines[] = '';
        }

        if ($lowStock->isNotEmpty()) {
            $lines[] = "<b>BELOW REORDER POINT (" . $lowStock->count() . "):</b>";
            foreach ($lowStock->take(20) as $p) {
                $qty = $p->stock?->quantity ?? 0;
                $reorder = $p->reorder_point ?? '—';
                $lines[] = "• " . $this->escapeTelegram($p->sku) . " " . $this->escapeTelegram($p->name) . " (qty: {$qty}, reorder: {$reorder})";
            }
            if ($lowStock->count() > 20) {
                $lines[] = "<i>... and " . ($lowStock->count() - 20) . " more</i>";
            }
            $lines[] = '';
        }

        $lines[] = "<a href=\"{$adminUrl}\">View dashboard →</a>";
        return implode("\n", $lines);
    }

    private function escapeTelegram(string $s): string
    {
        return str_replace(['<', '>', '&'], ['&lt;', '&gt;', '&amp;'], $s);
    }

    private function parseEmails(?string $value): array
    {
        if (empty($value)) {
            return [];
        }
        return array_filter(array_map('trim', preg_split('/[\s,;]+/', $value)), fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL));
    }

    private function parseChatIds(?string $value): array
    {
        if (empty($value)) {
            return [];
        }
        return array_filter(array_map('trim', preg_split('/[\s,;]+/', $value)), fn ($c) => $c !== '');
    }

    /**
     * Send a test alert (dummy data) to verify email/Telegram configuration.
     */
    public function sendTestAlert(string $channel): array
    {
        $outOfStock = collect([(object) ['sku' => 'TEST-001', 'name' => 'Sample product (out of stock)', 'category' => null, 'stock' => null, 'reorder_point' => 10]]);
        $lowStock = collect([(object) ['sku' => 'TEST-002', 'name' => 'Sample product (low stock)', 'category' => null, 'stock' => (object) ['quantity' => 3], 'reorder_point' => 10]]);

        $data = ['out_of_stock' => $outOfStock, 'low_stock' => $lowStock];

        if ($channel === 'email') {
            return $this->sendEmailAlerts($data);
        }
        if ($channel === 'telegram') {
            return $this->sendTelegramAlerts($data);
        }
        return ['sent' => 0, 'failed' => []];
    }
}
