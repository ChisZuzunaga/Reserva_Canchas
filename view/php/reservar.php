<?php
require_once(__DIR__ . '/../../db/Database.php');
require_once(__DIR__ . '/../../model/modelogod.php'); // Ajusta la ruta según tu estructura de archivos
session_start();

date_default_timezone_set('America/Santiago');
if (!isset($_SESSION['session_email'])) {
    echo "No has iniciado sesión.";
    exit();
}

// Inicializar variables
$cancha_id = isset($_GET['cancha_id']) ? $_GET['cancha_id'] : '';
$fecha = isset($_POST['fecha']) ? $_POST['fecha'] : date('Y-m-d'); // Fecha predeterminada: hoy
$duracion = isset($_POST['duracion']) ? intval($_POST['duracion']) : 60; // Duración predeterminada
$database = new Database();

// Obtener las reservas para la fecha seleccionada
$horarios_ocupados = [];
if ($fecha && $cancha_id) {
    $model = new Clientes_Model($database);

    // Obtener reservas para la fecha seleccionada y la cancha seleccionada
    $reservas = $model->getReservasPorFecha($cancha_id, $fecha);

    // Procesar las reservas para visualizar horarios ocupados
    foreach ($reservas as $reserva) {
        $hora_inicio = strtotime($reserva['Hora_Inicio']);
        $hora_fin = strtotime($reserva['Hora_Fin']);
        while ($hora_inicio < $hora_fin) {
            $horarios_ocupados[date('H:i', $hora_inicio)] = true;
            $hora_inicio += 1800; // Avanzar en intervalos de 30 minutos
        }
    }
}

// Configuración de horarios
$jornadas = [
    'mañana' => [
        "07:00", "07:30", "08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30"
    ],
    'tarde' => [
        "12:00", "12:30", "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00", "17:30"
    ],
    'noche' => [
        "18:00", "18:30", "19:00", "19:30", "20:00", "20:30", "21:00", "21:30", "22:00"
    ]
];

$selected_hours = []; // Arreglo para las horas seleccionadas

// Obtener los días disponibles (hasta 10 días adicionales al día actual)
$fecha_actual = date('Y-m-d');
$offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0; // Para controlar las fechas a mostrar
// Obtener la hora actual y sumar una hora
$hora_actual = date('H:i');
$hora_limite = date('H:i', strtotime('+1 hour', strtotime($hora_actual)));
$max_offset = 15; // Límite de días que se pueden mostrar (10 días adicionales)
$fechas_disponibles = [];

// Limitar el avance para que no supere los 10 días desde el día actual
for ($i = 0; $i < 15; $i++) {
    $fecha_disponible = date('Y-m-d', strtotime("+" . ($offset + $i) . " days", strtotime($fecha_actual)));
    $fechas_disponibles[] = $fecha_disponible;
}

// Manejar la reserva si se envía una solicitud POST con hora_inicio
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hora_inicio'])) {
    $hora_inicio_reserva = $_POST['hora_inicio'];
    $cancha_id_reserva = $_POST['cancha_id'];
    $fecha_reserva = $_POST['fecha'];
    $duracion_reserva = $_POST['duracion'];

    // Redirigir al controlador para realizar la reserva
    header("Location: ../../controller/controlador.php?action=reservar&cancha_id={$cancha_id_reserva}&fecha={$fecha_reserva}&hora_inicio={$hora_inicio_reserva}&duracion={$duracion_reserva}");
    exit();
}
?>

<html>
<head>
    <title>Service Booking</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../css/reservar.css">
    <style>
        /* Estilos para el modal */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            text-align: center;
        }

        .close-btn {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-btn:hover,
        .close-btn:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div id="errorModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <p id="errorMessage">Hubo un problema al iniciar sesión. Por favor, intenta de nuevo.</p>
        </div>
    </div>

    <?php
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo',
            4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre',
            10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        // Crear el objeto DateTime
        $fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);

        // Obtener el día, mes y año
        $dia = $fechaObj->format('d');
        $mes = $meses[(int)$fechaObj->format('m')];
        $año = $fechaObj->format('Y');

        // Obtener el día de la semana
        $diasDeLaSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $diaSemana = $diasDeLaSemana[(int)$fechaObj->format('w')];

        // Formatear la fecha
        $fechaFormateada = "$diaSemana, $dia de $mes de $año";

    ?>

    <div class="container">
        <div class="left-panel">
            <div class="header">
                <a>Selecciona fecha y hora de tu servicio</a>
                <a href="../php/initial_page.php" class="back">Volver</a></div>
            <div class="tabs">
                <div class="tab active">Fecha y hora</div>
            </div>
            <p class="month"><?php echo $mes ?></p>
            <div class="calendar">
                <!-- Botón de retroceder -->
                <form action="" method="POST" style="display: inline;">
                    <input type="hidden" name="cancha_id" value="<?php echo htmlspecialchars($cancha_id); ?>">
                    <input type="hidden" name="offset" value="<?php echo $offset - 15; ?>">
                    <input type="hidden" name="duracion" value="<?php echo htmlspecialchars($duracion); ?>">
                    <button type="submit" class="arrow" <?php echo ($offset <= 0) ? 'disabled' : ''; ?>><</button>
                </form>
                <div class="days">
                    <?php foreach ($fechas_disponibles as $fecha_disponible): ?>


                        <?php
                            $meses = [
                                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo',
                                4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
                                7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre',
                                10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                            ];

                            // Crear el objeto DateTime
                            $fechaObj = DateTime::createFromFormat('Y-m-d', $fecha_disponible);

                            // Obtener el día y el día de la semana
                            $dia = $fechaObj->format('d'); // Número del día
                            $diasDeLaSemanas = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
                            $diaSemanas = $diasDeLaSemanas[(int)$fechaObj->format('w')]; // Día de la semana
                        ?>
                        <form action="" method="POST" style="display: inline;">
                            <input type="hidden" name="cancha_id" value="<?php echo htmlspecialchars($cancha_id); ?>">
                            <input type="hidden" name="fecha" value="<?php echo htmlspecialchars($fecha_disponible); ?>">
                            <input type="hidden" name="duracion" value="<?php echo htmlspecialchars($duracion); ?>">
                            <input type="hidden" name="offset" value="<?php echo htmlspecialchars($offset); ?>">
                            <div class="day <?php echo ($fecha_disponible == $fecha) ? 'active' : ''; ?>">
                                <button type="submit" class="fecha-boton">
                                    <span class="dia-semana"><?php echo htmlspecialchars($diaSemanas); ?></span>
                                    <span class="numero-dia"><?php echo htmlspecialchars($dia); ?></span>
                                </button>
                            </div>
                        </form>
                    <?php endforeach; ?>
                </div>
                <!-- Botón de avanzar -->
                <form action="" method="POST" style="display: inline;">
                    <input type="hidden" name="cancha_id" value="<?php echo htmlspecialchars($cancha_id); ?>">
                    <input type="hidden" name="offset" value="<?php echo $offset + 15; ?>">
                    <input type="hidden" name="duracion" value="<?php echo htmlspecialchars($duracion); ?>">
                    <button type="submit" class="arrow" <?php echo ($offset >= $max_offset) ? 'disabled' : ''; ?>>></button>
                </form>
            </div>

            <form id="formulario" action="" method="POST">
                <label for="duracion">Duración (minutos):</label>
                <select name="duracion" id="duracion" onchange="this.form.submit()">
                    <option value="60" <?php echo $duracion == 60 ? 'selected' : ''; ?>>60 minutos</option>
                    <option value="90" <?php echo $duracion == 90 ? 'selected' : ''; ?>>90 minutos</option>
                    <option value="120" <?php echo $duracion == 120 ? 'selected' : ''; ?>>120 minutos</option>
                </select>
                <input type="hidden" name="fecha" value="<?php echo htmlspecialchars($fecha); ?>"> <!-- Mantener la fecha seleccionada -->
                <input type="hidden" name="cancha_id" value="<?php echo htmlspecialchars($cancha_id); ?>"> <!-- También mantener el id de cancha -->
                <input type="hidden" name="offset" value="<?php echo $offset; ?>"> <!-- Offset para manejar las fechas -->
                <br><br>
            </form>
            <div class="time-slots">
                <?php foreach ($jornadas as $jornada => $horas): ?>
                    <div class="period"><?php echo ucfirst($jornada); ?></div>
                    <div class="bloque-horas">
                        <?php foreach ($horas as $hora): ?>
                            <?php
                            // Define los límites según la duración seleccionada
                            $horaLimite = match ($duracion) {
                                120 => "20:00", // Duración de 120 minutos permite reservar hasta las 20:00
                                90  => "20:30", // Duración de 90 minutos permite reservar hasta las 20:30
                                60  => "21:00", // Duración de 60 minutos permite reservar hasta las 21:00
                                default => "22:00" // Límite general
                            };

                            // Comprobar si la hora está ocupada o si ya pasó en el día actual
                            $horaBloque = strtotime($hora);
                            $horaActualBloque = strtotime($hora_actual);
                            $esHoy = ($fecha == date('Y-m-d'));
                            $horaPasada = ($esHoy && $horaBloque < $horaActualBloque);

                            if ($horaPasada || $hora > $horaLimite) {
                                continue; // Saltar esta hora si ya pasó o excede el límite permitido
                            }
                            ?>
                            <div class="hora <?php echo isset($horarios_ocupados[$hora]) ? 'ocupada' : ''; ?>">
                                <form action="" method="POST">
                                    <input type="hidden" name="hora_inicio" value="<?php echo htmlspecialchars($hora); ?>">
                                    <input type="hidden" name="cancha_id" value="<?php echo htmlspecialchars($cancha_id); ?>">
                                    <input type="hidden" name="fecha" value="<?php echo htmlspecialchars($fecha); ?>">
                                    <input type="hidden" name="duracion" value="<?php echo htmlspecialchars($duracion); ?>">
                                    <button type="button"
                                        class="hora-boton <?php echo isset($horarios_ocupados[$hora]) ? 'ocupado' : ''; ?>" 
                                        value="<?php echo htmlspecialchars($hora); ?>" 
                                        data-duracion="<?php echo $duracion; ?>" 
                                        onclick="resaltarHora(this)" 
                                        <?php echo isset($horarios_ocupados[$hora]) ? 'disabled' : ''; ?>>
                                        <?php echo htmlspecialchars($hora); ?>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>

            </div>


        </div>
        <div class="right-panel">
            <div class="header">Información de tus servicios</div>
            <div class="service-info">
                <div class="item">
                    <i class="fas fa-baseball-ball"></i>
                    Cancha <?php echo $cancha_id?>
                </div>
                <div class="item">
                    <i class="fas fa-dollar-sign"></i>
                    <span id="precio">$0</span>
                </div>
                <div class="item">
                    <i class="fas fa-calendar-alt"></i>
                    <?php echo htmlspecialchars($fechaFormateada); ?>
                </div>
                <div class="item">
                    <i class="fas fa-clock"></i>
                    <?php echo htmlspecialchars($duracion); ?> min
                </div>
            </div>
            <form id="reserva-form" action="../../controller/controlador.php?action=reservar" method="POST">
                <input type="hidden" name="fecha" value="<?php echo htmlspecialchars($fecha); ?>">
                <input type="hidden" name="cancha_id" value="<?php echo htmlspecialchars($cancha_id); ?>">
                <input type="hidden" name="duracion" value="<?php echo htmlspecialchars($duracion); ?>">
                <input type="hidden" name="hora_inicio" id="hora_inicio" value="">
                <input type="hidden" name="precio" id="precio_input" value="">
                <button type="submit" class="reservar" disabled id="boton_reservar">Reservar</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const error = urlParams.get('error');
            const cancha_id = urlParams.get('cancha_id');
            const modal = document.getElementById("errorModal");
            const errorMessage = document.getElementById("errorMessage");
            const closeModalButton = document.querySelector(".close-btn");

            // Mostrar el modal con el mensaje si hay un error
            if (error) {
                switch(error) {
                    case 'reservation_fail':
                        errorMessage.textContent = "Error: El horario seleccionado no está disponible.";
                        break;
                    default:
                        errorMessage.textContent = "Hubo un problema al procesar la reserva.";
                }
                modal.style.display = "block";
            }

            // Función para cerrar el modal y actualizar la URL
            const closeModal = () => {
                modal.style.display = "none";
                urlParams.delete('error'); // Eliminar solo el parámetro de error
                window.history.replaceState({}, document.title, `${window.location.pathname}?${urlParams}`);
            };

            // Cerrar el modal al hacer clic en la "X"
            closeModalButton.onclick = closeModal;

            // Cerrar el modal al hacer clic fuera de la ventana del modal
            window.onclick = function(event) {
                if (event.target == modal) {
                    closeModal();
                }
            }
        });

        function resaltarHora(boton) {
            const horas = document.querySelectorAll('.hora-boton');
            horas.forEach(hora => {
                hora.classList.remove('resaltado', 'fuente'); // Remueve las clases para restablecer el color original
            });

            // Resalta la hora seleccionada y las siguientes según la duración
            let duracion = parseInt(boton.getAttribute('data-duracion'));
            let inicio = boton.value;
            let tiempo = inicio.split(':');
            let horaInicio = new Date(0, 0, 0, parseInt(tiempo[0]), parseInt(tiempo[1]));

            for (let i = 0; i < duracion / 30; i++) {
                let horaResaltada = new Date(horaInicio.getTime() + (i * 30 * 60000)); // Incrementa 30 minutos
                let horaResaltadaStr = horaResaltada.toTimeString().substr(0, 5); // Formato HH:mm
                const botonResaltado = document.querySelector(`button[value='${horaResaltadaStr}']`);
                
                if (botonResaltado) {
                    botonResaltado.classList.add('resaltado', 'fuente'); // Agrega ambas clases
                }
            }
        }

        const horas = document.querySelectorAll('.hora-boton');
        horas.forEach(hora => {
            hora.addEventListener('click', function() {
                // Establecer el valor en el campo oculto
                document.getElementById('hora_inicio').value = this.value;

                // Activar el botón de reservar
                document.getElementById('boton_reservar').disabled = false;

                calcularPrecio();
            });
        });

        function calcularPrecio() {
            const fechaSeleccionada = new Date("<?php echo $fecha; ?>");
            const horaSeleccionada = (document.getElementById("hora_inicio").value); // Hora resaltada
            const [hora, minuto] = horaSeleccionada.split(':').map(Number);
            const diaSemana = fechaSeleccionada.getDay(); // 0 = Domingo, 1 = Lunes, ..., 6 = Sábado

            let durr = ("<?php echo htmlspecialchars($duracion); ?>")/60;
            let precio = 0;
            
            if (diaSemana >= 0 && diaSemana <= 4) { // Lunes a Viernes
                if (horaSeleccionada < "18:00") {
                    precio = (12000*durr); // $12.000 de 07:00 a 18:00
                } else {
                    precio = (16000*durr); // $16.000 después de las 18:00
                }
            } else { // Sábado y Domingo
                if (horaSeleccionada < "18:00") {
                    precio = (10000*durr); // $10.000 de 08:00 a 18:00
                } else {
                    precio = (12000*durr); // $12.000 después de las 18:00
                }
            }

            document.getElementById('precio_input').value = precio;
            document.getElementById('precio').innerText = `$${precio}`;
        }

        
    </script>
</body>
</html>