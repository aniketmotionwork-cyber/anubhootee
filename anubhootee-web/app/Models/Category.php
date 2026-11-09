<?php

namespace App\Models;

use App\Models\Concerns\HasDepthCutout;
use App\Models\Concerns\HasMarathi;
use App\Support\Lang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use HasDepthCutout, HasMarathi;

    protected $guarded = ['id'];

    protected $casts = [
        'show_on_home' => 'boolean',
        'home_order' => 'integer',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function url(): string
    {
        return Lang::route('category', $this);
    }

    public function label(): string
    {
        return $this->t('name');
    }

    public function coverUrl(): ?string
    {
        return $this->cover ? Storage::disk('public')->url($this->cover) : null;
    }

    /** Chapter data for the homepage showcase. */
    public function toChapter(): array
    {
        return [
            'title' => $this->big_title ?: $this->name,
            'deva' => (string) $this->deva_word,
            'tag' => $this->label(),
            'cta' => $this->t('cta') ?: __('Explore'),
            'url' => $this->link ? Lang::url($this->link) : $this->url(),
            'img' => $this->coverUrl(),
            'fg' => $this->depthUrl(),
            'credit' => (string) $this->cover_credit,
        ];
    }
}
