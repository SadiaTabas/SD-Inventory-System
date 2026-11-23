<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\ProductReview;
use App\Models\StockMovement;
use App\Models\ActivityLog;
use App\Services\CartService;
use Barryvdh\DomPDF\Facade\Pdf;

class AccountController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function dashboard()
    {
        $user = Auth::user();
        $recentOrders = $user->orders()->with('items.product')->latest()->take(5)->get();
        $totalOrders = $user->orders()->count();
        $pendingOrders = $user->orders()->whereIn('status', ['pending', 'processing'])->count();
        $deliveredOrders = $user->orders()->where('status', 'delivered')->count();
        $defaultAddress = $user->addresses()->where('is_default', true)->first() ?? $user->addresses()->first();
        $wishlistCount = $user->wishlists()->count();

        return view('customer.dashboard', compact(
            'user',
            'recentOrders',
            'totalOrders',
            'pendingOrders',
            'deliveredOrders',
            'defaultAddress',
            'wishlistCount'
        ));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('customer.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => ['required', 'string', 'regex:/^(?:\+?8801|01)[3-9]\d{8}$/'],
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => strtolower(trim($request->email)),
            'phone' => $request->phone,
        ]);

        ActivityLog::log('Customer Profile Updated', 'User', $user->id, "Customer updated profile details.");

        return back()->with('success', 'Profile information updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|current_password',
            'password'         => 'required|confirmed|min:8',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        ActivityLog::log('Customer Password Changed', 'User', $user->id, "Customer changed account password.");

        return back()->with('success', 'Password updated successfully.');
    }

    // Addresses
    public function addresses()
    {
        $addresses = Auth::user()->addresses()->latest()->get();
        return view('customer.addresses', compact('addresses'));
    }

    public function storeAddress(Request $request)
    {
        $request->validate([
            'full_name'             => 'required|string|max:255',
            'phone'                 => ['required', 'string', 'regex:/^(?:\+?8801|01)[3-9]\d{8}$/'],
            'address_line'          => 'required|string|max:500',
            'area'                  => 'nullable|string|max:100',
            'city'                  => 'required|string|max:100',
            'district'              => 'nullable|string|max:100',
            'postal_code'           => 'nullable|string|max:20',
            'delivery_instructions' => 'nullable|string|max:500',
            'type'                  => 'required|in:home,office,other',
            'is_default'            => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $isDefault = $request->boolean('is_default') || $user->addresses()->count() === 0;

        if ($isDefault) {
            $user->addresses()->update(['is_default' => false]);
        }

        $user->addresses()->create([
            'full_name'             => $request->full_name,
            'phone'                 => $request->phone,
            'address_line'          => $request->address_line,
            'area'                  => $request->area,
            'city'                  => $request->city,
            'district'              => $request->district ?? $request->city,
            'postal_code'           => $request->postal_code,
            'delivery_instructions' => $request->delivery_instructions,
            'type'                  => $request->type,
            'is_default'            => $isDefault,
        ]);

        return redirect()->route('customer.addresses')->with('success', 'New address saved successfully.');
    }

    public function updateAddress(Request $request, Address $address)
    {
        // Enforce ownership
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'full_name'             => 'required|string|max:255',
            'phone'                 => ['required', 'string', 'regex:/^(?:\+?8801|01)[3-9]\d{8}$/'],
            'address_line'          => 'required|string|max:500',
            'area'                  => 'nullable|string|max:100',
            'city'                  => 'required|string|max:100',
            'district'              => 'nullable|string|max:100',
            'postal_code'           => 'nullable|string|max:20',
            'delivery_instructions' => 'nullable|string|max:500',
            'type'                  => 'required|in:home,office,other',
            'is_default'            => 'nullable|boolean',
        ]);

        if ($request->boolean('is_default')) {
            Auth::user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update([
            'full_name'             => $request->full_name,
            'phone'                 => $request->phone,
            'address_line'          => $request->address_line,
            'area'                  => $request->area,
            'city'                  => $request->city,
            'district'              => $request->district ?? $request->city,
            'postal_code'           => $request->postal_code,
            'delivery_instructions' => $request->delivery_instructions,
            'type'                  => $request->type,
            'is_default'            => $request->boolean('is_default') || $address->is_default,
        ]);

        return redirect()->route('customer.addresses')->with('success', 'Address updated successfully.');
    }

    public function destroyAddress(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $next = Auth::user()->addresses()->first();
            if ($next) {
                $next->update(['is_default' => true]);
            }
        }

        return redirect()->route('customer.addresses')->with('success', 'Address deleted successfully.');
    }

    public function setDefaultAddress(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        Auth::user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return redirect()->route('customer.addresses')->with('success', 'Default address updated.');
    }

    // Orders
    public function orders(Request $request)
    {
        $status = $request->query('status');
        $query = Auth::user()->orders()->with(['items.product', 'payments'])->latest();

        if ($status && in_array($status, ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('customer.orders.index', compact('orders', 'status'));
    }

    public function showOrder(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order.');
        }

        $order->load(['items.product', 'payments', 'deliveryAgent', 'statusHistories']);

        return view('customer.orders.show', compact('order'));
    }

    public function downloadInvoice(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to invoice.');
        }

        $order->load(['user', 'deliveryAgent', 'items.product', 'payments']);

        $pdf = Pdf::loadView('orders.invoice', compact('order'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Invoice-' . ($order->order_number ?? $order->id) . '.pdf');
    }

    public function cancelOrder(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$order->canBeCancelled()) {
            return back()->with('error', 'This order cannot be cancelled as it is already ' . $order->status);
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($order, $request) {
            // Restore inventory
            foreach ($order->items as $item) {
                if ($item->product) {
                    $prevStock = $item->product->qty;
                    $item->product->increment('qty', $item->quantity);
                    $newStock = $item->product->qty;

                    StockMovement::create([
                        'product_id'     => $item->product_id,
                        'type'           => 'cancellation',
                        'quantity'       => $item->quantity,
                        'previous_stock' => $prevStock,
                        'new_stock'      => $newStock,
                        'reference_type' => 'Order',
                        'reference_id'   => $order->id,
                        'user_id'        => Auth::id(),
                        'user_name'      => Auth::user()->name,
                        'reason'         => 'Customer order cancellation: ' . $request->reason,
                    ]);
                }
            }

            $order->update([
                'status'        => 'cancelled',
                'cancel_reason' => $request->reason,
                'updated_by'    => Auth::user()->name,
            ]);

            $order->statusHistories()->create([
                'status'     => 'cancelled',
                'comment'    => 'Cancelled by customer: ' . $request->reason,
                'changed_by' => Auth::user()->name,
            ]);

            ActivityLog::log('Customer Cancelled Order', 'Order', $order->id, "Customer cancelled order {$order->order_number}. Reason: {$request->reason}");
        });

        return back()->with('success', 'Order has been cancelled and inventory released.');
    }

    // Wishlist
    public function wishlist()
    {
        $wishlists = Auth::user()->wishlists()->with('product.category')->latest()->get();
        return view('customer.wishlist', compact('wishlists'));
    }

    public function toggleWishlist(Request $request, Product $product)
    {
        $user = Auth::user();
        $existing = Wishlist::where('user_id', $user->id)->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->delete();
            $added = false;
            $message = 'Removed from your wishlist.';
        } else {
            Wishlist::create([
                'user_id'    => $user->id,
                'product_id' => $product->id,
            ]);
            $added = true;
            $message = 'Added to your wishlist!';
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'added'   => $added,
                'message' => $message,
                'count'   => $user->wishlists()->count(),
            ]);
        }

        return back()->with('success', $message);
    }

    public function removeFromWishlist(Product $product)
    {
        Auth::user()->wishlists()->where('product_id', $product->id)->delete();
        return back()->with('success', 'Item removed from wishlist.');
    }

    public function moveWishlistToCart(Product $product)
    {
        $result = $this->cartService->addItem($product->id, 1);

        if ($result['success']) {
            Auth::user()->wishlists()->where('product_id', $product->id)->delete();
            return redirect()->route('cart.index')->with('success', 'Item moved to your cart!');
        }

        return back()->with('error', $result['message']);
    }

    // Reviews
    public function reviews()
    {
        $reviews = Auth::user()->reviews()->with('product')->latest()->paginate(10);
        return view('customer.reviews', compact('reviews'));
    }

    public function storeReview(Request $request, Product $product)
    {
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'title'   => 'nullable|string|max:200',
            'comment' => 'required|string|max:2000',
        ]);

        $user = Auth::user();

        // Check if user has purchased this product
        $hasPurchased = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.user_id', $user->id)
            ->where('order_items.product_id', $product->id)
            ->where('orders.status', 'delivered')
            ->exists();

        ProductReview::updateOrCreate(
            [
                'product_id' => $product->id,
                'user_id'    => $user->id,
            ],
            [
                'rating'               => $request->rating,
                'title'                => $request->title,
                'comment'              => $request->comment,
                'is_verified_purchase' => $hasPurchased,
                'status'               => 'approved', // Auto-approved for verified purchases or pending
            ]
        );

        ActivityLog::log('Product Review Submitted', 'Product', $product->id, "Customer submitted a {$request->rating}-star review for {$product->name}");

        return back()->with('success', 'Thank you! Your review has been submitted.');
    }
}
