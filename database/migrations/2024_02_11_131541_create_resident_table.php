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
        Schema::create('resident', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('firstname');
            $table->string('middlename');
            $table->string('lastname');
            $table->string('gender');
            $table->date('birthdate');
            $table->string('birthplace');
            $table->string('civil_status');
            $table->string('religion');
            $table->string('educational_attainment');
            $table->foreignUuid('sitio_id')->constrained('sitio');
            $table->string('house_number');
            $table->string('occupation');
            $table->string('nationality');
            $table->boolean('voter_status');
            $table->boolean('archive_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resident');
    }
};
