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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resolution_id');
            $table->foreign('resolution_id')->references('id')->on('resolutions')->onDelete('cascade');
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->string('share');
            $table->string('phone');
            $table->string('user_name');
            $table->string('password');
            $table->string('otp')->nullable();
            $table->string('session_id')->nullable();
            $table->enum('approval_status',['draft','submited','approved'])->default('draft');
            $table->enum('email_sent',['Y','N'])->comment('0 = "Yes", 1 = "No"')->default('N');
            $table->string('reason')->nullable();
            $table->dateTime('sent_date')->nullable();
            $table->dateTime('delivery_date')->nullable();
            $table->unsignedBigInteger('add_by');
            $table->foreign('add_by')->references('id')->on('users');
            $table->boolean('is_active')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
