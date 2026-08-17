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
        Schema::create('hash_interactions', function (Blueprint $table) {
            $table->id();
            $table->string('action', 20); // 'generate' or 'compare'
            $table->string('algorithm', 20);
            $table->string('input_preview', 120);
            $table->string('second_input_preview', 120)->nullable();
            $table->string('hash_output', 128)->nullable();
            $table->string('second_hash_output', 128)->nullable();
            $table->unsignedInteger('differing_bits')->nullable();
            $table->float('differing_percentage')->nullable();
            $table->boolean('is_collision')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hash_interactions');
    }
};
