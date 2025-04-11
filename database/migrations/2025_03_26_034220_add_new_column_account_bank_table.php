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
        Schema::table('req_approvals', function (Blueprint $table) {
            $table->string('bankname')->after('norek')->nullable();
            $table->string('accountbankname')->after('bankname')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('req_approvals', function (Blueprint $table) {
            $table->dropColumn(['bankname', 'accountbankname']);
        });
    }
};
