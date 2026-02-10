<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
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
            'alert_email' => 'nullable|email|max:255',
            'telegram_bot_token' => 'nullable|string|max:255',
            'telegram_chat_id' => 'nullable|string|max:255',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value ? (string) $value : null);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Settings saved.');
    }
}
