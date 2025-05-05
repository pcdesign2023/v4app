<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFabricationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fabrication', function (Blueprint $table) {
            $table->id();
            $table->string('OFID');
            $table->string('Lot_Jus')->nullable();
            $table->dateTime('Valid_date')->nullable();
            $table->integer('effectif_Reel')->nullable();
            $table->dateTime('date_fabrication')->nullable();
            $table->integer('Pf_Qty');
            $table->integer('Sf_Qty');
            $table->integer('Set_qty');
            $table->integer('Tester_qty');
            $table->text('Comment_chaine')->nullable();
            $table->timestamps();
            
            $table->foreign('OFID', 'fabrication_ofid_foreign')->references('OFID')->on('fab_orders')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fabrication');
    }
}
