# Anubhootee theme: install on Hostinger

The file to upload is **`theme/build/anubhootee.zip`**. Rebuild it after any change with `theme/dev/build.sh`.

## 1. Install WordPress (once)
1. Log in to **Hostinger hPanel**, then go to **Websites → your domain → Dashboard**.
2. If WordPress isn't installed yet, use **WordPress → Install** (the auto-installer).
3. Open **WP Admin** from hPanel.

## 2. Upload and activate the theme
1. In WP Admin, go to **Appearance → Themes → Add New Theme → Upload Theme**.
2. Choose `anubhootee.zip`, then click **Install Now → Activate**.
3. Go to **Settings → Permalinks** and choose **Post name**, then **Save**.

The homepage now shows the full-screen showcase with **demo chapters and placeholder photos**.

## 3. Plugins (all free)
| Plugin | Why |
|---|---|
| **Polylang** | English + मराठी. Add the languages *English* and *मराठी (mr)*. The EN · मराठी switch appears in the header automatically. |
| **LiteSpeed Cache** | Speed (Hostinger usually pre-installs it). |
| **Rank Math SEO** | Search titles and descriptions, sitemap. |
| **WooCommerce** | The shop. The theme styles it, adds a cart icon to the header, and shows only free delivery when an order qualifies. |
| **Razorpay for WooCommerce** | Payments (UPI, cards, netbanking), once your Razorpay account is approved. |

The footer shows links to **Contact, Shipping, Refunds, Privacy and Terms** automatically once pages with the slugs `contact`, `shipping-policy`, `refund_returns`, `privacy-policy` and `terms` are published. To send a homepage chapter's button to the shop, paste the Shop page URL into that category's **Button link** field.

## 4. Set up your chapters (homepage)
Each chapter is a **category**.

1. Go to **Posts → Categories**, then add or edit a category (e.g. *Road Travel*).
2. Fill in the **Homepage chapter** section:
   - ✅ **Show on homepage**
   - **Chapter order**: 1, 2, 3 …
   - **Big title**: e.g. *Rasta*
   - **Devanagari word**: e.g. *रस्ता*
   - **Button text**: e.g. *Explore routes*
   - **Cover photo**: landscape, at least 1920 px wide
   - **Photo credit**: needed only if the photo isn't yours
3. Click **Update**.

As soon as one category is set up with a photo, the demo chapters are replaced by yours. With Polylang, translate each category into मराठी and fill in the same fields in Marathi.

## 5. Menu
Go to **Appearance → Editor → Navigation** and create a menu (Stories, Food, Culture, Road Travel, Films, Shop, About). The header and footer use it.

## 6. Writing a story
1. Go to **Posts → Add New**. Pick the **Story starter** pattern, or start blank.
2. **Featured image**: the full-screen cover. Put the photographer's credit in the image's **Caption**.
3. **Excerpt**: the small line under the title, e.g. *पावसाळ्यातला घाटमाथा*.
4. **Location** (optional, shown next to the category):
   1. Open the editor's ⋮ menu, go to **Preferences → General**, and turn on **Custom fields**.
   2. Add the field `anu_location`, e.g. *Western Ghats*.
5. Useful blocks:
   - **Route facts** and **Important tips**: under Patterns → Anubhootee.
   - **Quote**: shown as a large italic pull-quote.
   - **Image**: set Align to **Wide** for a big photo.
   - **YouTube or Instagram**: paste the video link on its own line.

## 7. Before launch
- **Replace the demo photos.** They're Wikimedia Commons (CC BY-SA 4.0); see `assets/img/CREDITS.md`.
- **Create your pages:** About, Contact, Privacy, Terms, Refunds and Shipping. Razorpay needs these.
- **Check speed:** Hostinger → Performance, LiteSpeed Cache → Image optimisation.

## Testing locally
To try changes in a temporary WordPress in the browser:
1. Start the file server: the `theme-files` entry in `.claude/launch.json`, or, without Python, run `powershell -ExecutionPolicy Bypass -File theme/dev/serve.ps1 4322`.
2. Open `http://localhost:4322/dev/playground.html`.

The page boots a complete test site (about a minute):

| Script | What it adds |
|---|---|
| `dev/setup.php` | The six homepage chapters with photos, and 4 sample stories |
| Plugins | Polylang, Rank Math SEO, LiteSpeed Cache, WooCommerce |
| `dev/content.php` | 19 more stories, diaries, vlogs and documentaries; About, Contact, Shipping, Refunds, Privacy and Terms pages |
| `dev/store.php` | A working shop in ₹: 14 products in Merch, Food & Spices, Handicrafts and Digital (one with sizes, three downloadable), reviews, coupon `ANUBHOOTEE10`, India delivery (₹60, free over ₹999), Cash/UPI on delivery |
| `dev/menus.php` | The header and footer menu |
| `dev/languages.php` | English + मराठी in Polylang, and a Marathi copy of one story |

All of it is **placeholder content** for testing; it never goes into the theme zip. The test store takes no real payments. On the real site, use Razorpay instead of Cash on delivery.

After changing theme files, rebuild the zip with `powershell -ExecutionPolicy Bypass -File theme/dev/build.ps1` (or `build.sh`), then reload the test page.
