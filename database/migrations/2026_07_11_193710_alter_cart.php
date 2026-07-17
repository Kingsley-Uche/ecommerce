<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_models', function (Blueprint $table) {
            // Nullable so existing cart rows aren't broken — size is optional
            // for products that don't have size variants
            $table->string('size', 10)->nullable()->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('cart_models', function (Blueprint $table) {
            $table->dropColumn('size');
        });
    }
};