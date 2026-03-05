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
        Schema::create('tasks', function (Blueprint $table) {
        $table->id();
        $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
        $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
        $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
        $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
        $table->string('title');
        $table->text('description');
        $table->date('due_date');
        $table->softDeletes(); // Membuat kolom deleted_at
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
