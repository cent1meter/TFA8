@extends('layouts.app')
@section('content')
<section class="page-banner compact-banner"><div class="container narrow"><p class="eyebrow">SwiftPOS account</p><h1>Welcome back</h1><p>Log in to manage customer and user accounts.</p></div></section>
<section class="section"><div class="container narrow"><div class="form-card">
@include('partials.form-errors')

@if (session('status'))<div class="alert alert-success" role="status">{{ session('status') }}</div>@endif
<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="field"><label for="email">Email address</label><input id="email" name="email" type="email" required autofocus autocomplete="username" value="{{ old('email') }}"></div>
    <div class="field"><label for="password">Password</label><input id="password" name="password" type="password" required autocomplete="current-password"></div>
    <label class="remember"><input type="checkbox" name="remember" value="1" @checked(old('remember'))> Remember me</label>
    <div class="form-actions"><button class="button button-primary" type="submit">Log in</button><a class="button button-secondary" href="{{ route('password.request') }}">Forgot password?</a></div>
</form>
<p class="form-hint">New here? <a href="{{ route('register') }}">Register an account</a>.</p>
</div></div></section>
@endsection
