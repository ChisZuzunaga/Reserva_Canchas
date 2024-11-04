<?php
require_once(__DIR__ . '/../db/Database.php');
require_once(__DIR__ . '/../modelo/modelogod.php');

session_start();
date_default_timezone_set('America/Santiago');

$adminEmail = 'prueba@a';
$database = new Database();

if (isset($_SESSION['session_email'])) {
    $email = $_SESSION['session_email'];
    $nombre = $_SESSION['session_nombre'];
    $img = $_SESSION['ruta_imagen']; // Obtener el nombre almacenado en la sesión

    if ($email !== $adminEmail) {
        echo "Acceso denegado. No tienes permisos para acceder a esta página.";
        // Puedes redirigir al usuario a otra página, como la página de inicio:
        header("Location: ../php/initial_page.php");
        exit();
    }

} else {
    echo "No has iniciado sesión.";
    // Puedes redirigir al usuario a la página de inicio de sesión si no está autenticado
    exit();
}

// Definir todas las horas desde las 07:00 hasta las 22:00
$horas = [
    "07:00:00", "07:30:00", "08:00:00", "08:30:00", "09:00:00", "09:30:00", "10:00:00", "10:30:00", "11:00:00", "11:30:00",
    "12:00:00", "12:30:00", "13:00:00", "13:30:00", "14:00:00", "14:30:00", "15:00:00", "15:30:00", "16:00:00", "16:30:00",
    "17:00:00", "17:30:00", "18:00:00", "18:30:00", "19:00:00", "19:30:00", "20:00:00", "20:30:00", "21:00:00", "21:30:00", "22:00:00", "22:30:00", "23:00:00"
];

// Manejo de la selección de cancha
$cancha_id = isset($_GET['cancha_id']) ? $_GET['cancha_id'] : 1; 

// Manejo de la fecha actual
$fecha_actual = isset($_GET['fecha']) ? new DateTime($_GET['fecha']) : new DateTime(); 

// Avanzar o retroceder días
if (isset($_GET['avanzar'])) {
    $fecha_actual->modify('+10 day');
} elseif (isset($_GET['retroceder'])) {
    $fecha_actual->modify('-10 day');
}

// Calcular las fechas (del día actual a 10 días más)
$fechas = [];
for ($i = 0; $i <= 9; $i++) {
    $fechas[] = (clone $fecha_actual)->modify("+$i day")->format('Y-m-d');
}

// Obtener reservas para cada fecha seleccionada
$reservas_model = new Clientes_model(); // Asegúrate de que este modelo esté disponible
$reservas = [];

foreach ($fechas as $fecha) {
    $reservas[$fecha] = $reservas_model->getReservasPorFecha($cancha_id, $fecha);
}

// Crear un array de reservas para acceder fácilmente por hora
$reservas_por_hora = [];
foreach ($reservas as $fecha => $lista_reservas) {
    foreach ($lista_reservas as $reserva) {
        $hora_inicio = strtotime($reserva['Hora_Inicio']);
        $hora_fin = strtotime($reserva['Hora_Fin']);

        // Iteramos desde la hora de inicio hasta la hora de fin en intervalos de 30 minutos
        while ($hora_inicio < $hora_fin) {
            $hora_actual = date('H:i:s', $hora_inicio);
            $reservas_por_hora[$fecha][$hora_actual] = $reserva;
            // Avanzar 30 minutos
            $hora_inicio = strtotime('+30 minutes', $hora_inicio);
        }
    }
}

// Función para calcular rowspan para reservas consecutivas
function calcularRowspan($reservas, $horas) {
    $rowspan_data = [];
    
    foreach ($reservas as $fecha => $horas_reservadas) {
        $rowspan_data[$fecha] = [];
        $ultima_reserva = null;
        $contador = 1;
        
        for ($i = 0; $i < count($horas); $i++) {
            $hora_actual = $horas[$i];
            
            if (isset($horas_reservadas[$hora_actual])) {
                $reserva_actual = $horas_reservadas[$hora_actual];
                
                if ($ultima_reserva && $ultima_reserva['Numero'] == $reserva_actual['Numero']) {
                    // Incrementamos el contador si la reserva es la misma que la anterior
                    $contador++;
                } else {
                    // Guardamos el rowspan de la última reserva
                    if ($ultima_reserva) {
                        $rowspan_data[$fecha][$horas[$i - $contador]] = $contador;
                    }
                    // Reiniciamos el contador
                    $contador = 1;
                }
                $ultima_reserva = $reserva_actual;
            } else {
                // Guardamos el rowspan si cambiamos a "disponible"
                if ($ultima_reserva) {
                    $rowspan_data[$fecha][$horas[$i - $contador]] = $contador;
                    $ultima_reserva = null;
                }
            }
        }
        // Guardamos el último rowspan si llegamos al final
        if ($ultima_reserva) {
            $rowspan_data[$fecha][$horas[count($horas) - $contador]] = $contador;
        }
    }
    return $rowspan_data;
}

// Calcular rowspan para las reservas
$rowspan_data = calcularRowspan($reservas_por_hora, $horas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Reservas</title>
    <link rel="icon" href="../uploads/icono.png" type="image/x-icon">
    <style>
        .reservado {
            background-color: #ffcccc; /* Rojo para reservado */
            height: 100%;
            justify-content: center;
            align-items: center;
            display: flex;
            cursor: pointer;
        }

        .confirmada {
            background-color: #ccffcc; /* Verde para confirmada */
            height: 100%;
            justify-content: center;
            align-items: center;
            display: flex;
            cursor: pointer;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }
        
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
            height: 30px;
        }

        /* Estilos para el modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            max-width: 500px;
            margin: auto;
        }

        .modal-buttons {
            display: flex;
            justify-content: flex-end;
        }

        .modal-buttons button {
            margin-left: 10px;
        }

        #conf{
            background-color: green;
            cursor: pointer;
        }

        #canc{
            background-color: red;
            cursor: pointer;
        }
    </style>
</head>
<body>

<!-- Modal para editar reservas -->
<div id="modalReserva" class="modal">
    <div class="modal-content">
        <h3>Detalles de la reserva</h3>
        <p id="infoReserva"></p>
        
        <div class="modal-buttons">
            <form id="formCancelarReserva" action="../../controller/controlador.php?action=cancelar_reserva" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_reserva" id="idReservaCancelar">
                <button type="submit" id="canc">Cancelar</button>
            </form>
            <form id="formConfirmarReserva" action="../../controller/controlador.php?action=confirmar_reserva" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_reserva" id="idReservaConfirmar">
                <button type="submit" id="conf">Confirmar</button>
            </form>
        </div>
    </div>
</div>

<!-- Selector de Cancha -->
<form method="GET" action="">
    <label for="cancha_id">Seleccione una cancha:</label>
    <select name="cancha_id" id="cancha_id" onchange="this.form.submit()">
        <option value="1" <?= ($cancha_id == 1) ? 'selected' : '' ?>>Cancha 1</option>
        <option value="2" <?= ($cancha_id == 2) ? 'selected' : '' ?>>Cancha 2</option>
    </select>
    <input type="hidden" name="fecha" value="<?= $fecha_actual->format('Y-m-d') ?>">
</form>

<!-- Botones de avance/retroceso de días -->
<a href="?cancha_id=<?= $cancha_id ?>&fecha=<?= $fecha_actual->format('Y-m-d') ?>&retroceder=1">Anterior</a>
<a href="?cancha_id=<?= $cancha_id ?>&fecha=<?= $fecha_actual->format('Y-m-d') ?>&avanzar=1">Siguiente</a>

<!-- Contenedor para la tabla de reservas -->
<div id="reservas-container">
    <table>
        <thead>
            <tr>
                <th>Hora / Fecha</th>
                <?php foreach ($fechas as $fecha): ?>
                    <th><?= $fecha ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($horas as $hora): ?>
                <tr>
                    <td><?= $hora ?></td>
                    <?php foreach ($fechas as $fecha): ?>
                        <?php if (isset($rowspan_data[$fecha][$hora])): ?>
                            <td rowspan="<?= $rowspan_data[$fecha][$hora] ?>">
                                <?php 
                                if (isset($reservas_por_hora[$fecha][$hora])) {
                                    $reserva = $reservas_por_hora[$fecha][$hora];
                                    
                                    // Determinar el color de fondo basado en el estado
                                    $colorClass = '';
                                    if ($reserva['Estado'] === 'reservado') {
                                        $colorClass = 'reservado';
                                    } elseif ($reserva['Estado'] === 'confirmada') {
                                        $colorClass = 'confirmada';
                                    }
                                    ?>
                                    <div class="<?= $colorClass ?>" onclick="abrirModal('<?= htmlspecialchars(json_encode($reserva)) ?>')">
                                        Reservado por: <?= htmlspecialchars($reserva['Nombre']) ?><br>
                                        Teléfono: <?= htmlspecialchars($reserva['Numero']) ?><br>
                                        Estado: <?= htmlspecialchars($reserva['Estado']) ?>
                                    </div>
                                    <?php
                                } else {
                                    echo 'Disponible';
                                }
                                ?>
                            </td>
                        <?php elseif (!isset($reservas_por_hora[$fecha][$hora])): ?>
                            <td>Disponible</td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<form action="../../controller/controlador.php?action=mostrar_horas_canceladas" method="POST" enctype="multipart/form-data">
    <button>Mostrar canceladas</button>
</form>


<script>
    // Función para abrir el modal
    function abrirModal(reservaJson) {
        
        const reserva = JSON.parse(reservaJson);
        const fechaReserva = new Date(reserva.Fecha); // Crear un objeto Date a partir de la fecha
        const diaDeLaSemana = fechaReserva.getDay();
        const diasNombres = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];
        const nombreDia = diasNombres[diaDeLaSemana];
        document.getElementById('infoReserva').innerHTML = `
            <strong>Fecha:</strong> ${reserva.Fecha}<br>
            <strong>Día:</strong> ${nombreDia}<br>
            <strong>Reservado por:</strong> ${reserva.Nombre}<br>
            <strong>Teléfono:</strong> ${reserva.Numero}<br>
            <strong>Estado:</strong> ${reserva.Estado}<br>
            <strong>Hora de Inicio:</strong> ${reserva.Hora_Inicio}<br>
            <strong>Hora de Fin:</strong> ${reserva.Hora_Fin}<br>
            <strong>Duración:</strong> ${reserva.Duracion} minutos<br>
            <strong>Precio:</strong>$ ${reserva.Precio}<br>
        `;
        document.getElementById('idReservaCancelar').value = reserva.ID_Reserva; // Asignar ID a cancelar
        document.getElementById('idReservaConfirmar').value = reserva.ID_Reserva; // Asignar ID a confirmar
        document.getElementById('modalReserva').style.display = 'flex';
    }

    // Función para cerrar el modal
    window.onclick = function(event) {
        const modal = document.getElementById('modalReserva');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };

    // Función para manejar la cancelación de una reserva
    document.getElementById('cancelarReserva').onclick = function() {
        // Lógica para cambiar el estado a 'Cancelada'
        alert('Reserva cancelada');
        document.getElementById('modalReserva').style.display = 'none';
    };

    // Función para confirmar una reserva
    document.getElementById('confirmarReserva').onclick = function() {
        // Lógica para actualizar el estado a 'Confirmada'
        alert('Reserva confirmada');
        document.getElementById('modalReserva').style.display = 'none';
    };

    // Función para editar una reserva
    document.getElementById('editarReserva').onclick = function() {
        // Lógica para abrir otro modal o mostrar campos de edición
        alert('Editando reserva');
    };

</script>
<script>
    setTimeout(function() {
        location.reload(); // Recarga la página
    }, 60000); // 60000 ms = 60 segundos
</script>
</body>
</html>
