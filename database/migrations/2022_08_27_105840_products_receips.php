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
        Schema::create('products_recipes', function (Blueprint $table) {
            $table->foreignId('product_id')->costrained('products')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('ingredient_id')->costrained('ingredients')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('quantity')->unsigned()->min(1)->default(1);
            $table->timestamps();
            $table->primary(['product_id', 'ingredient_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products_recipes');
    }
};
