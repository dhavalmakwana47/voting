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
        Schema::create('resolutions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->enum('evsn_type',[0,1,2])->comment('0 = "Resolution", 1 = "Instruction", 2 => "Option"');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('member_file');
            $table->boolean('is_active')->default(0);
            $table->enum('approval_status',['draft','submited','approved'])->default('draft');
            $table->enum('sentemail_approval',['Y','N','P','H'])->default('N');
            $table->enum('sentemail_reportuser',['Y','N','P'])->default('N');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resolutions');
    }
};
