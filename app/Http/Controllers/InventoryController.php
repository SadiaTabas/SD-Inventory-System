<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\StockMovement;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'out_of_stock') {
                $query->where('qty', '<=', 0);
            } elseif ($request->stock_status === 'low_stock') {
                $query->where('qty', '>', 0)->whereRaw('qty <= min_stock_level');
            } elseif ($request->stock_status === 'in_stock') {
                $query->whereRaw('qty > min_stock_level');
            }
        }

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::where('status', true)->get();

        $totalStockQty = (int) Product::sum('qty');
        $lowStockCount = Product::where('qty', '>', 0)->whereRaw('qty <= min_stock_level')->count();
        $outOfStockCount = Product::where('qty', '<=', 0)->count();
        $inventoryValue = (float) Product::select(DB::raw('SUM(qty * CASE WHEN cost_price > 0 THEN cost_price ELSE price END) as val'))->value('val');

        return view('inventory.index', compact(
            'products',
            'categories',
            'totalStockQty',
            'lowStockCount',
            'outOfStockCount',
            'inventoryValue'
        ));
    }

    public function movements(Request $request)
    {
        $query = StockMovement::with(['product', 'user'])->latest();

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $movements = $query->paginate(20)->withQueryString();
        $products = Product::select('id', 'name', 'sku')->orderBy('name')->get();

        return view('inventory.movements', compact('movements', 'products'));
    }

    public function adjustStock(Request $request, Product $product)
    {
        $request->validate([
            'adjustment_type' => 'required|in:add,subtract',
            'quantity'        => 'required|integer|min:1',
            'reason'          => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request, $product) {
            $product = Product::lockForUpdate()->findOrFail($product->id);
            $qty = intval($request->quantity);
            $prevStock = $product->qty;

            if ($request->adjustment_type === 'subtract') {
                if ($qty > $prevStock) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'quantity' => "Cannot deduct {$qty} units. Current stock is only {$prevStock}.",
                    ]);
                }
                $newStock = $prevStock - $qty;
                $movementQty = -$qty;
            } else {
                $newStock = $prevStock + $qty;
                $movementQty = $qty;
            }

            $product->update([
                'qty'        => $newStock,
                'updated_by' => Auth::user()->name,
            ]);

            StockMovement::create([
                'product_id'     => $product->id,
                'type'           => 'adjustment',
                'quantity'       => $movementQty,
                'previous_stock' => $prevStock,
                'new_stock'      => $newStock,
                'reference_type' => 'Manual Adjustment',
                'reference_id'   => null,
                'user_id'        => Auth::id(),
                'user_name'      => Auth::user()->name,
                'reason'         => $request->reason,
            ]);

            ActivityLog::log('Stock Adjusted', 'Product', $product->id, "Stock adjusted by {$movementQty} for {$product->name}. Reason: {$request->reason}");
        });

        return back()->with('success', 'Stock level adjusted successfully.');
    }
}
