<!-- resumen_reserva.php -->
<?php
session_start();

// Verifica si la sesión contiene los datos de la reserva
if (isset($_SESSION['reserva'])) {
    $reserva = $_SESSION['reserva'];
} else {
    // Redirige al formulario de reserva si no hay datos de reserva en la sesión
    header("Location: formulario_reserva.php");
    exit;
}
?>
<?php
    // Recupera los datos de la reserva desde la sesión
    $fecha = $reserva['fecha'] ?? '';
    $cancha_id = $reserva['cancha_id'] ?? '';
    $duracion = $reserva['duracion'] ?? '';
    $hora_inicio = $reserva['hora_inicio'] ?? '';
    $precio = $reserva['precio'] ?? '';

    // Calcular la hora de fin
    $hora_inicio_timestamp = strtotime($hora_inicio);
    $hora_fin_timestamp = $hora_inicio_timestamp + ($duracion * 60); // Duración en segundos
    $hora_fin = date('H:i', $hora_fin_timestamp);
    
    // Obtener el nombre y email del usuario de la sesión
    $nombre = $_SESSION['session_nombre'] ?? ''; // Asegúrate de que el nombre esté en la sesión
    $email = $_SESSION['session_email'] ?? ''; // Asegúrate de que el email esté en la sesión

    // Diccionario para traducir días y meses
    $dias_es = [
        'Sunday' => ' Domingo ', 'Monday' => ' Lunes ', 'Tuesday' => ' Martes ', 
        'Wednesday' => ' Miércoles ', 'Thursday' => ' Jueves ', 'Friday' => ' Viernes ', 
        'Saturday' => ' Sábado '
    ];

    $meses_es = [
        '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', 
        '06' => '06', '07' => '07', '08' => '08', '09' => '09', '10' => '10', 
        '11' => '11', '12' => '12'
    ];

    // Obtener el día y el mes en inglés
    $dia_semana = date('l', strtotime($fecha)); // Día de la semana en inglés
    $dia_mes = date('d', strtotime($fecha));    // Día del mes
    $mes = date('m', strtotime($fecha));        // Mes en inglés (número)

    // Convertir al español
    $dia_semana_es = $dias_es[$dia_semana];
    $mes_es = $meses_es[$mes];
    /*echo "<p><strong>Nombre:</strong> $nombre</p>";
    echo "<p><strong>Email:</strong> $email</p>";
    echo "<p><strong>Fecha:</strong> $fecha</p>";
    echo "<p><strong>Cancha ID:</strong> $cancha_id</p>";
    echo "<p><strong>Duración:</strong> $duracion minutos</p>";
    echo "<p><strong>Hora de Inicio:</strong> $hora_inicio</p>";
    echo "<p><strong>Hora de Término:</strong> $hora_fin</p>"; // Mostrar la hora de término
    echo "<p><strong>Precio:</strong> $$precio</p>";*/
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title>Resumen de Reserva</title>
    <style>
        /* Estilo básico para el loader */
        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
            display: block;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        #resumen-contenido {
            display: none; /* Oculto por defecto hasta que termine la carga */
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div id="loader" class="loader"></div>

    <div class="bg-white shadow-md rounded-lg p-6 w-full max-w-md" id="resumen-contenido">
        <div class="text-center mb-6">
            <i class="fas fa-check-circle text-4xl text-black"></i>
            <h1 class="text-2xl font-semibold mt-2">Reserva realizada con éxito!</h1>
        </div>
        <div class="border-t border-gray-300 pt-4">
            <div class="flex items-center mb-4">
                <i class="fas fa-user-circle text-xl text-gray-700 mr-2"></i>
                <div>
                    <p class="font-semibold"><?php echo $nombre?></p>
                    <p class="text-gray-600"><?php echo $email?></p>
                </div>
            </div>
            <div class="flex items-center mb-4">
                <i class="fas fa-clock text-xl text-gray-700 mr-2"></i>
                <div>
                    <p class="font-semibold"><?php echo $hora_inicio?> - <?php echo $hora_fin?> el <span class="font-bold"><?php echo $dia_semana_es , $dia_mes?> - <?php echo $mes_es?></span></p>
                </div>
            </div>
            <div class="flex items-center mb-4">
                <i class="fas fa-hourglass-end text-xl text-gray-700 mr-2"></i>
                <div>
                    <p class="font-semibold"><?php echo $duracion?> minutos</p>
                </div>
            </div>
            <div class="flex items-center mb-4">
                <i class="fas fa-dollar-sign text-xl text-gray-700 mr-2"></i>
                <div>
                    <p class="font-semibold">$<?php echo $precio?></p>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-300 pt-4">
            <div class="flex items-center mb-4">
                <i class="fas fa-map-marker-alt text-xl text-gray-700 mr-2"></i>
                <div>
                    <p class="font-semibold">La serena</p>
                    <p class="text-gray-600">Los Arándanos, Ceres s/n</p>
                    <p class="text-gray-600">Cancha <?php echo $cancha_id?></p>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-300 pt-4 text-center">
            <div class="button-container">
                <form action="../php/initial_page.php" method="post">
                    <button type="submit" class="text-black font-semibold" name="reset">Volver al Inicio</button>
                </form> 
            </div>
   
        </div>
    </div>

    <script>
        // Simulamos un tiempo de carga y luego mostramos el contenido
        setTimeout(() => {
            document.getElementById('loader').style.display = 'none';
            document.getElementById('resumen-contenido').style.display = 'block';
        }, 2000); // Tiempo de espera simulado (2 segundos)

        // Elimina los datos de la reserva de la sesión al redirigir al inicio
        document.querySelector('.button-container form').onsubmit = function() {
            <?php unset($_SESSION['reserva']); ?>
        };
    </script>
</body>
</html>
