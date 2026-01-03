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
        Schema::create('admin_menus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->default(0);
            $table->integer('custom_order')->default(0);
            $table->string('title');
            $table->string('icon')->nullable();
            $table->string('url')->nullable();
            $table->integer('url_type')->default(1);
            $table->integer('visible')->default(1);
            $table->integer('is_home')->default(0);
            $table->integer('keep_alive')->nullable();
            $table->string('iframe_url')->nullable();
            $table->string('component')->nullable();
            $table->integer('is_full')->default(0);
            $table->string('extension')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_menus');
    }
};
