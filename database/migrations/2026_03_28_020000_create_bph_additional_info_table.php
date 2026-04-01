<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bph_additional_info')) {
            return;
        }

        Schema::create('bph_additional_info', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_id')->index();
            $table->longText('content');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bph_additional_info');
    }
};
