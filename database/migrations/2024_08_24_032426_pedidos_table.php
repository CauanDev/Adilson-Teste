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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->unsignedBigInteger('funcionario_id')->nullable();
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('set null'); 
            $table->foreign('funcionario_id')->references('id')->on('funcionarios')->onDelete('set null'); 
            $table->json('produtos');
            $table->float('total');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
