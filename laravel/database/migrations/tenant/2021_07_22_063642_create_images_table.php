<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('src')->default(''); // TO-DO implement
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('images');
    }
}
