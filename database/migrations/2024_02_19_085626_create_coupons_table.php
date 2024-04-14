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
        Schema::disableForeignKeyConstraints();

        Schema::create('coupons', function (Blueprint $table) {
             $table->bigIncrements("id");
            $table->unsignedBigInteger('coupon_type_id');
            $table->foreign('coupon_type_id')->references('id')->on('coupons_types');
            $table->bigInteger('value');
            $table->bigInteger('price');
            $table->longText('description');
            $table->dateTime('created_at');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
