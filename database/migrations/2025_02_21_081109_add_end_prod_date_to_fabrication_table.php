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
        Schema::table('fabrication', function (Blueprint $table) {
            $table->dateTime('End_Fab_date')->nullable()->after('date_fabrication');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fabrication', function (Blueprint $table) {
            $table->dropColumn('End_Fab_date');
        });
    }
};
