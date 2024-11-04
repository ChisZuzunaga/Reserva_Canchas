<!DOCTYPE html>
<html>
<head>
    <title>Ceres Padel Club</title>
    <link rel="icon" href="../uploads/icono.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/login_register.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="../java/perfil_modal.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
</head>
<body>
    <div class="main">
        <input type="checkbox" id="chk" aria-hidden="true">
            <div class="signup" id="signupForm">
                <form action="../../controller/controlador.php?action=insertar" method="POST" enctype="multipart/form-data">
                <div class="division">
                    <div class="first-half">
                        <div class="parcial-first-half">
                            <div class="first-input-half">  
                                <input type="text" id="nom" name="nombre" required placeholder=" Nombre">
                                <input type="text" id="ape" name="apellido" required placeholder=" Apellido">
                            </div>
                            <div class="rest-input">
                                <input type="text" id="i-email" name="email" required placeholder=" Email"> 
                                <input type="password" id="i-password" name="clave" required placeholder=" Contraseña">
                                <input type="text" id="i-numero" name="numero" required placeholder=" Teléfono">   
                                <button>Registrarse</button>  
                                <div class="label-first">
                                    <a>¿Ya tienes una cuenta?</a><label for="chk" class="login-text" aria-hidden="true">Inicia Sesión</label>              
                                </div>
                            </div>
                        </div>
                    </div>
                        <div class="second-division">
                            <div class="sd-first-half">
                                <img id="f-perfil" src="https://t4.ftcdn.net/jpg/06/62/07/19/360_F_662071971_7bhQFAgB5CgjXYeLO6LMQCnklyYAsw99.jpg" alt="Imagen de perfil">
                                <img id="monito" src="../uploads/fondod.png">
                                <input type="file" id="input-imagen" name="imagen" required style="display: none;" placeholder="Imagen de perfil..">
                            </div>
                            <div class="sd-second-half">
                                <div class="canchas">
                                    <a id="text-can">Ceres</a><br>
                                    <a id="first-tempo">Padel Club</a>
                                </div>
                                <div class="logo-first-tempo">
                                    <img id="logo" src="https://i.imgur.com/ywwk1E0.png">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        <div class="login" id="loginForm">
            <form action="../../controller/controlador.php?action=verificar" method="POST">
                <div class="cnt-login">
                    <div class="cnt-first-half">
                    </div>
                    <div class="cnt-second-half">
                        <div class="megadivision">
                            <div class="info">
                                <input type="text" name="rer_email" required placeholder = " Email">
                            </div>
                            <div class="login-pass">
                                <input type="password" name="rer_clave" required placeholder = " Contraseña">
                                <button>Iniciar Sesión</button>
                                <div class="label-first">
                                    <a>¿Aún no tienes una cuenta?</a><label for="chk" class="login-text" aria-hidden="true">Registrate</label>              
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
