<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class DeliveryAgent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'emergency_contact',
        'vehicle_type',
        'vehicle_number',
        'address',
        'status', // available, busy, inactive
        'created_by',
        'updated_by',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'delivery_agent_id');
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'delivery_agent_id');
    }
}
