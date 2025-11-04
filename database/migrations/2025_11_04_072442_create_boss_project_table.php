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
        Schema::create('boss_project', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('boss_id');
            $table->unsignedBigInteger('project_id');
            $table->string('role')->nullable(); // ví dụ: Leader, Manager, Supervisor
            $table->foreign('boss_id')->references('id')->on('boss')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boss_project');
    }
};
