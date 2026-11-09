<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // About, Contact, Privacy, Terms …
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('title_mr')->nullable();
            $table->longText('body')->nullable();
            $table->longText('body_mr')->nullable();
            $table->string('cover')->nullable();
            $table->boolean('in_menu')->default(false);     // header menu
            $table->boolean('in_footer')->default(false);   // footer links
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // Contact form submissions
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('ip', 45)->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // Newsletter sign-ups
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('lang', 5)->default('en');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscribers');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('pages');
    }
};
