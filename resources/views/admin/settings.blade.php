@extends('admin.layout')

@section('title', 'Settings')
@section('heading', 'Admin Settings')
@section('subheading', 'Configure defaults for report handling and map moderation.')

@section('content')
<div class="panel-grid">
    <article class="panel">
        <h3>Verification Rules</h3>
        <p>Set default review queue behavior for incoming incident reports.</p>
    </article>
    <article class="panel">
        <h3>Heatmap Threshold</h3>
        <p>Adjust cluster sensitivity to control when hotspot zones appear.</p>
    </article>
    <article class="panel">
        <h3>Notification Settings</h3>
        <p>Manage alerts sent to moderators and responders.</p>
    </article>
</div>
@endsection
