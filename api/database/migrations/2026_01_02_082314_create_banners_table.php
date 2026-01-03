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
        Schema::create('banners', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 100);
            $table->string('path')->nullable();
            $table->string('image');
            $table->boolean('is_status')->nullable()->default(false);
            $table->integer('sort')->nullable()->default(0);
            $table->timestamps();
            $table->integer('mch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
