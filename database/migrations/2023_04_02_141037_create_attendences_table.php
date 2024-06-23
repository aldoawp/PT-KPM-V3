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
        // Schema::create('attendences', function (Blueprint $table) {
        //     $table->bigInteger('employee_id')->unsigned();
        //     $table->enum('status', ['Hadir', 'Ijin', 'Tanpa Kabar'])
        //         ->default('Tanpa Kabar');

        //     $table->foreign('employee_id')->references('id')
        //         ->on('employees')->onDelete('cascade');

        //     $table->timestamps();
        // });

        Schema::create('attendences', function (Blueprint $table) {
            $table->bigInteger('employee_id')->unsigned();
            $table->date('date');
            $table->enum('status', ['Hadir', 'Ijin', 'Tanpa Kabar'])
                ->default('Tanpa Kabar');

            $table->foreign('employee_id')->references('id')
                ->on('employees')->onDelete('cascade');

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
