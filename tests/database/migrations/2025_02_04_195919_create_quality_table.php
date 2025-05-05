<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQualityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quality', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chaineID');
            $table->unsignedBigInteger('OF_ID');
            $table->timestamps();
            
            $table->foreign('chaineID', 'quality_chaineid_foreign')->references('id')->on('chaine')->onDelete('cascade');
            $table->foreign('OF_ID', 'quality_of_id_foreign')->references('id')->on('fab_orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quality');
    }
}
