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
            $table->string('name')->unique()->max(50);
            $table->string('category')->nullable()->max(20)->default(NULL);
            $table->string("provider")->max(50)->nullable()->default(NULL);
            $table->foreignId("team")->nullable()->default(NULL)->constrained("teams")->onDelete("set null")->onUpdate("cascade");
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
