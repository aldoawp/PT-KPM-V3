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
        Schema::create('pay_salaries', function (Blueprint $table) {
            $table->bigInteger('employee_id')->unsigned();
            $table->integer('paid_amount');
            $table->bigInteger('advance_salary_id')->unsigned()->nullable();
            $table->integer('due_salary');

            $table->foreign('employee_id')->references('id')
                ->on('employees')->onDelete('cascade');

            $table->foreign('advance_salary_id')->references('id')
                ->on('advance_salaries')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pay_salaries');
    }
};
