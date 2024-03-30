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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('days');
            $table->string('period');
            $table->integer('images_count')->default(50);
            $table->double('price');
            $table->string('currency');
            $table->boolean('active')->default(1);
            $table->string('stripe_price_id')->nullable();
            $table->string('paypal_plan_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
