<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('driver_warnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained()->cascadeOnDelete();
            $table->foreignId('added_by')->constrained('users');
            $table->text('warning'); // متن تذکر
            $table->decimal('debt',10,2)->default(0); // بدهی اضافه شده
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('driver_warnings');
    }
};
