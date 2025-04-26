<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('state')->default('Lagos');
            $table->boolean('status')->default(1); // 1 = active, 0 = inactive
            $table->softDeletes();
            $table->timestamps();
        });

        DB::table('locations')->insert([
            ['name' => 'Ikeja', 'state' => 'Lagos', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lekki', 'state' => 'Lagos', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Surulere', 'state' => 'Lagos', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Yaba', 'state' => 'Lagos', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Victoria Island', 'state' => 'Lagos', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ikorodu', 'state' => 'Lagos', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ajah', 'state' => 'Lagos', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
