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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">    <title>Inicio</title>
</head>
<body>
    <div class="main">
        <header>
            <div class="division">
                <div class="first-half">
                    <img id="logoaqui" src="https://st5.depositphotos.com/69915036/62675/v/450/depositphotos_626754468-stock-illustration-your-logo-here-placeholder-symbol.jpg">
                    <h1>Tu logo aquí</h1>
                </div>
                <div class="second-half">
                    <div class="ff-h">
                        <h1><?php echo "Bienvenido " ,$nombre?></h1>
                    </div>
                    <div class="ss-h">
                        <img id="f-perfil" src="<?php echo $img?>" id="foto">
                        <img id="monito" src="../uploads/fondod.png">
                    </div>
                </div>
            </div>
        </header>
        <div class="content">
            <div class="carru">

            <div id="demo" class="carousel slide" data-bs-ride="carousel">

            <!-- Indicators/dots -->
            <div class="carousel-indicators">
            <button type="button" data-bs-target="#demo" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#demo" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#demo" data-bs-slide-to="2"></button>
            </div>

            <!-- The slideshow/carousel -->
            <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://cdn.britannica.com/70/234870-050-D4D024BB/Orange-colored-cat-yawns-displaying-teeth.jpg" alt="Los Angeles" class="d-block w-100">
                <div class="carousel-caption">
                    <h3>Imagen 1</h3>
                    <p>Imagen 1...</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://cdn.britannica.com/34/235834-050-C5843610/two-different-breeds-of-cats-side-by-side-outdoors-in-the-garden.jpg" alt="Chicago" class="d-block w-100">
                <div class="carousel-caption">
                    <h3>Imagen 2</h3>
                    <p>Imagen 2...</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://www.rover.com/blog/wp-content/uploads/group-of-cats-four-cats-outside-960x540.jpg" alt="New York" class="d-block w-100">
                <div class="carousel-caption">
                    <h3>Imagen 3</h3>
                    <p>Imagen 3...</p>
                </div>
            </div>
            </div>

            <!-- Left and right controls/icons -->
            <button class="carousel-control-prev" type="button" data-bs-target="#demo" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#demo" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
            </button>
            </div>

            </div>
            <div class="servicios">
                <form action="../controlador/controlador.php?action=cerrar" method="post">
                    <button type="submit">Cerrar sesión</button>
                    dd
                </form>
            </div>

        </div>
        <footer>
            <div class="division">
                <div class="uno-d">
                    <img id="uno-d-logo" src="https://st5.depositphotos.com/69915036/62675/v/450/depositphotos_626754468-stock-illustration-your-logo-here-placeholder-symbol.jpg">
                    <a>Tu logo aquí</a>
                </div>
                <div class="dos-d">
                    <div class="dos-d-rr">
                        <a id="rrss">Redes Sociales</a><br>
                        <div class="img-rr">
                            <a id="a-ref" href="https://www.instagram.com/cerespadel/">
                                <img id="rr-ins1" src="https://cdn-icons-png.flaticon.com/512/87/87390.png">
                            </a>
                            <a id="sa-ref" href="https://l.instagram.com/?u=https%3A%2F%2Fwa.me%2Fmessage%2FGEHSNHLW763MP1&e=AT1iwBN23enwvJmLaZxhXyK-VZxevsFP99c02lVXktaTKfwbSUglTbnCalKTLA3yPfDHVijODglQ8m7kVvTX3PuNwR4bWQA1Upi6VP8IgTVHF6wEQ_8urg">
                                <img id="rr-ins2" src="https://i.pinimg.com/564x/6e/2c/f9/6e2cf920c73d260231a7cb8a16933486.jpg">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="tre-d">
                    <div class="tre-txt">
                        <a id="cnt-d">Contacto Directo</a><br>
                        <a id="cnt-gm">asdsdasda@gmail.com</a>
                    </div>
                </div>
                <div class="cua-d">
                    <a id="cnt-d">Sobre Nosotros...</a><br>
                </div>
            </div>
            <div class="copy">
                &copy; Copyright <?php echo date('Y'); ?>, Queso - All Rights Reserved
            </div>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>

</html>