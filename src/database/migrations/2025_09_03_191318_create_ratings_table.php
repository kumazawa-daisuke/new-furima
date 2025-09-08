<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->onDelete('cascade'); // どの取引に対する評価か
            $table->foreignId('rater_id')->constrained('users')->onDelete('cascade'); // 評価した人
            $table->foreignId('rated_id')->constrained('users')->onDelete('cascade'); // 評価された人
            $table->unsignedTinyInteger('rating'); // 評価点（例：1～5）
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique(['purchase_id', 'rater_id']); // 一つの取引で同じ人が複数回評価できないようにする
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ratings');
    }
}
