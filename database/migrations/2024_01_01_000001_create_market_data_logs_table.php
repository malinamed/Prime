<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_data_logs', function (Blueprint $table) {
            $table->id();
            $table->string('source', 64);
            $table->timestamp('fetched_at');
            $table->unsignedSmallInteger('record_count')->default(0);
            $table->boolean('success')->default(false);
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_data_logs');
    }
};
