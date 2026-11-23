<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\ProductReview;
use App\Models\StockMovement;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'qty',
        'price',
        'cost_price',
        'min_stock_level',
        'description',
        'category_id',
        'image',
        'status',
        'featured',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'price'           => 'float',
        'cost_price'      => 'float',
        'qty'             => 'integer',
        'min_stock_level' => 'integer',
        'status'          => 'boolean',
        'featured'        => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'name');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'name');
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class)->where('status', 'approved');
    }

    public function allReviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class)->latest();
    }

    public function getAverageRatingAttribute(): float
    {
        return (float) round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    public function getImageUrlAttribute(): string
    {
        if (empty($this->image)) {
            return asset('uploads/products/default.png');
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        return asset($this->image);
    }

    public function getIsInStockAttribute(): bool
    {
        return $this->status && $this->qty > 0;
    }
}
