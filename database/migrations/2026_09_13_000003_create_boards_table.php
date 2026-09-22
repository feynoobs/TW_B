<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boards', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('group_id')->constrained();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('name_nns');
            $table->unsignedBigInteger('status')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boards');
    }
};
