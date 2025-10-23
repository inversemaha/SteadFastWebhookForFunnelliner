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
            $table->string('notification_type')->nullable();
            $table->string('consignment_id')->nullable();
            $table->string('invoice')->unique();
            $table->string('status');
            $table->decimal('cod_amount', 10, 2)->nullable();
            $table->decimal('delivery_charge', 10, 2)->nullable();
            $table->text('tracking_message')->nullable();
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
