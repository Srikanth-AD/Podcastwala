<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('podcasts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('machine_name');
            $table->string('feed_url');
            $table->string('web_url')->nullable();
            $table->string('feed_thumbnail_location')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unique(['machine_name', 'user_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('podcasts');
    }
};
