<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>SuperAdmin Dashboard</title>
    @vite(['resources/css/admin-panel.css', 'resources/css/store.css'])
</head>
<body>
<div class="page-wrapper">

    <a href="{{ route('dashboard') }}" class="back-link">&larr; Nazad na dashboard</a>

    <div class="page-header">
        <h1>Lista svih admina</h1>
        <span class="count-badge">{{ count($admins) }} admina</span>
    </div>

    <table>
        <thead>
        <tr>
            <th>Ime</th>
            <th>Email</th>
            <th>Datum registracije</th>
            <th>Akcije</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($admins as $admin)
            <tr>
                <td>{{ $admin->name }}</td>
                <td>{{ $admin->email }}</td>
                <td>{{ $admin->created_at->format('m/d/Y') }}</td>
                <td>
                    <button type="button" class="btn-secondary" data-open-modal="edit-user-modal-{{ $admin->id }}">Izmeni</button>

                    <form method="POST" action="{{ route('admins.destroy', $admin) }}" data-confirm="Obrisi admina?" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-link-danger">Obrisi</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr class="empty-row">
                <td colspan="4">Nema admina u sistemu</td>
            </tr>
        @endforelse
        </tbody>
    </table>

</div>

{{-- Modali su OVDE, van tabele, ali i dalje po jedan za svakog admina --}}
@foreach ($admins as $admin)
    <div class="modal-overlay" id="edit-user-modal-{{ $admin->id }}">
        <div class="modal-box">
            <h2>Izmeni admina</h2>

            <form method="POST" action="{{ route('admins.update', $admin) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Ime</label>
                    <input type="text" name="name" value="{{ old('name', $admin->name) }}" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email', $admin->email) }}" required>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel-modal" data-close-modal="edit-user-modal-{{ $admin->id }}">Otkaži</button>
                    <button type="submit" class="btn-submit-modal">Sačuvaj izmene</button>
                </div>
            </form>
        </div>
    </div>
@endforeach

@vite(['resources/js/addBundle.js'])

</body>
</html>
