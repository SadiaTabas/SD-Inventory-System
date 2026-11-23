<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function getCart(): Cart
    {
        if (Auth::check()) {
            $user = Auth::user();
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);

            // If there's also a session-based cart, merge it
            $sessionId = Session::getId();
            $sessionCart = Cart::where('session_id', $sessionId)
                ->whereNull('user_id')
                ->first();

            if ($sessionCart && $sessionCart->id !== $cart->id) {
                $this->mergeCarts($sessionCart, $cart);
            }

            return $cart->load(['items.product']);
        }

        $sessionId = Session::getId();
        $cart = Cart::firstOrCreate(['session_id' => $sessionId, 'user_id' => null]);

        return $cart->load(['items.product']);
    }

    public function addItem(int $productId, int $quantity = 1): array
    {
        $product = Product::findOrFail($productId);

        if (!$product->status || $product->qty <= 0) {
            return [
                'success' => false,
                'message' => 'This product is currently out of stock.',
            ];
        }

        $cart = $this->getCart();
        $item = $cart->items()->where('product_id', $productId)->first();

        $currentQty = $item ? $item->quantity : 0;
        $newQty = $currentQty + $quantity;

        if ($newQty > $product->qty) {
            return [
                'success' => false,
                'message' => "Only {$product->qty} units available in stock. You already have {$currentQty} in your cart.",
            ];
        }

        if ($item) {
            $item->update([
                'quantity' => $newQty,
                'price'    => $product->price,
            ]);
        } else {
            $cart->items()->create([
                'product_id' => $productId,
                'quantity'   => $quantity,
                'price'      => $product->price,
            ]);
        }

        return [
            'success' => true,
            'message' => 'Product added to cart successfully!',
        ];
    }

    public function updateQuantity(int $itemId, int $quantity): array
    {
        $cart = $this->getCart();
        $item = $cart->items()->where('id', $itemId)->firstOrFail();
        $product = $item->product;

        if ($quantity <= 0) {
            $item->delete();
            return [
                'success' => true,
                'message' => 'Item removed from cart.',
            ];
        }

        if ($quantity > $product->qty) {
            return [
                'success' => false,
                'message' => "Only {$product->qty} units available in stock.",
            ];
        }

        $item->update([
            'quantity' => $quantity,
            'price'    => $product->price,
        ]);

        return [
            'success' => true,
            'message' => 'Cart updated.',
        ];
    }

    public function removeItem(int $itemId): array
    {
        $cart = $this->getCart();
        $cart->items()->where('id', $itemId)->delete();

        return [
            'success' => true,
            'message' => 'Item removed from cart.',
        ];
    }

    public function clearCart(): void
    {
        $cart = $this->getCart();
        $cart->items()->delete();
        $cart->update(['coupon_code' => null]);
    }

    public function applyCoupon(string $code): array
    {
        $cart = $this->getCart();
        $coupon = Coupon::where('code', strtoupper(trim($code)))->where('status', true)->first();

        if (!$coupon) {
            return [
                'success' => false,
                'message' => 'Invalid or expired coupon code.',
            ];
        }

        $subtotal = $this->calculateSubtotal($cart);
        if ($subtotal < $coupon->min_order_amount) {
            return [
                'success' => false,
                'message' => "This coupon requires a minimum order amount of ৳" . number_format($coupon->min_order_amount, 2),
            ];
        }

        $cart->update(['coupon_code' => $coupon->code]);

        return [
            'success' => true,
            'message' => 'Coupon applied successfully!',
        ];
    }

    public function removeCoupon(): array
    {
        $cart = $this->getCart();
        $cart->update(['coupon_code' => null]);

        return [
            'success' => true,
            'message' => 'Coupon removed.',
        ];
    }

    public function getCartTotals(): array
    {
        $cart = $this->getCart();
        $subtotal = $this->calculateSubtotal($cart);

        $discount = 0.00;
        $coupon = null;

        if ($cart->coupon_code) {
            $coupon = Coupon::where('code', $cart->coupon_code)->where('status', true)->first();
            if ($coupon && $subtotal >= $coupon->min_order_amount) {
                $discount = $coupon->calculateDiscount($subtotal);
            } else {
                // Invalidate if subtotal dropped below min order
                $cart->update(['coupon_code' => null]);
                $coupon = null;
            }
        }

        $defaultDeliveryCharge = (float) BusinessSetting::get('default_delivery_charge', 60.00);
        $freeDeliveryThreshold = (float) BusinessSetting::get('free_delivery_threshold', 0.00);

        $deliveryCharge = $defaultDeliveryCharge;
        if ($freeDeliveryThreshold > 0 && $subtotal >= $freeDeliveryThreshold) {
            $deliveryCharge = 0.00;
        }

        if ($subtotal <= 0) {
            $deliveryCharge = 0.00;
        }

        $total = max(0, ($subtotal - $discount) + $deliveryCharge);

        return [
            'cart'             => $cart,
            'items'            => $cart->items,
            'subtotal'         => $subtotal,
            'discount'         => $discount,
            'coupon'           => $coupon,
            'coupon_code'      => $cart->coupon_code,
            'delivery_charge'  => $deliveryCharge,
            'total'            => $total,
            'total_items_count'=> (int) $cart->items->sum('quantity'),
        ];
    }

    protected function calculateSubtotal(Cart $cart): float
    {
        $subtotal = 0.00;
        foreach ($cart->items as $item) {
            if ($item->product) {
                $subtotal += $item->product->price * $item->quantity;
            }
        }
        return (float) $subtotal;
    }

    public function mergeCarts(Cart $sourceCart, Cart $targetCart): void
    {
        DB::transaction(function () use ($sourceCart, $targetCart) {
            foreach ($sourceCart->items as $sourceItem) {
                $existing = $targetCart->items()->where('product_id', $sourceItem->product_id)->first();
                if ($existing) {
                    $product = Product::find($sourceItem->product_id);
                    $availQty = $product ? $product->qty : $existing->quantity;
                    $mergedQty = min($availQty, $existing->quantity + $sourceItem->quantity);
                    $existing->update(['quantity' => $mergedQty]);
                } else {
                    $targetCart->items()->create([
                        'product_id' => $sourceItem->product_id,
                        'quantity'   => $sourceItem->quantity,
                        'price'      => $sourceItem->price,
                    ]);
                }
            }

            if ($sourceCart->coupon_code && !$targetCart->coupon_code) {
                $targetCart->update(['coupon_code' => $sourceCart->coupon_code]);
            }

            $sourceCart->items()->delete();
            $sourceCart->delete();
        });
    }
}
