Schema::create('report_notes', function (Blueprint $table) {
    $table->id();

    $table->foreignId('report_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();

    $table->text('note');

    $table->timestamps();
});
