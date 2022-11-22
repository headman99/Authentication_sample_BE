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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique()->max(100);
            $table->string('peso')->nullable();
            $table->string('categoria')->nullable()->max(100);
            $table->string('gruppo')->nullable();
            $table->timestamps();

            $table->foreign('gruppo')
            ->references('gruppo')
            ->on('products_groups')
            ->onDelete('set null')
            ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
