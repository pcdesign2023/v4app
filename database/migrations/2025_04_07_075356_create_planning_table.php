<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('planning', function (Blueprint $table) {
            $table->id(); // id: Primary key
            $table->string('N_commande'); // N_commande: VARCHAR
            $table->unsignedBigInteger('Client_id'); // Client_id: FK
            $table->date('date_Planif')->nullable(); // date_Planif: DATE
            $table->date('date_debut')->nullable(); // date_debut: DATE
            $table->date('date_fin')->nullable(); // date_fin: DATE
            $table->string('Instruction')->nullable(); // Instruction: VARCHAR (default length is 255)
            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('Client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planning');
    }
};
