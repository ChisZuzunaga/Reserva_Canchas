<?php
require_once(__DIR__ . '/../../model/modelogod.php');
require_once(__DIR__ . '/../../db/Database.php');
require_once(__DIR__ . '/../../utils/decrypt.php');

// Obtener las credenciales descifradas
$credentials = getCredentials();
$adminEmail = $credentials['admin_email']; // Mover esta línea arriba
$database = new Database();
$reservas_canceladas_model = new Clientes_model($database); // Asegúrate de que este modelo esté disponible

// Obtener las reservas canceladas desde el modelo
$reservasCanceladas = $reservas_canceladas_model->getReservasCanceladas(); // Asegúrate de que este método exista


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

date_default_timezone_set('America/Santiago');



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

$fecha_anter = clone $fecha_actual;
$fecha_futur = clone $fecha_actual;
$fecha_futur->modify("+9 day");

// Array de traducción de meses abreviados
$meses_abreviados = [
    'Jan' => 'Ene',
    'Feb' => 'Feb',
    'Mar' => 'Mar',
    'Apr' => 'Abr',
    'May' => 'May',
    'Jun' => 'Jun',
    'Jul' => 'Jul',
    'Aug' => 'Ago',
    'Sep' => 'Sep',
    'Oct' => 'Oct',
    'Nov' => 'Nov',
    'Dec' => 'Dic'
];

// Obtén el mes abreviado y día de las fechas en inglés
$mes_dia_anter = $fecha_anter->format('M-d'); // 'M' para el mes abreviado, 'd' para el día
$mes_dia_futur = $fecha_futur->format('M-d');

// Calcular las fechas (del día actual a 10 días más)
$fechas = [];
for ($i = 0; $i <= 9; $i++) {
    $fechas[] = (clone $fecha_actual)->modify("+$i day")->format('Y-m-d');
}

// Obtener reservas para cada fecha seleccionada
$reservas_model = new Clientes_model($database); // Asegúrate de que este modelo esté disponible
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <link rel="stylesheet" href="../css/new_admin_page.css">
    <link rel="stylesheet" href="../css/admin_page.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <body>
    <div class="header">
        <div class="division">
            <div class="first-half">
                <img id="logoaqui" src="https://i.imgur.com/ywwk1E0.png">
                <h1>Ceres Padel Club</h1>
            </div>
            <div class="second-half">
                <div class="first-p">
                    <div class="perfil-header">
                        <div class="ff-h">
                            <h1><?php echo "Bienvenido " ,$nombre?></h1>
                        </div>
                        <div class="ss-h">
                            <img id="f-perfil" src="<?php echo $img?>">
                            <img id="monito" src="../uploads/fondod.png">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="main">
        <input type="checkbox" id="chk1" style="display:none;">
        <input type="checkbox" id="chk2" style="display:none;">

        <div class="parent">
            <span class="toggle-arrow" onclick="toggleSidebar()">&#9664;</span>
            <div class="div1">
                <div class="first-dd">
                    <div class="btn-1">
                        <div class="btn-25">
                            <span class="izq-can"></span>
                            <i class="fa fa-calendar-times-o" aria-hidden="true"></i>
                        </div>
                        <div class="btn-75" onclick="toggleDiv('chk1', 'chk2')">
                            <span class="btn-chk">Horas Canceladas</span>
                        </div>
                    </div>
                    <div class="btn-2">
                        <div class="btn-25">
                            <span class="izq-ver"></span>
                            <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                        </div>
                        <div class="btn-75" onclick="toggleDiv('chk2', 'chk1')">
                            <span class="btn-chk">Horas Reservadas</span>
                        </div>
                    </div>
                </div>
                <div class="second-dd">
                    <div class="btn-111">
                        <form action="../../controller/controlador.php?action=cerrar" method="post">
                            <button id="ekis" type="submit">Cerrar Sesión</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="div2">
                <div class="hrs-can">
                    <h1>Reservas Canceladas</h1>
                    <table>
                        <thead>
                            <tr>
                                <th>ID Reserva</th>
                                <th>Fecha</th>
                                <th>Hora de Inicio</th>
                                <th>Hora de Fin</th>
                                <th>Duración</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Numero</th>
                                <th>Estado</th>
                                <th>Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reservasCanceladas)): ?>
                                <tr>
                                    <td colspan="9">No hay reservas canceladas.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($reservasCanceladas as $cancelada): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($cancelada['ID_Reserva']) ?></td>
                                        <td><?= htmlspecialchars($cancelada['Fecha']) ?></td>
                                        <td><?= htmlspecialchars($cancelada['Hora_Inicio']) ?></td>
                                        <td><?= htmlspecialchars($cancelada['Hora_Fin']) ?></td>
                                        <td><?= htmlspecialchars($cancelada['Duracion']) ?> minutos</td>
                                        <td><?= htmlspecialchars($cancelada['Nombre']) ?></td>
                                        <td><?= htmlspecialchars($cancelada['Email']) ?></td>
                                        <td><?= htmlspecialchars($cancelada['Numero']) ?></td>
                                        <td><?= htmlspecialchars($cancelada['Estado']) ?></td>
                                        <td>$ <?= htmlspecialchars($cancelada['Precio']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>   
                </div>
                <div class="hrs-ver">
                    
                    <!-- Modal para editar reservas -->
                    <div id="modalReserva" class="modal">
                        <div class="modal-content">
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
                    <div class="header-container">
                        <div class="parent-admin">
                            <div class="contenedor">
                                <div class="divi-1">
                                    <div class="btns">
                                        <form method="GET" action="">
                                            <button class="cancha1" type="submit" name="cancha_id" value="1">
                                                Cancha 1
                                            </button>
                                            <button class="cancha1" type="submit" name="cancha_id" value="2">
                                                Cancha 2
                                            </button>
                                            <input type="hidden" name="fecha" value="<?= $fecha_actual->format('Y-m-d') ?>">
                                        </form>
                                    </div>
                                </div>
                                <div class="divi-2">
                                    <img id="foto-padel" src="../uploads/padel-icon.png">
                                    <span id="txt">Cancha <?= $cancha_id ?></span>
                                </div>
                                <div class="divi-3">
                                    <div class="divi-3-05">
                                        <h1><?php echo str_replace(array_keys($meses_abreviados), $meses_abreviados, $mes_dia_anter) . ' / ' . str_replace(array_keys($meses_abreviados), $meses_abreviados, $mes_dia_futur); ?></h1>
                                    </div>
                                    <div class="divi-3-05-02">
                                        <form method="get" action="">
                                            <input type="date" id="fecha" name="fecha" value="<?php echo $fecha_actual->format('Y-m-d'); ?>" onchange="this.form.submit()"></input>
                                        </form>
                                        <form method="GET" action="" style="display: inline;">
                                            <button class="retro" type="submit" name="retroceder" value="1">◀</button>
                                            <input type="hidden" name="cancha_id" value="<?= $cancha_id ?>">
                                            <input type="hidden" name="fecha" value="<?= $fecha_actual->format('Y-m-d') ?>">
                                        </form>

                                        <!-- Botón para avanzar 10 días -->
                                        <form method="GET" action="" style="display: inline;">
                                            <button class="avanza" type="submit" name="avanzar" value="1" >▶</button>
                                            <input type="hidden" name="cancha_id" value="<?= $cancha_id ?>">
                                            <input type="hidden" name="fecha" value="<?= $fecha_actual->format('Y-m-d') ?>">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                </div>
                </div>
            </div>
        </div>   
    </div>

    <script>
        function toggleSidebar() {
            const parentDiv = document.querySelector('.parent');
            parentDiv.classList.toggle('hidden');
            
            const arrow = document.querySelector('.toggle-arrow');
            arrow.innerHTML = arrow.innerHTML === '▶' ? '◀' : '▶';

            // Guardar el estado del sidebar en localStorage
            if (parentDiv.classList.contains('hidden')) {
                localStorage.setItem('sidebarState', 'hidden');
            } else {
                localStorage.setItem('sidebarState', 'visible');
            }
        }

        function toggleDiv(showCheckboxId, hideCheckboxId) {
            const showDiv = document.querySelector('.hrs-can');
            const hideDiv = document.querySelector('.hrs-ver');
            const showBtn = document.querySelector('.btn-1'); // Horas canceladas
            const hideBtn = document.querySelector('.btn-2'); // Horas globales
            const showIzq = document.querySelector('.izq-can');
            const hideIzq = document.querySelector('.izq-ver');

            if (showCheckboxId === 'chk1') {
                showDiv.style.display = 'block';
                hideDiv.style.display = 'none';
                showBtn.style.backgroundColor = '#D6D6D6';
                hideBtn.style.backgroundColor = '#FFFFFF';
                showIzq.style.backgroundColor = '#00D976';
                hideIzq.style.backgroundColor = '#FFFFFF';
                localStorage.setItem('selectedDiv', 'chk1');
            } else {
                showDiv.style.display = 'none';
                hideDiv.style.display = 'block';
                showBtn.style.backgroundColor = '#FFFFFF';
                hideBtn.style.backgroundColor = '#D6D6D6';
                showIzq.style.backgroundColor = '#FFFFFF';
                hideIzq.style.backgroundColor = '#00D976';
                localStorage.setItem('selectedDiv', 'chk2');
            }
        }

        window.onload = function() {
            const parentDiv = document.querySelector('.parent');

            // Quita la animación temporalmente al cargar la página
            document.body.classList.add('no-transition');
            
            const sidebarState = localStorage.getItem('sidebarState');
            if (sidebarState === 'hidden') {
                parentDiv.classList.add('hidden');
                document.querySelector('.toggle-arrow').innerHTML = '▶';
            } else {
                document.querySelector('.toggle-arrow').innerHTML = '◀';
            }

            // Restaurar el estado del contenido seleccionado
            const savedSelection = localStorage.getItem('selectedDiv');
            if (savedSelection === 'chk1') {
                toggleDiv('chk1', 'chk2');
            } else if (savedSelection === 'chk2') {
                toggleDiv('chk2', 'chk1');
            }

            // Reactiva la animación después de 100 ms
            setTimeout(() => document.body.classList.remove('no-transition'), 100);
        }

        // Función para abrir el modal
        function abrirModal(reservaJson) {
            
            const reserva = JSON.parse(reservaJson);
            const fechaReserva = new Date(reserva.Fecha); // Crear un objeto Date a partir de la fecha
            const diaDeLaSemana = fechaReserva.getDay();
            const diasNombres = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];
            const nombreDia = diasNombres[diaDeLaSemana];
            document.getElementById('infoReserva').innerHTML = `
                <div class="m-modal">
                    <div class="m-header">
                        <div class="m-izq">
                            <img id="calendar" src="../uploads/calendar.png">
                        </div>
                        <div class="m-der">
                            Detalles de reserva
                        </div>
                    </div>
                    <div class="m-cero">
                        Detalles de reserva
                    </div>
                    <div class="m-content">
                        <div class="m-izq">
                            <div class="m-uno">
                                Reservado Por:
                            </div>
                            <div class="m-dos">
                                Teléfono:
                            </div>
                            <div class="m-tres">
                                Fecha:
                            </div>
                            <div class="m-cuatro">
                                Día:
                            </div>
                            <div class="m-cinco">
                                Estado:
                            </div>
                            <div class="m-seis">
                                Hora de inicio:
                            </div>
                            <div class="m-siete">
                                Hora de termino:
                            </div>
                            <div class="m-ocho">
                                Duración:
                            </div>
                            <div class="m-nueve">
                                Precio:
                            </div>
                        </div>
                        <div class="m-der">
                            <div class="m-uno">
                                ${reserva.Nombre}
                            </div>
                            <div class="m-dos">
                                ${reserva.Numero}
                            </div>
                            <div class="m-tres">
                                ${nombreDia}
                            </div>
                            <div class="m-cuatro">
                                ${reserva.Fecha}
                            </div>
                            <div class="m-cinco">
                                ${reserva.Estado}
                            </div>
                            <div class="m-seis">
                                ${reserva.Hora_Inicio}
                            </div>
                            <div class="m-siete">
                                ${reserva.Hora_Fin}
                            </div>
                            <div class="m-ocho">
                                ${reserva.Duracion} minutos
                            </div>
                            <div class="m-nueve">
                                $${reserva.Precio}
                            </div>
                        </div>
                    </div>
                </div>
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
</body>
</html>
