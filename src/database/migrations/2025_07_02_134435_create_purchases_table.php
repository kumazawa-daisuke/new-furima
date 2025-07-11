<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');   // 購入者
            $table->unsignedBigInteger('item_id');   // 購入商品
            $table->string('payment_method');        // 支払い方法
            $table->integer('price');                // 購入金額（またはitems.priceを参照）
            $table->string('status')->default('pending'); // 購入ステータス（例：pending, completed, canceled）
            $table->text('shipping')->nullable(); 
            $table->timestamps();

            // 外部キー制約
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases');
    }
}
