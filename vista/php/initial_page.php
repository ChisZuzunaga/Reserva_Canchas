<?php
session_start(); // Asegúrate de iniciar la sesión en la página de destino

if (isset($_SESSION['session_email'])) {
    $email = $_SESSION['session_email'];
    $nombre = $_SESSION['session_nombre'];
    $img = $_SESSION['ruta_imagen']; // Obtener el nombre almacenado en la sesión

} else {
    echo "No has iniciado sesión.";
    // Puedes redirigir al usuario a la página de inicio de sesión si no está autenticado
    exit();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/initial_page.css">
    <link rel="stylesheet" href="../css/test.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="main">
        <div class="parent">
            <div class="div-1">
                <div class="division">
                    <div class="first-half">
                        <img id="logoaqui" src="https://i.imgur.com/ywwk1E0.png">
                        <h1>Ceres Padel Club</h1>
                    </div>
                    <input type="checkbox" id="chk" aria-hidden="true">
                    <div class="second-half">
                        <div class="first-p">
                            <div class="perfil-header">
                                <div class="ff-h">
                                    <h1><?php echo "Bienvenido " ,$nombre?></h1>
                                </div>
                                <div class="ss-h">
                                    <label for="chk" class="ppfil" aria-hidden="true">
                                        <img id="f-perfil" src="<?php echo $img?>">
                                        <img id="monito" src="../../uploads/fondod.png">
                                    </label> 
                                </div>
                            </div>
                        </div>
                    
                        <div class="side-perfil">
                            <div class="colorr">
                                <div class="separ">
                                    <label for="chk" class="separr" aria-hidden="true">
                                        <img id="f-perfil-side" src="<?php echo $img?>">
                                    </label>
                                </div>
                                <div class="nombrerr">
                                    <a><?php echo $nombre?></a>
                                </div>
                            </div>
                            <div class="asad">
                                aa
                            </div>
                            <div class="cerrar-sin">
                                <form action="../../controlador/controlador.php?action=cerrar" method="post">
                                    <button id="ekis" type="submit">Cerrar Sesión</button>
                                </form>
                            </div>                
                        </div>
                    </div>
                </div>
            </div>
            <div class="div-2">
                <div class="megacar">
                    <div class="carru">
                        <div id="demo" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-indicators">
                                <button type="button" data-bs-target="#demo" data-bs-slide-to="0" class="active"></button>
                                <button type="button" data-bs-target="#demo" data-bs-slide-to="1"></button>
                                <button type="button" data-bs-target="#demo" data-bs-slide-to="2"></button>
                            </div>
                        
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="https://sportlink.com.br/wp-content/uploads/2021/12/BANNER-ESPORTES-PADEL.jpg" class="cat-img">
                                    <div class="carousel-caption">
                                        <h3>Imagen 1</h3>
                                        <p>Imagen 1</p>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <img src="https://sportbalin.com/sites/default/files/banner%20padel.png?1668518058" class="cat-img">
                                    <div class="carousel-caption">
                                        <h3>Imagen 2</h3>
                                        <p>Imagen 2</p>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <img src="https://media.babolat.com//image/upload/f_auto,q_auto,c_crop,w_2000,h_751/Website_content/Padel_News/02092020-Launch/padel-equipment/equipment-banner-2.jpg" class="cat-img">
                                    <div class="carousel-caption">
                                        <h3>Imagen 3</h3>
                                        <p>Imagen 3</p>
                                    </div>
                                </div>
                            </div>
                        
                            <button class="carousel-control-prev" type="button" data-bs-target="#demo" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#demo" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="div-3">
                <a>Selecciona los servicios que deseas agendar</a><br>
                <div class="padel">
                    <div class="pdel-txt">
                        <a>Pádel</a>
                    </div>
                </div>

                <div class="opciones-padel">
                    <div class="canchaA">
                        <div class="tx">
                            <p>Cancha 1</p>
                        </div>
                        <div class="tx-1">
                            <p>60, 90 o 120 minutos</p>
                        </div>
                        <div class="tx-2">
                            <p>$11.111</p>
                        </div>
                        <div class="tx-3">
                            <p>Insertar directrices de la cancha</p>
                        </div>
                        <div class="tx-4">
                            <a href="horarios_dispo.php?cancha_id=1" class="btn">Agendar Servicio</a>
                        </div>
                    </div>
                    <div class="canchaB">
                        <div class="tx">
                            <p>Cancha 2</p>
                        </div>
                        <div class="tx-1">
                            <p>60, 90 o 120 minutos</p>
                        </div>
                        <div class="tx-2">
                            <p>$11.111</p>
                        </div>
                        <div class="tx-3">
                            <p>Insertar directrices de la cancha</p>
                        </div>
                        <div class="tx-4">
                            <a href="horarios_dispo.php?cancha_id=2" class="btn">Agendar servicio</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="div-4">
                <div class="elfsight-app-51441d3c-bcc2-499b-b179-deecfdbe6a58" data-elfsight-app-lazy></div>
            </div>
            <div class="div-5">
                <h3 id="ubi-donde">Dónde estamos ubicados</h3>
            </div>
            <div class="div-6">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3457.9828895920327!2d-71.20232832445089!3d-29.922394774985317!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9691cba177970af5%3A0x5ab495569a432b8e!2sCeres%20P%C3%A1del%20Club!5e0!3m2!1ses!2scl!4v1728229020003!5m2!1ses!2scl"
                    id="mapa" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <div class="div-7">
                <div class="bodd">
                    <div class="card1">
                        <div class="headerr">
                            <h2>Ubicación</h2>
                            <i class="fas fa-chevron-up toggle-icon"></i>
                        </div>
                        <div class="contenrt">
                            <input type="text" placeholder="Los Arándanos, Ceres" class="input" readonly>
                        </div>
                    </div>

                    <div class="card2">
                        <div class="headerr">
                            <h2>Horarios del Club</h2>
                            <i class="fas fa-chevron-up toggle-icon"></i>
                        </div>
                        <div class="contenrt">
                            <p class="p">Lunes, Martes, Miércoles, Jueves, Viernes, Sábado: <span>7:00 a 22:00</span></p>
                            <p class="p">Domingo: <span>8:00 a 22:00</span></p>
                        </div>
                    </div>

                    <div class="card3">
                        <div class="headerr">
                            <h2>Servicios del Club</h2>
                            <i class="fas fa-chevron-up toggle-icon"></i>
                        </div>
                        <div class="contenrt">
                            <div class="services">
                                <div class="a">
                                    <div><i class="fas fa-car"></i> Estacionamiento</div>
                                    <div><i class="fas fa-birthday-cake"></i> Cumpleaños</div>
                                </div>
                                <div class="a">
                                    <div><i class="fas fa-trophy"></i> Torneo</div>
                                    <div><i class="fas fa-school"></i> Escuela Deportiva</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="div-8">
                <footer>
                    <div class="divisions">
                        <div class="uno-d">
                            <img id="uno-d-logo" src="https://i.imgur.com/ywwk1E0.png"><br>
                            <a>Ceres Padel Club</a>
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
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../java/test.js"></script>
    <script src="https://static.elfsight.com/platform/platform.js" async></script>
</body>

</html>
