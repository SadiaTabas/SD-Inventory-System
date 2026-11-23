<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryAgent;
use App\Models\Order;
use App\Models\Delivery;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    public function index()
    {
        $agents = DeliveryAgent::withCount('orders')->latest()->paginate(15);
        $pendingDeliveries = Order::whereNotNull('delivery_agent_id')
            ->whereNotIn('delivery_status', ['delivered', 'failed'])
            ->with(['deliveryAgent', 'user'])
            ->latest()
            ->take(10)
            ->get();

        return view('deliveries.index', compact('agents', 'pendingDeliveries'));
    }

    public function create()
    {
        return view('deliveries.create-agent');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'phone'             => 'required|string|max:50',
            'email'             => 'nullable|email|max:100',
            'emergency_contact' => 'nullable|string|max:50',
            'vehicle_type'      => 'nullable|string|max:50',
            'vehicle_number'    => 'nullable|string|max:50',
            'address'           => 'nullable|string|max:500',
            'status'            => 'required|in:available,busy,inactive',
        ]);

        $agent = DeliveryAgent::create([
            'name'              => $request->name,
            'phone'             => $request->phone,
            'email'             => $request->email,
            'emergency_contact' => $request->emergency_contact,
            'vehicle_type'      => $request->vehicle_type,
            'vehicle_number'    => $request->vehicle_number,
            'address'           => $request->address,
            'status'            => $request->status,
            'created_by'        => Auth::user()->name,
        ]);

        ActivityLog::log('Delivery Agent Created', 'DeliveryAgent', $agent->id, "Rider {$agent->name} created.");

        return redirect()->route('deliveries.index')->with('success', 'Delivery rider registered successfully.');
    }

    public function edit(DeliveryAgent $delivery)
    {
        $agent = $delivery;
        return view('deliveries.edit-agent', compact('agent'));
    }

    public function update(Request $request, DeliveryAgent $delivery)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'phone'             => 'required|string|max:50',
            'email'             => 'nullable|email|max:100',
            'emergency_contact' => 'nullable|string|max:50',
            'vehicle_type'      => 'nullable|string|max:50',
            'vehicle_number'    => 'nullable|string|max:50',
            'address'           => 'nullable|string|max:500',
            'status'            => 'required|in:available,busy,inactive',
        ]);

        $delivery->update($request->all() + ['updated_by' => Auth::user()->name]);

        ActivityLog::log('Delivery Agent Updated', 'DeliveryAgent', $delivery->id, "Rider {$delivery->name} updated.");

        return redirect()->route('deliveries.index')->with('success', 'Rider details updated successfully.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'delivery_status' => 'required|in:pending,assigned,out_for_delivery,delivered,failed',
            'delivery_notes'  => 'nullable|string',
        ]);

        $order->update([
            'delivery_status' => $request->delivery_status,
            'delivery_notes'  => $request->delivery_notes ?? $order->delivery_notes,
            'updated_by'      => Auth::user()->name,
        ]);

        if ($request->delivery_status === 'delivered') {
            $order->update(['status' => 'delivered']);
        }

        $order->statusHistories()->create([
            'status'     => $order->status,
            'comment'    => "Delivery status updated to: " . ucfirst(str_replace('_', ' ', $request->delivery_status)),
            'changed_by' => Auth::user()->name,
        ]);

        ActivityLog::log('Delivery Status Updated', 'Order', $order->id, "Order #{$order->order_number} delivery status set to {$request->delivery_status}");

        return back()->with('success', 'Delivery status updated.');
    }
}
