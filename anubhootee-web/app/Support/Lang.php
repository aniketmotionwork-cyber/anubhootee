<?php

namespace App\Support;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;

/**
 * English lives at /…, Marathi at /mr/…. Slugs are the same in both languages.
 */
class Lang
{
    public const LOCALES = ['en' => 'EN', 'mr' => 'मराठी'];

    public static function isMarathi(): bool
    {
        return App::getLocale() === 'mr';
    }

    /** route() for the current language. */
    public static function route(string $name, mixed $params = []): string
    {
        return route(self::isMarathi() ? 'mr.'.$name : $name, $params);
    }

    /** Prefix a site path (e.g. "/about") with /mr on Marathi pages. Full URLs pass through. */
    public static function url(string $path): string
    {
        if (preg_match('~^https?://~', $path)) {
            return $path;
        }

        return url((self::isMarathi() ? '/mr' : '').'/'.ltrim($path, '/'));
    }

    /** The current page in the other language. */
    public static function switchUrl(string $locale): string
    {
        $path = '/'.ltrim(Request::path(), '/');
        $path = preg_replace('~^/mr(?=/|$)~', '', $path) ?: '/';
        $path = $locale === 'mr' ? '/mr'.($path === '/' ? '' : $path) : $path;
        $query = Request::getQueryString();

        return url($path).($query ? '?'.$query : '');
    }

    /** Latin digits → Devanagari digits on Marathi pages. */
    public static function digits(string|int $text): string
    {
        $text = (string) $text;

        return self::isMarathi() ? strtr($text, ['०', '१', '२', '३', '४', '५', '६', '७', '८', '९']) : $text;
    }

    /** A date like "9 November 2026", in Marathi month names on Marathi pages. */
    public static function date(?\DateTimeInterface $date): string
    {
        if (! $date) {
            return '';
        }
        if (! self::isMarathi()) {
            return $date->format('j F Y');
        }
        $months = ['जानेवारी', 'फेब्रुवारी', 'मार्च', 'एप्रिल', 'मे', 'जून', 'जुलै', 'ऑगस्ट', 'सप्टेंबर', 'ऑक्टोबर', 'नोव्हेंबर', 'डिसेंबर'];

        return self::digits($date->format('j')).' '.$months[(int) $date->format('n') - 1].' '.self::digits($date->format('Y'));
    }
}
