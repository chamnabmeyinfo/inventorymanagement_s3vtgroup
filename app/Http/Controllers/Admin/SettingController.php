<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\LowStockAlertService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = ['low_stock_threshold', 'alert_email', 'telegram_bot_token', 'telegram_chat_id'];
        $values = [];
        foreach ($settings as $key) {
            $values[$key] = Setting::get($key) ?? config("inventory.{$key}") ?? '';
        }
        return view('admin.settings.index', compact('values'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'low_stock_threshold' => 'nullable|integer|min:0|max:9999',
            'alert_email' => 'nullable|string|max:500',
            'telegram_bot_token' => 'nullable|string|max:255',
            'telegram_chat_id' => 'nullable|string|max:500',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value ? (string) $value : null);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Settings saved.');
    }

    public function testAlert(Request $request, LowStockAlertService $alertService)
    {
        $request->validate(['channel' => 'required|in:email,telegram']);

        $result = $alertService->sendTestAlert($request->channel);

        if ($result['sent'] > 0) {
            $msg = $request->channel === 'email'
                ? "Test email sent to {$result['sent']} recipient(s)."
                : "Test Telegram message sent to {$result['sent']} chat(s).";
            return redirect()->route('admin.settings.index')->with('success', $msg);
        }

        $err = $request->channel === 'email'
            ? 'Email test failed. Check alert_email and MAIL_* configuration.'
            : 'Telegram test failed. Check telegram_bot_token and telegram_chat_id.';
        if (!empty($result['failed'])) {
            $err .= ' Failed: ' . implode(', ', $result['failed']);
        }
        return redirect()->route('admin.settings.index')->with('error', $err);
    }
}
