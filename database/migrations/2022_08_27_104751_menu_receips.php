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
        Schema::create('menu_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->costrained('menu')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('product_id')->costrained('products')->onDelete('cascade')->onUpdate('cascade');
            $table->string('gruppo')->max(100);
            $table->string('sezione')->nullabel()->max(100);
            $table->boolean('extra')->default(false);
            $table->unique(["menu_id","product_id","gruppo","sezione"]);
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
        Schema::dropIfExists('menu_recipes');
    }
};
