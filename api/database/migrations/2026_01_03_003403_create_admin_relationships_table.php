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
        Schema::create('admin_relationships', function (Blueprint $table) {
            $table->increments('id');
            $table->string('model');
            $table->string('title');
            $table->string('type');
            $table->string('remark')->nullable();
            $table->text('args')->nullable();
            $table->text('extra')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_relationships');
    }
};
