<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYobitNonces extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yobit_nonces', function(Blueprint $table) {
            $table->increments('id');
            $table->string('auth_hash');
            $table->unsignedInteger('nonce');

            $table->unique('auth_hash');
            $table->timestamps();
        });
    }
}
