@extends('layouts.app')

@section('content')

<section class="page-banner compact-banner">
    <div class="container">
        <p class="eyebrow">Records</p>
        <h1>Customer Accounts</h1>
        <p>Contact information for the customers currently listed in SwiftPOS.</p>
    </div>
</section>

<section class="section table-section">
    <div class="container">
        <div class="table-card">
            <div class="table-heading">
                <div>
                    <h2>Customers</h2>
                    <p>{{ count($customers) }} database records</p>
                </div>
                <a class="button button-primary" href="{{ route('customers.create') }}">Create Customer</a>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">Full name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Created</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr>
                                <td><strong>{{ $customer->full_name }}</strong></td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone ?? '' }}</td>
                                <td>{{ $customer->created_at?->format('M d, Y') ?? '—' }}</td>
                                <td><div class="row-actions"><a class="edit-link" href="{{ route('customers.show', $customer) }}">View<span class="sr-only"> {{ $customer->full_name }}</span></a><a class="edit-link" href="{{ route('customers.edit', $customer) }}">Edit<span class="sr-only"> {{ $customer->full_name }}</span></a><form method="POST" action="{{ route('customers.destroy', $customer) }}" class="inline-form" onsubmit="return confirm('Delete this record? This cannot be undone.');">@csrf @method('DELETE')<button class="delete-link" type="submit">Delete<span class="sr-only"> {{ $customer->full_name }}</span></button></form></div></td>
                            </tr>
                        @empty
                            <tr><td colspan="5">No customers found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

@endsection
