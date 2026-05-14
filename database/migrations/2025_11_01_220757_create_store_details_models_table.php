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
        Schema::create('store_details_models', function (Blueprint $table) {
        $table->id();
        $table->string('store_name');
        $table->string('email');
        $table->string('phone');
        $table->string('address', 500);
        $table->text('tagline')->nullable();
        $table->string('logo_path')->nullable();
        $table->json('social_icons')->nullable();
        $table->json('social_links')->nullable();
        $table->json('products')->nullable(); // Stores array of name/price/description
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_details_models');
    }
};
