<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class StorefrontController extends Controller
{
    public function index()
    {
        // 1. Featured Categories with active product counts
        $categories = Category::where('status', true)
            ->withCount(['products' => function ($q) {
                $q->where('status', true);
            }])
            ->get();

        // 2. Featured Products
        $featuredProducts = Product::where('status', true)
            ->where('featured', true)
            ->with('category')
            ->latest()
            ->take(8)
            ->get();

        if ($featuredProducts->isEmpty()) {
            $featuredProducts = Product::where('status', true)
                ->with('category')
                ->latest()
                ->take(8)
                ->get();
        }

        // 3. Latest Arrivals
        $newArrivals = Product::where('status', true)
            ->with('category')
            ->latest()
            ->take(8)
            ->get();

        // 4. Best Selling Products
        $bestSellerIds = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(8)
            ->pluck('product_id');

        $bestSellers = Product::whereIn('id', $bestSellerIds)
            ->where('status', true)
            ->with('category')
            ->get();

        if ($bestSellers->isEmpty()) {
            $bestSellers = Product::where('status', true)
                ->where('qty', '>', 0)
                ->with('category')
                ->inRandomOrder()
                ->take(8)
                ->get();
        }

        // 5. Active promotional coupons
        $coupons = Coupon::where('status', true)->latest()->take(4)->get();

        return view('storefront.index', compact(
            'categories',
            'featuredProducts',
            'newArrivals',
            'bestSellers',
            'coupons'
        ));
    }

    public function shop(Request $request)
    {
        $query = Product::where('status', true)->with('category');

        // Search
        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $catId = $request->category;
            $query->where('category_id', $catId);
        }

        // Price filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', floatval($request->min_price));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', floatval($request->max_price));
        }

        // Availability
        if ($request->filled('in_stock') && $request->in_stock == '1') {
            $query->where('qty', '>', 0);
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'popularity':
                $query->withCount('orderItems')->orderByDesc('order_items_count');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::where('status', true)->withCount('products')->get();
        $maxPrice = Product::where('status', true)->max('price') ?? 10000;

        return view('storefront.shop', compact('products', 'categories', 'maxPrice', 'sort'));
    }

    public function category($category)
    {
        if ($category instanceof Category) {
            $cat = $category;
        } else {
            $cat = Category::where('slug', $category)->orWhere('id', $category)->firstOrFail();
        }

        if (!$cat->status) {
            abort(404);
        }
        $category = $cat;

        $products = Product::where('status', true)
            ->where('category_id', $category->id)
            ->with('category')
            ->latest()
            ->paginate(12);

        $categories = Category::where('status', true)->withCount('products')->get();

        return view('storefront.category', compact('category', 'products', 'categories'));
    }

    public function productDetails($product)
    {
        if ($product instanceof Product) {
            $prod = $product;
        } else {
            $prod = Product::where('slug', $product)->orWhere('id', $product)->firstOrFail();
        }

        if (!$prod->status) {
            abort(404, 'Product not found or currently unavailable.');
        }

        $product = $prod;
        $product->load(['category', 'reviews.user']);

        $relatedProducts = Product::where('status', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        $userHasPurchased = false;
        if (auth()->check()) {
            $userHasPurchased = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.user_id', auth()->id())
                ->where('order_items.product_id', $product->id)
                ->where('orders.status', 'delivered')
                ->exists();
        }

        return view('storefront.product-details', compact('product', 'relatedProducts', 'userHasPurchased'));
    }
}
