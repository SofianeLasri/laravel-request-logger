<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('logged_requests', function (Blueprint $table) {
            $table->enum('method', ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'CONNECT', 'HEAD', 'OPTIONS', 'TRACE', 'PROPFIND'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('logged_requests', function (Blueprint $table) {
            $table->enum('method', ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'CONNECT', 'HEAD', 'OPTIONS', 'TRACE'])->change();
        });
    }
};
