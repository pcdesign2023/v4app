<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('ref_id',50);
            $table->string('product_name');
            $table->string('component_name')->nullable();
            $table->string('component_code')->nullable();
            $table->float('quantity')->default(0);
            $table->timestamps();

            $table->unique(['product_name', 'ref_id', 'component_name', 'component_code', 'quantity'], 'unique_product_components');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
