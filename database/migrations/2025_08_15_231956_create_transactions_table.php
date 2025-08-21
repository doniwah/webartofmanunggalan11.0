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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->string('name');
            $table->string('phone', 20);
            $table->string('email');
            $table->integer('quantity')->default(1);
            $table->decimal('amount', 12, 2);
            $table->decimal('admin_fee', 8, 2)->default(2500);
            $table->enum('status', ['pending', 'paid', 'failed', 'expired', 'cancelled'])->default('pending');
            $table->text('snap_token')->nullable();
            $table->string('payment_type')->nullable();
            $table->json('midtrans_response')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();



            // Indexes for better performance
            $table->index(['phone', 'name']);
            $table->index(['status', 'expires_at']);
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};