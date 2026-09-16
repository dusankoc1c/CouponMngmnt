<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $store->name }}</title>
    @vite(['resources/css/store.css'])
</head>

<body>

<div class="content">
    <a href="{{ route('dashboard') }}" class="back-link">&larr; Nazad na dashboard</a>

    <div class="header">
        <div class="header-info">
            <h1>{{ $store->name }}</h1>
            <div class="total-value">Ukupna vrednost : <strong>${{ number_format($totalValue, 2) }}</strong></div>
        </div>

        <button type="button" class="btn-secondary" onclick="openModal('edit-store-modal')">Izmeni prodavnicu</button>
        <button type="button" class="btn-add" onclick="openModal()">Dodaj bundle</button>


    </div>

    <table>
        <thead>
        <tr>
            <th>Created</th>
            <th>Name</th>
            <th>Number of codes</th>
            <th>Total Value</th>
            <th>Exp. Date</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($bundles as $bundle)
            <tr>
                <td>{{ $bundle->created_at->format('m/d/Y') }}</td>
                <td>{{ $bundle->name }}</td>
                <td>{{ $bundle->numberOfCodes() }}</td>
                <td>${{ number_format($bundle->getTotalValue(), 2) }}</td>
                <td>{{ $bundle->expires_at ? $bundle->expires_at->format('m/d/Y') : 'Not set' }}</td>
                <td>
                    <div class="dropdown">
                        <button type="button" class="action-link" onclick="toggleDropdown(this)">Actions &#9662;</button>
                        <div class="dropdown-menu">
                            <a href="{{ route('bundle.show', $bundle) }}" class="dropdown-item">View</a>
                            <form method="POST" action="{{ route('bundle.destroy', $bundle) }}" onsubmit="return confirm('Obrisati ovaj bundle?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item dropdown-item-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
        @empty
            <tr class="empty-row">
                <td colspan="6">Još uvek nema bundle-ova za ovu prodavnicu.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="export-bar">
        <button type="button" class="btn-secondary" onclick="openModal('export-modal')">Export</button>
    </div>
</div>


<div class="modal-overlay" id="bundle-modal">
    <div class="modal-box">
        <h2>Novi bundle</h2>

        <form method="POST" action="{{ route('bundle.store', $store) }}">
            @csrf

            <div class="form-group">
                <label>Naziv bundle-a</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label>Opis</label>
                <textarea name="description"></textarea>
            </div>

            <div class="form-group">
                <label>Datum isteka </label>
                <input type="date" name="expires_at">
            </div>

            <div class="coupons-section">
                <div class="coupons-section-header">
                    <h3>Kuponi</h3>
                    <button type="button" class="btn-small" onclick="addCouponRow()">+ Dodaj kupon</button>
                </div>

                <table class="coupons-table">
                    <thead>
                    <tr>
                        <th>Ime</th>
                        <th>Email</th>
                        <th>Iznos ($)</th>
                        <th>Datum slanja</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="coupons-tbody"></tbody>
                </table>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel-modal" onclick="closeModal()">Otkazi</button>
                <button type="submit" class="btn-submit-modal">Sačuvaj</button>
            </div>
        </form>
    </div>
</div>

{{--                ------MODAL ZA IZMENU--------           }}--}}

<div class="modal-overlay" id="edit-store-modal">
    <div class="modal-box">
        <h2>Izmeni prodavnicu</h2>

        <form method="POST" action="{{ route('store.update', $store) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Naziv prodavnice</label>
                <input type="text" name="name" value="{{ old('name', $store->name) }}" required>
            </div>

            <div class="form-group">
                <label>Opis</label>
                <textarea name="description">{{ old('description', $store->description) }}</textarea>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel-modal" onclick="closeModal('edit-store-modal')">Otkaži</button>
                <button type="submit" class="btn-submit-modal">Sačuvaj izmene</button>
            </div>
        </form>
    </div>
</div>


{{------------------------MODAL ZA EXPORT--------------------}}
<div class="modal-overlay" id="export-modal">
    <div class="modal-box">
        <h2>Export kupona</h2>
        <p class="modal-subtitle">Izaberi bundle-ove za export</p>

        <form method="POST" action="{{ route('store.export-codes', $store) }}" onsubmit="closeModal('export-modal')">
            @csrf

            <div class="filter-row">
                <div class="form-group">
                    <label>Datum Kreiranja</label>
                    <input type="date" name="created_from"/>
                </div>

                <div class="form-group">
                    <label>Kranji Datum</label>
                    <input type="date" name="created_to">
                </div>
            </div>

            <div class="filter-row">
                <div class="form-group">
                    <label>Status Kupona</label>
                    <select name="status">
                        <option value="all">Svi</option>
                        <option value="unused">Neiskorisceni</option>
                        <option value="used">Iskorisceni</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Cena Od</label>
                    <input type="number" step="0.01" name="amount_min">
                </div>

                <div class="form-group">
                    <label>Cena Do</label>
                    <input type="number" step="0.01" name="amount_max">
                </div>
            </div>


            <div class="bundle-checkbox-list">
                @forelse ($bundles as $bundle)
                    <label class="bundle-checkbox-item">
                        <input type="checkbox" name="bundle_ids[]" value="{{ $bundle->id }}">
                        <span>{{ $bundle->name }}</span>
                    </label>
                @empty
                    <p class="modal-subtitle">Nema bundle-ova za export.</p>
                @endforelse
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel-modal" onclick="closeModal('export-modal')">Otkaži</button>
                <button type="submit" class="btn-submit-modal">Export CSV</button>
            </div>
        </form>
    </div>
</div>

@vite(['resources/js/addBundle.js'])

</body>
</html>
