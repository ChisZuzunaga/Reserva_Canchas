<!DOCTYPE html>
<html>
<head>
    <title>Agregar Clientes</title>
</head>
<body>
    <h1>Agregar Clientes</h1>
    <form action="../controlador/controlador.php?action=insertar" method="POST" enctype="multipart/form-data">
        <label for="nombre">Nombre:</label>    
        <input type="text" name="nombre" required><br>
        
        <label for="apellido">Apellido</label>
        <input type="text" name="apellido" required><br>

        <label for="email">Correo:</label>
        <input type="text" name="email" required><br>

        <label for="password">Clave:</label>
        <input type="password" name="clave" required><br>

        <label for="numero">Numero</label>
        <input type="text" name="numero" required><br>

        <label for="imagen">Imagen</label>
        <input type="file" name="imagen" required><br>

        <input type="submit" value="Guardar">
        <a href="iniciar.php">Iniciar Sesion</a>
    </form>
</body>
</html>
