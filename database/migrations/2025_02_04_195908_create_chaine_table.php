<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChaineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chaine', function (Blueprint $table) {
            $table->id();
            $table->integer('Num_chaine');
            $table->unsignedBigInteger('responsable_QLTY_id')->nullable();
            $table->unsignedBigInteger('chef_de_chaine_id')->nullable();
            $table->integer('nbr_operateur');
            $table->timestamps();
            
            $table->foreign('chef_de_chaine_id', 'chaine_chef_de_chaine_id_foreign')->references('id')->on('users')->onDelete('set NULL');
            $table->foreign('responsable_QLTY_id', 'chaine_responsable_qlty_id_foreign')->references('id')->on('users')->onDelete('set NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chaine');
    }
}
