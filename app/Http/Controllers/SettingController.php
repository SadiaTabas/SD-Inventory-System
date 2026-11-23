<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index()
    {
        $settings = BusinessSetting::pluck('value', 'key')->toArray();
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'business_name'           => 'required|string|max:255',
            'business_email'          => 'nullable|email|max:255',
            'business_phone'          => 'nullable|string|max:50',
            'business_address'        => 'nullable|string|max:500',
            'currency_symbol'         => 'required|string|max:10',
            'currency_code'           => 'required|string|max:10',
            'default_delivery_charge' => 'required|numeric|min:0',
            'free_delivery_threshold' => 'nullable|numeric|min:0',
            'min_order_amount'        => 'nullable|numeric|min:0',
            'enable_cod'              => 'nullable|boolean',
            'enable_bkash'            => 'nullable|boolean',
            'enable_nagad'            => 'nullable|boolean',
            'bkash_number'            => 'nullable|string|max:50',
            'nagad_number'            => 'nullable|string|max:50',
            'payment_instructions'    => 'nullable|string',
        ]);

        $keys = [
            'business_name',
            'business_email',
            'business_phone',
            'business_address',
            'currency_symbol',
            'currency_code',
            'default_delivery_charge',
            'free_delivery_threshold',
            'min_order_amount',
            'enable_cod',
            'enable_bkash',
            'enable_nagad',
            'bkash_number',
            'nagad_number',
            'payment_instructions',
        ];

        foreach ($keys as $key) {
            $val = $request->input($key, '');
            if (in_array($key, ['enable_cod', 'enable_bkash', 'enable_nagad'])) {
                $val = $request->has($key) ? '1' : '0';
            }
            BusinessSetting::set($key, (string) $val);
        }

        ActivityLog::log('Settings Updated', 'BusinessSetting', null, "System business and store settings updated by " . Auth::user()->name);

        return back()->with('success', 'Settings updated successfully.');
    }
}
