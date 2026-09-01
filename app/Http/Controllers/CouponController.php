<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->get();
        return view('coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('coupons.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'             => 'required|string|max:50|unique:coupons,code',
            'type'             => 'required|in:fixed,percent',
            'value'            => 'required|numeric|min:0.01',
            'min_order_amount' => 'nullable|numeric|min:0',
            'status'           => 'required|boolean',
        ]);

        Coupon::create([
            'code'             => strtoupper(trim($request->code)),
            'type'             => $request->type,
            'value'            => $request->value,
            'min_order_amount' => $request->min_order_amount ?? 0.00,
            'status'           => $request->status,
        ]);

        return redirect()
            ->route('coupons.index')
            ->with('success', 'Coupon created successfully.');
    }

    public function edit(Coupon $coupon)
    {
        return view('coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code'             => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'type'             => 'required|in:fixed,percent',
            'value'            => 'required|numeric|min:0.01',
            'min_order_amount' => 'nullable|numeric|min:0',
            'status'           => 'required|boolean',
        ]);

        $coupon->update([
            'code'             => strtoupper(trim($request->code)),
            'type'             => $request->type,
            'value'            => $request->value,
            'min_order_amount' => $request->min_order_amount ?? 0.00,
            'status'           => $request->status,
        ]);

        return redirect()
            ->route('coupons.index')
            ->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()
            ->route('coupons.index')
            ->with('success', 'Coupon deleted successfully.');
    }
}