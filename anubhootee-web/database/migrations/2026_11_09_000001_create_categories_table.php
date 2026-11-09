<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sections of the site (Road Travel, Food, …). Each can also be a homepage chapter.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('name_mr')->nullable();
            $table->text('description')->nullable();
            $table->text('description_mr')->nullable();

            // Homepage chapter
            $table->boolean('show_on_home')->default(false);
            $table->unsignedSmallInteger('home_order')->default(0);
            $table->string('big_title')->nullable();      // e.g. Rasta
            $table->string('deva_word')->nullable();      // e.g. रस्ता
            $table->string('cta')->nullable();            // e.g. Explore routes
            $table->string('cta_mr')->nullable();
            $table->string('cover')->nullable();          // image path on the public disk
            $table->string('cover_credit')->nullable();
            $table->string('link')->nullable();           // optional override for the chapter button

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
