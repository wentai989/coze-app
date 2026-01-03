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
        Schema::create('upload_imgs', function (Blueprint $table) {
            $table->comment('算力扣点记录表');
            $table->increments('id');
            $table->unsignedInteger('mch_id')->nullable()->comment('商户ID');
            $table->string('url')->nullable()->comment('推广人');
            $table->string('file')->nullable()->default('0.00')->comment('分成');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upload_imgs');
    }
};
