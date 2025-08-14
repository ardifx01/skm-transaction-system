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
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Kolom utama
            $table->string('no_blanko')->nullable()->unique();
            $table->string('no_policy')->nullable();
            $table->string('consignee')->nullable();
            $table->string('no_bl')->nullable();
            $table->string('shipping_carrier')->nullable();
            $table->decimal('insured_value', 15, 2)->nullable();
            $table->string('currency')->nullable();

            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            // Status & pembayaran
            $table->enum('status', [
                'draft', 
                'pending_verification', 
                'verified', 
                'pending_payment', 
                'paid', 
                'rejected'
            ])->default('draft');
            $table->decimal('premium_price', 15, 2)->nullable();
            $table->text('verification_reason')->nullable();
            $table->string('payment_proof')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policies');
    }
};
