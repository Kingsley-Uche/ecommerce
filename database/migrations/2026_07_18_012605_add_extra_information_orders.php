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
        Schema::table('orders', function (Blueprint $table) {

            $table->text('extra_information')
                ->nullable()
                ->after('delivery_address');

            $table->timestamp('paid_at')
                ->nullable()
                ->after('total_paid');

        });


      
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->dropColumn([
                'extra_information',
                'paid_at',
            ]);

        });


       
    }
};