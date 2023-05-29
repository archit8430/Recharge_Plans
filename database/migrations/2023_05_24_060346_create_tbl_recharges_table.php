<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblRechargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_recharges', function (Blueprint $table) {
            $table->id();
            $table->string('company');
            $table->string('mobile');
            $table->decimal('amount');
            $table->enum('status',['Pending','Success','Failed','Credit']);
            $table->date('recharge_date');
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
        Schema::dropIfExists('tbl_recharges');
    }
}