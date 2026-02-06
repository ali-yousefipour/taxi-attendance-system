Schema::create('report_status_logs', function (Blueprint $table) {
    $table->id();

    $table->foreignId('report_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();

    $table->string('action'); // created / forwarded / approved / rejected

    $table->timestamps();
});
