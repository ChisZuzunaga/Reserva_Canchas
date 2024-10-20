<!-- views/reservas_canceladas.php -->
<?php
require_once(__DIR__ . '/../../modelo/modelogod.php');

$reservas_canceladas_model = new Clientes_model(); // Asegúrate de que este modelo esté disponible

// Obtener las reservas canceladas desde el modelo
$reservasCanceladas = $reservas_canceladas_model->getReservasCanceladas(); // Asegúrate de que este método exista

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas Canceladas</title>
    <link rel="stylesheet" href="path/to/your/css/styles.css"> <!-- Asegúrate de que la ruta sea correcta -->
</head>
<body>
    <h1>Reservas Canceladas</h1>

    <table>
        <thead>
            <tr>
                <th>ID Reserva</th>
                <th>Fecha</th>
                <th>Hora de Inicio</th>
                <th>Hora de Fin</th>
                <th>Duración</th>
                <th>Email</th>
                <th>Estado</th>
                <th>Precio</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reservasCanceladas)): ?>
                <tr>
                    <td colspan="7">No hay reservas canceladas.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($reservasCanceladas as $cancelada): ?>
                    <tr>
                        <td><?= htmlspecialchars($cancelada['ID_Reserva']) ?></td>
                        <td><?= htmlspecialchars($cancelada['Fecha']) ?></td>
                        <td><?= htmlspecialchars($cancelada['Hora_Inicio']) ?></td>
                        <td><?= htmlspecialchars($cancelada['Hora_Fin']) ?></td>
                        <td><?= htmlspecialchars($cancelada['Duracion']) ?> minutos</td>
                        <td><?= htmlspecialchars($cancelada['Email']) ?></td>
                        <td><?= htmlspecialchars($cancelada['Estado']) ?></td>
                        <td>$ <?= htmlspecialchars($cancelada['Precio']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
