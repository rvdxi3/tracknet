<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockService
{
    public function increase(
        Product $product,
        int $quantity,
        string $reason,
        ?string $notes = null,
        ?Model $reference = null,
        ?int $userId = null
    ): StockMovement {
        return DB::transaction(function () use ($product, $quantity, $reason, $notes, $reference, $userId) {
            $inventory = $product->inventory ?? Inventory::create([
                'product_id' => $product->id,
                'quantity' => 0,
                'low_stock_threshold' => 5,
            ]);

            $inventory->increment('quantity', $quantity);
            $inventory->refresh();

            $movement = StockMovement::create([
                'product_id' => $product->id,
                'user_id' => $userId ?? auth()->id(),
                'type' => StockMovement::TYPE_IN,
                'quantity' => $quantity,
                'reason' => $reason,
                'notes' => $notes,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference?->id,
                'balance_after' => $inventory->quantity,
            ]);

            // Clear low stock / out of stock alerts if back above threshold
            if ($inventory->quantity > $inventory->low_stock_threshold) {
                Alert::where('product_id', $product->id)
                    ->whereIn('type', ['low_stock', 'out_of_stock'])
                    ->where('is_read', false)
                    ->update(['is_read' => true]);
            }

            return $movement;
        });
    }

    public function decrease(
        Product $product,
        int $quantity,
        string $reason,
        ?string $notes = null,
        ?Model $reference = null,
        ?int $userId = null
    ): StockMovement {
        return DB::transaction(function () use ($product, $quantity, $reason, $notes, $reference, $userId) {
            $inventory = $product->inventory;

            if (!$inventory || $inventory->quantity < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Insufficient stock. Current stock: ' . ($inventory->quantity ?? 0),
                ]);
            }

            $inventory->decrement('quantity', $quantity);
            $inventory->refresh();

            $movement = StockMovement::create([
                'product_id' => $product->id,
                'user_id' => $userId ?? auth()->id(),
                'type' => StockMovement::TYPE_OUT,
                'quantity' => $quantity,
                'reason' => $reason,
                'notes' => $notes,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference?->id,
                'balance_after' => $inventory->quantity,
            ]);

            // Create alerts if stock is low or out
            if ($inventory->quantity === 0) {
                Alert::create([
                    'product_id' => $product->id,
                    'type' => 'out_of_stock',
                    'message' => "Product \"{$product->name}\" is out of stock.",
                ]);
            } elseif ($inventory->quantity <= $inventory->low_stock_threshold) {
                Alert::create([
                    'product_id' => $product->id,
                    'type' => 'low_stock',
                    'message' => "Product \"{$product->name}\" is low on stock ({$inventory->quantity} remaining).",
                ]);
            }

            return $movement;
        });
    }
}
