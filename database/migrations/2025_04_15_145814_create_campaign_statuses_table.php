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
        Schema::create('campaign_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_visible')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });

        DB::table('campaign_statuses')->insert([
            ['name' => 'Running', 'is_visible' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Stopped', 'is_visible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Paused', 'is_visible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ended', 'is_visible' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cancelled', 'is_visible' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pending', 'is_visible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Completed', 'is_visible' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_statuses');
    }
};
