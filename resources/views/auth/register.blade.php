@extends('layouts.app')
@section('content')
<section class="page-banner compact-banner"><div class="container narrow"><p class="eyebrow">SwiftPOS account</p><h1>Create your account</h1><p>Register to access the SwiftPOS workspace.</p></div></section>
<section class="section"><div class="container narrow"><div class="form-card">
@include('partials.form-errors')

<form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="field"><label for="full_name">Full name</label><input id="full_name" name="full_name" required maxlength="100" autocomplete="name" autofocus value="{{ old('full_name') }}"></div>
    <div class="field"><label for="username">Username</label><input id="username" name="username" required maxlength="50" autocomplete="username" value="{{ old('username') }}"></div>
    <div class="field"><label for="email">Email address</label><input id="email" name="email" type="email" required maxlength="255" autocomplete="email" value="{{ old('email') }}"></div>
    <div class="field"><label for="password">Password</label><input id="password" name="password" type="password" required minlength="8" autocomplete="new-password"><p class="form-hint">Use at least eight characters.</p></div>
    <div class="field"><label for="password_confirmation">Confirm password</label><input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password"></div>
    <div class="form-actions"><button class="button button-primary" type="submit">Register</button><a class="button button-secondary" href="{{ route('login') }}">Already registered?</a></div>
</form>
</div></div></section>
@endsection
