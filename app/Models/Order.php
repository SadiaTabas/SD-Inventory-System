<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\DeliveryAgent;

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
        'total_amount',
        'status',
        'payment_status',
        'payment_method',
        'delivery_status',
        'shipping_address',
        'delivery_slot',
        'delivery_man_name',
        'delivery_man_phone',
        'delivery_notes',
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

  
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    
    public function deliveryAgent()
    {
        return $this->belongsTo(DeliveryAgent::class, 'delivery_agent_id');
    }

     
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}