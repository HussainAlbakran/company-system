public function up(): void
{
    Schema::create('departments', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('code')->nullable();
        $table->text('description')->nullable();
        $table->timestamps();
    });
}