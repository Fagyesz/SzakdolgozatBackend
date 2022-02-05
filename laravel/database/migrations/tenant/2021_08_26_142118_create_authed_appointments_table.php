<?php

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthedAppointmentsTable extends Migration
{
    public function up(): void
    {
        Schema::create('authed_appointments', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Service::class);
            $table->timestamp('begin_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authed_appointments');
    }
}
