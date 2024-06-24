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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->text('address')->nullable();
            $table->string('experience')->nullable();
            $table->string('photo')->nullable();
            $table->string('city')->nullable();
            $table->integer('salary')->nullable();
            $table->integer('vacation')->nullable();
            $table->integer('branch_id')->unsigned()->nullable();

            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('set null');
            $table->foreign('branch_id')->references('id')
                ->on('branches')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
