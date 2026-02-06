<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // نام کامل
            $table->string('email')->unique(); // ایمیل
            $table->string('phone')->nullable(); // شماره موبایل
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade'); // نقش کاربر
            $table->string('password');       // رمز عبور
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
