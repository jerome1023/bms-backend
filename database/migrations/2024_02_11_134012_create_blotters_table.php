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
        Schema::create('blotters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('complainant');
            $table->string('complainant_age');
            $table->string('complainant_address');
            $table->string('complainant_contact_number');
            $table->string('complainee');
            $table->string('complainee_age');
            $table->string('complainee_address');
            $table->string('complainee_contact_number');
            $table->date('date');
            $table->string('complain');
            $table->string('agreement')->nullable();
            $table->foreignUuid('namagitan')->constrained('officials')->nullable();
            $table->string('witness')->nullable();
            $table->string('status');
            $table->boolean('archive_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blotters');
    }
};
