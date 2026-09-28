<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('standalone_form_settings')) {
            return;
        }

        Schema::create('standalone_form_settings', function (Blueprint $table) {
            $table->id();
            $table->string('form_key', 50)->unique();
            $table->boolean('is_required')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standalone_form_settings');
    }
};
