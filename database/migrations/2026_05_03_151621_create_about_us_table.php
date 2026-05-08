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
        Schema::create('about_us', function (Blueprint $table) {
            $table->id();
            $table->string('image_url')->nullable();          // left-side photo
            $table->string('heading');                        // "We Deliver the Best"
            $table->string('heading_highlight')->nullable();  // "Shopping Experience" (coloured span)
            $table->text('body');                             // paragraph text
            $table->jsonb('features')->nullable();            // ["Nationwide delivery…", "100% secure…"]
            $table->string('cta_label', 100)->nullable();     // "Learn More"
            $table->string('cta_link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_us');
    }
};
