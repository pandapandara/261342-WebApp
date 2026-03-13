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
        Schema::create('payments', function (Blueprint $table) {
        $table->id('payment_id');
        $table->timestamps();
        $table->string('status')->default('pending');   
        $table->string('method');
        $table->decimal('amount', 10, 2);
        $table->foreignId('order_id')->constrained('orders','order_id')->onDelete('cascade');
        $table->timestamp('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
