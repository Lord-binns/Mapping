@extends('admin.layout')

@section('title', 'Users')
@section('heading', 'Users')
@section('subheading', 'Manage user access for the mapping platform.')

@section('content')
@php
    $users = \App\Models\User::orderBy('created_at', 'desc')->get();
@endphp

<table class="page-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Registered</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
            <td><strong>{{ $user->name }}</strong></td>
            <td style="color:#55706a;">{{ $user->email }}</td>
            <td>
                @if($user->role === 'admin')
                    <span style="display:inline-block; padding:3px 8px; border-radius:6px; font-size:11px; font-weight:700; background:#304d46; color:#fff; text-transform:uppercase;">Admin</span>
                @else
                    <span style="display:inline-block; padding:3px 8px; border-radius:6px; font-size:11px; font-weight:700; background:#e7fbf5; color:#0b6d5a; text-transform:uppercase;">User</span>
                @endif
            </td>
            <td>
                @if($user->email_verified_at)
                    <span style="color:#0b6d5a; font-weight:600; font-size:13px;">Active</span>
                @else
                    <span style="color:#e67e00; font-weight:600; font-size:13px;">Pending</span>
                @endif
            </td>
            <td style="color:#7aada0; font-size:12px;">{{ $user->created_at->format('M d, Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
