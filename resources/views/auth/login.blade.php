@extends('layouts.app')
@section('title', 'Sign in')
@section('content')
<div class="split">
  <aside class="split-art">
    <div class="brand"><span class="brand-mark">📣</span> Sumbungan ng Bayan</div>
    <div>
      <h2>Welcome back{{ "\n" }}to your community.</h2>
      <p>Sign in to file concerns, follow their progress and chat with barangay officials.</p>
    </div>
    <small>Brgy. Maslog · Balilihan, Bohol</small>
  </aside>
  <main class="split-form">
    <div class="form-box">
      <a class="back-link" href="{{ route('landing') }}">← Back</a>
      <h1>Welcome back</h1>
      <p class="sub">Sign in to your account</p>
      @if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif
      <form method="POST" action="{{ url('/login') }}" novalidate>
        @csrf
        <div class="field"><label for="email">Email address</label>
          <input class="input" id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus></div>
        <div class="field"><label for="password">Password</label>
          <div class="pw-wrap"><input class="input" id="password" type="password" name="password" autocomplete="current-password" required>
          <button type="button" class="pw-toggle" data-toggle-pw="password" aria-label="Show password">👁</button></div></div>
        <button class="btn btn-primary btn-block" type="submit">Sign In</button>
      </form>
      <p class="form-alt">Don't have an account? <a href="{{ route('register') }}">Create Account</a></p>
      <p class="form-alt"><a href="{{ route('admin.login') }}">Barangay Admin Portal</a></p>
    </div>
  </main>
</div>
@endsection
