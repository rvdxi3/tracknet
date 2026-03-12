<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(false)->after('role');
            $table->string('mfa_method')->nullable()->after('is_active');       // 'email' | 'totp'
            $table->text('mfa_secret')->nullable()->after('mfa_method');        // encrypted TOTP secret
            $table->timestamp('mfa_verified_at')->nullable()->after('mfa_secret');
            $table->timestamp('approved_at')->nullable()->after('mfa_verified_at');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
            $table->timestamp('rejected_at')->nullable()->after('approved_by');
            $table->text('rejection_reason')->nullable()->after('rejected_at');

            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'is_active', 'mfa_method', 'mfa_secret', 'mfa_verified_at',
                'approved_at', 'approved_by', 'rejected_at', 'rejection_reason',
            ]);
        });
    }
};
