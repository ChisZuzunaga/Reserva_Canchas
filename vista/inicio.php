<?php
session_start(); // Asegúrate de iniciar la sesión en la página de destino

if (isset($_SESSION['session_email'])) {
    $email = $_SESSION['session_email'];
    $nombre = $_SESSION['session_nombre'];
    $img = $_SESSION['ruta_imagen']; // Obtener el nombre almacenado en la sesión

} else {
    echo "No has iniciado sesión.";
    // Puedes redirigir al usuario a la página de inicio de sesión si no está autenticado
    //agregar si le gustaria crear cuenta
    //header("Location: ../vista/crear_clientes.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../script.js"></script>
    <link rel="stylesheet" href="index.css">

    <title>Inicio</title>
</head>
<body>
    <div>
        <img src="<?php echo $img?>" id="foto">
        <h1><?php echo "Bienvenido " ,$nombre?></h1>
        <form action="../controlador/controlador.php?action=cerrar" method="post">
            <button type="submit">Cerrar sesión</button>
        </form>
    </div>

</body>
</html>
