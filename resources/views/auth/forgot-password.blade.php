@extends('layouts.app')
@section('content')
<section class="page-banner compact-banner"><div class="container narrow"><p class="eyebrow">SwiftPOS account</p><h1>Forgot your password?</h1><p>Enter the email address you registered with to request a reset link.</p></div></section>
<section class="section"><div class="container narrow"><div class="form-card">
@include('partials.form-errors')

@if (session('status'))<div class="alert alert-success" role="status">{{ session('status') }}</div>@endif
<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="field"><label for="email">Email address</label><input id="email" name="email" type="email" required autofocus autocomplete="email" value="{{ old('email') }}"></div>
    <div class="form-actions"><button class="button button-primary" type="submit">Send reset link</button><a class="button button-secondary" href="{{ route('login') }}">Back to log in</a></div>
</form>
</div></div></section>
@endsection
