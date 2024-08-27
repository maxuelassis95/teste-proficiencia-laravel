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
        Schema::create('pedido_produto', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('pedido_id');
            $table->foreign('pedido_id')->references('id')->on('pedidos')->onDelete('cascade');
            
            $table->unsignedBigInteger('produto_id');
            $table->foreign('produto_id')->references('id')->on('produtos')->onDelete('cascade');
            
            $table->integer('quantidade');        
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido_produto');
    }
};
