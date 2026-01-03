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
        Schema::create('ai_token_providers', function (Blueprint $table) {
            $table->comment('AI Token 提供商表');
            $table->increments('id');
            $table->string('name')->comment('名称');
            $table->string('token_value')->comment('Token值');
            $table->unsignedInteger('remaining_points')->nullable()->comment('剩余点数');
            $table->text('error_reason')->nullable()->comment('异常原因');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_token_providers');
    }
};
