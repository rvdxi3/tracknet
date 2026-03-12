<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    const TYPE_IN = 'stock_in';
    const TYPE_OUT = 'stock_out';

    const REASON_PURCHASE_ORDER = 'purchase_order';
    const REASON_CUSTOMER_RETURN = 'customer_return';
    const REASON_MANUAL_ADJUSTMENT = 'manual_adjustment';
    const REASON_DAMAGED = 'damaged';
    const REASON_LOST_STOLEN = 'lost_stolen';
    const REASON_EXPIRED = 'expired';
    const REASON_CUSTOMER_ORDER = 'customer_order';
    const REASON_ORDER_CANCELLED = 'order_cancelled';
    const REASON_ORDER_REFUNDED = 'order_refunded';
    const REASON_PAYMENT_FAILED = 'payment_failed';

    protected $fillable = [
        'product_id', 'user_id', 'type', 'quantity',
        'reason', 'notes', 'reference_type', 'reference_id',
        'balance_after',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    public static function reasonLabel(string $reason): string
    {
        return match ($reason) {
            self::REASON_PURCHASE_ORDER => 'Purchase Order',
            self::REASON_CUSTOMER_RETURN => 'Customer Return',
            self::REASON_MANUAL_ADJUSTMENT => 'Manual Adjustment',
            self::REASON_DAMAGED => 'Damaged',
            self::REASON_LOST_STOLEN => 'Lost / Stolen',
            self::REASON_EXPIRED => 'Expired',
            self::REASON_CUSTOMER_ORDER => 'Customer Order',
            self::REASON_ORDER_CANCELLED => 'Order Cancelled',
            self::REASON_ORDER_REFUNDED => 'Order Refunded',
            self::REASON_PAYMENT_FAILED => 'Payment Failed',
            default => ucfirst(str_replace('_', ' ', $reason)),
        };
    }
}
