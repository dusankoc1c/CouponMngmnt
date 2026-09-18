<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    @vite(['resources/css/dashboard.css', 'resources/css/store.css'])
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
            <button type="button" class="btn-superadmin" onclick="openModal('export-all-modal')">Export All</button>
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
                <div class="card-store-avatar">{{ $store->user?->name ?? '?' }}</div>

                <h3>{{ $store->name }}</h3>
                <p>{{ $store->description ?? 'Bez opisa' }}</p>

                <div class="card-store-meta">
                    <span class="badge-count">{{ $store->bundles->count() }} bundle-ova</span>
                    @if ($store->value_limit !== null)
                        <span class="store-owner">Limit: ${{ number_format($store->value_limit, 2) }}</span>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
</div>

{{------------------------------MODAL ZA EXPORT ALL-------------------}}
<div class="modal-overlay" id="export-all-modal">
    <div class="modal-box">
        <h2>Export svih prodavnica</h2>
        <p class="modal-subtitle">Izaberi prodavnice za export</p>

        <form method="POST" action="{{ route('superadmin.export-all') }}" onsubmit="closeModal('export-all-modal')">
            @csrf

            <div class="filter-row">
                <div class="form-group">
                    <label>Datum kreiranja</label>
                    <input type="date" name="created_from"/>
                </div>

                <div class="form-group">
                    <label>Krajnji datum</label>
                    <input type="date" name="created_to">
                </div>
            </div>

            <div class="filter-row">
                <div class="form-group">
                    <label>Status kupona</label>
                    <select name="status">
                        <option value="all">Svi</option>
                        <option value="unused">Neiskorišćeni</option>
                        <option value="used">Iskorišćeni</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Cena od</label>
                    <input type="number" step="0.01" name="amount_min">
                </div>

                <div class="form-group">
                    <label>Cena do</label>
                    <input type="number" step="0.01" name="amount_max">
                </div>
            </div>

            <div class="bundle-checkbox-list">
                @forelse ($stores as $store)
                    <label class="bundle-checkbox-item">
                        <input type="checkbox" name="store_ids[]" value="{{ $store->id }}">
                        <span>{{ $store->name }}</span>
                    </label>
                @empty
                    <p class="modal-subtitle">Nema prodavnica za export.</p>
                @endforelse
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel-modal" onclick="clearExportFilters('export-all-modal')">Obriši filtere</button>
                <button type="button" class="btn-cancel-modal" onclick="closeModal('export-all-modal')">Otkaži</button>
                <button type="submit" class="btn-submit-modal">Export CSV</button>
            </div>
        </form>
    </div>
</div>

@vite(['resources/js/addBundle.js'])

</body>
</html>
