<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_source_details', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->unsignedInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->string('name');
            $table->string('address',2000);
            $table->string('phone',11);
            $table->decimal('lat',10,8);
            $table->decimal('long',11,8);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_source_details');
    }
};
