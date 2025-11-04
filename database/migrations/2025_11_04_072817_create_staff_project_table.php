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
        Schema::create('staff_project', function (Blueprint $table) {
           $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('staff_id');
            $table->decimal('salary', 10, 2)->nullable();
            $table->string('role')->nullable(); // ví dụ: Developer, Designer
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_project');
    }
};
