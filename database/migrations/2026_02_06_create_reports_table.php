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

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('current_reviewer_id')->nullable()->constrained('users');

            $table->string('title');
            $table->text('description');

            $table->enum('status', [
                'draft',
                'submitted',
                'in_review',
                'approved',
                'rejected',
                'returned'
            ])->default('draft');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
    }
};
