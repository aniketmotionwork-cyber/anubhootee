<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Stories, diaries, vlogs and documentaries. English fields plus optional Marathi (_mr) versions.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 20)->default('story'); // story | diary | vlog | documentary
            $table->string('slug')->unique();

            $table->string('title');
            $table->string('title_mr')->nullable();
            $table->string('subtitle')->nullable();       // the small line under the title, often Marathi
            $table->text('summary')->nullable();          // for cards, search and SEO
            $table->text('summary_mr')->nullable();
            $table->longText('body')->nullable();         // HTML from the editor
            $table->longText('body_mr')->nullable();

            $table->string('location')->nullable();
            $table->string('cover')->nullable();
            $table->string('cover_credit')->nullable();
            $table->string('video_url')->nullable();      // YouTube / Instagram link for vlogs and films
            $table->json('facts')->nullable();            // [{label, value}] e.g. Distance 186 km
            $table->json('tips')->nullable();             // [{title, text}]
            $table->json('tags')->nullable();             // ["Monsoon", "RoadTrip"]

            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable(); // null = draft
            $table->timestamps();

            $table->index(['published_at', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
