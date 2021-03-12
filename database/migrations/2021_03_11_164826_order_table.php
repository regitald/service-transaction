<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order', function (Blueprint $table) {
            $table->increments('order_id');
            $table->string('order_code');
            $table->integer('member_id');
            $table->string('member_email')->nullable();
            $table->text('member_address')->nullable();
            $table->integer('payment_method_id')->nullable();
            $table->integer('shipping_id')->nullable();
            $table->integer('distance')->nullable();
            $table->integer('shipping_cost')->nullable();
            $table->string('attachment')->nullable();
            $table->double('order_total_price',20,2);
			$table->enum('order_status', [0, 1,2])->comment('0 placed | 1 success |2 failed')->default(0);
			$table->dateTime('deleted_at')->nullable();
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
        Schema::dropIfExists('order');
    }
}
