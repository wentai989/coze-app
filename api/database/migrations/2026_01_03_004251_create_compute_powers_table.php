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
        Schema::create('compute_powers', function (Blueprint $table) {
            $table->comment('算力套餐表');
            $table->increments('id');
            $table->string('name')->comment('套餐名称');
            $table->unsignedInteger('power_value')->comment('算力值');
            $table->decimal('amount', 10)->comment('金额');
            $table->boolean('is_status')->nullable()->default(true)->comment('状态');
            $table->integer('sort')->nullable()->default(0)->comment('排序');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('mch_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compute_powers');
    }
};
