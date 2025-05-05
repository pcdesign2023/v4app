<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConformityDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conformity_details', function (Blueprint $table) {
            $table->id();
            $table->integer('Qty_NC')->nullable();
            $table->string('type_product')->nullable();
            $table->unsignedBigInteger('AnoId');
            $table->string('OFID');
            $table->unsignedBigInteger('default')->nullable();
            $table->string('RespDefaut');
            $table->dateTime('DateInterv')->nullable();
            $table->string('is_confirmed')->default('pending');
            $table->text('Comment')->nullable();
            $table->timestamps();
            
            $table->foreign('AnoId', 'conformity_details_anoid_foreign')->references('AnoID')->on('anomalies')->onDelete('cascade');
            $table->foreign('default', 'conformity_details_default_foreign')->references('id')->on('default_entries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conformity_details');
    }
}
