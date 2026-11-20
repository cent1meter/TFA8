@extends('layouts.app')
@section('content')
<section class="page-banner compact-banner"><div class="container"><p class="eyebrow">Customer accounts</p><h1>{{ $title }}</h1><p>{{ $customer->exists ? 'Update this customer’s contact information.' : 'Add a customer to your SwiftPOS directory.' }}</p></div></section>
<section class="section"><div class="container narrow">
    <div class="form-card">
        @include('partials.form-errors')
        <form method="POST" action="{{ $customer->exists ? route('customers.update', $customer) : route('customers.store') }}">
            @csrf
            @if ($customer->exists) @method('PUT') @endif
            <p class="form-hint">Fields marked * are required.</p>
            <div class="field">
                <label for="full_name">Full name *</label>
                <input id="full_name" name="full_name" type="text" autocomplete="name" required maxlength="100" value="{{ old('full_name', $customer->full_name) }}" @error('full_name') aria-invalid="true" aria-describedby="full_name-error" @enderror>
                @error('full_name')<p class="field-error" id="full_name-error">{{ $message }}</p>@enderror
            </div>
            <div class="field">
                <label for="email">Email address *</label>
                <input id="email" name="email" type="email" autocomplete="email" required maxlength="100" value="{{ old('email', $customer->email) }}" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                @error('email')<p class="field-error" id="email-error">{{ $message }}</p>@enderror
            </div>
            <div class="field">
                <label for="phone">Phone number <span>(optional)</span></label>
                <input id="phone" name="phone" type="tel" autocomplete="tel" maxlength="20" value="{{ old('phone', $customer->phone) }}" @error('phone') aria-invalid="true" aria-describedby="phone-error" @enderror>
                @error('phone')<p class="field-error" id="phone-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-actions"><button class="button button-primary" type="submit">{{ $customer->exists ? 'Save changes' : 'Create customer' }}</button><a class="button button-secondary" href="{{ route('customers.index') }}">Cancel</a></div>
        </form>
    </div>
</div></section>
@endsection
