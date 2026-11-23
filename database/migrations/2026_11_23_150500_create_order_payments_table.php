<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('order_payments')) {
            Schema::create('order_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->decimal('amount', 10, 2);
                $table->string('payment_method', 50)->default('cash'); // cod, bkash, nagad, cash
                $table->string('transaction_id', 100)->nullable();
                $table->date('payment_date')->nullable();
                $table->string('payment_status', 30)->default('pending'); // pending, paid, partially_paid, failed, refunded
                $table->string('verification_status', 30)->default('pending'); // pending, verified, rejected
                $table->text('note')->nullable();
                $table->string('recorded_by')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
