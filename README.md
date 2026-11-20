# SwiftPOS — TFA8: Resource Controllers and Authentication

**Rainier Ian Sayson · TX33 · IT0049**  
Professor: Prof. Mar Eli Sagsagat

Continues the attached TFA7 Laravel POS project. Both customer and user account controllers implement all seven resource actions. One `Route::resource()` call per controller registers its routes inside an `auth` middleware group. The official Laravel Breeze package is declared as a development dependency, and an adapted Breeze-style Blade authentication scaffold is included. TFA7 forms, server validation, old input, avatar migration/processing, and the SwiftPOS design are retained.

**Delivery status:** this is prepared source. PHP, Composer, and a PHP package download route were unavailable in the delivery workspace, so neither dependency installation, the Breeze generator, the application, PHPUnit, nor Docker was run here. Static source checks are recorded in `docs/VERIFICATION.md`. Local execution remains necessary before submission. No successful installer or test run is claimed.

## Fresh local setup — Windows Command Prompt

Requires PHP **8.2+** and Composer 2. Enable Laravel's normal PHP extensions and **gd**, **fileinfo**, **mbstring**, and **pdo_sqlite**; GD must support JPEG, PNG, and WebP. The existing Intervention Image v3 avatar package is retained from TFA7; confirm instructor acceptance if not already done. No Node, npm, Vite manifest, or frontend build is required by the customized views.

Open a terminal inside the extracted `swiftpos-laravel-tfa8` folder:

```cmd
composer install
copy .env.example .env
php artisan key:generate
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate
php artisan db:seed
php artisan storage:link
php artisan optimize:clear
php artisan serve
```

Stop if any command fails. Composer creates `vendor/autoload.php`, which every Artisan command needs. The first dependency installation generates `composer.lock`; commit it to your repository. On macOS/Linux use `cp` instead of `copy`.

Alternatively, `SETUP_WINDOWS.cmd` performs dependency installation and non-destructive migrations, preserves an existing key, and stops on failures. It does not seed or start the server automatically. The script's SQLite file creation is intended for the default local setup; existing MySQL/PostgreSQL configurations still use their configured connection.

Open **http://127.0.0.1:8000/register** and create your own login. For example, choose username `rainier.demo`, your name, and your email, then choose a private password and confirm it. Do not use a password from a public README. The existing seed usernames such as `admin01` are already taken.

### Existing TFA7 data

Back up your database and avatars. Keep your `.env`, database, APP_KEY, and `storage/app/public` folder when applying these source changes. Run:

```cmd
composer update
php artisan optimize:clear
php artisan migrate
php artisan storage:link
php artisan serve
```

`composer update` adds Breeze to an existing lock file. Skip `storage:link` if the correct link already exists. Do not use `migrate:fresh`: the additive migration preserves existing rows and adds email, email_verified_at, password, remember_token, and a password-reset-token table.

Legacy TFA7 users have **no login email or password**, and remain valid directory records. No shared or plaintext password is assigned to them. Register a new account to log in. The original logged-in Create User form still creates a directory record; the separate Register flow creates a login account. User management cannot overwrite another user's login email/password through extra form fields. Each signed-in user can change their own password through the Password page.

## Resource controller mapping

| Action | Verb | Customer example | Responsibility |
| --- | --- | --- | --- |
| index | GET | `/customers` | List records |
| create | GET | `/customers/create` | Show blank form |
| store | POST | `/customers` | Validate and insert |
| show | GET | `/customers/{customer}` | Show one record |
| edit | GET | `/customers/{customer}/edit` | Show prefilled form |
| update | PUT/PATCH | `/customers/{customer}` | Validate and save edits |
| destroy | DELETE | `/customers/{customer}` | Delete selected record |

The same mapping applies to `/users/{user}`. Route names follow the conventional `customers.index`, `customers.store`, `users.show`, etc. All previous listing links/redirects were migrated to these names.

`routes/web.php` registers:

```php
Route::middleware('auth')->group(function (): void {
    // Other authenticated workspace routes omitted from this excerpt.
    Route::resource('customers', CustomerController::class);
    Route::resource('users', UserController::class);
});
```

Both controllers contain all seven conventional methods. Show pages and deletion forms were added. Delete forms use POST with `_method=DELETE` and CSRF tokens. The server prevents deletion of the account currently being used. Deleting another user's record removes its avatar after successful deletion.

Home, About, login, registration, and password recovery remain public. Dashboard, account-password settings, customer resources, and user resources require a session. This activity requests authentication, so all registered users receive account-management access; no manager/cashier policy or role restriction is claimed. For a production organization, restrict registration and add authorization according to its requirements.

## Breeze integration and secure behavior

`composer install` installs `laravel/breeze` for development. Check it with:

```cmd
composer show laravel/breeze
```

The included auth controllers, LoginRequest and routes follow Breeze's structure, adapted to the inherited schema and visual design. The generator was **not executed in this workspace**. Read `docs/BREEZE_INTEGRATION.md` for that provenance and the customization details. Running `breeze:install` over this finished source can overwrite custom files and assume an incompatible default `name` column; keep a backup if comparing the upstream generator output locally.

Preserved authentication behavior:

- Login authenticates email/password and redirects to the originally requested protected page.
- Login and registration regenerate the session ID.
- Five failed login attempts per normalized email/IP are allowed before temporary throttling.
- Passwords use Laravel hashing, default password rules and confirmation; hidden password/token fields are excluded from model serialization.
- Remember-me is supported. Logout is POST-only, invalidates the session and renews the CSRF token.
- Registration validates unique username and email; database unique indexes provide a second constraint.
- Password recovery uses Laravel's broker with hashed, expiring, single-use reset tokens.
- Password changes require the current password. Session/security defaults are not disabled.
- Optional email verification endpoints require signed verification links and throttle notification requests. Verification is **not mandatory** for resource access, which uses `auth` as the activity requests.

Breeze is a development scaffolding dependency; the application controllers continue to work in a production install using `--no-dev`.

## Email configuration

Local `.env.example` uses `MAIL_MAILER=log`, so reset and optional verification messages go to **storage/logs/laravel.log**, not an actual inbox. Basic login and registration do not depend on SMTP. Only use log email for a local demonstration, and keep logs private.

For real delivery configure a working SMTP provider:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=your-provider-host
MAIL_PORT=587
MAIL_USERNAME=your-provider-username
MAIL_PASSWORD=your-provider-password
MAIL_FROM_ADDRESS=your-verified-sender-address
MAIL_FROM_NAME=SwiftPOS
```

Set MAIL_SCHEME as required by the provider; Laravel/Symfony may negotiate STARTTLS on supported connections. Run `php artisan config:clear` after changes. Keep credentials out of GitHub. A successful request to the configured mail transport is not proof of inbox delivery; check provider delivery logs. This PDF does not require sale processing or low-stock mail, so those features are not added to the inherited account-management application.

## Avatars retained from TFA7

On User Edit, an optional JPEG, PNG, or WebP up to **2 MB** and 4096 × 4096 pixels is decoded by Intervention Image/GD and cropped/resized to a **256 × 256 JPEG** with metadata stripped. It is stored using `Storage::disk('public')` with a UUID filename, and only its relative path goes in `users.avatar`. No original upload is published.

Text-only edits keep the current image. A failed database save cleans up the new file; successful replacement removes the old generated image. The listing and show page use a bundled placeholder for missing files. Keep APP_URL consistent with the URL used in your browser, and run `php artisan storage:link` so `/storage` can serve public images.

## Access-control demonstration

1. Start the app and log out. Open `/customers`, `/customers/create`, `/users`, and `/users/create` directly. Each should redirect to `/login`.
2. Register a new account. Open those same URLs while logged in; they should show the normal forms/lists.
3. Create and edit a customer. Try an invalid email; verify errors and retained input. Open its View page and delete the test record.
4. Create a directory user. Try an existing username; verify rejection. Edit a user without changing its username; verify success. Add an avatar and check the listing.
5. Log out and revisit a protected page; verify another redirect. Also try the previously copied edit/show URL.
6. For requests that write data, use the included tests or an HTTP client with a valid CSRF/session context: hidden buttons alone are not an access-control test. A browser write without CSRF should be rejected.

Capture real logged-out and logged-in screenshots if needed. This package contains no fabricated application or conversation screenshots.

## Automated checks

```cmd
composer test
php artisan route:list
php artisan view:cache
php artisan route:cache
php artisan config:cache
```

Run `php artisan optimize:clear` before returning to local editing. The workflow installs PHP/GD and Composer dependencies, then runs the tests and cache checks. Test-only bcrypt rounds are reduced for speed; `.env.example` and normal application defaults remain 12.

Tests cover all resource actions' auth middleware, guest GET/POST/PUT/PATCH/DELETE denial without writes, logged-in access, registration validation/hashing, session rotation, invalid credentials, throttling, logout, remember-me, CSRF, reset tokens, signed verification, CRUD, old input, username update exceptions, schema/seeds, image validation, thumbnail preparation, replacement and rollback cleanup. **These tests are supplied but not executed in the delivery workspace.**

## If you see 500 Server Error locally

Set `APP_ENV=local` and `APP_DEBUG=true` in `.env`, then run `php artisan optimize:clear` and restart `php artisan serve`. Read the displayed exception or the latest error in `storage/logs/laravel.log`.

- `vendor/autoload.php` missing: finish `composer install` before Artisan.
- Encryption key missing: `php artisan key:generate` on a new setup. Preserve an existing deployment's key.
- Database file/table/column missing: create SQLite as above and run `php artisan migrate`.
- GD missing: `php --ini` shows the php.ini to edit; enable `extension=gd` and check `php -m`.
- Incorrect PHP executable: check `where php` and `php -v`.
- Sessions, cache or logs not writable: give the running PHP process write access to `storage` and `bootstrap/cache`.
- Missing avatar images: verify APP_URL and the storage link. Windows may need Developer Mode or permission to create symlinks.

Send only the exception text when getting help, not `.env` or credentials. Keep debug disabled on a public deployment.

## Publish and submit

No repository or hosted URL has been created in this workspace; GitHub/hosting integrations were previously reported disabled. Create a GitHub repository, push these raw project files (including migrations, seeders, hidden workflow files and your generated composer.lock), then deploy the same revision to a PHP-capable host. Exclude `.env`, vendor, session files and user uploads.

Production requirements: PHP 8.2+, installed extensions, a stable APP_KEY, `APP_ENV=production`, `APP_DEBUG=false`, HTTPS APP_URL, and writable storage/cache directories. Point the document root at **public/**. Install with `composer install --no-dev --optimize-autoloader`, run `php artisan migrate --force`, create the storage link, then `php artisan optimize`. Seed only a fresh demonstration database. Do not reseed on every deploy after editing seed identities.

The included PHP 8.3 Apache Docker configuration accepts PORT (default 10000), requires an external persistent MySQL/PostgreSQL database, and reads APP_KEY and APP_URL from environment variables. Set SEED_DEMO_DATA=true only for initial demo data. Mount persistent storage at `/var/www/html/storage/app/public` for avatars. In a multi-instance production deployment, use shared/persistent session storage as well; the supplied default uses local file sessions. GitHub Pages cannot run PHP.

Submission links, to fill with actual published URLs:

- GitHub repository: **pending publication**
- Hosted working application: **pending deployment**

## AI contribution and references

ChatGPT assisted with resource routing, authentication integration, schema migration, views, tests and documentation. The student must review and run the source and verify the published application. For the separate AI bug-hunt exercise, use the actual conversation and your own corrected explanations/reflection; `docs/RESOURCE_METHODS_AND_REVIEW.md` provides an AI-assisted reference.

References: [Laravel resource controllers](https://laravel.com/docs/12.x/controllers#resource-controllers), [authentication](https://laravel.com/docs/12.x/authentication), [password reset](https://laravel.com/docs/12.x/passwords), [Breeze upstream](https://github.com/laravel/breeze), [file storage](https://laravel.com/docs/12.x/filesystem). Laravel 12 is retained from the supplied TFA7 application for PHP 8.2 support.
