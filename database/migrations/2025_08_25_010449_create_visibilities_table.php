<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visibilities', function (Blueprint $table) {
        $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT
        $table->string('code', 32)->unique();
    });


        // 初期データ投入
        DB::table('visibilities')->insert([
            ['code' => 'public'],
            ['code' => 'followers'],
            ['code' => 'draft'],
        ]);
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visibilities');
    }
};
