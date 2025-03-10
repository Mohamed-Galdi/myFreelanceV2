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
        Schema::create('works', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('project_status', ['ongoing', 'completed', 'cancelled'])->default('ongoing');
            $table->enum('payment_status', ['paid', 'pending', 'refunded', 'cancelled'])->default('pending');
            $table->enum('payment_method', ['paypal', 'bank_transfer', 'platform', 'other'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('works');
    }
};
