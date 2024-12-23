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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id');
            $table->string('event');
            $table->string('region');
            $table->string('province');
            $table->string('city');
            $table->string('barangay');
            $table->string('street');
            $table->string('zipcode');
            $table->string('contactperson');
            $table->string('contactno');
            $table->string('email');
            $table->date('date');
            $table->time('time');
            $table->foreignId('packageid')->constrained('packages', 'id');
            $table->string('packagename');
            $table->string('alias');
            $table->string('packagesize');
            $table->string('backdroptype');
            $table->string('backdropcolor');
            $table->integer('number_of_shots');
            $table->string('price');
            $table->string('extension');
            $table->string('theme');
            $table->string('suggestion');
            $table->string('images')->nullable();
            $table->string('status')->default('Pending');
            $table->string('reference_number')->nullable();
            $table->string('payment_status')->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};