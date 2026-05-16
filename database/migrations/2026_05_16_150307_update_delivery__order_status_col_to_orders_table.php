<?php

use App\Enums\DeliveryStatus;
use App\Enums\OrderStatus;
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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_status')->nullable()->default(null)->change();
            $table->string('order_status')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_status')->default(DeliveryStatus::Pending->value)->change();
            $table->string('order_status')->default(OrderStatus::OrderRequest->value)->change();
        });
    }
};
