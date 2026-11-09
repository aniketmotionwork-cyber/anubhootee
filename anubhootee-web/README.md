# Anubhootee website (Laravel)

Stories, food, culture and road travel from India, in English and मराठी. The shop is not included yet.

| Part | Tech |
|---|---|
| Backend | Laravel 13 (PHP 8.4), MySQL on Hostinger / SQLite on your PC |
| Admin | Filament 5 at `/admin` |
| Frontend | Blade templates, Tailwind CSS 4, Alpine.js, built with Vite |
| Homepage | Full-screen chapter showcase with a WebGL photo transition (`resources/js/showcase.js`) |
| Languages | English at `/…`, Marathi at `/mr/…` (UI text in `lang/mr.json`) |

## Run it on your PC

```bash
php artisan serve
```

Open http://127.0.0.1:8000. The admin is at http://127.0.0.1:8000/admin; the login is `ADMIN_EMAIL` / `ADMIN_PASSWORD` in `.env`.

After changing CSS or JS, rebuild with `npm run build` (or keep `npm run dev` running while you work).

To reset the database with fresh demo content:

```bash
php artisan migrate:fresh --seed
```

## What's where

| You want to change… | File |
|---|---|
| Header, footer, menus, SEO tags | `resources/views/layouts/site.blade.php` |
| Homepage showcase | `resources/views/home.blade.php`, `resources/js/showcase.js`, `resources/css/showcase.css` |
| Story page | `resources/views/post.blade.php` |
| Section page, search, pages, contact | `resources/views/*.blade.php` |
| Colours, fonts, all styling | `resources/css/app.css` |
| Marathi UI text | `lang/mr.json` |
| URLs | `routes/web.php` |
| Admin screens | `app/Filament/Resources/` |
| Demo content | `database/seeders/DemoContentSeeder.php` (local only) |

## Depth effect (iPhone lock-screen style)

Hero titles sit **behind** the main subject of the photo, like the clock on an iPhone depth wallpaper. Homepage chapters and story heroes both use it.

- **How:** `php artisan anubhootee:depth` finds the subject of every cover photo with AI segmentation (BiRefNet Lite, MIT licence, runs locally through transformers.js in `scripts/depth-cutouts.mjs`) and saves a transparent cutout in `storage/app/public/depth/`.
  - The first run downloads the model (about 200 MB) once.
  - Saving a section or story with a new cover in the admin runs this automatically, where Node.js is available.
- **Layers:** photo → title → subject cutout → labels and navigation.
  - On the homepage the cutout is drawn with WebGL using the same distortion, transition and shading as the photo, so it stays aligned during drags and transitions.
- **When it switches off:** as on iOS, the effect turns off for a slide when the subject would hide more than 42% of the title, or doesn't touch it at all. It's measured live for each screen size.
  - The rule lives in `MAX_COVER` in `resources/js/showcase.js` and the matching value in `resources/js/app.js`.
- **Hostinger:** shared hosting has no Node.js. Run `php artisan anubhootee:depth` on your PC and upload `storage/app/public/depth/`, or upload a cutout per story in the admin ("Depth cutout").

## Using the admin

- **Stories & videos:** write a story, diary, vlog or documentary. The English and मराठी tabs hold the two versions; leave Marathi empty to show English on Marathi pages. Empty publish date = draft.
- **Sections & chapters:** the six sections. Tick *Show on homepage*, add a cover photo, and set the order to build the homepage showcase.
- **Pages:** About, Contact, Privacy, Terms. *Show in header menu* / *Show in footer links* control where they appear.
- **Inbox:** contact-form messages (unread count in the menu) and newsletter sign-ups (download as CSV).

## Deploy to Hostinger (Premium / Business plan)

1. In hPanel: **Websites → Advanced → PHP Configuration**, choose PHP 8.4. Create a MySQL database and user under **Databases**.
2. On your PC: `npm run build` and `composer install --no-dev --optimize-autoloader`.
3. Upload the whole project (except `node_modules`, `.env`, `database/database.sqlite`) to a folder **next to** `public_html`, e.g. `~/anubhootee`.
4. Point the domain at the `public` folder: either set the website's root to `anubhootee/public` in hPanel, or replace `public_html` with a symlink to it over SSH: `ln -s ~/anubhootee/public ~/public_html`.
5. Create `.env` on the server from `.env.example`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://yourdomain.com`, the MySQL details (`DB_CONNECTION=mysql` …), mail settings, and `ADMIN_EMAIL` / `ADMIN_PASSWORD` (a strong one).
6. Over SSH, in `~/anubhootee`:
   ```bash
   php artisan key:generate
   php artisan migrate --force
   php artisan db:seed --force
   php artisan storage:link
   php artisan optimize
   ```
   On production the seeder creates only the admin account, not the demo content.
7. Log in at `https://yourdomain.com/admin`, then add your sections, cover photos and stories.

## Before launch

- Replace all demo text and photos. The demo photos are Wikimedia Commons, CC BY-SA 4.0 (`database/seeders/images/CREDITS.md`).
- Set `ANU_YOUTUBE`, `ANU_INSTAGRAM` and `ANU_EMAIL` in `.env`.
- Submit `https://yourdomain.com/sitemap.xml` in Google Search Console.
