<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('restocks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('supplier_id')->unsigned()->nullable();
            $table->integer('branch_id')->unsigned();
            $table->integer('total');
            $table->bigInteger('user_id')->unsigned()->nullable();

            $table->foreign('branch_id')->references('id')
                ->on('branches')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')
                ->on('suppliers')->onDelete('set null');
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
        Schema::dropIfExists('restocks');
    }
};
