<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // نویسنده
            $table->foreignId('current_receiver_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('title');
            $table->longText('content');

            $table->string('status')->default('pending');
            // pending / approved / rejected / forwarded

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
    }
};
