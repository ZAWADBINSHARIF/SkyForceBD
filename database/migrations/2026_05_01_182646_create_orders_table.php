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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('order_number')->unique();     // #SKY10001
            $table->timestamp('order_receive_date')->useCurrent();
            $table->string('delivery_status')->default('pending');
            $table->date('delivery_date')->nullable();
            $table->jsonb('products');                   // [{name,product_link,quantity,unit_price,total_price}]
            $table->string('work_process')->default('pending');
            $table->string('customer_name')->nullable();
            $table->string('customer_phone');
            $table->text('customer_address')->nullable();
            $table->text('customer_remark')->nullable();
            $table->text('employee_remark')->nullable();
            $table->timestamp('order_place_date')->nullable();
            $table->text('purchase_product_link')->nullable();
            $table->string('shipment_type')->nullable();
            $table->string('order_call')->nullable();
            $table->decimal('total_price', 12, 2)->default(0);
            $table->decimal('shipping_charge', 12, 2)->nullable()->default(0);
            $table->decimal('product_weight', 10, 3)->nullable();
            $table->decimal('advance_payment', 12, 2)->nullable()->default(0);
            $table->decimal('due_payment', 12, 2)->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
