<?php

namespace App\Models;

use App\Models\Concerns\HasMarathi;
use App\Support\Lang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Page extends Model
{
    use HasMarathi;

    protected $guarded = ['id'];

    protected $casts = [
        'in_menu' => 'boolean',
        'in_footer' => 'boolean',
        'is_published' => 'boolean',
        'sort' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function url(): string
    {
        return Lang::route('page', $this);
    }

    public function coverUrl(): ?string
    {
        return $this->cover ? Storage::disk('public')->url($this->cover) : null;
    }
}
