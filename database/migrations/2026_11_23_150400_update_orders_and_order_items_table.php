<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Make customer_id nullable in orders table
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE orders MODIFY customer_id BIGINT UNSIGNED NULL');
        }

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('order_number')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('orders', 'order_source')) {
                $table->string('order_source', 20)->default('online')->after('user_id'); // 'online' or 'admin'
            }
            if (!Schema::hasColumn('orders', 'subtotal_amount')) {
                $table->decimal('subtotal_amount', 10, 2)->default(0.00)->after('order_source');
            }
            if (!Schema::hasColumn('orders', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0.00)->after('subtotal_amount');
            }
            if (!Schema::hasColumn('orders', 'coupon_code')) {
                $table->string('coupon_code', 50)->nullable()->after('discount_amount');
            }
            if (!Schema::hasColumn('orders', 'paid_amount')) {
                $table->decimal('paid_amount', 10, 2)->default(0.00)->after('total_amount');
            }
            if (!Schema::hasColumn('orders', 'due_amount')) {
                $table->decimal('due_amount', 10, 2)->default(0.00)->after('paid_amount');
            }
            if (!Schema::hasColumn('orders', 'order_placed_by')) {
                $table->string('order_placed_by', 50)->default('customer')->after('due_amount');
            }
            if (!Schema::hasColumn('orders', 'delivery_slot')) {
                $table->string('delivery_slot', 100)->nullable()->after('shipping_address');
            }
            if (!Schema::hasColumn('orders', 'delivery_man_name')) {
                $table->string('delivery_man_name', 100)->nullable()->after('delivery_slot');
            }
            if (!Schema::hasColumn('orders', 'delivery_man_phone')) {
                $table->string('delivery_man_phone', 50)->nullable()->after('delivery_man_name');
            }
            if (!Schema::hasColumn('orders', 'delivery_notes')) {
                $table->text('delivery_notes')->nullable()->after('delivery_man_phone');
            }
            if (!Schema::hasColumn('orders', 'cancel_reason')) {
                $table->text('cancel_reason')->nullable()->after('delivery_notes');
            }
            if (!Schema::hasColumn('orders', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('cancel_reason');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'product_name')) {
                $table->string('product_name')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('order_items', 'product_sku')) {
                $table->string('product_sku', 100)->nullable()->after('product_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $cols = [
                'user_id', 'order_source', 'subtotal_amount', 'discount_amount',
                'coupon_code', 'paid_amount', 'due_amount', 'order_placed_by',
                'delivery_slot', 'delivery_man_name', 'delivery_man_phone',
                'delivery_notes', 'cancel_reason', 'admin_notes'
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    if ($col === 'user_id') {
                        $table->dropForeign(['user_id']);
                    }
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            foreach (['product_name', 'product_sku'] as $col) {
                if (Schema::hasColumn('order_items', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
