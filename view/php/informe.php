<?php
require_once(__DIR__ . '/../../model/modelogod.php');
require_once(__DIR__ . '/../../db/Database.php');
require_once(__DIR__ . '/../../utils/decrypt.php');

$database = new Database();
$modelo = new Clientes_model($database);

$clientes_totales = $modelo->obtenerTotalClientes();
$clientes_nuevos = $modelo->obtenerNuevosClientes();
$clientes_antiguos = $modelo->obtenerClientesAntiguos();
$clientes_sin_reservas = $modelo->obtenerClientesSinReservas();
$reservas_totales = $modelo->obtenerTotalReservas();
$reservas_canceladas = $modelo->obtenerReservasCanceladas();
$reservas_confirmadas = $modelo->obtenerReservasConfirmadas();
$reservas_pendientes = $modelo->obtenerReservasPendientes();
$cancha_1 = $modelo->obtenerUsoCancha(1);
$cancha_2 = $modelo->obtenerUsoCancha(2);
$horarios_frecuentes = $modelo->obtenerHorariosFrecuentes();
$dias_frecuentes = $modelo->obtenerDiasFrecuentes();


?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Document</title>
    <style>
        /* Asegura que los gráficos se ajusten al 100% del contenedor */
        canvas {
            width: 100% !important;    /* Asegura que el gráfico ocupe todo el ancho disponible */
            height: 400px;             /* Establece una altura fija para los gráficos */
            max-width: 100%;           /* Evita que el gráfico se estire más allá del contenedor */
        }

        /* Estilos para el contenedor de los gráficos */
        div {
            margin-bottom: 20px;       /* Agrega espacio entre las secciones de gráficos */
        }

        /* Títulos */
        h1, h2 {
            font-family: Arial, sans-serif; /* Establece una fuente limpia para los títulos */
            color: #333;                   /* Color oscuro para los títulos */
        }

        /* Asegura que los textos dentro de los párrafos estén bien alineados */
        p {
            font-family: Arial, sans-serif;
            font-size: 16px;
            color: #666;                /* Texto de color gris para los párrafos */
            margin: 5px 0;              /* Espaciado vertical entre los párrafos */
        }

        /* Agrega un fondo ligero a las secciones */
        div > h2 {
            background-color: #f4f4f4;   /* Fondo gris claro para los títulos de cada sección */
            padding: 10px;                /* Agrega padding alrededor de los títulos */
            border-radius: 8px;           /* Bordes redondeados */
        }

        .contenedor{
            display:flex;
            width: 100%;
            border-radius: 8px;  
        }

        .dias, .horas{
            width: 50%;
        }

        .grafico{
            display:flex;
            justify-content: center;
            align-items: center;
            padding: 50px;
        }

        .informe-contenedor{
            width: 100%;
            height: auto;
            outline: 2px solid black;
        }

        /* Media Query para pantallas pequeñas */
        @media (max-width: 768px) {
            canvas {
                height: 250px; /* Hacer los gráficos más pequeños en pantallas móviles */
            }

            .contenedor {
                flex-direction: column; /* Cambiar a disposición vertical en pantallas pequeñas */
            }

            .dias, .horas {
                width: 100%; /* Hacer que cada gráfico ocupe todo el ancho disponible */
            }

            .grafico {
                padding: 20px; /* Reducir padding en dispositivos pequeños */
            }
        }

        /* Media Query para pantallas extra pequeñas */
        @media (max-width: 480px) {
            h1 {
                font-size: 18px; /* Reducir tamaño del título en pantallas muy pequeñas */
            }

            p {
                font-size: 14px; /* Reducir tamaño de los párrafos */
            }
        }
    </style>
</head>
<body>
    
    <div class="informe-contenedor">
        <h1>Informe de Reservas</h1>

        <div>
            <h2>Clientes</h2>
            <p>Total Clientes: <?php echo $clientes_totales; ?></p>
            <p>Nuevos Clientes: <?php echo $clientes_nuevos; ?></p>
            <p>Clientes Antiguos: <?php echo $clientes_antiguos; ?></p>
            <p>Clientes Sin Reservas: <?php echo $clientes_sin_reservas; ?></p>
        </div>

        <div>
            <h2>Reservas</h2>
            <p>Total Reservas: <?php echo $reservas_totales; ?></p>
            <p>Reservas Confirmadas: <?php echo $reservas_confirmadas; ?></p>
            <p>Reservas Canceladas: <?php echo $reservas_canceladas; ?></p>
            <p>Reservas Pendientes: <?php echo $reservas_pendientes; ?></p>
        </div>

        <div>
            <h2>Canchas Más Usadas</h2>
            <p>Cancha 1: <?php echo $cancha_1; ?></p>
            <p>Cancha 2: <?php echo $cancha_2; ?></p>
        </div>

        <div class="contenedor">
            <div class="horas">
                <h2>Horarios Más Frecuentes</h2>
                <div class="grafico">
                    <canvas id="graficoHorarios"></canvas>
                </div>
            </div>
            <div class="dias">
                <h2>Días Más Frecuentes</h2>
                <div class="grafico">
                    <canvas id="graficoDias"></canvas>
                </div>
            </div>
        </div>
    </div>
</body>
    <script>
        // Función para agregar el porcentaje a las etiquetas del gráfico
        function porcentajeLabel(context) {
            const dataset = context.chart.data.datasets[0];
            const data = dataset.data;
            const index = context.dataIndex;
            const value = data[index];
            const total = data.reduce((a, b) => a + b, 0);
            const porcentaje = Math.round((value / total) * 100);
            return `${porcentaje}%`;
        }

        const ctxDias = document.getElementById('graficoDias').getContext('2d');
        new Chart(ctxDias, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($dias_frecuentes, 'dia')); ?>,
                datasets: [{
                    label: 'Reservas por Día',
                    data: <?php echo json_encode(array_column($dias_frecuentes, 'cantidad')); ?>,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#C9CBCF'],
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const dataset = context.chart.data.datasets[0];
                                const data = dataset.data;
                                const index = context.dataIndex;
                                const value = data[index];
                                const total = data.reduce((a, b) => a + b, 0);
                                const porcentaje = Math.round((value / total) * 100);
                                return `${porcentaje}%`; // Muestra el porcentaje en el tooltip
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de Horarios
        const ctxHorarios = document.getElementById('graficoHorarios').getContext('2d');
        new Chart(ctxHorarios, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($horarios_frecuentes, 'hora_inicio')); ?>,
                datasets: [{
                    label: 'Reservas por Horario',
                    data: <?php echo json_encode(array_column($horarios_frecuentes, 'porcentaje')); ?>,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#C9CBCF'],
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: porcentajeLabel
                        }
                    }
                }
            }
        });
    </script>
</html>




