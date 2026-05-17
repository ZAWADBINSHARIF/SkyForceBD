<?php

use App\Enums\PurchaseStatus;
use App\Models\ProductPurchase;
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
        Schema::create('product_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();

            // Product info — snapshot from order.products JSON
            $table->string('product_title');
            $table->string('customer_product_link')->nullable();
            $table->decimal('product_buy_amount')->default(0);   // what customer pays

            // Customer snapshot
            $table->string('customer_name')->nullable();
            $table->string('phone_number', 30)->nullable();

            // Purchase details
            $table->string('pay_done_by')->nullable();                   // who made the purchase
            $table->string('ecommerce_platform')->nullable();            // Taobao, 1688, Amazon…
            $table->string('receiver')->nullable();                      // who receives the product
            $table->string('account_name')->nullable();                  // which account was used

            // Logistics
            $table->string('status')->default(PurchaseStatus::Pending->value);                // pending | purchased | shipped | arrived | delivered
            $table->string('logistics_company')->nullable();
            $table->string('logistics_tracking')->nullable();
            $table->string('information_link')->nullable();
            $table->string('courier_entry')->nullable();

            // Financials
            $table->decimal('product_purchase_price')->nullable();  // actual cost to buy
            $table->decimal('shipping_and_extra_cost')->default(0);
            $table->decimal('profit')->default(0);                   // auto-calculated

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_purchases');
    }
};
