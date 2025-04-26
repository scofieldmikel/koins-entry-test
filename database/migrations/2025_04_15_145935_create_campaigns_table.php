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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('status')->nullable()->constrained('campaign_statuses')->onDelete('cascade');
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('amount');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
