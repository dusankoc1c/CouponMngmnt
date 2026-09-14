<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $store->name }}</title>
    @vite(['resources/css/store.css'])
<body>

<div class="content">
    <a href="{{ route('dashboard') }}" class="back-link">&larr; Nazad na dashboard</a>

    <div class="header">
        <div class="header-info">
            <h1>{{ $store->name }}</h1>
            <div class="total-value">Ukupna vrednost : <strong>${{ number_format($totalValue, 2) }}</strong></div>
        </div>

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
                <button type="button" class="btn-cancel-modal" onclick="closeModal()">Otkaži</button>
                <button type="submit" class="btn-submit-modal">Sačuvaj</button>
            </div>
        </form>
    </div>
</div>

@vite(['resources/js/addBundle.js'])

</body>
</html>
