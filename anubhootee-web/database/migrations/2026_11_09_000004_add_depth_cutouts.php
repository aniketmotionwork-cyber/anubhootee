<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Depth effect: a transparent cutout of the cover photo's main subject, layered above the title.
 * cover_fg = path of the cutout (null = not generated yet), cover_fg_coverage = share of the photo that is subject.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['categories', 'posts'] as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->string('cover_fg')->nullable()->after('cover');
                $table->float('cover_fg_coverage')->nullable()->after('cover_fg');
            });
        }
    }

    public function down(): void
    {
        foreach (['categories', 'posts'] as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->dropColumn(['cover_fg', 'cover_fg_coverage']);
            });
        }
    }
};
