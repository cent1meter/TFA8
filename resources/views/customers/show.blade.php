@extends('layouts.app')
@section('content')
<section class="page-banner compact-banner"><div class="container"><p class="eyebrow">Customer details</p><h1>{{ $customer->full_name }}</h1></div></section>
<section class="section"><div class="container narrow"><div class="form-card">
    <dl class="record-details"><div><dt>Email</dt><dd>{{ $customer->email }}</dd></div><div><dt>Phone</dt><dd>{{ $customer->phone ?? 'Not provided' }}</dd></div><div><dt>Created</dt><dd>{{ $customer->created_at?->format('M d, Y') }}</dd></div></dl>
    <div class="form-actions"><a class="button button-primary" href="{{ route('customers.edit', $customer) }}">Edit customer</a><a class="button button-secondary" href="{{ route('customers.index') }}">Back to customers</a></div>
</div></div></section>
@endsection
