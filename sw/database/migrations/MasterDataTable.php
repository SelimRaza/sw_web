<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MasterDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up($cont_id)
    {
        $country = (new \App\MasterData\Country())->country($cont_id);
        if ($country != null) {
            Schema::connection($country->cont_conn)->create('tm_cont', function (Blueprint $table) {
                $table->integerIncrements('id');
                $table->string('cont_name', 20);
                $table->string('cont_code', 10);
                $table->string('cont_tzon', 10);
                $table->string('cont_conn', 30);
                $table->string('cont_imgf', 10);
                $table->integer('cont_dgit');
                $table->string('cont_cncy', 50);
                $table->tinyInteger('cont_rund');
                $table->integer('var')->default(1);
                $table->string('attr1', 5)->default("-");
                $table->string('attr2', 5)->default("-");
                $table->integer('attr3')->default(0);
                $table->integer('attr4')->default(0);
                $table->timestamps();
            });
            Schema::connection($country->cont_conn)->create('tm_lfcl', function (Blueprint $table) {
                $table->integerIncrements('id');
                $table->string('lfcl_name', 20);
                $table->string('lfcl_code', 10);
                $table->integer('var')->default(1);
                $table->string('attr1', 5)->default("-");
                $table->string('attr2', 5)->default("-");
                $table->integer('attr3')->default(0);
                $table->integer('attr4')->default(0);
                $table->timestamps();
            });
            Schema::connection($country->cont_conn)->create('tm_role', function (Blueprint $table) {
                $table->integerIncrements('id');
                $table->string('role_name', 20);
                $table->string('role_code', 10);
                $table->foreign('cont_id')->references('id')->on('tm_cont');
                $table->foreign('lfcl_id')->references('id')->on('tm_lfcl');;
                $table->foreign('aemp_iusr')->references('id')->on('tm_aemp');
                $table->foreign('aemp_eusr')->references('id')->on('tm_aemp');
                $table->integer('var')->default(1);
                $table->string('attr1', 5)->default("-");
                $table->string('attr2', 5)->default("-");
                $table->integer('attr3')->default(0);
                $table->integer('attr4')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tm_cont');
        Schema::dropIfExists('tm_lfcl');
        Schema::dropIfExists('tm_role');
    }
}
