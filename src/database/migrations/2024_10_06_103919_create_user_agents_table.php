<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_agents', function (Blueprint $table) {
            $table->id();
            $table->string('user_agent')->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_agents');
    }
};
