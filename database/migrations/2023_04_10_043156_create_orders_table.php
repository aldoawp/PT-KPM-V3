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
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->enum('order_status', ['pending', 'complete']);
            $table->integer('total_products');
            $table->integer('sub_total');
            $table->integer('vat')->nullable();
            $table->string('invoice_no');
            $table->integer('total');
            $table->enum('payment_status', ['tunai', 'cek', 'bon']);
            $table->integer('pay')->default(0);
            $table->integer('due')->default(0);
            $table->bigInteger('user_id')->unsigned()->nullable();

            $table->foreign('customer_id')->references('id')
                ->on('customers')->onDelete('set null');

            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('set null');

            $table->timestamps();
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
