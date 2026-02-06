<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('report_status_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('changed_by')->constrained('users');

            $table->string('old_status')->nullable();
            $table->string('new_status');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('report_status_histories');
    }
};
