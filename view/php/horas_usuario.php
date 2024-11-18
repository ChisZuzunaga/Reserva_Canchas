<!-- views/reservas_canceladas.php -->
<?php
require_once(__DIR__ . '/../../db/Database.php');
require_once(__DIR__ . '/../../model/modelogod.php');

session_start();
if (isset($_SESSION['session_email'])) {
    $email = $_SESSION['session_email'];
    $nombre = $_SESSION['session_nombre'];
    $img = $_SESSION['ruta_imagen']; // Obtener el nombre almacenado en la sesión
} else {
    echo "No has iniciado sesión.";
    // Puedes redirigir al usuario a la página de inicio de sesión si no está autenticado
    exit();
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
    <link rel="stylesheet" href="../../view/css/estilo.css">
    <title>Horas Reservadas</title>
    <style>
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgb(0,0,0); 
            background-color: rgba(0,0,0,0.4); 
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 80%; 
        }

        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .disabled-button {
            cursor: not-allowed;
            background-color: #ccc;
            color: #666;
            border: 1px solid #aaa;
        }
    </style>
</head>
<body>
    <div id="successModal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Cancelación Exitosa</h2>
            <p>La hora ha sido cancelada exitosamente.</p>
        </div>
    </div>

    <a href="../php/initial_page.php">Volver</a>
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
            <th>Acción</th>
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
                        <?php if ($reserva['Estado'] !== 'confirmada'): ?>
                            <form action="../../controller/controlador.php?action=cancelar_reserva_usu" method="post" onsubmit="return mostrarModal(event)">
                                <input type="hidden" name="id_reserva" value="<?php echo $reserva['ID_Reserva']; ?>">
                                <button type="submit">Cancelar</button>
                            </form>
                        <?php else: ?>
                            <button class="disabled-button" disabled>No Disponible</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
    function mostrarModal(event) {
        event.preventDefault(); // Evita que el formulario se envíe inmediatamente
        var form = event.target; // Obtiene el formulario que disparó el evento
        var modal = document.getElementById("successModal");
        modal.style.display = "block";

        // Esperar 1 segundo y enviar el formulario
        setTimeout(function() {
            modal.style.display = "none"; // Cierra el modal
            form.submit(); // Envía el formulario después de mostrar el modal
        }, 1000); // 1000 ms = 1 segundo

        // Manejo de cerrar el modal
        var span = document.getElementsByClassName("close-button")[0];
        span.onclick = function() {
            modal.style.display = "none";
        };

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        };
    }
</script>
</body>
</html>
