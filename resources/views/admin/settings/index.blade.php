@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="card">
    <h2 style="margin-top: 0;">Settings</h2>
    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Low stock threshold</label>
            <input type="number" min="0" name="low_stock_threshold" value="{{ old('low_stock_threshold', $values['low_stock_threshold'] ?? '') }}" placeholder="Default: 5 (from .env if not set)">
            <small style="color: #64748b;">Products with qty ≤ this are considered low stock when no per-product reorder point is set.</small>
        </div>
        <div class="form-group">
            <label>Alert email</label>
            <input type="text" name="alert_email" value="{{ old('alert_email', $values['alert_email'] ?? '') }}" placeholder="owner@example.com">
            <small style="color: #64748b;">Receives low-stock alerts (HTML email). Multiple: comma or semicolon separated. Requires MAIL_* configured.</small>
            <div style="margin-top: 0.5rem;">
                <form action="{{ route('admin.settings.test-alert') }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="channel" value="email">
                    <button type="submit" class="btn btn-secondary btn-sm">Test email</button>
                </form>
            </div>
        </div>
        <div class="form-group">
            <label>Telegram bot token</label>
            <input type="text" name="telegram_bot_token" value="{{ old('telegram_bot_token', $values['telegram_bot_token'] ?? '') }}" placeholder="From @BotFather" autocomplete="off">
            <small style="color: #64748b;">Create a bot via @BotFather to receive alerts on Telegram.</small>
        </div>
        <div class="form-group">
            <label>Telegram chat ID</label>
            <input type="text" name="telegram_chat_id" value="{{ old('telegram_chat_id', $values['telegram_chat_id'] ?? '') }}" placeholder="Your chat ID">
            <small style="color: #64748b;">Message your bot, then visit api.telegram.org/bot&lt;TOKEN&gt;/getUpdates to find chat_id. Multiple: comma separated.</small>
            <div style="margin-top: 0.5rem;">
                <form action="{{ route('admin.settings.test-alert') }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="channel" value="telegram">
                    <button type="submit" class="btn btn-secondary btn-sm">Test Telegram</button>
                </form>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save settings</button>
        </div>
    </form>
</div>
@endsection
