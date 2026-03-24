@extends('admin.layout')

@section('title', 'Users')
@section('heading', 'Users')
@section('subheading', 'Manage user access for the mapping platform.')

@section('content')
<table class="page-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Ana Lopez</td>
            <td>ana@example.com</td>
            <td>Reporter</td>
            <td>Active</td>
        </tr>
        <tr>
            <td>Marco Reyes</td>
            <td>marco@example.com</td>
            <td>Moderator</td>
            <td>Active</td>
        </tr>
        <tr>
            <td>Lea Santos</td>
            <td>lea@example.com</td>
            <td>Reporter</td>
            <td>Pending</td>
        </tr>
    </tbody>
</table>
@endsection
