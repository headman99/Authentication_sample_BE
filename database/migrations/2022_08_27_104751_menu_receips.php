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
            $table->string('gruppo')->nullable()->max(100);
            $table->string('sezione')->nullable()->default('')->max(100);
            $table->integer("extra")->default(0);
            $table->integer("groupPosition");
            $table->float("ratio")->default(1);
            $table->foreignId("alternative")->nullable()->default(NULL)->constrained("products")->onDelete('cascade')->onUpdate("cascade");
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
