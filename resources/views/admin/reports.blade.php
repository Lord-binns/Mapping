@extends('admin.layout')

@section('title', 'Incident Reports')
@section('heading', 'Incident Reports')
@section('subheading', 'Review user-submitted reports and update statuses.')

@section('content')
<table class="page-table">
    <thead>
        <tr>
            <th>Category</th>
            <th>Location</th>
            <th>Status</th>
            <th>Submitted</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Illegal Dumping</td>
            <td>Barangay Riverside</td>
            <td>Pending</td>
            <td>2026-03-23</td>
        </tr>
        <tr>
            <td>Flood-Prone Area</td>
            <td>Mabini Street</td>
            <td>Verified</td>
            <td>2026-03-22</td>
        </tr>
        <tr>
            <td>Water Condition</td>
            <td>North Creek</td>
            <td>Resolved</td>
            <td>2026-03-20</td>
        </tr>
    </tbody>
</table>
@endsection
