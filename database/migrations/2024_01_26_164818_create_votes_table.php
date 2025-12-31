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
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resolution_id');
            $table->foreign('resolution_id')->references('id')->on('resolutions')->onDelete('cascade');
            $table->unsignedBigInteger('member_id');
            $table->foreign('member_id')->references('id')->on('members');
            $table->unsignedBigInteger('resolution_details_id')->onDelete('cascade');
            $table->foreign('resolution_details_id')->references('id')->on('resolution_details');
            $table->enum('resolution_choice',['YES','N0','ABSTAIN','NA'])->default('NA');
            $table->string('instr_comment');
            $table->dateTime('voting_date');
            $table->string('ipaddress');
            $table->boolean('is_active')->default(0);
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('members');
            $table->unsignedBigInteger('updated_by');
            $table->foreign('updated_by')->references('id')->on('members');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
