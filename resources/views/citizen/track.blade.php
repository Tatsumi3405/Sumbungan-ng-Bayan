@extends('layouts.app')
@section('title', 'Track a Report')
@section('content')
@include('layouts.topbar')
<div class="container narrow">
  <a class="back-link" href="{{ route('citizen.dashboard') }}">← Back to dashboard</a>
  <h1 class="page-title">Track a Report</h1>
  <p class="page-sub">Enter a report ID (for example <b>SNB-00012</b> or <b>12</b>) to see its status.</p>

  <form id="searchForm" class="card card-pad" style="display:flex;gap:10px;margin-bottom:22px">
    <input class="input" id="searchId" placeholder="Report ID" autocomplete="off">
    <button class="btn btn-primary" type="submit">Search</button>
  </form>
  <div id="result"></div>

  <div class="section-head"><h2>My Reports</h2></div>
  <div id="list"><div class="spinner"></div></div>
</div>
@endsection
@push('scripts')<script src="{{ asset('js/track.js') }}"></script>@endpush
