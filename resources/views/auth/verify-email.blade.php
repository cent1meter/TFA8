@extends('layouts.app')
@section('content')
<section class="page-banner compact-banner"><div class="container narrow"><p class="eyebrow">SwiftPOS account</p><h1>Verify your email</h1><p>Request a verification link for your registered email address.</p></div></section>
<section class="section"><div class="container narrow"><div class="form-card">
@include('partials.form-errors')

@if (session('status') === 'verification-link-sent')<div class="alert alert-success" role="status">A new verification link has been sent.</div>@endif
<form method="POST" action="{{ route('verification.send') }}">@csrf<button class="button button-primary" type="submit">Resend verification email</button></form>
<p><a href="{{ route('dashboard') }}">Return to workspace</a></p>
</div></div></section>
@endsection
