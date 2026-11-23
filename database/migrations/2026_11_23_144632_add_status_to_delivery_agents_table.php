<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('delivery_agents', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_agents', 'status')) {
                $table->string('status')->default('available');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_agents', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_agents', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};