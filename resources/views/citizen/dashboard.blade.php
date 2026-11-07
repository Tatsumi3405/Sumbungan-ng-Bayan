@extends('layouts.app')
@section('title', 'My Dashboard')
@section('content')
@include('layouts.topbar')
<div class="container">
  <div class="welcome">
    <h2>Mabuhay, {{ \Illuminate\Support\Str::of($user->name)->explode(' ')->first() }}! 👋</h2>
    <p>Your reports matter. The barangay is listening.</p>
  </div>

  <div class="stats" id="stats"></div>

  <div class="actions">
    <a class="action a1" href="{{ route('citizen.submit') }}"><span class="ico">📝</span><div><b>Submit a Report</b><span>File a new concern</span></div></a>
    <a class="action a2" href="{{ route('citizen.track') }}"><span class="ico">🔎</span><div><b>Track a Report</b><span>Search by report ID</span></div></a>
  </div>

  <div class="section-head"><h2>My Reports</h2></div>
  <div class="filters" id="filters"></div>
  <div id="list"><div class="spinner"></div></div>
</div>
@endsection
@push('scripts')<script src="{{ asset('js/citizen-dashboard.js') }}"></script>@endpush
