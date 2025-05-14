<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\{Order, Product, Category};

// Route model binding
Route::model('order', Order::class);
Route::model('product', Product::class);
Route::model('category', Category::class);

// Authentication Routes
Auth::routes();

// Public Website Routes
Route::get('/', [App\Http\Controllers\Website\HomeController::class, 'index'])->name('home');
Route::get('/products', [App\Http\Controllers\Website\ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [App\Http\Controllers\Website\ProductController::class, 'show'])->name('products.show');
Route::get('/categories/{category}', [App\Http\Controllers\Website\ProductController::class, 'category'])->name('products.category');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Customer Routes
    Route::middleware(['throttle:60,1'])->group(function () {
        Route::resource('cart', App\Http\Controllers\Website\CartController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('checkout', App\Http\Controllers\Website\CheckoutController::class)->only(['index', 'store']);
    });
    
    // Account Management
    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/', [App\Http\Controllers\Website\AccountController::class, 'index'])->name('index');
        Route::get('/edit', [App\Http\Controllers\Website\AccountController::class, 'edit'])->name('edit');
        Route::put('/', [App\Http\Controllers\Website\AccountController::class, 'update'])->name('update');
        Route::get('/orders', [App\Http\Controllers\Website\AccountController::class, 'orders'])->name('orders');
        Route::get('/orders/{order}', [App\Http\Controllers\Website\AccountController::class, 'orderShow'])->name('orders.show');
    });
    
    // Admin Routes
   // filepath: c:\xampp\htdocs\byteme\InventoryManagements\routes\web.php
    Route::prefix('admin')->middleware('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');
        Route::resource('users', App\Http\Controllers\Admin\UserController::class);
        Route::resource('departments', App\Http\Controllers\Admin\DepartmentController::class);
    });
    
    // Inventory Routes
    Route::prefix('inventory')->middleware('inventory')->name('inventory.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Inventory\InventoryController::class, 'dashboard'])->name('dashboard');
        Route::resource('products', App\Http\Controllers\Inventory\ProductController::class);
        Route::resource('suppliers', App\Http\Controllers\Inventory\SupplierController::class);
        Route::resource('purchase-orders', App\Http\Controllers\Inventory\PurchaseOrderController::class);
        Route::post('purchase-orders/{purchaseOrder}/approve', [App\Http\Controllers\Inventory\PurchaseOrderController::class, 'approve'])
            ->name('purchase-orders.approve');
        Route::get('stock', [App\Http\Controllers\Inventory\StockController::class, 'index'])->name('stock.index');
        Route::get('alerts', [App\Http\Controllers\Inventory\StockController::class, 'alerts'])->name('stock.alerts');
        Route::post('stock/reorder/{product}', [App\Http\Controllers\Inventory\StockController::class, 'reorder'])->name('stock.reorder');
    });
    
    // Sales Routes
    Route::prefix('sales')->middleware('sales')->name('sales.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Sales\SalesController::class, 'dashboard'])->name('dashboard');
        Route::resource('orders', App\Http\Controllers\Sales\OrderController::class);
        Route::resource('customers', App\Http\Controllers\Sales\CustomerController::class);
        Route::post('orders/{order}/fulfill', [App\Http\Controllers\Sales\OrderController::class, 'fulfill'])->name('orders.fulfill');
        Route::post('orders/{order}/cancel', [App\Http\Controllers\Sales\OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('orders/{order}/refund', [App\Http\Controllers\Sales\OrderController::class, 'refund'])->name('orders.refund');
    });
});