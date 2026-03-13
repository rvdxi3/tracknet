<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // email_hash: SHA-256 hash for lookups (email column will be encrypted, not queryable)
            $table->string('email_hash', 64)->nullable()->after('email');
        });

        // Change email column to text to hold encrypted values
        Schema::table('users', function (Blueprint $table) {
            $table->text('email')->change();
        });

        // Drop the unique index on email, add it on email_hash instead
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->unique('email_hash');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email_hash']);
            $table->dropColumn('email_hash');
            $table->string('email', 255)->unique()->change();
        });
    }
};
