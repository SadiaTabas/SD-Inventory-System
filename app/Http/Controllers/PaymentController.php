<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderPayment;
use App\Models\Order;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = OrderPayment::with(['order.user', 'order.customer', 'user'])->latest();

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('verification_status')) {
            $query->where('verification_status', $request->verification_status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($oq) use ($search) {
                      $oq->where('order_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $payments = $query->paginate(15)->withQueryString();

        $totalCollected = OrderPayment::where('verification_status', 'verified')->sum('amount');
        $pendingVerificationCount = OrderPayment::where('verification_status', 'pending')->count();

        return view('payments.index', compact('payments', 'totalCollected', 'pendingVerificationCount'));
    }

    public function verify(Request $request, OrderPayment $payment)
    {
        $request->validate([
            'action' => 'required|in:verify,reject',
            'note'   => 'nullable|string|max:255',
        ]);

        $order = $payment->order;

        DB::transaction(function () use ($payment, $order, $request) {
            if ($request->action === 'verify') {
                $payment->update([
                    'verification_status' => 'verified',
                    'payment_status'      => 'paid',
                    'note'                => $payment->note . ' (Verified by ' . Auth::user()->name . ')',
                ]);

                if ($order) {
                    $newPaid = $order->paid_amount + $payment->amount;
                    $newDue = max(0, $order->total_amount - $newPaid);
                    $paymentStatus = $newDue <= 0 ? 'paid' : 'partially_paid';

                    $order->update([
                        'paid_amount'    => $newPaid,
                        'due_amount'     => $newDue,
                        'payment_status' => $paymentStatus,
                        'updated_by'     => Auth::user()->name,
                    ]);
                }

                ActivityLog::log('Payment Verified', 'OrderPayment', $payment->id, "Transaction {$payment->transaction_id} verified.");
            } else {
                $payment->update([
                    'verification_status' => 'rejected',
                    'payment_status'      => 'failed',
                    'note'                => $payment->note . ' (Rejected: ' . ($request->note ?? 'Invalid reference') . ')',
                ]);

                ActivityLog::log('Payment Rejected', 'OrderPayment', $payment->id, "Transaction {$payment->transaction_id} rejected.");
            }
        });

        return back()->with('success', 'Payment status updated successfully.');
    }
}
