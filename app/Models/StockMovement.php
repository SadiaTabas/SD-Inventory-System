<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type', // initial, purchase, sale, adjustment, cancellation, return
        'quantity',
        'previous_stock',
        'new_stock',
        'reference_type',
        'reference_id',
        'user_id',
        'user_name',
        'reason',
    ];

    protected $casts = [
        'quantity'       => 'integer',
        'previous_stock' => 'integer',
        'new_stock'      => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
