<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('mime_types', function (Blueprint $table) {
            $table->text('mime_type')->change();
        });
    }

    public function down()
    {
        Schema::table('mime_types', function (Blueprint $table) {
            $table->string('mime_type')->change();
        });
    }
};
