<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 32)->unique();
            $table->foreignId('service_id')->constrained('services')->restrictOnDelete();
            $table->string('customer_name', 120);
            $table->string('customer_phone', 30);
            $table->text('pickup_address');
            $table->string('status', 30)->default('waiting_pickup');

            // Kosong saat permintaan jemput dibuat; diisi setelah penimbangan/perhitungan.
            $table->unsignedInteger('actual_weight_grams')->nullable();
            $table->decimal('billed_weight_kg', 6, 2)->nullable();
            $table->unsignedInteger('item_count')->nullable();
            $table->unsignedInteger('applied_unit_price')->nullable();
            $table->unsignedTinyInteger('applied_surcharge_percent')->nullable();
            $table->unsignedInteger('final_amount')->nullable();
            $table->timestamp('weighed_at')->nullable();
            $table->timestamp('estimated_completion_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            // Status pembayaran terpisah dari status operasional cucian.
            $table->string('payment_status', 20)->default('unpaid');
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index(['payment_status', 'paid_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
