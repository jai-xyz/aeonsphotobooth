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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('alias');
            // $table->string('price');
            $table->string('duration');
            // $table->string('number_of_shots');
            $table->string('size')->nullable(); 
            $table->string('size2')->nullable();
            $table->string('size3')->nullable();
            $table->longText('inclusion');
            $table->string('note');
            // $table->string('extension');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
