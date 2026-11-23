<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CartItem;
use App\Services\CartService;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cartData = $this->cartService->getCartTotals();
        return view('storefront.cart', $cartData);
    }

    public function add(Request $request, Product $product)
    {
        $quantity = max(1, intval($request->input('quantity', 1)));
        $result = $this->cartService->addItem($product->id, $quantity);

        if ($request->wantsJson() || $request->ajax()) {
            $cartData = $this->cartService->getCartTotals();
            return response()->json([
                'success'           => $result['success'],
                'message'           => $result['message'],
                'total_items_count' => $cartData['total_items_count'],
                'subtotal'          => $cartData['subtotal'],
            ], $result['success'] ? 200 : 422);
        }

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        if ($request->boolean('buy_now')) {
            return redirect()->route('checkout.index');
        }

        return back()->with('success', $result['message']);
    }

    public function update(Request $request, CartItem $item)
    {
        $quantity = intval($request->input('quantity', 1));
        $result = $this->cartService->updateQuantity($item->id, $quantity);

        if ($request->wantsJson() || $request->ajax()) {
            $cartData = $this->cartService->getCartTotals();
            return response()->json([
                'success'           => $result['success'],
                'message'           => $result['message'],
                'total_items_count' => $cartData['total_items_count'],
                'subtotal'          => $cartData['subtotal'],
                'total'             => $cartData['total'],
            ], $result['success'] ? 200 : 422);
        }

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('cart.index')->with('success', $result['message']);
    }

    public function remove(CartItem $item)
    {
        $result = $this->cartService->removeItem($item->id);
        return redirect()->route('cart.index')->with('success', $result['message']);
    }

    public function clear()
    {
        $this->cartService->clearCart();
        return redirect()->route('cart.index')->with('success', 'Your cart has been cleared.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50',
        ]);

        $result = $this->cartService->applyCoupon($request->coupon_code);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('cart.index')->with('success', $result['message']);
    }

    public function removeCoupon()
    {
        $result = $this->cartService->removeCoupon();
        return redirect()->route('cart.index')->with('success', $result['message']);
    }
}
