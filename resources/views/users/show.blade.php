@extends('layouts.app')
@section('content')
<section class="page-banner compact-banner"><div class="container"><p class="eyebrow">User details</p><h1>{{ $user->full_name }}</h1></div></section>
<section class="section"><div class="container narrow"><div class="form-card">
    <img class="avatar avatar-preview" src="{{ $user->avatar_url }}" alt="Avatar for {{ $user->full_name }}" width="96" height="96">
    <dl class="record-details"><div><dt>Username</dt><dd>{{ $user->username }}</dd></div><div><dt>Email</dt><dd>{{ $user->email ?? 'Directory record — no login email' }}</dd></div><div><dt>Created</dt><dd>{{ $user->created_at?->format('M d, Y') }}</dd></div></dl>
    <div class="form-actions"><a class="button button-primary" href="{{ route('users.edit', $user) }}">Edit user</a><a class="button button-secondary" href="{{ route('users.index') }}">Back to users</a></div>
</div></div></section>
@endsection
