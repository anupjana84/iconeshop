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
        Schema::create('order_free_gifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('order_item_id')->nullable()->constrained('orders_items')->onDelete('cascade');
            $table->foreignId('main_product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('free_product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->string('gift_name')->nullable();
            $table->integer('quantity')->default(1);
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_free_gifts');
    }
};
