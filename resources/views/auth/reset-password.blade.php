@extends('layouts.app')
@section('content')
<section class="page-banner compact-banner"><div class="container narrow"><p class="eyebrow">SwiftPOS account</p><h1>Reset your password</h1><p>Choose a new password for your account.</p></div></section>
<section class="section"><div class="container narrow"><div class="form-card">
@include('partials.form-errors')

<form method="POST" action="{{ route('password.store') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">
    <div class="field"><label for="email">Email address</label><input id="email" name="email" type="email" required autocomplete="username" value="{{ old('email', $request->email) }}"></div>
    <div class="field"><label for="password">New password</label><input id="password" name="password" type="password" required minlength="8" autocomplete="new-password" autofocus></div>
    <div class="field"><label for="password_confirmation">Confirm new password</label><input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password"></div>
    <button class="button button-primary" type="submit">Reset password</button>
</form>
</div></div></section>
@endsection
