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
        Schema::create('resolution_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resolution_id');
            $table->foreign('resolution_id')->references('id')->on('resolutions')->onDelete('cascade');
            $table->unsignedBigInteger('add_by');
            $table->foreign('add_by')->references('id')->on('users');
            $table->integer('resolution_number')->nullable();
            $table->text('description');
            $table->string('file_name');
            $table->enum('option_type',['radio','checkbox'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resolution_details');
    }
};
