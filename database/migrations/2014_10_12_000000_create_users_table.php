<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone');
            $table->string('pan_number');
            $table->boolean('is_active')->default(0);
            $table->enum('type',[0,1,2])->comment('0 = "Super Admin", 1 = "Authorized Person"')->default(1);
            $table->enum('user_type',[1,2])->comment('1 = "Authorized Person", 2 = "Scrutinizer"');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

       User::insert([
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make(123456),
            'type' => "0",
            'phone' => "",
            'pan_number' => "",
            'is_active' => 1
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
