@extends('layouts.app')
@section('content')
<section class="page-banner compact-banner"><div class="container narrow"><p class="eyebrow">SwiftPOS account</p><h1>Change your password</h1><p>Update the password for your signed-in account.</p></div></section>
<section class="section"><div class="container narrow"><div class="form-card">
@include('partials.form-errors')

@if (session('status') === 'password-updated')<div class="alert alert-success" role="status">Your password has been updated.</div>@endif
@if ($errors->updatePassword->any())
    <div class="alert alert-error" role="alert"><ul>@foreach ($errors->updatePassword->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif
<form method="POST" action="{{ route('password.update') }}">
    @csrf @method('PUT')
    <div class="field"><label for="current_password">Current password</label><input id="current_password" name="current_password" type="password" required autocomplete="current-password"></div>
    <div class="field"><label for="password">New password</label><input id="password" name="password" type="password" required minlength="8" autocomplete="new-password"></div>
    <div class="field"><label for="password_confirmation">Confirm new password</label><input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password"></div>
    <button class="button button-primary" type="submit">Save password</button>
</form>
</div></div></section>
@endsection
