<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\{Order, Product, Category};

// Route model binding
Route::model('order', Order::class);
Route::model('product', Product::class);
Route::model('category', Category::class);

// Authentication Routes (reset disabled — using custom OTP-based reset below)
Auth::routes(['reset' => false]);

// Password Reset — OTP-based (3 steps: email → OTP verify → new password)
Route::get('password/reset',        [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email',       [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/otp',          [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showOtpForm'])->name('password.otp');
Route::post('password/otp',         [App\Http\Controllers\Auth\ForgotPasswordController::class, 'verifyOtp'])->name('password.otp.verify');
Route::post('password/otp/resend',  [App\Http\Controllers\Auth\ForgotPasswordController::class, 'resendOtp'])->name('password.otp.resend');
Route::get('password/reset/new',    [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset.form');
Route::post('password/reset',       [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

// Login MFA verification (OTP on every login)
Route::get('login/verify',   [App\Http\Controllers\Auth\LoginController::class, 'showLoginVerify'])->name('login.verify');
Route::post('login/verify',  [App\Http\Controllers\Auth\LoginController::class, 'verifyLoginOtp']);
Route::post('login/resend',  [App\Http\Controllers\Auth\LoginController::class, 'resendLoginOtp'])->name('login.resend');

// MFA Routes (session-guarded, no auth middleware — user is not logged in yet)
Route::prefix('mfa')->name('mfa.')->group(function () {
    Route::get('method',       [App\Http\Controllers\Auth\MfaController::class, 'showMethodSelect'])->name('method');
    Route::post('method',      [App\Http\Controllers\Auth\MfaController::class, 'setMethod'])->name('method.set');
    Route::get('email',        [App\Http\Controllers\Auth\MfaController::class, 'showEmailVerify'])->name('email');
    Route::post('email',       [App\Http\Controllers\Auth\MfaController::class, 'verifyEmail'])->name('email.verify');
    Route::post('email/resend',[App\Http\Controllers\Auth\MfaController::class, 'resendEmail'])->name('email.resend');
    Route::get('totp/setup',   [App\Http\Controllers\Auth\MfaController::class, 'showTotpSetup'])->name('totp.setup');
    Route::post('totp/setup',  [App\Http\Controllers\Auth\MfaController::class, 'confirmTotp'])->name('totp.confirm');
    Route::get('pending',      [App\Http\Controllers\Auth\MfaController::class, 'showPending'])->name('pending');
});

// PayMongo Webhook (no auth, no CSRF — verified by signature)
Route::post('webhooks/paymongo', [App\Http\Controllers\Website\PayMongoWebhookController::class, 'handle'])
    ->name('webhooks.paymongo');

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
        Route::get('checkout/success/{order}', [App\Http\Controllers\Website\CheckoutController::class, 'success'])->name('checkout.success');
        Route::get('checkout/cancel/{order}', [App\Http\Controllers\Website\CheckoutController::class, 'cancel'])->name('checkout.cancel');
    });
    
    // Account Management
    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/', [App\Http\Controllers\Website\AccountController::class, 'index'])->name('index');
        Route::get('/edit', [App\Http\Controllers\Website\AccountController::class, 'edit'])->name('edit');
        Route::put('/', [App\Http\Controllers\Website\AccountController::class, 'update'])->name('update');
        Route::get('/orders', [App\Http\Controllers\Website\AccountController::class, 'orders'])->name('orders');
        Route::get('/orders/{order}', [App\Http\Controllers\Website\AccountController::class, 'orderShow'])->name('orders.show');
        Route::get('/orders/{order}/receipt', [App\Http\Controllers\Website\AccountController::class, 'orderReceipt'])->name('orders.receipt');
        Route::get('/orders/{order}/receipt/pdf', [App\Http\Controllers\Website\AccountController::class, 'orderReceiptPdf'])->name('orders.receipt.pdf');
        Route::post('/orders/{order}/cancel', [App\Http\Controllers\Website\AccountController::class, 'cancelOrder'])->name('orders.cancel');
    });
    
    // Admin Routes
    Route::prefix('admin')->middleware('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');
        Route::resource('users', App\Http\Controllers\Admin\UserController::class);
        Route::resource('departments', App\Http\Controllers\Admin\DepartmentController::class);
        // Pending user approvals
        Route::get('/pending-users', [App\Http\Controllers\Admin\PendingUserController::class, 'index'])->name('pending-users.index');
        Route::post('/pending-users/{user}/approve', [App\Http\Controllers\Admin\PendingUserController::class, 'approve'])->name('pending-users.approve');
        Route::post('/pending-users/{user}/reject', [App\Http\Controllers\Admin\PendingUserController::class, 'reject'])->name('pending-users.reject');
        // Customer Management
        Route::resource('customers', App\Http\Controllers\Admin\CustomerController::class)->only(['index', 'show', 'edit', 'update']);
        // Activity Log
        Route::get('/activity-log', [App\Http\Controllers\Admin\AdminController::class, 'activityLog'])->name('activity-log');
    });
    
    // Inventory Routes
    Route::prefix('inventory')->middleware('inventory')->name('inventory.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Inventory\InventoryController::class, 'dashboard'])->name('dashboard');
        Route::resource('products', App\Http\Controllers\Inventory\ProductController::class);
        Route::resource('suppliers', App\Http\Controllers\Inventory\SupplierController::class);
        Route::resource('purchase-orders', App\Http\Controllers\Inventory\PurchaseOrderController::class);
        Route::post('purchase-orders/{purchaseOrder}/approve', [App\Http\Controllers\Inventory\PurchaseOrderController::class, 'approve'])
            ->name('purchase-orders.approve');
        Route::post('purchase-orders/{purchaseOrder}/receive', [App\Http\Controllers\Inventory\PurchaseOrderController::class, 'receive'])
            ->name('purchase-orders.receive');
        Route::post('purchase-orders/{purchaseOrder}/cancel', [App\Http\Controllers\Inventory\PurchaseOrderController::class, 'cancel'])
            ->name('purchase-orders.cancel');
        Route::get('stock', [App\Http\Controllers\Inventory\StockController::class, 'index'])->name('stock.index');
        Route::get('alerts', [App\Http\Controllers\Inventory\StockController::class, 'alerts'])->name('stock.alerts');
        Route::get('stock/movements', [App\Http\Controllers\Inventory\StockController::class, 'movements'])->name('stock.movements');
        Route::get('stock/reorder/{product}', [App\Http\Controllers\Inventory\StockController::class, 'reorder'])->name('stock.reorder');
        Route::post('stock/reorder/{product}', [App\Http\Controllers\Inventory\StockController::class, 'processReorder'])->name('stock.processReorder');
        Route::post('stock/in/{product}', [App\Http\Controllers\Inventory\StockController::class, 'stockIn'])->name('stock.in');
        Route::post('stock/out/{product}', [App\Http\Controllers\Inventory\StockController::class, 'stockOut'])->name('stock.out');
    });
    
    // Sales Routes
    Route::prefix('sales')->middleware('sales')->name('sales.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Sales\SalesController::class, 'dashboard'])->name('dashboard');
        Route::resource('orders', App\Http\Controllers\Sales\OrderController::class);
        Route::post('orders/{order}/fulfill', [App\Http\Controllers\Sales\OrderController::class, 'fulfill'])->name('orders.fulfill');
        Route::post('orders/{order}/cancel', [App\Http\Controllers\Sales\OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('orders/{order}/refund', [App\Http\Controllers\Sales\OrderController::class, 'refund'])->name('orders.refund');
        Route::get('orders/{order}/receipt', [App\Http\Controllers\Sales\OrderController::class, 'receipt'])->name('orders.receipt');
        Route::get('orders/{order}/receipt/pdf', [App\Http\Controllers\Sales\OrderController::class, 'receiptPdf'])->name('orders.receipt.pdf');
        // Reports & Analytics
        Route::get('/reports', [App\Http\Controllers\Sales\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [App\Http\Controllers\Sales\ReportController::class, 'export'])->name('reports.export');
        Route::get('/reports/export-pdf', [App\Http\Controllers\Sales\ReportController::class, 'exportPdf'])->name('reports.export-pdf');
    });
});