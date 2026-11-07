@extends('layouts.app')
@section('title', 'Create account')
@section('content')
<div class="split">
  <aside class="split-art">
    <div class="brand"><span class="brand-mark">📣</span> Sumbungan ng Bayan</div>
    <div>
      <h2>Become a voice for change{{ "\n" }}in Barangay Maslog.</h2>
      <p>Register as a citizen of Barangay Maslog and start filing concerns today.</p>
    </div>
    <small>Barangay Maslog — Balilihan Bohol</small>
  </aside>
  <main class="split-form">
    <div class="form-box">
      <a class="back-link" href="{{ route('landing') }}">← Back</a>
      <h1>Join Your Community</h1>
      <p class="sub">Register as a citizen of Barangay Maslog</p>
      @if($errors->any())<div class="alert alert-error">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
      <form method="POST" action="{{ url('/register') }}" novalidate>
        @csrf
        <div class="field"><label for="name">Full name</label>
          <input class="input" id="name" name="name" value="{{ old('name') }}" autocomplete="name" required autofocus></div>
        <div class="field"><label for="email">Email address</label>
          <input class="input" id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required></div>
        <div class="field"><label for="password">Password</label>
          <div class="pw-wrap"><input class="input" id="password" type="password" name="password" autocomplete="new-password" minlength="6" required>
          <button type="button" class="pw-toggle" data-toggle-pw="password" aria-label="Show password">👁</button></div></div>
        <div class="field"><label for="password_confirmation">Confirm password</label>
          <input class="input" id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required></div>
        <button class="btn btn-primary btn-block" type="submit">Create Account</button>
      </form>
      <p class="form-alt">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
    </div>
  </main>
</div>
@endsection
