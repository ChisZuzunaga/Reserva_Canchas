<!DOCTYPE html>
<html>
<head>
    <title>Ceres Padel Club</title>
    <link rel="icon" href="../uploads/icono.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/login_register.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="../java/perfil_modal.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
    <style>
        /* Estilos para el modal */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            text-align: center;
        }

        .close-btn {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-btn:hover,
        .close-btn:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        #loader {
            position: absolute;
            left: 50%;
            top: 50%;
            z-index: 1;
            width: 120px;
            height: 120px;
            margin: -76px 0 0 -76px;
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid #3498db;
            -webkit-animation: spin 2s linear infinite;
            animation: spin 2s linear infinite;
        }

        @-webkit-keyframes spin {
            0% { -webkit-transform: rotate(0deg); }
            100% { -webkit-transform: rotate(360deg); }
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

            /* Add animation to "page content" */
        .animate-bottom {
            position: relative;
            -webkit-animation-name: animatebottom;
            -webkit-animation-duration: 1s;
            animation-name: animatebottom;
            animation-duration: 1s
        }

        @-webkit-keyframes animatebottom {
            from { bottom:-50px; opacity:0 } 
            to { bottom:0px; opacity:1 }
        }

        @keyframes animatebottom { 
            from{ bottom:-50px; opacity:0 } 
            to{ bottom:0; opacity:1 }
        }

        #myDiv {
            display: none;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

    </style>
</head>
<body onload="myFunction()" style="margin:0;">

    <div id="loader"></div>
    <div style="display:none;" id="myDiv" class="animate-bottom">
        <div class="main">

        <div id="errorModal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <p id="errorMessage">Hubo un problema al iniciar sesión. Por favor, intenta de nuevo.</p>
            </div>
        </div>
            <input type="checkbox" id="chk" aria-hidden="true">
                <div class="signup" id="signupForm">
                    <form action="../../controller/controlador.php?action=insertar" method="POST" enctype="multipart/form-data">
                        <div class="division">
                            <div class="first-half">
                                <div class="parcial-first-half">
                                    <div class="first-input-half">  
                                        <input type="text" id="nom" name="nombre"  placeholder=" Nombre">
                                        <input type="text" id="ape" name="apellido"  placeholder=" Apellido">
                                    </div>
                                    <div class="rest-input">
                                        <input type="hidden" id="i-codigo-pais" name="codigo_pais">
                                        <input type="email" id="i-email" name="email" placeholder="Email"> 
                                        <input type="password" id="i-password" name="clave" placeholder="Contraseña">
                                        <!-- Input para teléfono con código de país -->
                                        <input  type="tel" 
                                                id="i-numero" 
                                                name="numero" 
                                                placeholder="Teléfono"
                                                maxlength="9"               
                                                inputmode="numeric"        
                                                pattern="[0-9]{9}"          
                                                oninput="validarTelefono(this)"
                                                id="i-numero" name="numero" placeholder="Teléfono">
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
                                    <input type="file" id="input-imagen" name="imagen" style="display: none;" placeholder="Imagen de perfil..">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script>
        var myVar;

        function myFunction() {
        myVar = setTimeout(showPage, 1000);
        }

        function showPage() {
        document.getElementById("loader").style.display = "none";
        document.getElementById("myDiv").style.display = "flex";
        }


        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const error = urlParams.get('error');
            const modal = document.getElementById("errorModal");
            const errorMessage = document.getElementById("errorMessage");
            const closeModalButton = document.querySelector(".close-btn");

            // Mostrar el modal y mensaje según el error
            if (error) {
                switch(error) {
                    case 'user_not_found':
                        errorMessage.textContent = "Error: Usuario o contraseña incorrecta.";
                        break;
                    case 'incorrect_password':
                        errorMessage.textContent = "Error: Usuario o contraseña incorrecta.";
                        break;
                    case 'missing_data':
                        errorMessage.textContent = "Error: Falta agregar información.";
                        break;
                    case 'image_upload':
                        errorMessage.textContent = "Error: Debe agregar una imagen.";
                        break;
                    case 'registration_failed':
                        errorMessage.textContent = "Error: Fallo en el registro de usuario.";
                        break;
                    case 'email_exists':
                        errorMessage.textContent = "Error: Email ya registrado en el sistema.";
                        break;
                    case 'invalid_image_extension':
                        errorMessage.textContent = "Error: Extensión de imágen no admitida.";
                        break;
                    default:
                        errorMessage.textContent = "Hubo un problema al iniciar sesión.";
                }
                modal.style.display = "block";
            }

            // Cerrar el modal al hacer clic en la "X"
            closeModalButton.onclick = function() {
                modal.style.display = "none";
                window.history.replaceState({}, document.title, window.location.pathname);
            }

            // Cerrar el modal al hacer clic fuera de la ventana del modal
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                    window.history.replaceState({}, document.title, window.location.pathname);
                }
            }
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Inicializar intl-tel-input
            var input = document.querySelector("#i-numero");
            var iti = window.intlTelInput(input, {
                preferredCountries: ["cl"],  // Puedes agregar los países preferidos aquí
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"  // Este script es necesario para la validación
            });

            // Actualizar el número de teléfono con el código del país antes de enviar
            document.querySelector("form").addEventListener("submit", function(event) {
                // Obtener el número de teléfono completo (con código de país)
                var fullNumber = iti.getNumber();  // Esto incluye el código de país
                var countryCode = iti.getSelectedCountryData().dialCode;  // Código del país sin '+'
                
                // Colocar el número con el código en el campo de teléfono
                document.querySelector("#i-numero").value = fullNumber;

                // Colocar el código del país en el campo oculto
                document.querySelector("#i-codigo-pais").value = countryCode;
            });
        });
    </script>
    <script>
        function validarTelefono(input) {
            // Eliminar cualquier cosa que no sea un número
            input.value = input.value.replace(/\D/g, '');
            
            // Limitar la longitud a 9 caracteres
            if (input.value.length > 9) {
                input.value = input.value.slice(0, 9);
            }
        }
    </script>
</body>
</html>
