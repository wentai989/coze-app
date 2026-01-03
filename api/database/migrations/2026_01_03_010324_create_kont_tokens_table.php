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
        Schema::create('kont_tokens', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('token');
            $table->integer('expires_in');
            $table->string('app_id');
            $table->timestamp('created_at')->nullable()->useCurrent()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent()->comment('更新时间');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kont_tokens');
    }
};
