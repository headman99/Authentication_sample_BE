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
        Schema::create('ingredients', function(Blueprint $table){
            $table->id();
            $table->string('name',255)->unique()->max(100);
            $table->string('description')->nullable();
            $table->string('category')->nullable();
            $table->string("operator")->min(8)->max(8);
            
            $table->foreign("operator")
                ->references("badge")
                ->on("users")
                ->onDelete("cascade")
                ->onUpdate("cascade");
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
        Schema::dropIfExists('ingredients');
    }
};
