<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mime_types', function (Blueprint $table) {
            $table->id();
            $table->string('mime_type')->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mime_types');
    }
};
