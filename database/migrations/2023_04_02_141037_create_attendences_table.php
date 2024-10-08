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
        Schema::create('attendences', function (Blueprint $table) {
            $table->bigInteger('employee_id')->unsigned();
            $table->unsignedInteger('branch_id');
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->enum('status', ['Hadir', 'Ijin', 'Tanpa Kabar'])
                ->default('Tanpa Kabar');

            $table->foreign('employee_id')->references('id')
                ->on('employees')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')
                ->on('branches');
            $table->foreign('user_id')->references('id')
                ->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendences');
    }
};
