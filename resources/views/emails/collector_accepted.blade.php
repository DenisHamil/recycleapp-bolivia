<html>
<head>
    <title>¡Tu cuenta ha sido aprobada!</title>
</head>
<body>
    <h2>Hola {{ $collector->representative_name }},</h2>

    <p>¡Felicidades! Tu cuenta como empresa recolectora ha sido aprobada. Ahora puedes comenzar a recibir donaciones y gestionar tu actividad.</p>

    <p>Detalles de tu cuenta:</p>
    <ul>
        <li><strong>Nombre de la empresa:</strong> {{ $collector->company_name }}</li>
        <li><strong>Responsable:</strong> {{ $collector->representative_name }}</li>
        <li><strong>Email:</strong> {{ $collector->email }}</li>
    </ul>

    <p>¡Gracias por formar parte de nuestro sistema de reciclaje!</p>

    <p>Atentamente,</p>
    <p>El equipo de RecycleApp</p>
</body>
</html>
