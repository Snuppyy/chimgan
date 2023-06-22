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
    Schema::create('orders', function (Blueprint $table) {
      $table->id();
      $table->bigInteger('client_id')->unsigned();
      $table->foreign('client_id')->references('id')->on('clients');
      $table->bigInteger('user_id')->unsigned()->nullable();
      $table->foreign('user_id')->references('id')->on('moonshine_users');
      $table->bigInteger('payment_id')->unsigned()->nullable();
      $table->foreign('payment_id')->references('id')->on('payments');
      $table->date('date');
      $table->integer('price');
      $table->integer('given_bottle');
      $table->integer('taken_bottle')->nullable();
      $table->longText('description')->nullable();
      $table->boolean('status')->default(1);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('orders');
  }
};
