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
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('payment_subscription_id')->nullable();
            $table->string('payment_plan_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('subscriber_email')->nullable();
            $table->double('fee')->nullable();
            $table->string('title')->nullable();
            $table->double('price')->nullable();
            $table->string('currency')->nullable();
            $table->integer('images_count')->nullable();
            $table->boolean('active')->default(0);
            $table->boolean('auto_renewal')->default(1);
            $table->timestamp('ends_at')->nullable();
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
