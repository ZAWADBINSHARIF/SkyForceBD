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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('transaction_number')->unique();
            $table->string('validation_id')->nullable();
            $table->string('card_brand')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('card_issuer_country')->nullable();
            $table->decimal('payment_amount', 12, 2)->nullable();
            $table->decimal('store_amount', 12, 2)->nullable();
            $table->string('status')->default('pending');
            $table->string('bank_transaction_image')->nullable();
            $table->boolean('bank_payment_approved')->nullable();
            $table->jsonb('payment_info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
