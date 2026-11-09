<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\App;

/**
 * English fields with optional Marathi twins: title / title_mr, body / body_mr …
 * $model->t('title') returns the Marathi value on Marathi pages, falling back to English.
 */
trait HasMarathi
{
    public function t(string $field): ?string
    {
        if (App::getLocale() === 'mr') {
            $mr = $this->getAttribute($field.'_mr');
            if (filled($mr)) {
                return $mr;
            }
        }

        return $this->getAttribute($field);
    }

    /** Editor HTML for the current language, sanitized, with uploaded images resolved. */
    public function html(string $field): \Illuminate\Support\HtmlString
    {
        $content = $this->t($field);
        if (blank($content)) {
            return new \Illuminate\Support\HtmlString('');
        }

        return new \Illuminate\Support\HtmlString(
            \Filament\Forms\Components\RichEditor\RichContentRenderer::make($content)
                ->fileAttachmentsDisk('public')
                ->fileAttachmentsVisibility('public')
                ->toHtml()
        );
    }

    /** Does this record have its own Marathi version (title at least)? */
    public function hasMarathi(): bool
    {
        return filled($this->getAttribute('title_mr') ?? $this->getAttribute('name_mr'));
    }
}
