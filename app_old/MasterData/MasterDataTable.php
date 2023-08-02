<?php

namespace App\MasterData;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            //Schema::connection($country->cont_conn)->dropIfExists('tm_cont');
            Schema::connection($country->cont_conn)->create('tm_cont', function (Blueprint $table) {
                $table->integerIncrements('id');
                $table->string('cont_name', 20);
                $table->string('cont_code', 10);
                $table->string('cont_tzon', 10);
                $table->string('cont_conn', 30);
                $table->string('cont_imgf', 10);
                $table->integer('cont_dgit');
                $table->string('cont_cncy', 50);
                $table->integer('cont_rund');
                $table->tinyInteger('var')->default(1);
                $table->string('attr1', 5)->default("-");
                $table->string('attr2', 5)->default("-");
                $table->integer('attr3')->default(0);
                $table->integer('attr4')->default(0);
                $table->timestamps();
            });
            Schema::connection($country->cont_conn)->dropIfExists('tm_lfcl');
            Schema::connection($country->cont_conn)->create('tm_lfcl', function (Blueprint $table) {
                $table->integerIncrements('id');
                $table->string('lfcl_name', 100);
                $table->string('lfcl_code', 50);
                $table->tinyInteger('var')->default(1);
                $table->string('attr1', 5)->default("-");
                $table->string('attr2', 5)->default("-");
                $table->integer('attr3')->default(0);
                $table->integer('attr4')->default(0);
                $table->timestamps();
            });
            Schema::connection($country->cont_conn)->dropIfExists('tm_role');
            Schema::connection($country->cont_conn)->create('tm_role', function (Blueprint $table) {
                $table->integerIncrements('id');
                $table->string('role_name', 20);
                $table->string('role_code', 10);
                $table->integer('cont_id');
                $table->integer('lfcl_id');
                $table->integer('aemp_iusr');
                $table->integer('aemp_eusr');
                $table->tinyInteger('var')->default(1);
                $table->string('attr1', 5)->default("-");
                $table->string('attr2', 5)->default("-");
                $table->integer('attr3')->default(0);
                $table->integer('attr4')->default(0);
                $table->timestamps();
                $table->foreign('cont_id')->references('id')->on('tm_cont');
                $table->foreign('lfcl_id')->references('id')->on('tm_lfcl');
            });
            Schema::connection($country->cont_conn)->dropIfExists('tm_edsg');
            Schema::connection($country->cont_conn)->create('tm_edsg', function (Blueprint $table) {
                $table->integerIncrements('id');
                $table->string('edsg_name', 20);
                $table->string('edsg_code', 10);
                $table->integer('cont_id');
                $table->integer('lfcl_id');
                $table->integer('aemp_iusr');
                $table->integer('aemp_eusr');
                $table->tinyInteger('var')->default(1);
                $table->string('attr1', 5)->default("-");
                $table->string('attr2', 5)->default("-");
                $table->integer('attr3')->default(0);
                $table->integer('attr4')->default(0);
                $table->timestamps();
                $table->foreign('cont_id')->references('id')->on('tm_cont');
                $table->foreign('lfcl_id')->references('id')->on('tm_lfcl');
            });
            Schema::connection($country->cont_conn)->dropIfExists('tm_aemp');
            Schema::connection($country->cont_conn)->create('tm_aemp', function (Blueprint $table) {
                $table->integerIncrements('id');
                $table->string('aemp_name', 100);
                $table->string('aemp_onme', 100);
                $table->string('aemp_stnm', 100);
                $table->string('aemp_mob1', 45);
                $table->string('aemp_dtsm', 20);
                $table->string('aemp_emal', 50);
                $table->string('aemp_otml', 10);
                $table->string('aemp_emcc', 255);
                $table->integer('aemp_lued');
                $table->integer('edsg_id');
                $table->integer('role_id');
                $table->string('aemp_usnm', 10);
                $table->string('aemp_pimg', 100);
                $table->string('aemp_picn', 100);
                $table->integer('aemp_mngr');
                $table->integer('aemp_lmid');
                $table->double('aemp_aldt');
                $table->tinyInteger('aemp_lcin');
                $table->tinyInteger('aemp_lonl');
                $table->string('aemp_utkn', 50);
                $table->integer('site_id');
                $table->double('aemp_crdt');
                $table->integer('aemp_issl');
                $table->integer('amng_id');
                $table->integer('cont_id');
                $table->integer('lfcl_id');
                $table->integer('aemp_iusr');
                $table->integer('aemp_eusr');
                $table->tinyInteger('var')->default(1);
                $table->string('attr1', 5)->default("-");
                $table->string('attr2', 5)->default("-");
                $table->integer('attr3')->default(0);
                $table->integer('attr4')->default(0);
                $table->timestamps();
                $table->foreign('edsg_id')->references('id')->on('tm_edsg');
                $table->foreign('role_id')->references('id')->on('tm_role');
                $table->foreign('aemp_mngr')->references('id')->on('tm_aemp');
                $table->foreign('aemp_lmid')->references('id')->on('tm_aemp');
                $table->foreign('cont_id')->references('id')->on('tm_cont');
                $table->foreign('lfcl_id')->references('id')->on('tm_lfcl');;
                $table->foreign('aemp_iusr')->references('id')->on('tm_aemp');
                $table->foreign('aemp_eusr')->references('id')->on('tm_aemp');
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

    }
}
