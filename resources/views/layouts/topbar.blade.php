<header class="topbar {{ ($admin ?? false) ? 'admin' : '' }}">
  <div class="topbar-in">
    <a class="brand" href="{{ ($admin ?? false) ? route('admin.dashboard') : route('citizen.dashboard') }}" style="color:#fff">
      <span class="brand-mark">{{ ($admin ?? false) ? '🏛️' : '📣' }}</span>
      <span>{{ ($admin ?? false) ? 'Admin Panel' : 'Sumbungan ng Bayan' }}</span>
    </a>
    <span class="spacer"></span>
    <div class="who"><b>{{ auth()->user()->name }}</b>{{ ($admin ?? false) ? 'Administrator' : 'Citizen' }}</div>
    <form method="POST" action="{{ route('logout') }}">@csrf
      <button class="btn btn-outline-light btn-sm" type="submit">Sign out</button>
    </form>
  </div>
</header>
