<?php

// app/Models/Order.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'subtotal',
        'tax',
        'shipping',
        'total',
        'payment_method',
        'shipping_address',
        'billing_address',
        'notes',
        'paymongo_checkout_session_id',
        'paymongo_payment_intent_id',
        'payment_status',
        'receipt_pdf_path',
    ];

    protected $casts = [
        'shipping_address' => 'encrypted',
        'billing_address'  => 'encrypted',
        'notes'            => 'encrypted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class); // customer
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function sale()
    {
        return $this->hasOne(Sale::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isAwaitingPayment(): bool
    {
        return $this->payment_status === 'awaiting_payment';
    }
}
