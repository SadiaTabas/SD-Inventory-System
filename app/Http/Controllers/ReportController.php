<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\OrderPayment;
use App\Models\Purchase;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reportType = $request->get('type', 'sales');
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());

        $data = [];

        switch ($reportType) {
            case 'sales':
                $data = Order::where('status', '!=', 'cancelled')
                    ->whereDate('created_at', '>=', $dateFrom)
                    ->whereDate('created_at', '<=', $dateTo)
                    ->with('user')
                    ->latest()
                    ->get();
                break;

            case 'orders':
                $data = Order::whereDate('created_at', '>=', $dateFrom)
                    ->whereDate('created_at', '<=', $dateTo)
                    ->with('user')
                    ->latest()
                    ->get();
                break;

            case 'products':
                $data = OrderItem::select('product_id', 'product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_sales'))
                    ->whereHas('order', function ($q) use ($dateFrom, $dateTo) {
                        $q->where('status', '!=', 'cancelled')
                          ->whereDate('created_at', '>=', $dateFrom)
                          ->whereDate('created_at', '<=', $dateTo);
                    })
                    ->groupBy('product_id', 'product_name')
                    ->orderByDesc('total_sales')
                    ->get();
                break;

            case 'inventory':
            case 'low_stock':
                $query = Product::with('category');
                if ($reportType === 'low_stock') {
                    $query->whereRaw('qty <= min_stock_level');
                }
                $data = $query->orderBy('qty')->get();
                break;

            case 'customers':
                $data = User::where('role', 'customer')
                    ->withCount(['orders' => function ($q) use ($dateFrom, $dateTo) {
                        $q->whereDate('created_at', '>=', $dateFrom)
                          ->whereDate('created_at', '<=', $dateTo);
                    }])
                    ->withSum(['orders' => function ($q) use ($dateFrom, $dateTo) {
                        $q->where('status', '!=', 'cancelled')
                          ->whereDate('created_at', '>=', $dateFrom)
                          ->whereDate('created_at', '<=', $dateTo);
                    }], 'total_amount')
                    ->latest()
                    ->get();
                break;

            case 'payments':
                $data = OrderPayment::whereDate('payment_date', '>=', $dateFrom)
                    ->whereDate('payment_date', '<=', $dateTo)
                    ->with('order')
                    ->latest()
                    ->get();
                break;

            case 'purchases':
                $data = Purchase::whereDate('purchase_date', '>=', $dateFrom)
                    ->whereDate('purchase_date', '<=', $dateTo)
                    ->with('supplier')
                    ->latest()
                    ->get();
                break;

            case 'profit':
                // Selling price minus cost price per item
                $items = OrderItem::whereHas('order', function ($q) use ($dateFrom, $dateTo) {
                    $q->where('status', '!=', 'cancelled')
                      ->whereDate('created_at', '>=', $dateFrom)
                      ->whereDate('created_at', '<=', $dateTo);
                })->with('product')->get();

                $totalSales = $items->sum('subtotal');
                $totalCost = $items->sum(function ($it) {
                    $cost = $it->product && $it->product->cost_price > 0 ? $it->product->cost_price : ($it->price * 0.7); // fallback if cost price not yet set
                    return $cost * $it->quantity;
                });
                $grossProfit = $totalSales - $totalCost;

                $data = [
                    'items'       => $items,
                    'total_sales' => $totalSales,
                    'total_cost'  => $totalCost,
                    'profit'      => $grossProfit,
                    'margin'      => $totalSales > 0 ? round(($grossProfit / $totalSales) * 100, 2) : 0,
                ];
                break;
        }

        // CSV Export handling
        if ($request->get('export') === 'csv') {
            return $this->exportCsv($reportType, $data, $dateFrom, $dateTo);
        }

        return view('reports.index', compact('reportType', 'dateFrom', 'dateTo', 'data'));
    }

    protected function exportCsv(string $type, $data, string $from, string $to)
    {
        $filename = "report_{$type}_{$from}_{$to}.csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($type, $data) {
            $file = fopen('php://output', 'w');

            if ($type === 'sales' || $type === 'orders') {
                fputcsv($file, ['Order #', 'Date', 'Customer', 'Source', 'Total', 'Paid', 'Due', 'Status', 'Payment Method']);
                foreach ($data as $order) {
                    fputcsv($file, [
                        $order->order_number,
                        $order->created_at->format('Y-m-d H:i'),
                        $order->user->name ?? 'Customer',
                        $order->order_source,
                        $order->total_amount,
                        $order->paid_amount,
                        $order->due_amount,
                        $order->status,
                        $order->payment_method
                    ]);
                }
            } elseif ($type === 'inventory' || $type === 'low_stock') {
                fputcsv($file, ['Product ID', 'Name', 'SKU', 'Category', 'Stock Qty', 'Price', 'Cost Price', 'Status']);
                foreach ($data as $p) {
                    fputcsv($file, [
                        $p->id,
                        $p->name,
                        $p->sku,
                        $p->category->name ?? 'None',
                        $p->qty,
                        $p->price,
                        $p->cost_price,
                        $p->status ? 'Active' : 'Inactive'
                    ]);
                }
            } else {
                fputcsv($file, ['Report Export', date('Y-m-d H:i')]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
