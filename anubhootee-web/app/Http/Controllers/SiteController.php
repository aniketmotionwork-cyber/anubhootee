<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Message;
use App\Models\Page;
use App\Models\Post;
use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class SiteController extends Controller
{
    public function home(): View
    {
        $chapters = Category::where('show_on_home', true)->whereNotNull('cover')
            ->orderBy('home_order')->get()
            ->map->toChapter()->values();

        return view('home', ['chapters' => $chapters]);
    }

    public function category(Request $request, Category $category): View
    {
        $posts = $category->posts()->published()
            ->when($request->query('type'), fn ($q, $type) => $q->where('type', $type))
            ->latest('published_at')->paginate(12)->withQueryString();

        return view('category', ['category' => $category, 'posts' => $posts]);
    }

    public function post(Post $post): View
    {
        abort_unless($post->published_at && $post->published_at->isPast() || auth()->check(), 404);

        return view('post', ['post' => $post, 'next' => $post->nextStory()]);
    }

    public function search(Request $request): View
    {
        $q = trim((string) $request->query('q'));
        $posts = Post::published()
            ->when($q !== '', function ($query) use ($q) {
                $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $q).'%';
                $query->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('title_mr', 'like', $like)
                    ->orWhere('subtitle', 'like', $like)->orWhere('summary', 'like', $like)
                    ->orWhere('body', 'like', $like)->orWhere('body_mr', 'like', $like)
                    ->orWhere('location', 'like', $like)->orWhere('tags', 'like', $like));
            }, fn ($query) => $query->whereRaw('1 = 0'))
            ->latest('published_at')->paginate(12)->withQueryString();

        return view('search', ['q' => $q, 'posts' => $posts]);
    }

    public function page(Page $page): View
    {
        abort_unless($page->is_published, 404);

        return view('page', ['page' => $page]);
    }

    public function contact(): View
    {
        $page = Page::where('slug', 'contact')->where('is_published', true)->first();

        return view('contact', ['page' => $page]);
    }

    public function sendContact(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'subject' => ['nullable', 'string', 'max:190'],
            'message' => ['required', 'string', 'max:5000'],
            'website' => ['prohibited'], // honeypot: real people leave it empty
        ]);

        $key = 'contact:'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withInput()->withErrors(['message' => __('Too many messages. Please try again in a few minutes.')]);
        }
        RateLimiter::hit($key, 600);

        Message::create($data + ['ip' => $request->ip()]);

        return back()->with('status', __('Thank you! We will reply within two working days.'));
    }

    public function subscribe(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('subscribe', ['email' => ['required', 'email', 'max:190']]);
        Subscriber::firstOrCreate(['email' => strtolower($data['email'])], ['lang' => app()->getLocale()]);

        return back()->with('subscribed', __('You are on the list. Thank you!'));
    }

    public function sitemap(): Response
    {
        $urls = collect([url('/'), url('/mr')]);
        foreach (Category::all() as $c) {
            $urls->push(route('category', $c), route('mr.category', $c));
        }
        foreach (Post::published()->get() as $p) {
            $urls->push(route('post', $p));
            if ($p->title_mr) {
                $urls->push(route('mr.post', $p));
            }
        }
        foreach (Page::where('is_published', true)->get() as $p) {
            $urls->push(route('page', $p));
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($urls->unique() as $u) {
            $xml .= '<url><loc>'.e($u).'</loc></url>';
        }

        return response($xml.'</urlset>', 200, ['Content-Type' => 'application/xml']);
    }
}
