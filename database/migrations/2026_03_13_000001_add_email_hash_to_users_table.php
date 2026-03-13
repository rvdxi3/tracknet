<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add email_hash column if it doesn't already exist
        if (!Schema::hasColumn('users', 'email_hash')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('email_hash', 64)->nullable()->after('email');
            });
        }

        // Change email column to text to hold encrypted values
        Schema::table('users', function (Blueprint $table) {
            $table->text('email')->change();
        });

        // Drop the unique index on email (if exists), add unique on email_hash
        $indexes = collect(Schema::getIndexes('users'))->pluck('name')->toArray();

        if (in_array('users_email_unique', $indexes)) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['email']);
            });
        }

        if (!in_array('users_email_hash_unique', $indexes)) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('email_hash');
            });
        }
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
