<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class CheckLowStock extends Command
{
    protected $signature = 'inventory:check-low-stock';

    protected $description = 'Check for low stock and send alerts (Telegram or email)';

    public function handle(): int
    {
        $threshold = (int) (Setting::get('low_stock_threshold') ?? config('inventory.low_stock_threshold', 5));
        $alertEmail = Setting::get('alert_email') ?? config('inventory.alert_email');
        $telegramToken = Setting::get('telegram_bot_token') ?? config('inventory.telegram_bot_token');
        $telegramChatId = Setting::get('telegram_chat_id') ?? config('inventory.telegram_chat_id');

        $needingAttention = Product::with(['category', 'stock'])
            ->get()
            ->filter(function ($p) use ($threshold) {
                $qty = $p->stock?->quantity ?? 0;
                $reorderPoint = $p->reorder_point;
                if ($reorderPoint !== null) {
                    return $qty <= $reorderPoint;
                }
                return $qty <= $threshold || ($p->stock?->status ?? '') === 'out_of_stock';
            });

        if ($needingAttention->isEmpty()) {
            $this->info('All products adequately stocked.');
            return self::SUCCESS;
        }

        $outOfStock = $needingAttention->filter(fn ($p) => ($p->stock?->quantity ?? 0) <= 0);
        $lowStock = $needingAttention->filter(fn ($p) => ($p->stock?->quantity ?? 0) > 0);

        $this->info(sprintf(
            'Low stock alert: %d out of stock, %d below reorder point.',
            $outOfStock->count(),
            $lowStock->count()
        ));

        $sent = false;

        if ($telegramToken && $telegramChatId) {
            try {
                $response = Http::post("https://api.telegram.org/bot{$telegramToken}/sendMessage", [
                    'chat_id' => $telegramChatId,
                    'text' => $this->buildTelegramMessage($outOfStock, $lowStock),
                ]);
                if ($response->successful()) {
                    $this->info('Alert sent to Telegram.');
                    $sent = true;
                } else {
                    $this->warn('Telegram send failed: ' . $response->body());
                }
            } catch (\Throwable $e) {
                $this->error('Telegram error: ' . $e->getMessage());
            }
        }

        if ($alertEmail) {
            try {
                Mail::raw($this->buildEmailBody($outOfStock, $lowStock), function ($m) use ($alertEmail) {
                    $m->to($alertEmail)
                        ->subject('[S3VT Inventory] Low stock alert');
                });
                $this->info("Alert sent to {$alertEmail}");
                $sent = true;
            } catch (\Throwable $e) {
                $this->error('Email failed: ' . $e->getMessage());
            }
        }

        if (!$sent && !$telegramToken && !$alertEmail) {
            $this->comment('No alerts configured. Set TELEGRAM_BOT_TOKEN + TELEGRAM_CHAT_ID or INVENTORY_ALERT_EMAIL in .env');
        }

        return self::SUCCESS;
    }

    private function buildTelegramMessage($outOfStock, $lowStock): string
    {
        $lines = ["⚠️ S3VT Inventory – Low Stock Alert\n"];
        if ($outOfStock->isNotEmpty()) {
            $lines[] = "OUT OF STOCK:";
            foreach ($outOfStock->take(15) as $p) {
                $lines[] = "• {$p->sku} {$p->name}";
            }
            if ($outOfStock->count() > 15) {
                $lines[] = "... and " . ($outOfStock->count() - 15) . " more";
            }
            $lines[] = '';
        }
        if ($lowStock->isNotEmpty()) {
            $lines[] = "BELOW REORDER POINT:";
            foreach ($lowStock->take(15) as $p) {
                $qty = $p->stock?->quantity ?? 0;
                $lines[] = "• {$p->sku} {$p->name} (qty: {$qty})";
            }
            if ($lowStock->count() > 15) {
                $lines[] = "... and " . ($lowStock->count() - 15) . " more";
            }
        }
        $lines[] = "\n" . config('app.url') . "/admin";
        return implode("\n", $lines);
    }

    private function buildEmailBody($outOfStock, $lowStock): string
    {
        $lines = ["S3VT Inventory – Low Stock Alert\n"];
        if ($outOfStock->isNotEmpty()) {
            $lines[] = "OUT OF STOCK:";
            foreach ($outOfStock as $p) {
                $lines[] = "  - {$p->sku} {$p->name}";
            }
            $lines[] = '';
        }
        if ($lowStock->isNotEmpty()) {
            $lines[] = "BELOW REORDER POINT:";
            foreach ($lowStock as $p) {
                $qty = $p->stock?->quantity ?? 0;
                $lines[] = "  - {$p->sku} {$p->name} (qty: {$qty})";
            }
        }
        $lines[] = "\nLogin to your admin dashboard to record stock or view reports.";
        return implode("\n", $lines);
    }
}
