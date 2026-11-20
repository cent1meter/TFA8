@extends('layouts.app')

@section('content')

<section class="page-banner compact-banner">
    <div class="container">
        <p class="eyebrow">Access management</p>
        <h1>User Accounts</h1>
        <p>Staff accounts currently registered in the SwiftPOS database.</p>
    </div>
</section>

<section class="section table-section">
    <div class="container">
        <div class="table-card">
            <div class="table-heading">
                <div>
                    <h2>Users</h2>
                    <p>{{ count($users) }} database records</p>
                </div>
                <a class="button button-primary" href="{{ route('users.create') }}">Create User</a>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">Avatar</th>
                            <th scope="col">Username</th>
                            <th scope="col">Full name</th>
                            <th scope="col">Created</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td><img class="avatar" src="{{ $user->avatar_url }}" alt="Avatar for {{ $user->full_name }}" width="48" height="48" loading="lazy"></td>
                                <td><strong>{{ $user->username }}</strong></td>
                                <td>{{ $user->full_name }}</td>
                                <td>{{ $user->created_at?->format('M d, Y') ?? '—' }}</td>
                                <td><div class="row-actions"><a class="edit-link" href="{{ route('users.show', $user) }}">View<span class="sr-only"> {{ $user->full_name }}</span></a><a class="edit-link" href="{{ route('users.edit', $user) }}">Edit<span class="sr-only"> {{ $user->full_name }}</span></a>@if (! auth()->user()->is($user))<form method="POST" action="{{ route('users.destroy', $user) }}" class="inline-form" onsubmit="return confirm('Delete this record? This cannot be undone.');">@csrf @method('DELETE')<button class="delete-link" type="submit">Delete<span class="sr-only"> {{ $user->full_name }}</span></button></form>@endif</div></td>
                            </tr>
                        @empty
                            <tr><td colspan="5">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

@endsection
