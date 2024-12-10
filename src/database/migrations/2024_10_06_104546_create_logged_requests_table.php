<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SlProjects\LaravelRequestLogger\app\Models\Url;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('logged_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ip_address_id');
            $table->string('country_code')->nullable();
            $table->enum('method', ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'CONNECT', 'HEAD', 'OPTIONS', 'TRACE']);
            $table->unsignedBigInteger('content_length')->nullable();
            $table->unsignedInteger('status_code')->nullable();
            $table->foreignId('user_agent_id')->nullable();
            $table->foreignId('mime_type_id')->nullable();
            $table->foreignIdFor(Url::class, 'url_id')->nullable();
            $table->foreignIdFor(Url::class, 'referer_url_id')->nullable();
            $table->foreignIdFor(Url::class, 'origin_url_id')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logged_requests');
    }
};
