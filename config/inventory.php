<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Low Stock Threshold
    |--------------------------------------------------------------------------
    |
    | Default quantity below which a product is considered "low stock" when
    | no per-product reorder_point is set. Products at or below this level
    | will appear in dashboard alerts and low-stock reports.
    |
    */

    'low_stock_threshold' => env('INVENTORY_LOW_STOCK_THRESHOLD', 5),

    /*
    |--------------------------------------------------------------------------
    | Alert Email (Optional)
    |--------------------------------------------------------------------------
    |
    | Email address to receive low-stock alerts. If set, the CheckLowStock
    | command can send daily summaries. Leave null to disable email alerts.
    |
    */

    'alert_email' => env('INVENTORY_ALERT_EMAIL'),

    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Alerts (Free)
    |--------------------------------------------------------------------------
    |
    | Send low-stock alerts to Telegram. Create a bot via @BotFather, get the
    | token, then message your bot or add it to a group to get the chat_id.
    | Leave null to disable Telegram alerts.
    |
    */

    'telegram_bot_token' => env('TELEGRAM_BOT_TOKEN'),
    'telegram_chat_id' => env('TELEGRAM_CHAT_ID'),

];
