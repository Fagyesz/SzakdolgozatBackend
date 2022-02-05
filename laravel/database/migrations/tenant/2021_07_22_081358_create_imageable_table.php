<?php

use App\Models\Image;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImageableTable extends Migration
{
    public function up(): void
    {
        Schema::create('imageables', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Image::class);
            $table->uuidMorphs('imageable');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imageable');
    }
}
