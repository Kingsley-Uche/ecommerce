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
        Schema::create('category_assigns', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                  ->constrained('product_models')
                  ->cascadeOnDelete();

            $table->foreignId('category_id')
                  ->constrained('product_category_models')
                  ->cascadeOnDelete();

            $table->timestamps();

            // Prevent assigning the same category to the same product more than once
            $table->unique(['product_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_assigns');
    }
};