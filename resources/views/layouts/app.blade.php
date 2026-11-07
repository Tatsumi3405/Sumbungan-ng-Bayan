<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Sumbungan ng Bayan') · Brgy. Maslog</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="@yield('body-class')">
  @yield('content')
  @php
    $snbConfig = [
      'user' => auth()->check()
        ? ['id' => auth()->id(), 'name' => auth()->user()->name, 'role' => auth()->user()->role]
        : null,
      'urls' => [
        'base'      => url('/'),
        'dashboard' => route('citizen.dashboard'),
        'track'     => route('citizen.track'),
        'submit'    => route('citizen.submit'),
      ],
      'sectors'    => \App\Support\Grievance::SECTORS,
      'statuses'   => \App\Support\Grievance::STATUSES,
      'categories' => \App\Support\Grievance::CATEGORIES,
    ];
  @endphp
  <script>window.SNB_CONFIG = @json($snbConfig);</script>
  <script src="{{ asset('js/app.js') }}"></script>
  @stack('scripts')
</body>
</html>
