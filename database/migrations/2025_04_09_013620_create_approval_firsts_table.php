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
        Schema::create('approval_firsts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_anak');
            $table->bigInteger('status');
            $table->unsignedBigInteger('approve_by_id')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->foreign('id_anak')->on('data_anaks')->references('id');
            $table->foreign('approve_by_id')->on('users')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_firsts');
    }
};
