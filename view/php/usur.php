<?php
require_once(__DIR__ . '/../../model/modelogod.php');
require_once(__DIR__ . '/../../db/Database.php');
define('BASE_URL', '../');

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


        .usuario-card {
            display: flex;
            align-items: center;
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            gap: 15px;
        }

        .usuario-imagen {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }

        .usuario-info {
            flex: 1;
        }

        .usuario-nombre {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
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
            position: relative;
            color: white;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
        }
        .barra-estado .cancelada {
            background-color: #FF4C4C;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .barra-estado .reservada {
            background-color: #FFA500;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .barra-estado .confirmada {
            background-color: #4CAF50;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .detalles-btn {
            padding: 8px 12px;
            font-size: 14px;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }

        .detalles-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Usuarios y sus Reservas</h1>
        <?php foreach ($usuarios_reservas as $usuario): ?>
            <div class="usuario-card">
                <!-- Imagen del usuario -->
                <img src="<?php echo BASE_URL . $usuario['Imagen']; ?>" alt="Imagen Usuario" class="usuario-imagen">

                <!-- Información del usuario -->
                <div class="usuario-info">
                    <p class="usuario-nombre">
                        <?php echo htmlspecialchars($usuario['Nombre'] . ' ' . $usuario['Apellido']); ?>
                    </p>
                    <div class="barra-estado">

                        <?php
                            $total_reservas = $usuario['canceladas'] + $usuario['reservadas'] + $usuario['confirmadas'];
                            $porcentaje_canceladas = $total_reservas ? ($usuario['canceladas'] / $total_reservas) * 100 : 0;
                            $porcentaje_reservadas = $total_reservas ? ($usuario['reservadas'] / $total_reservas) * 100 : 0;
                            $porcentaje_confirmadas = $total_reservas ? ($usuario['confirmadas'] / $total_reservas) * 100 : 0;
                            $total_canchas = $usuario['canceladas'] + $usuario['reservadas'] + $usuario['confirmadas'];
                        ?>
                        <div class="barra w-100 d-flex">
                            <div class="cancelada" style="width: <?php echo $porcentaje_canceladas; ?>%;">
                                <?php echo $porcentaje_canceladas > 0 ? round($porcentaje_canceladas) . '%' : ''; ?>
                            </div>
                            <div class="reservada" style="width: <?php echo $porcentaje_reservadas; ?>%;">
                                <?php echo $porcentaje_reservadas > 0 ? round($porcentaje_reservadas) . '%' : ''; ?>
                            </div>
                            <div class="confirmada" style="width: <?php echo $porcentaje_confirmadas; ?>%;">
                                <?php echo $porcentaje_confirmadas > 0 ? round($porcentaje_confirmadas) . '%' : ''; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botón para ver más detalles -->
                <button 
                    class="detalles-btn" 
                    data-bs-toggle="modal" 
                    data-bs-target="#modalUsuario<?php echo htmlspecialchars($usuario['Email']); ?>">
                    Detalles
                </button>
            </div>

            <!-- Modal para mostrar detalles -->
            <div class="modal fade" id="modalUsuario<?php echo htmlspecialchars($usuario['Email']); ?>" tabindex="-1" aria-labelledby="modalLabel<?php echo htmlspecialchars($usuario['Email']); ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel<?php echo htmlspecialchars($usuario['Email']); ?>">
                                Detalles de <?php echo htmlspecialchars($usuario['Nombre'] . ' ' . $usuario['Apellido']); ?>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['Email']); ?></p>
                            <p><strong>Telefono:</strong> <?php echo htmlspecialchars($usuario['Numero']); ?></p>
                            <p><strong>Canchas Canceladas:</strong> <?php echo $usuario['canceladas']; ?></p>
                            <p><strong>Canchas Reservadas:</strong> <?php echo $usuario['reservadas']; ?></p>
                            <p><strong>Canchas Confirmadas:</strong> <?php echo $usuario['confirmadas']; ?></p>
                            <p><strong>Canchas Totales:</strong> <?php echo $total_canchas ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
