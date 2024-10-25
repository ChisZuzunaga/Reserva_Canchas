<!-- views/reservas_canceladas.php -->
<?php
require_once(__DIR__ . '/../../modelo/modelogod.php');

if (isset($_SESSION['session_email'])) {
    $email = $_SESSION['session_email'];
    $nombre = $_SESSION['session_nombre'];
    $img = $_SESSION['ruta_imagen']; // Obtener el nombre almacenado en la sesión
}

$reservas_usuario_model = new Clientes_model(); // Asegúrate de que este modelo esté disponible

$reservasUsuario = $reservas_usuario_model->getReservasUsuario($email); // Asegúrate de que este método exista


?>


<!DOCTYPE html>
<html lang="es">
<head>
    <title>Mis Reservas</title>
    <link rel="icon" href="../uploads/icono.png" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/estilo.css">
    <title>Horas Reservadas</title>
</head>
<body>
    <h1>Horas reservadas por <?php echo $_SESSION['session_nombre']; ?></h1>
    <table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Hora Inicio</th>
            <th>Hora Fin</th>
            <th>Duración</th>
            <th>Cancha</th>
            <th>Precio</th>
            <th>Estado</th>
            <th>Acción</th> <!-- Nueva columna para acciones -->
        </tr>
    </thead>
    <tbody>
        <?php foreach ($reservasUsuario as $reserva): ?>
        <tr>
            <!--<td><?php echo $reserva['ID_Reserva']; ?></td>!-->
            <td><?php echo $reserva['Fecha']; ?></td>
            <td><?php echo $reserva['Hora_Inicio']; ?></td>
            <td><?php echo $reserva['Hora_Fin']; ?></td>
            <td><?php echo $reserva['Duracion']; ?></td>
            <td><?php echo $reserva['ID_Cancha']; ?></td>
            <td>$<?php echo $reserva['Precio']; ?></td>
            <td><?php echo $reserva['Estado']; ?></td>
            <td>
                <?php if ($reserva['Estado'] != 'cancelada'): ?>
                    <!-- Formulario para cancelar la reserva -->
                    <form action="../controlador/controlador.php?action=cancelar_reserva" method="post">
                        <input type="hidden" name="id_reserva" value="<?php echo $reserva['ID_Reserva']; ?>">
                        <button type="submit">Cancelar</button>
                    </form>
                <?php else: ?>
                    <!-- Si la reserva ya está cancelada, mostrar texto "Cancelada" -->
                    <span></span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>

