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
        Schema::create('product_instance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('order')->constrained('orders_menu')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('barcode')->unique()->max(30);

            $table->string("operator")->min(8)->max(8)->nullable();

            $table->foreign("operator")
                ->references("badge")
                ->on("users")
                ->onDelete("cascade")
                ->onUpdate("set null");

            $table->boolean("checked")->default(false);
            $table->integer("page")->unsigned()->min(1);
            $table->timestamps();
            $table->timestamp('scanned_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_instance');
    }
};
