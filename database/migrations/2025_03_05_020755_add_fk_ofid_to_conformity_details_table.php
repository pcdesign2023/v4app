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
        Schema::table('conformity_details', function (Blueprint $table) {
            // Add the new column `fk_OFID`
            $table->unsignedBigInteger('fk_OFID')->after('id');

            // Add foreign key constraint
            $table->foreign('fk_OFID')
                ->references('ID')
                ->on('fab_orders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conformity_details', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['fk_OFID']);

            // Drop the column
            $table->dropColumn('fk_OFID');
        });
    }
};
