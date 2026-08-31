<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\DeliveryAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'deliveryAgent', 'items.product'])
            ->latest()
            ->get();

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $customers = User::where('role', 'customer')
            ->where('status', 1)
            ->with('addresses')
            ->get();

        $products = Product::where('qty', '>', 0)->get();
        $deliveryAgents = DeliveryAgent::where('status', 'available')->orWhere('status', 1)->get();

        return view('orders.create', compact('customers', 'products', 'deliveryAgents'));
    }

    public function store(Request $request)
    {
        $authUser = Auth::user();
        $isAdmin = $authUser && $authUser->role === 'admin';

        $rules = [
            'product_id'        => 'required|exists:products,id',
            'quantity'          => 'required|integer|min:1',
            'shipping_address'  => 'required|string',
            'payment_method'    => 'nullable|string',
            'delivery_agent_id' => 'nullable|exists:delivery_agents,id',
            'delivery_slot'     => 'nullable|string',
            'delivery_notes'    => 'nullable|string',
        ];

        if ($isAdmin) {
            $rules['user_id'] = 'required|exists:users,id';
        }

        $request->validate($rules);

        $targetUserId = $isAdmin ? $request->user_id : $authUser->id;

        DB::transaction(function () use ($request, $targetUserId, $isAdmin, $authUser) {
            $product = Product::lockForUpdate()->findOrFail($request->product_id);

            if ($request->quantity > $product->qty) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'quantity' => 'Not enough product stock available. Only ' . $product->qty . ' left.',
                ]);
            }

            $subtotal = $product->price * $request->quantity;

            $agentName = null;
            $agentPhone = null;
            if ($request->filled('delivery_agent_id')) {
                $agent = DeliveryAgent::find($request->delivery_agent_id);
                if ($agent) {
                    $agentName = $agent->name;
                    $agentPhone = $agent->phone;
                }
            }

            $order = Order::create([
                'order_number'       => 'ORD-' . strtoupper(uniqid()),
                'user_id'            => $targetUserId,
                'customer_id'        => null,
                'delivery_agent_id'  => $request->delivery_agent_id ?: null,
                'order_placed_by'    => $isAdmin ? 'admin' : 'customer',
                'created_by'         => $authUser->name ?? 'System',
                'total_amount'       => $subtotal,
                'status'             => 'pending',
                'payment_status'     => 'pending',
                'payment_method'     => $request->payment_method ?? 'cod',
                'delivery_status'    => 'pending',
                'shipping_address'   => $request->shipping_address,
                'delivery_slot'      => $request->delivery_slot,
                'delivery_man_name'  => $agentName,
                'delivery_man_phone' => $agentPhone,
                'delivery_notes'     => $request->delivery_notes,
            ]);

            $order->items()->create([
                'product_id' => $product->id,
                'quantity'   => $request->quantity,
                'price'      => $product->price,
                'subtotal'   => $subtotal,
            ]);

            $product->decrement('qty', $request->quantity);
        });

        return redirect()
            ->route('order.index')
            ->with('success', 'Order created successfully.');
    }

    public function show(Order $order)
    {
        $order->load(['user', 'deliveryAgent', 'items.product']);

        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $customers = User::where('role', 'customer')->where('status', 1)->get();
        $deliveryAgents = DeliveryAgent::all();

        return view('orders.edit', compact('order', 'customers', 'deliveryAgents'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status'            => 'required|string',
            'payment_status'    => 'required|string',
            'payment_method'    => 'nullable|string',
            'delivery_status'   => 'required|string',
            'shipping_address'  => 'required|string',
            'delivery_agent_id' => 'nullable|exists:delivery_agents,id',
            'delivery_slot'     => 'nullable|string',
            'delivery_notes'    => 'nullable|string',
        ]);

        $agentName = $order->delivery_man_name;
        $agentPhone = $order->delivery_man_phone;

        if ($request->filled('delivery_agent_id') && $request->delivery_agent_id != $order->delivery_agent_id) {
            $agent = DeliveryAgent::find($request->delivery_agent_id);
            if ($agent) {
                $agentName = $agent->name;
                $agentPhone = $agent->phone;
            }
        } elseif (!$request->filled('delivery_agent_id')) {
            $agentName = null;
            $agentPhone = null;
        }

        if ($request->status === 'cancelled' && $order->status !== 'cancelled') {
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('qty', $item->quantity);
                }
            }
        }

        $order->update([
            'delivery_agent_id'  => $request->delivery_agent_id ?: null,
            'status'             => $request->status,
            'payment_status'     => $request->payment_status,
            'payment_method'     => $request->payment_method ?? $order->payment_method,
            'delivery_status'    => $request->delivery_status,
            'shipping_address'   => $request->shipping_address,
            'delivery_slot'      => $request->delivery_slot,
            'delivery_man_name'  => $agentName,
            'delivery_man_phone' => $agentPhone,
            'delivery_notes'     => $request->delivery_notes,
            'updated_by'         => Auth::user()->name ?? 'System',
        ]);

        return redirect()
            ->route('order.index')
            ->with('success', 'Order updated successfully.');
    }

    public function destroy(Order $order)
    {
        if ($order->status !== 'cancelled') {
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('qty', $item->quantity);
                }
            }
        }

        $order->delete();

        return redirect()
            ->route('order.index')
            ->with('success', 'Order deleted successfully.');
    }
}