<?php

// app/Models/Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'sku',
        'image',
        'is_featured'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return Storage::disk('s3')->url($this->image);
        }
        return asset('images/no-image.png');
    }

    public function getStockAttribute()
    {
        return $this->inventory ? $this->inventory->quantity : 0;
    }

    public function isLowStock()
    {
        if (!$this->inventory) return false;
        return $this->inventory->quantity <= $this->inventory->low_stock_threshold;
    }
}