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
        Schema::table('subject_levels', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
        });
    }

    /**
     * Cofnij migrację.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subject_levels', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
        });
    }
};
