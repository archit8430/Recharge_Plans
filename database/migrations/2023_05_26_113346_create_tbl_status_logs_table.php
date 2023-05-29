<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblStatusLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_status_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('recharge_id');
            $table->enum('old_status',['Pending','Success','Failed','Credit'])->nullable();
            $table->enum('new_status',['Pending','Success','Failed','Credit']);
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
        Schema::dropIfExists('status_logs');
    }
}