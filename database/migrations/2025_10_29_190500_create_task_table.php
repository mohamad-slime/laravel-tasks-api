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
        Schema::create('task', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            $table->foreignId('status_id')
                ->constrained('task_statuses')
                ->cascadeOnUpdate();

            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');

            $table->dateTime('due_date')->nullable();
            $table->dateTime('completed_at')->nullable();

            $table->timestamps();
        });
    }



    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task');
    }
};
