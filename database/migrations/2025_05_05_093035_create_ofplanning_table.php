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
        Schema::create('ofplanning', function (Blueprint $table) {
            $table->id();
            $table->string('OFID');
            $table->string('prod_ref');
            $table->string('prod_des');
            $table->string('client');
            $table->date('date_planifie');
            $table->string('commande');
            $table->integer('qte_plan');
            $table->integer('qte_reel');
            $table->string('statut', 50);
            $table->string('Priority', 50);
            $table->integer('qty_produced' );
            $table->string('instruction', 500);
            $table->string('comment', 800);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ofplanning');
    }
};
