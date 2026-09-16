<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>SuperAdmin Dashbard</title>
    @vite(['resources/css/admin-panel.css'])
</head>
<body>
<div class="page-wrapper">

    <div>
        <a href="{{ route('dashboard') }}" class="back-link">Nazad na dashboard -></a>

        <div class="page-header">
            <h1>Lista Svih Admina</h1>
            <span class="count-badge">{{ count($admins) }} admina</span>
        </div>

        <table>
            <thead>
            <tr>
                <th>Ime</th>
                <th>Email</th>
                <th>Datum Registracije</th>
            </tr>
            </thead>

            <tbody>
            @if(count($admins) == 0 )
                <tr class="empty-row">
                    <td colspan="4">Nema Admina u sistemu</td>
                </tr>
            @endif
            @foreach($admins as $admin)
                <tr>
                    <td>{{ $admin->name }}</td>
                    <td>{{ $admin->email }}</td>
                    <td>{{ $admin->created_at->format('m/d/Y') }}</td>
                    <td>
                        <form method="POST" action="{{ route('admins.destroy', $admin) }}" onsubmit="return confirm('Obrisi  admina?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-link-danger">Obrisi</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
