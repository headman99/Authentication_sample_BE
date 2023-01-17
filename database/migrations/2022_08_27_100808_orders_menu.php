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
        Schema::create('orders_menu', function (Blueprint $table) {
            $table->id();
            $table->string("code")->min(6)->max(6)->unique();
            $table->foreignId('menu_id')->constrained('menu')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId("client_id")->constrained("users")->onDelete("cascade")->onUpdate("cascade");
            $table->string("event_date");
            $table->integer('quantity')->unsigned()->min(1)->default(1);
            $table->string("richiesta")->max(250)->nullable();
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
        Schema::dropIfExists('orders_menu');

    }
};
