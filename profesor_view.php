<?php
    // Incluir el archivo que contiene la definición de la clase ProfesorModel
    require_once 'modelogod.php';

    // Crear una instancia del modelo
    $clientesModel = new Clientes_Model();

    // Obtener la lista de profesores utilizando el método getProfesores()
    $clientes = $clientesModel->getClientes();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lista de Profesores</title>
</head>
<body>
    <h1>Lista de Profesores</h1>

    <table>
        <tr>
            <th>RUT</th>
            <th>Nombre</th>
            <th>Apellido Paterno</th>
            <th>Apellido Materno</th>
            <th>Correo</th>
            <th>Tipo de Contrato</th>
        </tr>
        <?php foreach ($clientes as $cliente) : ?>
        <tr>
            <td><?php echo $cliente['rut']; ?></td>
            <td><?php echo $cliente['nombre']; ?></td>
            <td><?php echo $cliente['apellido_paterno']; ?></td>
            <td><?php echo $cliente['apellido_materno']; ?></td>
            <td><?php echo $cliente['correo']; ?></td>
            <td><?php echo $cliente['tipo_contrato']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>