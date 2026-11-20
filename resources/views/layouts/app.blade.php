<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SwiftPOS customer and user account management.">
    <title>{{ $title ?? 'SwiftPOS' }} | SwiftPOS</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
    <a class="skip-link" href="#main-content">Skip to content</a>
    <header class="site-header">
        <div class="container nav-wrap">
            <a class="brand" href="{{ route('home') }}" aria-label="SwiftPOS home"><span class="brand-mark">S</span><span>SwiftPOS</span></a>
            <nav aria-label="Main navigation">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('about') }}">About</a>
                <a href="{{ route('customers.index') }}">Customers</a>
                <a href="{{ route('users.index') }}">Users</a>
                @auth
                    <a href="{{ route('account.security') }}">Password</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline-form">@csrf<button class="nav-button" type="submit">Log out</button></form>
                @else
                    <a href="{{ route('login') }}">Log in</a>
                    <a href="{{ route('register') }}">Register</a>
                @endauth
            </nav>
        </div>
    </header>
    <main id="main-content">
        @if (session('success'))<div class="container"><div class="alert alert-success" role="status">{{ session('success') }}</div></div>@endif
        @if (session('error'))<div class="container"><div class="alert alert-error" role="alert">{{ session('error') }}</div></div>@endif
        @yield('content')
    </main>
    <footer class="site-footer"><div class="container footer-wrap"><p>&copy; {{ date('Y') }} SwiftPOS</p><p>Rainier Ian Sayson · TX33 · IT0049</p></div></footer>
</body>
</html>
