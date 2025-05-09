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
            $table->renameColumn('is_confirmed', 'Component');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conformity_details', function (Blueprint $table) {
            $table->renameColumn('Component', 'is_confirmed');
        });
    }
};
