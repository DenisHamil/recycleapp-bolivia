<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Canje de recompensa confirmado</title>
</head>
<body>
    <p>
        Hola 
        @if($user->donor_type === 'organization')
            {{ $user->organization_name }}
            @if($user->representative_name)
                (Responsable: {{ $user->representative_name }})
            @endif
        @else
            {{ $user->first_name }} {{ $user->last_name }}
        @endif,
    </p>

    <p>¡Tu canje ha sido confirmado con éxito! 🎉</p>

    <p>Has canjeado la siguiente recompensa:</p>
    <ul>
        <li><strong>Nombre:</strong> {{ $reward->name }}</li>
        <li><strong>Puntos usados:</strong> {{ $reward->points_required }}</li>
        <li><strong>Fecha de canje:</strong> {{ $userReward->redeemed_at->format('d/m/Y H:i') }}</li>
    </ul>

    <p>Tu recompensa será entregada en los próximos días. Te avisaremos cuando esté en camino.</p>

    <p>Gracias por ser parte de <strong>RecycleApp Bolivia ♻️</strong></p>
</body>
</html>
