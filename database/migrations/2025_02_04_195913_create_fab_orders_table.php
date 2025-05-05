<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFabOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fab_orders', function (Blueprint $table) {
            $table->id();
            $table->string('OFID')->unique('fab_orders_ofid_unique');
            $table->unsignedBigInteger('Prod_ID');
            $table->unsignedBigInteger('chaineID');
            $table->string('saleOrderId');
            $table->unsignedBigInteger('client_id');
            $table->dateTime('creation_date_Of')->default('2025-01-30 18:15:53');
            $table->dateTime('date_fabrication')->nullable();
            $table->integer('Pf_Qty');
            $table->integer('Sf_Qty');
            $table->integer('Set_qty');
            $table->integer('Tester_qty');
            $table->string('Lot_Set');
            $table->text('instruction')->nullable();
            $table->dateTime('End_Prod')->nullable();
            $table->string('Statut_of', 40)->default('Planifié');
            
            $table->foreign('chaineID', 'fab_orders_chaineid_foreign')->references('id')->on('chaine')->onDelete('cascade');
            $table->foreign('client_id', 'fab_orders_client_id_foreign')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('Prod_ID', 'fab_orders_prod_id_foreign')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fab_orders');
    }
}
