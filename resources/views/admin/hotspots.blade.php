@extends('admin.layout')

@section('title', 'Hotspots')
@section('heading', 'Hotspot Areas')
@section('subheading', 'Monitor high-risk clusters generated from map reports.')

@section('content')
<div class="panel-grid">
    <article class="panel">
        <h3>Red Zone</h3>
        <p>Downtown cluster with repeated illegal dumping incidents.</p>
    </article>
    <article class="panel">
        <h3>Blue Zone</h3>
        <p>Flood-prone neighborhood with recurring rain impact reports.</p>
    </article>
    <article class="panel">
        <h3>Emerging Zone</h3>
        <p>New report concentration around East Drainage Channel.</p>
    </article>
</div>
@endsection
