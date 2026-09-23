<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $bundle->name }}</title>
    @vite(['resources/css/store.css'])
</head>

@php
    $modalToOpenOnError = '';

    if ($errors->has('csv_file')) {
        $modalToOpenOnError = 'import-csv-modal';
    } elseif ($errors->any()) {
        $modalToOpenOnError = 'coupon-modal';
    }
@endphp

<body data-open-modal-on-load="{{ $modalToOpenOnError }}">

<div class="content">
    @if ($errors->any())
        <div class="errors">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <a href="{{ route('store.show', $bundle->store) }}" class="back-link">&larr; Prodavnica</a>

    <div class="header">
        <div class="header-info">
            <h1>{{ $bundle->name }}</h1>
            <div class="total-value">Ukupna vrednost : <strong>${{ number_format($bundle->getTotalValue(), 2) }}</strong></div>
        </div>
        <div class="header-actions">
            <button type="button" class="btn-secondary" data-open-modal="import-csv-modal">Import from CSV</button>
            <button type="button" class="btn-add" data-open-modal="coupon-modal">Dodaj Kupon</button>
            <form method="POST" action="{{ route('bundle.resend-all', $bundle) }}" data-confirm="Poslati mejlove svim kuponima u ovom bundle-u?" style="display:inline;">
                @csrf
                <button type="submit" class="btn-secondary">Resend All</button>
            </form>
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th>Code</th>
            <th>Receiver</th>
            <th>Email</th>
            <th>Amount</th>
            <th>Send Date</th>
            <th>Send Immediately</th>
            <th>Status</th>
            <th>Initial Sent</th>
            <th>Reminder Sent</th>
            <th>Expires At</th>
            <th>Subscribed</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($coupons as $coupon)
            <tr>
                <td>{{ $coupon->code }}</td>
                <td>{{ $coupon->receiver_name }}</td>
                <td>{{ $coupon->receiver_email }}</td>
                <td>${{ number_format($coupon->discount_amount, 2) }}</td>
                <td>{{ $coupon->send_date ? $coupon->send_date->format('m/d/Y') : 'Not set' }}</td>
                <td>{{ $coupon->send_immediately }}</td>
                <td>
                    @if($coupon->is_expired)
                        <span class="badge-expired">Istekao</span>
                    @elseif($coupon->is_used)
                        <span class="badge-used">Iskoriscen</span>
                    @else
                        <span class="badge-unused">Neiskoriscen</span>
                    @endif
                </td>
                <td>{{ $coupon->email_sent_at ? $coupon->email_sent_at->format('m/d/Y') : '/' }}</td>
                <td>{{ $coupon->last_sent_at ? $coupon->last_sent_at->format('m/d/Y') : '/' }}</td>
                <td>{{ $coupon->expires_at ? $coupon->expires_at->format('m/d/Y') : '/' }}</td>
                <td>
                    @if ($coupon->subscribed)
                        <span class="badge-yes">Da</span>
                    @else
                        <span class="badge-no">Ne</span>
                    @endif
                </td>
                <td>
                    <div class="dropdown">
                        <button type="button" class="action-link" data-toggle-dropdown>Actions &#9662;</button>
                        <div class="dropdown-menu">
                            <a href="{{ route('coupon.edit', $coupon) }}" class="dropdown-item">Edit</a>

                            <form method="POST" action="{{ route('coupon.toggle-used', $coupon) }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    {{ $coupon->is_used ? 'Označi kao neiskorišćen' : 'Označi kao iskorišćen' }}
                                </button>
                            </form>

                            @if ($coupon->receiver_email && !$coupon->is_used && !$coupon->is_expired)
                                <form method="POST" action="{{ route('coupon.resend-initial', $coupon) }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Resend Initial</button>
                                </form>

                                <form method="POST" action="{{ route('coupon.resend-reminder', $coupon) }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Resend Reminder</button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('coupon.destroy', $coupon) }}" data-confirm="Obrisi kupon?">
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
                <td colspan="10">Ovaj bundle nema kupone</td>
            </tr>
        @endforelse
        </tbody>

    </table>
</div>

<div class="modal-overlay" id="coupon-modal">
    <div class="modal-box">
        <h2>Novi kupon</h2>

        @if ($errors->any())
            <div class="errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('coupon.store', $bundle) }}">
            @csrf

            <div class="form-group">
                <label>Ime primaoca</label>
                <input type="text" name="receiver_name" required>
            </div>

            <div class="form-group">
                <label>Email primaoca</label>
                <input type="email" name="receiver_email">
            </div>

            <div class="form-group">
                <label>Iznos ($)</label>
                <input type="number" step="0.01" min="0" name="discount_amount" required>
            </div>

            <div class="form-group">
                <label>Datum slanja (opciono)</label>
                <input type="date" name="send_date">
            </div>

            <div class="form-group">
                <label>Posalji Odmah</label>
                <input type="checkbox" name="send_immediately" value="1">
            </div>

            <div class="form-group">
                <label>Datum isteka (koristi bundle-ov bez unosa)</label>
                <input type="date" name="expires_at">
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel-modal" data-close-modal="coupon-modal">Otkazi</button>
                <button type="submit" class="btn-submit-modal">Sacuvaj</button>
            </div>
        </form>
    </div>
</div>



{{----------------------MODAL ZA IMPORT CSV-----------------------}}

<div class="modal-overlay" id="import-csv-modal">
    <div class="modal-box">
        <h2>Import kupona iz CSV-a</h2>
        <p class="modal-subtitle">Kolone: Bundle Name, Code, Receiver Name, Receiver Email, Amount, Send Date, Status, Created At. Prvi red (header) se preskace.</p>

        <form method="POST" action="{{ route('coupon.import', $bundle) }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label>CSV fajl</label>
                <input type="file" name="csv_file" accept=".csv,.txt" required>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel-modal" data-close-modal="import-csv-modal">Otkaži</button>
                <button type="submit" class="btn-submit-modal">Uvezi</button>
            </div>
        </form>
    </div>
</div>

@vite(['resources/js/addBundle.js'])

</body>
</html>
