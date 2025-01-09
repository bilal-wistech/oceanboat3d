<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePredefinedListDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('predefined_list_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('list_id');
            $table->string('lang',10)->default('en');
            $table->string('title',150);
            $table->longText('descp');

            $table->index('list_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('predefined_list_detail');
    }
}
