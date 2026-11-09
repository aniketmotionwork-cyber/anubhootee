<?php

use App\Http\Controllers\SiteController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

/*
| The same routes twice: English at /…, Marathi at /mr/… (names prefixed "mr.").
| The admin panel is at /admin (Filament).
*/
$site = function () {
    Route::get('/', [SiteController::class, 'home'])->name('home');
    Route::get('/category/{category}', [SiteController::class, 'category'])->name('category');
    Route::get('/stories/{post}', [SiteController::class, 'post'])->name('post');
    Route::get('/search', [SiteController::class, 'search'])->name('search');
    Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
    Route::post('/contact', [SiteController::class, 'sendContact'])->name('contact.send')->middleware('throttle:10,1');
    Route::post('/subscribe', [SiteController::class, 'subscribe'])->name('subscribe')->middleware('throttle:10,1');
    Route::view('/brand', 'brand')->name('brand'); // brand style guide (not in the menus)
    Route::get('/{page}', [SiteController::class, 'page'])->name('page')
        ->where('page', '(?!admin\b|livewire\b|filament\b|storage\b|build\b|up\b)[a-z0-9\-_]+');
};

Route::get('/sitemap.xml', [SiteController::class, 'sitemap'])->name('sitemap');

Route::prefix('mr')->name('mr.')->middleware(SetLocale::class.':mr')->group($site);
Route::middleware(SetLocale::class.':en')->group($site);
