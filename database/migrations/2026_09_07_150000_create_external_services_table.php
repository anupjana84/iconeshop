<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('external_services', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 20);
            $table->string('name');
            $table->string('address');
            $table->string('pincode', 10);
            $table->string('product');
            $table->string('serial')->nullable();
            $table->decimal('budget', 10, 2)->nullable();
            $table->decimal('final_cost', 10, 2)->nullable();
            $table->date('receive_date');
            $table->string('vendor')->nullable();
            $table->date('sent_date')->nullable();
            $table->date('back_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->enum('warranty', ['In Warranty', 'Out of Warranty'])->default('Out of Warranty');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('external_services');
    }
};
