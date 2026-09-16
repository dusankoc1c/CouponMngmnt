<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    @vite(['resources/css/dashboard.css'])
</head>
<body>

<nav class="navbar">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-logout">Odjavi se</button>
    </form>

    <div class="navbar-brand">Coupon Manager</div>

    <div class="navbar-user">
        @if (Auth::user()->role == 'superadmin')
            <a href="{{ route('admins.index') }}" class="btn-superadmin">Admin Dash</a>
            <a href="{{ route('invite.create') }}" class="btn-superadmin">Send Invite</a>
        @else
            <span>{{ Auth::user()->name }}</span>
        @endif
    </div>
</nav>

<div class="content">
    <h1>Prodavnice</h1>

    <div class="cards-grid">
        <a href="{{ route('store.create') }}" class="card-add">
            <span class="plus-icon">+</span>
            <span>Dodaj prodavnicu</span>
        </a>

        @foreach ($stores as $store)
            <a href="{{ route('store.show', $store) }}" class="card-store">
                <div class="card-store-avatar">{{ $store->user->name }}</div>

                <h3>{{ $store->name }}</h3>
                <p>{{ $store->description ?? 'Bez opisa' }}</p>

                <div class="card-store-meta">
                    <span class="badge-count">{{ $store->bundles->count() }} bundle-ova</span>

                </div>
            </a>
        @endforeach
    </div>
</div>

</body>
</html>
