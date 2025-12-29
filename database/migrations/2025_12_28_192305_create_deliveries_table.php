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
    Schema::create('deliveries', function (Blueprint $table) {
        $table->id();
        // Relation avec la commande
        $table->foreignId('order_id')->constrained()->onDelete('cascade');
        // Relation avec le livreur (qui est un User)
        $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null');
        
        // Statuts : en attente, assigné, en cours, livré, annulé
        $table->enum('status', ['pending', 'assigned', 'on_delivery', 'delivered', 'cancelled'])->default('pending');
        
        $table->timestamp('assigned_at')->nullable();
        $table->timestamp('delivered_at')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
