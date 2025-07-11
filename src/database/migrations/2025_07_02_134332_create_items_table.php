<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id'); // 出品者
            $table->string('name', 255);
            $table->string('brand', 255)->nullable();
            $table->text('description');
            $table->string('category', 100);
            $table->string('condition', 50);
            $table->integer('price');
            $table->string('img_url', 255);
            $table->tinyInteger('status')->default(0); // 0:販売中, 1:売り切れ
            $table->timestamps();

            // 外部キー制約
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
