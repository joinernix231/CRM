<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Detalle del cliente — {{ $client->name }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 0;
            padding: 24px 28px;
        }
        .header {
            border-bottom: 2px solid #1e3a5f;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0 0 4px;
            font-size: 20px;
            color: #1e3a5f;
        }
        .header .meta {
            margin: 0;
            font-size: 10px;
            color: #64748b;
        }
        .section {
            margin-bottom: 22px;
        }
        .section h2 {
            margin: 0 0 10px;
            font-size: 13px;
            color: #1e3a5f;
            border-left: 4px solid #2563eb;
            padding-left: 8px;
        }
        table.info {
            width: 100%;
            border-collapse: collapse;
        }
        table.info th,
        table.info td {
            padding: 8px 10px;
            text-align: left;
            border: 1px solid #e2e8f0;
        }
        table.info th {
            width: 28%;
            background: #f8fafc;
            font-weight: 600;
            color: #475569;
        }
        table.contacts {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        table.contacts th {
            background: #1e3a5f;
            color: #fff;
            font-size: 10px;
            padding: 8px 10px;
            text-align: left;
        }
        table.contacts td {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        table.contacts tr:nth-child(even) td {
            background: #f8fafc;
        }
        .badge {
            display: inline-block;
            background: #dcfce7;
            color: #166534;
            font-size: 9px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
        }
        .empty {
            color: #64748b;
            font-style: italic;
            padding: 8px 0;
        }
        .footer {
            margin-top: 28px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            font-size: 9px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Detalle del cliente</h1>
        <p class="meta">Generado el {{ $generatedAt }}</p>
    </div>

    <div class="section">
        <h2>Información del cliente</h2>
        <table class="info">
            <tr>
                <th>Nombre</th>
                <td>{{ $client->name }}</td>
            </tr>
            <tr>
                <th>Empresa</th>
                <td>{{ $client->company }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $client->email }}</td>
            </tr>
            <tr>
                <th>Teléfono</th>
                <td>{{ $client->phone }}</td>
            </tr>
            <tr>
                <th>Estado</th>
                <td>{{ $statusLabel }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Contactos ({{ $client->contacts->count() }})</h2>

        @if ($client->contacts->isEmpty())
            <p class="empty">Este cliente no tiene contactos registrados.</p>
        @else
            <table class="contacts">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Cargo</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($client->contacts as $contact)
                        <tr>
                            <td>
                                {{ $contact->name }}
                                @if ($contact->is_primary)
                                    <span class="badge">Principal</span>
                                @endif
                            </td>
                            <td>{{ $contact->position }}</td>
                            <td>{{ $contact->email }}</td>
                            <td>{{ $contact->phone }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="footer">
        Mini CRM — Documento generado automáticamente
    </div>
</body>
</html>
