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
        Schema::table('articles', function (Blueprint $table) {
            $table->string('category')->default('General')->after('title');
            $table->string('sentiment')->default('neutral')->after('category'); // positive, negative, neutral
            $table->string('source')->nullable()->after('sentiment');
            $table->text('image_url')->nullable()->after('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['category', 'sentiment', 'source', 'image_url']);
        });
    }
};
