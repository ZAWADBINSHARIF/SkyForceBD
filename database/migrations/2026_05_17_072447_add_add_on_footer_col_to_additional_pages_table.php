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
        Schema::table('additional_pages', function (Blueprint $table) {
            $table->boolean('published')->default(false);
            $table->boolean('add_on_footer')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('_additional_pages', function (Blueprint $table) {
            $table->dropColumn(['published', 'add_on_footer']);
        });
    }
};
