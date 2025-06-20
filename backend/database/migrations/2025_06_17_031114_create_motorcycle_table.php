<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motorcycle', function (Blueprint $table) {
            $table->id();
            $table->integer('searchkey')->nullable(0);
            $table->unsignedInteger('maker_code');
            $table->foreign('maker_code')->references('code')->on('maker')->onDelete('cascade');
            $table->unsignedInteger('model_code');
            $table->foreign('model_code')->references('code')->on('model')->onDelete('cascade');
            $table->boolean('type')->nullable(false);
            $table->integer('ippan_kakaku')->nullable(0);
            $table->tinyInteger('nensiki')->nullable(0);
            $table->integer('soukou')->nullable(0);
            $table->integer('soukou_fumei_flg')->nullable(0);
            $table->tinyInteger('haikiryo')->nullable(0);
            $table->string('color_code', 2)->nullable();
            $table->string('color', 64)->nullable();
            $table->integer('noridasi_kakaku')->nullable(0);
            $table->boolean('iyoukyo')->nullable(false);
            $table->string('photo1', 255)->nullable();
            $table->string('photo1_pr', 64)->nullable();
            $table->string('photo2', 255)->nullable();
            $table->string('photo2_pr', 64)->nullable();
            $table->string('photo3', 255)->nullable();
            $table->string('photo3_pr', 64)->nullable();
            $table->string('photo4', 255)->nullable();
            $table->string('photo4_pr', 64)->nullable();
            $table->string('photo5', 255)->nullable();
            $table->string('photo5_pr', 64)->nullable();
            $table->string('photo6', 255)->nullable();
            $table->string('photo6_pr', 64)->nullable();
            $table->string('photo7', 255)->nullable();
            $table->string('photo7_pr', 64)->nullable();
            $table->string('photo8', 255)->nullable();
            $table->string('photo8_pr', 64)->nullable();
            $table->string('photo9', 255)->nullable();
            $table->string('photo9_pr', 64)->nullable();
            $table->string('photo10', 255)->nullable();
            $table->string('photo10_pr', 64)->nullable();
            $table->string('grade', 255)->nullable();
            $table->string('shatai_bangou', 255)->nullable();
            $table->tinyInteger('first_year_registration')->nullable(0);
            $table->string('last_update_id', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('motorcycle');
    }
};
