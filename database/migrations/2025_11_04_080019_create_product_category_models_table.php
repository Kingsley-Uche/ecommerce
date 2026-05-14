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
        Schema::create('product_category_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('excerpt', 500)->nullable();
            $table->enum('status', ['active', 'inactive']);
            $table->integer('priority')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
            $table->softDeletes();  // added soft delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_category_models', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::dropIfExists('product_category_models');
    }
};
