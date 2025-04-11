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
            $table->string('file')->nullable()->after('reason');
            $table->string('norek')->after('file')->nullable();
            $table->integer('isreimburst')->default(0)->after('norek');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('req_approvals', function (Blueprint $table) {
            $table->dropColumn(['file', 'norek', 'isreimburst']);
        });
    }
};
