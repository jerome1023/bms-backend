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
        Schema::create('requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users');
            $table->string('fullname');
            $table->integer('age');
            $table->foreignUuid('document_id')->constrained('documents');
            $table->string('purpose');
            $table->foreignUuid('sitio_id')->constrained('sitios');
            $table->integer('income')->nullable();
            $table->integer('price');
            $table->string('status');
            $table->string('reason')->nullable();
            $table->boolean('archive_status');
            $table->dateTime('date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
