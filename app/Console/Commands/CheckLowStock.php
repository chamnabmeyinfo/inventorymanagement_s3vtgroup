<?php

namespace App\Console\Commands;

use App\Services\LowStockAlertService;
use Illuminate\Console\Command;

class CheckLowStock extends Command
{
    protected $signature = 'inventory:check-low-stock';

    protected $description = 'Check for low stock and send alerts (Email and Telegram)';

    public function handle(LowStockAlertService $alertService): int
    {
        $result = $alertService->sendAllAlerts();

        $outOfStock = $result['data']['out_of_stock'];
        $lowStock = $result['data']['low_stock'];

        if ($outOfStock->isEmpty() && $lowStock->isEmpty()) {
            $this->info('All products adequately stocked.');
            return self::SUCCESS;
        }

        $this->info(sprintf(
            'Low stock: %d out of stock, %d below reorder point.',
            $outOfStock->count(),
            $lowStock->count()
        ));

        $email = $result['email'];
        if ($email['sent'] > 0) {
            $this->info("Email: sent to {$email['sent']} recipient(s).");
        }
        if (!empty($email['failed'])) {
            $this->warn('Email failed for: ' . implode(', ', $email['failed']));
        }

        $telegram = $result['telegram'];
        if ($telegram['sent'] > 0) {
            $this->info("Telegram: sent to {$telegram['sent']} chat(s).");
        }
        if (!empty($telegram['failed'])) {
            $this->warn('Telegram failed for chat ID(s): ' . implode(', ', $telegram['failed']));
        }

        $hadEmailConfig = $email['sent'] > 0 || !empty($email['failed']);
        $hadTelegramConfig = $telegram['sent'] > 0 || !empty($telegram['failed']);
        if (!$hadEmailConfig && !$hadTelegramConfig) {
            $this->comment('No alerts configured. Set alert_email and/or telegram_bot_token + telegram_chat_id in Settings.');
        }

        return self::SUCCESS;
    }
}
