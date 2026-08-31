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
        'emergency_contact',
        'vehicle_type',
        'vehicle_number',
        'address',
        'status',
        'created_by',
        'updated_by',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'delivery_agent_id');
    }
}