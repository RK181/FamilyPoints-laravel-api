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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            // CAMPOS ANYADIDOS
            $table->string('name');
            $table->string('point_name');
            $table->string('points_icon');
            $table->boolean('conf_t_approve')->default(true);
            $table->boolean('conf_t_validate')->default(true);
            $table->boolean('conf_t_invalidate')->default(true);
            $table->boolean('conf_r_valiadte')->default(true);

            // RELACTON TO USER CREATOR 1..1
            $table->foreignId('creator_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
