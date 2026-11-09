<?php

namespace App\Models;

use App\Models\Concerns\HasDepthCutout;
use App\Models\Concerns\HasMarathi;
use App\Support\Lang;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasDepthCutout, HasMarathi;

    public const TYPES = [
        'story' => 'Story',
        'diary' => 'Diary',
        'vlog' => 'Vlog',
        'documentary' => 'Documentary',
    ];

    public const TYPES_PLURAL = [
        'story' => 'Stories',
        'diary' => 'Diaries',
        'vlog' => 'Vlogs',
        'documentary' => 'Documentaries',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'facts' => 'array',
        'tips' => 'array',
        'tags' => 'array',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (Post $post) {
            if (blank($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function url(): string
    {
        return Lang::route('post', $this);
    }

    public function coverUrl(): ?string
    {
        return $this->cover ? Storage::disk('public')->url($this->cover) : null;
    }

    public function isVideo(): bool
    {
        return in_array($this->type, ['vlog', 'documentary'], true);
    }

    public function typeLabel(): string
    {
        return __(self::TYPES[$this->type] ?? 'Story');
    }

    /** Reading time in minutes, from the body in the current language. */
    public function readingMinutes(): int
    {
        $words = str_word_count(strip_tags((string) $this->t('body')));
        // str_word_count ignores Devanagari; count spaces as a fallback.
        $words = max($words, substr_count(strip_tags((string) $this->t('body')), ' '));

        return max(1, (int) ceil($words / 200));
    }

    /** YouTube/Instagram link → embeddable iframe URL, or null. */
    public function embedUrl(): ?string
    {
        $url = (string) $this->video_url;
        if (preg_match('~(?:youtube\.com/(?:watch\?v=|shorts/|live/|embed/)|youtu\.be/)([\w-]{11})~', $url, $m)) {
            return 'https://www.youtube-nocookie.com/embed/'.$m[1];
        }
        if (preg_match('~instagram\.com/(?:p|reel|reels)/([\w-]+)~', $url, $m)) {
            return 'https://www.instagram.com/reel/'.$m[1].'/embed';
        }

        return null;
    }

    /** The next story to read: the previous (older) one, or the newest if this is the oldest. */
    public function nextStory(): ?self
    {
        return static::published()->where('id', '!=', $this->id)
            ->where('published_at', '<', $this->published_at)->latest('published_at')->first()
            ?? static::published()->where('id', '!=', $this->id)->latest('published_at')->first();
    }
}
