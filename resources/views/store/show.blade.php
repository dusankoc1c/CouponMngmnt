<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $store->name }}</title>
    @vite(['resources/css/store.css'])
</head>

@php
    $modalToOpenOnError = '';

    if ($errors->has('initial_email_template') || $errors->has('reminder_email_template')) {
        $modalToOpenOnError = 'email-templates-modal';
    } elseif ($errors->any()) {
        $modalToOpenOnError = 'bundle-modal';
    }
@endphp

<body data-open-modal-on-load="{{ $modalToOpenOnError }}">

<div class="content">
    <a href="{{ route('dashboard') }}" class="back-link">&larr; Nazad na dashboard</a>

    <div class="header">
        <div class="header-info">
            <h1>{{ $store->name }}</h1>

            <div class="stat-box">
                <span class="stat-label">Ukupna Vrednost Kupona :</span>
                <span class="stat-value"><strong>${{ number_format($totalValue, 2) }}</strong></span>
            </div>

            @if($store->value_limit != null)
                <div class="stat-box">
                    <span class="stat-label">Limit :</span>
                    <span class="stat-value"><strong>${{ number_format($store->value_limit, 2) }}</strong></span>
                </div>

                <div class="stat-box">
                    <span class="stat-label">Preostalo :</span>
                    <span class="stat-value">${{ number_format($store->value_limit - $totalValue, 2) }}</span>
                </div>
            @endif
        </div>

        <div class="header-actions">
            <button type="button" class="btn-secondary" data-open-modal="edit-store-modal">Izmeni prodavnicu</button>
            <button type="button" class="btn-secondary" data-open-modal="email-templates-modal">Email Templejti</button>
            <button type="button" class="btn-add" data-open-modal="bundle-modal">Dodaj bundle</button>
        </div>

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
                        <button type="button" class="action-link" data-toggle-dropdown>Actions &#9662;</button>
                        <div class="dropdown-menu">
                            <a href="{{ route('bundle.show', $bundle) }}" class="dropdown-item">View</a>
                            <form method="POST" action="{{ route('bundle.destroy', $bundle) }}" data-confirm="Obrisati ovaj bundle?">
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
        <button type="button" class="btn-secondary" data-open-modal="export-modal">Export</button>
    </div>
</div>


<div class="modal-overlay" id="bundle-modal">
    <div class="modal-box">
        <h2>Novi bundle</h2>

        @if ($errors->any())
            <div class="errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

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
                    <button type="button" class="btn-small" data-add-coupon-row>+ Dodaj kupon</button>
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

            <div class="coupons-section">
                <div class="coupons-section-header">
                    <h3>Mass Add Kupona</h3>
                    <button type="button" class="btn-small" data-add-tier-row>+ Dodaj </button>
                </div>

                <table class="tier-table">
                    <thead>
                    <tr>
                        <th>Broj kupona</th>
                        <th>Iznos ($) po kuponu</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="tiers-tbody"></tbody>
                </table>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel-modal" data-close-modal="bundle-modal">Otkazi</button>
                <button type="submit" class="btn-submit-modal">Sačuvaj</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL ZA IZMENU --}}

<div class="modal-overlay" id="edit-store-modal">
    <div class="modal-box">
        <h2>Izmeni prodavnicu</h2>

        @if ($errors->any())
            <div class="errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

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

            <div class="form-group">
                <label>Broj dana do reminder mejla (prazno = 20 podrazumevano)</label>
                <input type="number" min="1" name="reminder_days" value="{{ old('reminder_days', $store->reminder_days) }}">
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel-modal" data-close-modal="edit-store-modal">Otkaži</button>
                <button type="submit" class="btn-submit-modal">Sačuvaj izmene</button>
            </div>
        </form>
    </div>
</div>


{{-- MODAL ZA EXPORT --}}
<div class="modal-overlay" id="export-modal">
    <div class="modal-box">
        <h2>Export kupona</h2>
        <p class="modal-subtitle">Izaberi bundle-ove za export</p>

        <form method="POST" action="{{ route('store.export-codes', $store) }}" data-close-on-submit="export-modal">
            @csrf

            <div class="filter-row">
                <div class="form-group">
                    <label>Datum Kreiranja</label>
                    <input type="date" name="created_from"/>
                </div>

                <div class="form-group">
                    <label>Krajnji Datum</label>
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
                <button type="button" class="btn-cancel-modal" data-clear-filters="export-modal">Obriši filtere</button>
                <button type="button" class="btn-cancel-modal" data-close-modal="export-modal">Otkaži</button>
                <button type="submit" class="btn-submit-modal">Export CSV</button>
            </div>
        </form>
    </div>
</div>


{{----------------------MODAL ZA EMAIL TEMPLATE--------------------}}
<div class="modal-overlay" id="email-templates-modal">
    <div class="modal-box">
        <h2>Email Template</h2>

        @if ($errors->any())
            <div class="errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('store.update-email-templates', $store) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Inicijalni mejl</label>
                <p class="modal-subtitle">Obavezno: @{{name}}, @{{ amount }}, @{{ store_name }}</p>
                <textarea name="initial_email_template" rows="6">{{ old('initial_email_template', $initialEmailTemplate) }}</textarea>
            </div>

            <div class="form-group">
                <label>Reminder mejl</label>
                <p class="modal-subtitle">Obavezno: @{{name}}, @{{ amount }}, @{{ store_name }}, @{{ days_left }}</p>
                <textarea name="reminder_email_template" rows="6">{{ old('reminder_email_template', $reminderEmailTemplate) }}</textarea>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel-modal" data-close-modal="email-templates-modal">Otkaži</button>
                <button type="submit" class="btn-submit-modal">Sačuvaj</button>
            </div>
        </form>
    </div>
</div>

@vite(['resources/js/addBundle.js'])

</body>
</html>
