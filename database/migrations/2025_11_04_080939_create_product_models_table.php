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
        Schema::create('product_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);

            // Correct foreign keys
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('product_category_models')
                ->nullOnDelete();

            $table->foreignId('sales_category_models_id')
                ->nullable()
                ->constrained('sales_category_models')
                ->nullOnDelete();

            $table->integer('stock')->default(0);
            $table->boolean('is_front_page')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Correct way to reverse soft deletes
        Schema::table('product_models', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::dropIfExists('product_models');
    }
};
