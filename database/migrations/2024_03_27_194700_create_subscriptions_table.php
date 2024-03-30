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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('payment_id');
            $table->string('payment_subscription_id');
            $table->string('payment_plan_id');
            $table->string('payment_method');
            $table->string('title');
            $table->double('price');
            $table->string('currency');
            $table->integer('images_count');
            $table->boolean('auto_renewal')->default(1);
            $table->timestamp('ends_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
