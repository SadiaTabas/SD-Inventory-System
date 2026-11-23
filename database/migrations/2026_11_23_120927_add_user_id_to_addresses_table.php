<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            if (!Schema::hasColumn('addresses', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('addresses', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            if (Schema::hasColumn('addresses', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('addresses', 'customer_id')) {
                $table->dropColumn('customer_id');
            }
        });
    }
};