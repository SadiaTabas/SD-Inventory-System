<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'amount',
        'payment_method',
        'transaction_id',
        'payment_date',
        'note',
        'recorded_by',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}