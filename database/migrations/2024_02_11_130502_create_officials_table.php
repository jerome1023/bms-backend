<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('officials', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('firstname');
            $table->string('middlename');
            $table->string('lastname');
            $table->string('gender');
            $table->string('position');
            $table->date('birthdate');
            $table->foreignUuid('sitio_id')->constrained('sitios');
            $table->date('start_term');
            $table->date('end_term');
            $table->boolean('archive_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('officials');
    }
};
