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
        Schema::create('group_user', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            // RELATION GROUP
            $table->foreignId('group_id')->constrained()->onUpdate('cascade')->onDelete('cascade');

            // RELACTION USERS
            $table->foreignId('couple_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');

            // Clave compuesta 
            $table->unique(['group_id', 'couple_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_user');
    }
};
