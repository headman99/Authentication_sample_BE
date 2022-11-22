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
            $table->string("order")->min(6)->max(6);
            $table->foreign("order")->references("code")->on("orders_menu")
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('barcode')->unique()->max(30);
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
