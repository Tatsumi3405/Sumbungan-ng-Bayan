@extends('layouts.app')
@section('title', 'Admin Portal')
@section('content')
<div class="split">
  <aside class="split-art admin">
    <div class="brand"><span class="brand-mark">🏛️</span> Admin Portal</div>
    <div>
      <h2>Barangay{{ "\n" }}Admin Portal</h2>
      <p>Restricted access — authorised personnel only.</p>
    </div>
    <small>Brgy. Maslog · Balilihan, Bohol</small>
  </aside>
  <main class="split-form">
    <div class="form-box">
      <a class="back-link" href="{{ route('landing') }}">← Back</a>
      <h1 style="color:var(--admin-3)">Admin Portal</h1>
      <p class="sub">Restricted access — authorised personnel only</p>
      @if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif
      <form method="POST" action="{{ url('/admin/login') }}" novalidate>
        @csrf
        <div class="field"><label for="email">Admin Email</label>
          <input class="input" id="email" type="email" name="email" value="{{ old('email') }}" placeholder="admin@maslog.gov.ph" autocomplete="username" required autofocus></div>
        <div class="field"><label for="password">Password</label>
          <div class="pw-wrap"><input class="input" id="password" type="password" name="password" autocomplete="current-password" required>
          <button type="button" class="pw-toggle" data-toggle-pw="password" aria-label="Show password">👁</button></div></div>
        <button class="btn btn-admin btn-block" type="submit">Sign in as Admin</button>
      </form>
      <p class="notice">This portal is monitored. Unauthorised access{{ "\n" }}attempts are logged and reported.</p>
    </div>
  </main>
</div>
@endsection
