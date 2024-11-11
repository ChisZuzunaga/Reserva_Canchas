<!-- views/reservas_canceladas.php -->
<?php
require_once(__DIR__ . '/../../db/Database.php');
require_once(__DIR__ . '/../../model/modelogod.php');

if (isset($_SESSION['session_email'])) {
    $email = $_SESSION['session_email'];
    $nombre = $_SESSION['session_nombre'];
    $img = $_SESSION['ruta_imagen']; // Obtener el nombre almacenado en la sesión
}


$database = new Database();
$reservas_usuario_model = new Clientes_model($database); // Asegúrate de que este modelo esté disponible

$reservasUsuario = $reservas_usuario_model->getReservasUsuario($email); // Asegúrate de que este método exista


?>


<!DOCTYPE html>
<html lang="es">
<head>
    <title>Mis Reservas</title>
    <link rel="icon" href="../uploads/icono.png" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../view/css/estilo.css">
    <title>Horas Reservadas</title>
</head>
<body>
    <a href="../view/php/initial_page.php">Volver</a>
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
            <?php 
            $row_class = '';
            if ($reserva['Estado'] === 'cancelada') {
                $row_class = 'cancelada';
            } elseif ($reserva['Estado'] === 'confirmada') {
                $row_class = 'confirmada';
            }
            
            // Solo mostrar filas que no estén canceladas
            if ($reserva['Estado'] != 'cancelada'):
            ?>
                <tr class="<?php echo $row_class; ?>">
                    <td><?php echo $reserva['Fecha']; ?></td>
                    <td><?php echo $reserva['Hora_Inicio']; ?></td>
                    <td><?php echo $reserva['Hora_Fin']; ?></td>
                    <td><?php echo $reserva['Duracion']; ?></td>
                    <td><?php echo $reserva['ID_Cancha']; ?></td>
                    <td>$<?php echo $reserva['Precio']; ?></td>
                    <td><?php echo $reserva['Estado']; ?></td>
                    <td>
                        <form action="../controller/controlador.php?action=cancelar_reserva" method="post">
                            <input type="hidden" name="id_reserva" value="<?php echo $reserva['ID_Reserva']; ?>">
                            <button type="submit">Cancelar</button>
                        </form>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>

