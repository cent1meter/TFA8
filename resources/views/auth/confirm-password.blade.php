@extends('layouts.app')
@section('content')
<section class="page-banner compact-banner"><div class="container narrow"><p class="eyebrow">SwiftPOS account</p><h1>Confirm your password</h1><p>Confirm your current password to continue.</p></div></section>
<section class="section"><div class="container narrow"><div class="form-card">
@include('partials.form-errors')

<form method="POST" action="{{ route('password.confirm') }}">
    @csrf
    <div class="field"><label for="password">Current password</label><input id="password" name="password" type="password" required autofocus autocomplete="current-password"></div>
    <button class="button button-primary" type="submit">Confirm password</button>
</form>
</div></div></section>
@endsection
