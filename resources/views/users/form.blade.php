@extends('layouts.app')
@section('content')
<section class="page-banner compact-banner"><div class="container"><p class="eyebrow">User accounts</p><h1>{{ $title }}</h1><p>{{ $user->exists ? 'Update account details and an optional profile picture.' : 'Add a staff record to your SwiftPOS directory.' }}</p></div></section>
<section class="section"><div class="container narrow">
    <div class="form-card">
        @include('partials.form-errors')
        <form method="POST" enctype="multipart/form-data" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}">
            @csrf
            @if ($user->exists) @method('PUT') @endif
            <p class="form-hint">Fields marked * are required.</p>
            <div class="field">
                <label for="username">Username *</label>
                <input id="username" name="username" type="text" autocomplete="username" required maxlength="50" value="{{ old('username', $user->username) }}" @error('username') aria-invalid="true" aria-describedby="username-error" @enderror>
                @error('username')<p class="field-error" id="username-error">{{ $message }}</p>@enderror
            </div>
            <div class="field">
                <label for="full_name">Full name *</label>
                <input id="full_name" name="full_name" type="text" autocomplete="name" required maxlength="100" value="{{ old('full_name', $user->full_name) }}" @error('full_name') aria-invalid="true" aria-describedby="full_name-error" @enderror>
                @error('full_name')<p class="field-error" id="full_name-error">{{ $message }}</p>@enderror
            </div>
            @if ($user->exists)
                <div class="field">
                    <label for="avatar">Profile picture <span>(optional)</span></label>
                    <img class="avatar avatar-preview" src="{{ $user->avatar_url }}" alt="Current profile picture" width="96" height="96">
                    <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp" aria-describedby="avatar-help{{ $errors->has('avatar') ? ' avatar-error' : '' }}" @error('avatar') aria-invalid="true" @enderror>
                    <p class="form-hint" id="avatar-help">JPEG, PNG, or WebP. Maximum 2 MB and 4096 × 4096 pixels. Saved as a square thumbnail. Leave blank to keep the current picture. Reselect the file if validation fails.</p>
                    @error('avatar')<p class="field-error" id="avatar-error">{{ $message }}</p>@enderror
                </div>
            @else
                <p class="form-hint">This creates a directory record. To create a login, use Register while logged out. Create the user first, then choose Edit to add a profile picture.</p>
            @endif
            <div class="form-actions"><button class="button button-primary" type="submit">{{ $user->exists ? 'Save changes' : 'Create user' }}</button><a class="button button-secondary" href="{{ route('users.index') }}">Cancel</a></div>
        </form>
    </div>
</div></section>
@endsection
