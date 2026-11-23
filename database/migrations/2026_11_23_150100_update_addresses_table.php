<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            if (!Schema::hasColumn('addresses', 'full_name')) {
                $table->string('full_name')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('addresses', 'phone')) {
                $table->string('phone', 50)->nullable()->after('full_name');
            }
            if (!Schema::hasColumn('addresses', 'address_line')) {
                $table->text('address_line')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('addresses', 'area')) {
                $table->string('area', 100)->nullable()->after('address_line');
            }
            if (!Schema::hasColumn('addresses', 'city')) {
                $table->string('city', 100)->nullable()->after('area');
            }
            if (!Schema::hasColumn('addresses', 'district')) {
                $table->string('district', 100)->nullable()->after('city');
            }
            if (!Schema::hasColumn('addresses', 'postal_code')) {
                $table->string('postal_code', 20)->nullable()->after('district');
            }
            if (!Schema::hasColumn('addresses', 'delivery_instructions')) {
                $table->text('delivery_instructions')->nullable()->after('postal_code');
            }
            if (!Schema::hasColumn('addresses', 'type')) {
                $table->string('type', 30)->default('home')->after('delivery_instructions'); // home, office, other
            }
            if (!Schema::hasColumn('addresses', 'is_default')) {
                $table->boolean('is_default')->default(false)->after('type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $cols = ['full_name', 'phone', 'address_line', 'area', 'city', 'district', 'postal_code', 'delivery_instructions', 'type', 'is_default'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('addresses', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
