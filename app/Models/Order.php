<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\DeliveryAgent;
use App\Models\OrderPayment;
use App\Models\OrderStatusHistory;
use App\Models\Delivery;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_id',
        'order_source', // online, admin
        'delivery_agent_id',
        'order_placed_by',
        'created_by',
        'updated_by',
        'subtotal_amount',
        'discount_amount',
        'coupon_code',
        'delivery_charge',
        'total_amount',
        'paid_amount',
        'due_amount',
        'status', // pending, processing, shipped, out_for_delivery, delivered, cancelled
        'payment_status', // pending, partially_paid, paid, refunded
        'payment_method', // cod, bkash, nagad, cash
        'delivery_status', // pending, assigned, out_for_delivery, delivered, failed
        'shipping_address',
        'delivery_slot',
        'delivery_man_name',
        'delivery_man_phone',
        'delivery_notes',
        'cancel_reason',
        'admin_notes',
    ];

    protected $casts = [
        'subtotal_amount' => 'float',
        'discount_amount' => 'float',
        'delivery_charge' => 'float',
        'total_amount'    => 'float',
        'paid_amount'     => 'float',
        'due_amount'      => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault(function () {
            // fallback if customer_id was used
            return new User(['name' => 'Walk-in Customer']);
        });
    }

    public function deliveryAgent()
    {
        return $this->belongsTo(DeliveryAgent::class, 'delivery_agent_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function payments()
    {
        return $this->hasMany(OrderPayment::class, 'order_id')->latest();
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class, 'order_id')->latest();
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class, 'order_id');
    }

    public function canBeEdited(): bool
    {
        return $this->created_at->gt(now()->subDays(2)) && !in_array($this->status, ['delivered', 'cancelled']);
    }

    public function canBeCancelled(): bool
    {
        return !in_array($this->status, ['delivered', 'cancelled']);
    }
}
