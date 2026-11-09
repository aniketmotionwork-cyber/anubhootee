<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Page;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Menu data for the site layout (header, mobile menu, footer).
        View::composer('layouts.site', function ($view) {
            static $data = null;
            if ($data === null) {
                $ready = Schema::hasTable('categories');
                $data = [
                    'navCategories' => $ready ? Category::orderBy('home_order')->orderBy('name')->get() : collect(),
                    'menuPages' => $ready ? Page::where('is_published', true)->where('in_menu', true)->orderBy('sort')->get() : collect(),
                    'footerPages' => $ready ? Page::where('is_published', true)->where('in_footer', true)->orderBy('sort')->get() : collect(),
                ];
            }
            $view->with($data);
        });
    }
}
