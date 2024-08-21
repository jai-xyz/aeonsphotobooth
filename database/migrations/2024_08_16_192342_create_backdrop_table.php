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
        Schema::create('backdroptypes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('backdropcolors', function (Blueprint $table) {
            $table->id();
            $table->string('color');
            $table->foreignId('backdroptype_id')->constrained('backdroptypes')->onDelete('cascade');
            $table->timestamps();
        });

    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backdroptypes');
        Schema::dropIfExists('backdropcolor');
    }
};
