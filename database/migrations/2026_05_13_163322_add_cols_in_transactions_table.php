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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('account_holder_name');
            $table->string('bank_branch')->nullable()->after('bank_name');
            $table->string('bank_account_number')->nullable()->after('bank_branch');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'bank_name',
                'bank_branch',
                'bank_account_number'
            ]);
        });
    }
};
