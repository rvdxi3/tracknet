<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('paymongo_checkout_session_id')->nullable()->after('notes');
            $table->string('paymongo_payment_intent_id')->nullable()->after('paymongo_checkout_session_id');
            $table->string('payment_status')->default('pending')->after('paymongo_payment_intent_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'paymongo_checkout_session_id',
                'paymongo_payment_intent_id',
                'payment_status',
            ]);
        });
    }
};
