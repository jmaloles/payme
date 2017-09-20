<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCommissary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commissary_inventories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('stock')->default(0); 
            $table->integer('reorder_level');
            $table->integer('category_id')->unsigned();
            $table->timestamps();
        });

        Schema::create('commissary_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('produce')->default(0);
            $table->decimal('price', 10, 2);
            $table->string('category');
            $table->timestamps();
        });

        Schema::create('commissary_inventory_product', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('inventory_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('commissary_products')->onDelete('cascade');
            $table->foreign('inventory_id')->references('id')->on('commissary_inventories')->onDelete('cascade');
        });

        Schema::create('commissary_stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('quantity')->unsigned();
            $table->decimal('price', 10, 2);
            $table->date('received');
            $table->date('expiration');
            $table->string('status')->default('NEW');
            $table->integer('inventory_id')->unsigned();
            $table->timestamps();
        });

        Schema::create('commissary_produce', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id');
            $table->integer('quantity');
            $table->date('date');
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
       Schema::dropIfExists('commissary_inventories');
       Schema::dropIfExists('commissary_products');
       Schema::dropIfExists('commissary_inventory_product');
       Schema::dropIfExists('commissary_stocks');
       Schema::dropIfExists('commissary_produce');
    }
}
