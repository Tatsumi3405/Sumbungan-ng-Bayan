@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('body-class', 'admin-page')
@section('content')
@include('layouts.topbar', ['admin' => true])
<div class="container">
  <div class="welcome" style="background:var(--admin-hero)">
    <h2>Grievance Overview</h2>
    <p>Review, respond to and resolve reports from Barangay Maslog.</p>
  </div>

  <div class="stats" id="stats"></div>

  <div class="overview">
    <div class="card card-pad">
      <div class="section-head" style="margin-top:0"><h2 style="color:var(--admin-3)">Status Distribution</h2><span class="chip" id="totalChip"></span></div>
      <div class="pie-wrap" id="pie"></div>
    </div>
    <div class="card card-pad">
      <div class="section-head" style="margin-top:0"><h2 style="color:var(--admin-3)">By Category</h2></div>
      <div id="catBars"></div>
    </div>
  </div>

  <div class="section-head"><h2 style="color:var(--admin-3)">All Reports</h2><span class="chip" id="countChip"></span></div>
  <div class="filters" id="filters"></div>
  <div class="toolbar">
    <input class="input" id="q" placeholder="Search title, name, address or ID…">
    <select class="select" id="catFilter">
      <option value="">All categories</option>
      @foreach($categories as $key => $c)<option value="{{ $key }}">{{ $c['icon'] }} {{ $c['label'] }}</option>@endforeach
    </select>
  </div>
  <div id="list"><div class="spinner"></div></div>
</div>
@endsection
@push('scripts')<script src="{{ asset('js/admin.js') }}"></script>@endpush
