<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with(['supplier', 'items.product'])->latest()->paginate(15);
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::where('status', true)->get();
        $products = Product::where('status', true)->get();

        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'         => 'required|exists:suppliers,id',
            'purchase_date'       => 'required|date',
            'items'               => 'required|array|min:1',
            'items.*.product_id'  => 'required|exists:products,id',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.unit_cost'   => 'required|numeric|min:0',
            'notes'               => 'nullable|string',
        ]);

        $purchase = DB::transaction(function () use ($request) {
            $totalAmount = 0.00;
            $itemsData = [];

            foreach ($request->items as $item) {
                $subtotal = floatval($item['unit_cost']) * intval($item['quantity']);
                $totalAmount += $subtotal;
                $itemsData[] = [
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'unit_cost'  => $item['unit_cost'],
                    'subtotal'   => $subtotal,
                ];
            }

            $purchaseNumber = 'PO-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $purchase = Purchase::create([
                'purchase_number' => $purchaseNumber,
                'supplier_id'     => $request->supplier_id,
                'total_amount'    => $totalAmount,
                'status'          => 'draft',
                'purchase_date'   => $request->purchase_date,
                'notes'           => $request->notes,
                'created_by'      => Auth::user()->name,
            ]);

            foreach ($itemsData as $itemData) {
                $purchase->items()->create($itemData);
            }

            ActivityLog::log('Purchase Order Created', 'Purchase', $purchase->id, "Purchase Order #{$purchase->purchase_number} created with total ৳" . number_format($totalAmount, 2));

            return $purchase;
        });

        return redirect()->route('purchases.show', $purchase)->with('success', 'Purchase order created successfully.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product']);
        return view('purchases.show', compact('purchase'));
    }

    public function receive(Request $request, Purchase $purchase)
    {
        if ($purchase->status === 'received') {
            return back()->with('error', 'This purchase order has already been marked as received.');
        }

        DB::transaction(function () use ($purchase) {
            foreach ($purchase->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);
                if ($product) {
                    $prevStock = $product->qty;
                    $product->increment('qty', $item->quantity);

                    // Update cost price on product if applicable
                    if ($item->unit_cost > 0) {
                        $product->update(['cost_price' => $item->unit_cost]);
                    }

                    $newStock = $product->qty;

                    // Log stock movement
                    StockMovement::create([
                        'product_id'     => $product->id,
                        'type'           => 'purchase',
                        'quantity'       => $item->quantity,
                        'previous_stock' => $prevStock,
                        'new_stock'      => $newStock,
                        'reference_type' => 'Purchase',
                        'reference_id'   => $purchase->id,
                        'user_id'        => Auth::id(),
                        'user_name'      => Auth::user()->name,
                        'reason'         => "Stock received from PO #{$purchase->purchase_number}",
                    ]);

                    $item->update(['received_quantity' => $item->quantity]);
                }
            }

            $purchase->update([
                'status'        => 'received',
                'received_date' => now()->toDateString(),
            ]);

            ActivityLog::log('Purchase Received', 'Purchase', $purchase->id, "Purchase Order #{$purchase->purchase_number} marked as received. Stock updated.");
        });

        return back()->with('success', 'Goods marked as received! Inventory quantities updated.');
    }
}
