# Anubhootee (अनुभूती): Website Plan

*Draft 1, 7 Nov 2026*

## 1. What we're building

**Anubhootee** is a bold, modern home for **Indian culture, food and road travel**, told through stories, diaries, vlogs and documentaries. It also has an online store.

- **Visitors come to:** read stories, watch vlogs and documentaries, and learn about places, food and culture.
- **Visitors can also:** buy merch, food and spices, handicrafts and digital products.
- **Languages:** English, with Marathi (मराठी) versions of pages and posts.
- **Look:** bold and modern, with big type, strong colours and confident layouts.

## 2. Brand identity (to be designed)

| Element | Proposal |
|---|---|
| Name / logo | ✅ **Chosen: Option C "Road line"**: **ANUBHOOTEE** in Anton over a dashed turmeric highway line, with "अनुभूती · प्रवास · चव · संस्कृती" beneath (see `design/logo-options.html`) |
| Tagline (options) | "Experience India, one road at a time" · "Stories you can taste" · "Feel the journey" |
| Primary colour | Saffron `#FF6B1A` |
| Secondary | Deep Indigo `#1E1B4B` |
| Accent | Turmeric `#F5B700` |
| Dark / Light | Ink `#0D0D0D` / Chalk `#FAF7F2` |
| Fonts | Display: *Anton* or *Archivo Black* · Body: *Inter* · Marathi: *Mukta* / *Tiro Devanagari Marathi* (all free Google Fonts) |
| Style | Full-bleed photos, oversized headlines, colour-blocked sections, bold category tags |

Logo direction locked on 7 Nov 2026 (Option C). Colours and fonts are as proposed above.

## 3. Technology: Hostinger setup

You already have a domain and Hostinger hosting, and you want an admin panel and a store. Given that, I recommend:

**WordPress + WooCommerce on Hostinger**, with a custom-built Anubhootee theme.

| Need | Solution |
|---|---|
| Admin panel to write posts | WordPress (one-click install in Hostinger hPanel) |
| Online store | WooCommerce (free) |
| Payments (UPI, cards, netbanking) | Razorpay for WooCommerce |
| English + Marathi | Polylang (free) |
| YouTube + Instagram videos | Native embeds plus a custom "Video" post type |
| Digital products | WooCommerce downloadable products |
| Bold modern design | Custom block theme built in this folder, uploaded to Hostinger |
| SEO | Rank Math or Yoast (free) |
| Speed | Hostinger LiteSpeed Cache + optimised images |

> **To confirm:** your Hostinger plan (Premium / Business / Cloud / VPS, or Website Builder). WordPress runs on all of them. If it's **Website Builder only**, the plan changes, so please tell me.

**Alternative (later):** Next.js frontend + WordPress as headless CMS. It's faster and more flexible, but more complex and needs a Node.js-capable plan. It's not needed for launch.

## 4. Site map

```
Home
├── Stories          (long-form travel & culture stories)
├── Diaries          (short personal journal entries)
├── Culture          (festivals, traditions, people, art)
├── Food             (dishes, recipes, street food, regional cuisine)
├── Road Travel      (routes, road trips, guides, important tips)
├── Vlogs            (YouTube + Instagram reels)
├── Documentaries    (long YouTube films, each with its own page)
├── Shop
│   ├── Merch
│   ├── Food & Spices
│   ├── Handicrafts
│   └── Digital (e-books, travel guides, recipes, presets)
├── About
└── Contact
```

Your pages aren't final yet, so treat this as a starting point. Sections are WordPress categories, so adding or renaming them later is easy.

## 5. Homepage layout

**Current direction (v3, minimal, 8 Nov 2026):** the same showcase, stripped down. Only the current chapter's title is shown, in medium-weight Inter, with a small Marathi line. There's no grain, outlined word, descriptions or socials, just thin progress lines and a "01 / 06" counter. The liquid transition is softer.

**v2 (8 Nov 2026):** an immersive full-screen "sticky image showcase", inspired by the feel of sticky-image-showcase.webflow.io. It has six chapters that visitors press-and-drag, scroll or tap through: **Rasta** (road travel), **Chav** (food), **Utsav** (festivals), **Goshti** (stories), **Kala** (art and shop) and **Chitrapat** (films). The photos change with a liquid WebGL transition, under giant titles, Marathi words in Devanagari, and Marathi numerals for the chapters. See `design/index.html`. The earlier bold grid design is kept as `design/v1-bold.html`.

The section list below still applies to the inner pages, which sit behind each chapter.

### Earlier bold grid layout (v1)

1. **Hero:** full-screen photo or video loop, big headline, "Watch latest" and "Read stories" buttons
2. **Latest stories:** bold card grid with category tags (Food / Culture / Road)
3. **Featured documentary:** wide video block
4. **Explore by theme:** colour-blocked tiles for Culture · Food · Road Travel · Diaries
5. **Vlogs strip:** YouTube videos + Instagram reels carousel
6. **Map of journeys** (optional): India map with pins of places covered
7. **Shop teaser:** 4 featured products
8. **Newsletter / follow:** email signup + social links
9. **Footer:** EN / मराठी switch, links, socials

## 6. Content types

| Type | Fields |
|---|---|
| Story / Diary / Article | Title, cover image, body, category, location, tags, language |
| Vlog | YouTube or Instagram URL, thumbnail, short description, location |
| Documentary | YouTube URL, poster, synopsis, credits, runtime, related stories |
| Product | WooCommerce product (physical or downloadable) |

## 7. Store: things to sort out before selling

- **GST registration:** needed once you sell across states or cross the turnover limit
- **FSSAI licence:** required to sell food and spices
- **Razorpay account:** needs KYC, a business bank account, and these policy pages: Privacy, Terms, Refund/Returns, Shipping, Contact
- **Shipping partner:** e.g. Shiprocket (integrates with WooCommerce)
- **Merch printing:** print-on-demand (e.g. Qikink, Printrove) to avoid holding stock
- **Handicrafts:** your sourcing, artisan credits and stock

Digital products are the easiest to launch first: no shipping and no FSSAI licence.

## 8. Phases

| Phase | What | Output |
|---|---|---|
| **1. Design** | Logo, colours, homepage + story page mockups (HTML, viewable in browser) | You approve the look |
| **2. Build theme** | Custom WordPress block theme in this folder | Theme zip |
| **3. Hostinger setup** | Install WordPress, theme, plugins, categories, Polylang | Live (private) site |
| **4. Content** | First 5–10 stories, vlogs, 1 documentary page, About | Real content |
| **5. Marathi** | Translate key pages and menus | EN / मराठी switch |
| **6. Shop** | WooCommerce, Razorpay, products, policy pages | Working store |
| **7. Launch** | SEO, Google Analytics, Search Console, speed, backups | Public launch 🚀 |

### Status (8 Nov 2026)
- ✅ **Phase 1, Design:** minimal showcase homepage + story page (`design/`)
- ✅ **Phase 2, Theme:** WordPress block theme `theme/anubhootee/`, zip at `theme/build/anubhootee.zip`. Tested in WordPress Playground: homepage showcase, story, category, search, 404 and admin chapter settings. See `theme/INSTALL.md`.
- ⏭ **Next, Phase 3:** install on Hostinger, set up Polylang (Marathi), and create real chapters and menus.

## 9. What I need from you

- [ ] Your Hostinger plan type, and the domain name
- [ ] Links to your YouTube channel and Instagram
- [ ] 10–20 of your best photos (travel, food, culture) for the design
- [ ] A short "About Anubhootee" in your own words
- [ ] Who will write the Marathi translations: you, or should I draft them?
- [ ] Whether the store launches with the site or later
- [ ] Any websites or channels you like the look of

## 10. Next step

Once you're happy with this plan, I'll start **Phase 1**: logo options and a bold homepage mockup you can open in the browser.
