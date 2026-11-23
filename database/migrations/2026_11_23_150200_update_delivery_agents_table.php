<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_agents', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_agents', 'name')) {
                $table->string('name')->nullable()->after('id');
            }
            if (!Schema::hasColumn('delivery_agents', 'phone')) {
                $table->string('phone', 50)->nullable()->after('name');
            }
            if (!Schema::hasColumn('delivery_agents', 'email')) {
                $table->string('email', 100)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('delivery_agents', 'emergency_contact')) {
                $table->string('emergency_contact', 50)->nullable()->after('email');
            }
            if (!Schema::hasColumn('delivery_agents', 'vehicle_type')) {
                $table->string('vehicle_type', 50)->nullable()->after('emergency_contact');
            }
            if (!Schema::hasColumn('delivery_agents', 'vehicle_number')) {
                $table->string('vehicle_number', 50)->nullable()->after('vehicle_type');
            }
            if (!Schema::hasColumn('delivery_agents', 'address')) {
                $table->text('address')->nullable()->after('vehicle_number');
            }
            if (!Schema::hasColumn('delivery_agents', 'created_by')) {
                $table->string('created_by')->nullable()->after('status');
            }
            if (!Schema::hasColumn('delivery_agents', 'updated_by')) {
                $table->string('updated_by')->nullable()->after('created_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('delivery_agents', function (Blueprint $table) {
            $cols = ['name', 'phone', 'email', 'emergency_contact', 'vehicle_type', 'vehicle_number', 'address', 'created_by', 'updated_by'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('delivery_agents', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
