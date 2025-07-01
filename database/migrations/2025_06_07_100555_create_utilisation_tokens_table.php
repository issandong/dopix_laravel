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
        Schema::create('utilisation_tokens', function (Blueprint $table) {
            $table->id(); // colonne auto-incrémentée
            $table->unsignedBigInteger('user_id');
            $table->string('medicine_name');
            $table->string('image_url')->nullable();
            $table->json('ingredients'); // [{ name, status, details, detectionTime }]
            $table->enum('overall_status', ['Autorisé', 'Attention', 'Interdit']);
            $table->timestamp('check_date')->useCurrent();
            $table->enum('source', ['Image', 'Texte']);
            $table->unsignedTinyInteger('ai_confidence'); // 0-100
            $table->integer('tokens_used')->default(0);
            $table->decimal('estimated_cost', 8, 2)->default(0.00); // en USD ou EUR
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilisation_tokens');
    }
};
