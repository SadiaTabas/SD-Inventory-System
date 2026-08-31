<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('customer')
            ->latest()
            ->get();

        return view('orders.index', compact('orders'));
    }


    public function create()
    {
        $customers = Customer::where('status', 1)->get();
        $products = Product::where('qty', '>', 0)->get();

        return view('orders.create', compact('customers', 'products'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|string',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($request->quantity > $product->qty) {
            return back()
                ->withErrors(['quantity' => 'Not enough product stock available.'])
                ->withInput();
        }

        $subtotal = $product->price * $request->quantity;

        $order = Order::create([
            'customer_id' => $request->customer_id,
            'total_amount' => $subtotal,
            'status' => 'pending',
            'payment_status' => 'pending',
            'delivery_status' => 'pending',
            'shipping_address' => $request->shipping_address,
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'price' => $product->price,
            'subtotal' => $subtotal,
        ]);

        $product->decrement('qty', $request->quantity);

        return redirect()
            ->route('order.index')
            ->with('success', 'Order created successfully.');
    }


    public function show(Order $order)
    {
        $order->load('customer', 'items.product');

        return view('orders.show', compact('order'));
    }


    public function edit(Order $order)
    {
        $customers = Customer::where('status', 1)->get();

        return view('orders.edit', compact('order', 'customers'));
    }


    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,completed,cancelled',
            'payment_status' => 'required|in:pending,paid,failed',
            'delivery_status' => 'required|in:pending,processing,shipped,delivered',
            'shipping_address' => 'required|string',
        ]);

        $order->update([
            'status' => $request->status,
            'payment_status' => $request->payment_status,
            'delivery_status' => $request->delivery_status,
            'shipping_address' => $request->shipping_address,
        ]);

        return redirect()
            ->route('order.index')
            ->with('success', 'Order updated successfully.');
    }


    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()
            ->route('order.index')
            ->with('success', 'Order deleted successfully.');
    }
}