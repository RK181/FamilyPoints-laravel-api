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
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            // CAMPOS ANYADIDOS
            $table->string('title');
            $table->string('description');
            $table->integer('cost');
            $table->boolean('redeem')->default(false);
            $table->boolean('validate')->default(false);
            $table->dateTime('expire_at');


            // RELACTON TO GROUP 1
            $table->foreignId('group_id')->constrained()->onUpdate('cascade')->onDelete('cascade');

            // RELACTON TO USER 1
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
