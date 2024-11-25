<?php
require_once(__DIR__ . '/../../model/modelogod.php');
require_once(__DIR__ . '/../../db/Database.php');

$database = new Database();
$model = new Clientes_model($database); // Asegúrate de que este modelo esté disponible

$usuarios_reservas = $model->obtenerUsuariosReservas();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios y Reservas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        .usuario-row {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .usuario-imagen {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
        }
        .barra-estado {
            flex-grow: 1;
            display: flex;
            align-items: center;
        }
        .barra-estado .barra {
            height: 20px;
            border-radius: 5px;
            overflow: hidden;
            display: flex;
        }
        .barra-estado .cancelada {
            background-color: #FF4C4C;
        }
        .barra-estado .reservada {
            background-color: #FFA500;
        }
        .barra-estado .confirmada {
            background-color: #4CAF50;
        }
        .boton-opciones {
            margin-left: 10px;
        }
    </style>
</head>
<body class="container mt-4">
    <h1>Usuarios y sus Reservas</h1>
    <?php foreach ($usuarios_reservas as $usuario): ?>
        <div class="usuario-row">
            <!-- Imagen del usuario -->
            <img src="<?php echo $usuario['Imagen']; ?>" alt="Imagen Usuario" class="usuario-imagen">
            
            <!-- Información del usuario y barra de porcentaje -->
            <div class="barra-estado">
                <div style="margin-right: 10px;">
                    <strong><?php echo $usuario['Nombre'] . ' ' . $usuario['Apellido']; ?></strong>
                </div>
                <?php
                    $total_reservas = $usuario['canceladas'] + $usuario['reservadas'] + $usuario['confirmadas'];
                    $porcentaje_canceladas = $total_reservas ? ($usuario['canceladas'] / $total_reservas) * 100 : 0;
                    $porcentaje_reservadas = $total_reservas ? ($usuario['reservadas'] / $total_reservas) * 100 : 0;
                    $porcentaje_confirmadas = $total_reservas ? ($usuario['confirmadas'] / $total_reservas) * 100 : 0;
                ?>
                <div class="barra w-100 d-flex">
                    <div class="cancelada" style="width: <?php echo $porcentaje_canceladas; ?>%;"></div>
                    <div class="reservada" style="width: <?php echo $porcentaje_reservadas; ?>%;"></div>
                    <div class="confirmada" style="width: <?php echo $porcentaje_confirmadas; ?>%;"></div>
                </div>
            </div>
            
            <!-- Botón para ver más datos -->
            <button 
                class="btn btn-primary boton-opciones" 
                data-bs-toggle="modal" 
                data-bs-target="#modalUsuario<?php echo $usuario['Email']; ?>">
                Opciones
            </button>
        </div>
        
        <!-- Modal para más datos -->
        <div class="modal fade" id="modalUsuario<?php echo $usuario['Email']; ?>" tabindex="-1" aria-labelledby="modalLabel<?php echo $usuario['Email']; ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel<?php echo $usuario['Email']; ?>">
                            Más datos de <?php echo $usuario['Nombre'] . ' ' . $usuario['Apellido']; ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Email:</strong> <?php echo $usuario['Email']; ?></p>
                        <p><strong>Canchas Canceladas:</strong> <?php echo $usuario['canceladas']; ?></p>
                        <p><strong>Canchas Reservadas:</strong> <?php echo $usuario['reservadas']; ?></p>
                        <p><strong>Canchas Confirmadas:</strong> <?php echo $usuario['confirmadas']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
