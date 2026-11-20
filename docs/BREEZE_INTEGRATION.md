# Breeze integration and customization

The project declares the official `laravel/breeze` package in Composer dev dependencies. `composer install` installs the package and Laravel discovers its service provider. Application authentication source is included under `app/Http/Controllers/Auth`, `app/Http/Requests/Auth/LoginRequest.php`, and `routes/auth.php` using Breeze's conventional authentication structure.

The included source was prepared and adapted in this workspace; the `breeze:install` generator and Composer could not be executed here because PHP/Composer are unavailable. This is not a recorded successful installer run. After local dependency installation, verify the package with `composer show laravel/breeze` and run the included authentication tests.

Customizations: use the existing `full_name` and unique `username` fields; add nullable email/password columns for legacy directory rows; normalize login/registration email; regenerate the registration session; retain original SwiftPOS CSS and use plain Blade views instead of Tailwind/Vite. Standard session regeneration, logout invalidation/CSRF renewal, login throttling, remember-me, hashed passwords, password-broker reset flow and signed/throttled verification routes remain in place.

Do not run `breeze:install` over your completed application without a backup: it overwrites application routes/views and assumes a default `name` column. If your assessment specifically requires evidence of executing the installer, do that in a separate disposable Laravel checkout with the same framework version, then compare its scaffold to this adaptation and capture your genuine local commands. Do not claim the installer was executed in the delivery workspace.

The resource route group requires `auth`, as requested. Email verification is scaffolded but is not required for account management; the User model does not implement the optional MustVerifyEmail contract. Registration remains public, so every registered user gains the account-management access specified by this assessment. No role/manager authorization is claimed.

Upstream: https://github.com/laravel/breeze (MIT); https://laravel.com/docs/12.x/authentication.
