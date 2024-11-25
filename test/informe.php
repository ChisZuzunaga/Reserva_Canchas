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
</head>
<body>
    
    <div class="informe-contenedor">
        <h1 class = "h2">Informe de Reservas</h1>
        <div class="parenter">
            <div class="div--1">
                <h2 class = "h2">Clientes</h2>
                <p class="p">Total Clientes: <?php echo $clientes_totales; ?></p>
                <p class="p">Nuevos Clientes: <?php echo $clientes_nuevos; ?></p>
                <p class="p">Clientes Antiguos: <?php echo $clientes_antiguos; ?></p>
                <p class="p">Clientes Sin Reservas: <?php echo $clientes_sin_reservas; ?></p>
            </div>
            <div class="div--2">
            <h2 class = "h2">Reservas</h2>
                <p class="p">Total Reservas: <?php echo $reservas_totales; ?></p>
                <p class="p">Reservas Confirmadas: <?php echo $reservas_confirmadas; ?></p>
                <p class="p">Reservas Canceladas: <?php echo $reservas_canceladas; ?></p>
                <p class="p">Reservas Pendientes: <?php echo $reservas_pendientes; ?></p>
            </div>
            <div class="div--3">
                <h2 class = "h2">Canchas Más Usadas</h2>
                <p class="p">Cancha 1: <?php echo $cancha_1; ?></p>
                <p class="p">Cancha 2: <?php echo $cancha_2; ?></p>
            </div>
            <div class="div--4">
                <div class="horas">
                    <h2 class = "h2">Horarios Más Frecuentes</h2>
                    <div class="grafico">
                        <canvas id="graficoHorarios"></canvas>
                    </div>
                </div>
            </div>
            <div class="div--5">
                <div class="dias">
                    <h2 class = "h2">Días Más Frecuentes</h2>
                    <div class="grafico">
                        <canvas id="graficoDias"></canvas>
                    </div>
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




