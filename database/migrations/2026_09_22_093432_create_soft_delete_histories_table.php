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
        Schema::create('soft_delete_histories', function (Blueprint $table) {
            $table->id();

            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');

            $table->string('parent_type')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();

            $table->string('action');

            $table->string('deletion_source')->nullable();

            $table->timestamp('event_at')->nullable();

            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->index(['parent_type', 'parent_id']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soft_delete_histories');
    }
};