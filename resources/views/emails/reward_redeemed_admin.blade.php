<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo canje de recompensa</title>
</head>
<body>
    <p><strong>Se ha realizado un nuevo canje de recompensa.</strong></p>

    <p>Detalles del canje:</p>
    <ul>
        <li>
            <strong>Donador:</strong>
            @if($user->donor_type === 'organization')
                {{ $user->organization_name }}
                @if($user->representative_name)
                    - Responsable: {{ $user->representative_name }}
                @endif
                ({{ $user->email }})
            @else
                {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
            @endif
        </li>
        <li><strong>Recompensa:</strong> {{ $reward->name }}</li>
        <li><strong>Puntos usados:</strong> {{ $reward->points_required }}</li>
        <li><strong>Fecha:</strong> {{ $userReward->redeemed_at->format('d/m/Y H:i') }}</li>
    </ul>


    <p><strong>RecycleApp Bolivia</strong></p>
</body>
</html>
