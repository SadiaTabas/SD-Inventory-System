<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\DeliveryAgent;
use App\Models\OrderPayment;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_id',
        'delivery_agent_id',
        'order_placed_by',
        'created_by',
        'updated_by',
        'subtotal_amount',
        'discount_amount',
        'coupon_code',
        'total_amount',
        'paid_amount',
        'due_amount',
        'status',
        'payment_status',
        'payment_method',
        'delivery_status',
        'shipping_address',
        'delivery_slot',
        'delivery_man_name',
        'delivery_man_phone',
        'delivery_notes',
        'delivery_charge'
    ];

    /*
      The user account associated with the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /*

     Alias relationship to retrieve customer details.
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /*
     * The assigned delivery agent.
     */
    public function deliveryAgent()
    {
        return $this->belongsTo(DeliveryAgent::class, 'delivery_agent_id');
    }

    /*
     * The items belonging to this order.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /*
     * The partial/advance payment transactions for this order.
     */
    public function payments()
    {
        return $this->hasMany(OrderPayment::class, 'order_id')->latest();
    }
}