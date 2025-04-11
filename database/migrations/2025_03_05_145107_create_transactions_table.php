<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_anak');
            $table->bigInteger('previous_balance')->default(0);
            $table->bigInteger('credit')->default(0);
            $table->bigInteger('running_balance')->default(0);
            $table->bigInteger('debit')->default(0);
            $table->bigInteger('final_balance')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('id_anak')->on('data_anaks')->references('id');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
