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
            $table->string('payment_ref')->unique();
            $table->string('user_name');
            $table->string('email_address');
            $table->string('phone');
            $table->string('delivery_city');
            $table->text('delivery_address');
            $table->string('order_status')->default('received');
            $table->string('payment_status')->default('pending');
            $table->json('product_id'); // store products or product info as JSON
            $table->string('cart_token')->index();
            $table->decimal('total_cost', 10,2);
            $table->decimal('total_paid', 10,2)->default(0);
            $table->timestamps();
            $table->softDeletes(); // enables soft deletes
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
