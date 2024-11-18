<?php
use Twilio\Rest\Client;
require_once(__DIR__ . '/../db/Database.php');
require_once(__DIR__ . '/../model/modelogod.php');
require_once(__DIR__ . '/../utils/decrypt.php');
require_once __DIR__ . '/../vendor/autoload.php';  // Ajusta la ruta si es necesario


class Clientes_controller {
    private $model;

    public function __construct() {
        $database = new Database(); // Crear la conexión a la base de datos
        $this->model = new Clientes_model($database); // Pasar la conexión al modelo
    }

    public function cerrarClientes() {
        session_start();
        
        // Destruir todas las variables de sesión
        $_SESSION = array();
        
        // Si se desea destruir la sesión completamente, también se deben destruir las cookies de sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Finalmente, destruir la sesión
        session_destroy();
        
        // Redirigir al usuario a la página de inicio de sesión u otra página deseada
        header("Location: ../view/php/login_register.php");
        exit();
    }

    public function agregarClientes() {
        session_start();
        // Verificar que todos los datos estén presentes
        if (empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['email']) || empty($_POST['clave']) || empty($_POST['numero'])) {
            header("Location: ../view/php/login_register.php?error=missing_data");
            exit();
        }

        // Continuar con la lógica actual
        $nombrer = $_POST['nombre'];
        $apellidor = $_POST['apellido'];
        $emailr = $_POST['email'];
        $claver = $_POST['clave'];
        $numeror = $_POST['numero'];

        $queryCheck = $this->model->contarClientes($emailr);   
        if ($queryCheck > 0) {
            // Redirige o maneja el error de duplicado
            header("Location: ../view/php/login_register.php?error=email_exists");
            exit();
        }

    
        // Manejo de la imagen subida
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $nombreImagen = $_FILES['imagen']['name'];
            $rutaTemporal = $_FILES['imagen']['tmp_name'];
            $directorioDestino = '../view/uploads/'.$nombreImagen;
    
            if (move_uploaded_file($rutaTemporal, $directorioDestino)) {
                $imagenRuta = $directorioDestino;
            } else {
                header("Location: ../view/php/login_register.php?error=image_upload");
                exit();
            }
        } else {
            header("Location: ../view/php/login_register.php?error=image_upload");
            exit();
        }
    
        $resultado = $this->model->insertClientes($nombrer, $apellidor, $emailr, $claver, $numeror, $imagenRuta);
    
        if ($resultado) {
            $_SESSION['session_email']= $emailr;
            $_SESSION['session_nombre'] = $nombrer;
            $_SESSION['ruta_imagen'] = '../' . $imagenRuta;
            $_SESSION['nnumero'] = $numeror;
            header("Location: ../view/php/initial_page.php");
        } else {
            header("Location: ../view/php/login_register.php?error=registration_failed");
        }
    }
    
    public function verificarClientes() {
        session_start();
        $email = $_POST['rer_email'];
        $clave = $_POST['rer_clave'];
        $credentials = getCredentials();
        $adminEmail = $credentials['admin_email'];
    
        $cliente = $this->model->verifysClientes($email);
    
        if ($cliente) {
            if ($clave == $cliente['Clave']) {
                $_SESSION['session_email'] = $email;
                $_SESSION['nnumero'] = $cliente['Numero'];
                $_SESSION['session_nombre'] = $cliente['Nombre'];
                $_SESSION['ruta_imagen'] = '../uploads/' . basename($cliente['Imagen']);
                
                if ($email == $adminEmail) {
                    header("Location: ../view/php/admin_page.php");
                } else {
                    header("Location: ../view/php/initial_page.php");
                }
            } else {
                header("Location: ../view/php/login_register.php?error=incorrect_password");
            }
        } else {
            header("Location: ../view/php/login_register.php?error=user_not_found");
        }
    }
    

    
    // Función para realizar reservas
    public function reservar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cancha_id = $_POST['cancha_id'];
            $fecha = $_POST['fecha'];
            $hora_inicio = $_POST['hora_inicio'];
            $duracion = $_POST['duracion'];
            $precio = $_POST['precio'];
            $telefonor = $_SESSION['nnumero']; // Número de teléfono del cliente
            $nombrers = $_SESSION['session_nombre']; // Correo electrónico del cliente

            $hora_inicio_obj = DateTime::createFromFormat('H:i', $hora_inicio);
            // Sumar la duración (en minutos)
            $hora_inicio_obj->modify("+$duracion minutes");

            // Obtener la hora de término en el formato deseado (HH:mm)
            $hora_termino = $hora_inicio_obj->format('H:i');

            // Llamada al modelo para reservar
            $resultado = $this->model->reservarCancha($cancha_id, $fecha, $hora_inicio, $duracion, $precio);

            if ($resultado) {
                // Si la reserva se realizó con éxito, guarda los datos en la sesión
                session_start();
                $_SESSION['reserva'] = [
                    'nombre' => $nombre,
                    'cancha_id' => $cancha_id,
                    'fecha' => $fecha,
                    'hora_inicio' => $hora_inicio,
                    'duracion' => $duracion,
                    'precio' => $precio
                ];

                $telefono = "+56966222508"; // Número del administrador o cliente
                $mensaje = "🗓️ *Nueva reserva pendiente de confirmación*:\n\n"
                . "👤 *Cliente*: $nombrers\n"
                . "📞 *Teléfono*: $telefonor\n"
                . "🎾 *Cancha solicitada*: $cancha_id\n"
                . "📅 *Fecha de la reserva*: $fecha\n"
                . "⏰ *Horario solicitado*: Desde las $hora_inicio hasta las $hora_termino\n"
                . "⏳ *Duración*: $duracion minutos\n"
                . "💰 *Valor estimado de la reserva*: $$precio\n\n"
                . "📲 *Por favor, contacta al cliente* para confirmar si asistirá a la reserva. "
                . "Una vez confirmado, accede al panel de administrador para *confirmar* ✅ o *cancelar* ❌ la reserva.";

                $this->enviarMensajeWhatsApp($telefono, $mensaje);

                // Redirige a la vista de resumen
                header("Location: ../view/php/resumen_reserva.php");
                exit;
            } else {
                // Redirigir con error y cancha_id
                header("Location: ../view/php/reservar.php?cancha_id=$cancha_id&error=reservation_fail");
                exit();
            }            
        }
    }

    // Función para mostrar la página de reservas con horarios disponibles
    public function mostrarReservas() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fecha']) && isset($_POST['cancha_id'])) {
            $fecha = $_POST['fecha'];
            $cancha_id = $_POST['cancha_id'];

            // Obtener reservas para la fecha seleccionada y la cancha seleccionada
            $reservas = $this->model->getReservasPorFecha($cancha_id, $fecha);

            // Procesar las reservas para visualizar horarios ocupados
            $horarios_ocupados = [];
            foreach ($reservas as $reserva) {
                $hora_inicio = strtotime($reserva['Hora_Inicio']);
                $hora_fin = strtotime($reserva['Hora_Fin']);
                while ($hora_inicio < $hora_fin) {
                    $horarios_ocupados[date('H:i', $hora_inicio)] = true;
                    $hora_inicio += 1800; // Avanzar en intervalos de 30 minutos
                }
            }

            // Guardar la información en sesión
            session_start();
            $_SESSION['horarios_ocupados'] = $horarios_ocupados;
            $_SESSION['fecha'] = $fecha;
            $_SESSION['cancha_id'] = $cancha_id;

            // Redirigir a la página de disponibilidad
            header("Location: ../view/php/disponibilidad.php");
            exit();
        } else {
            // Si no hay fecha o cancha_id en el POST, redirigir a la página de reservas
            header("Location: ../view/php/reservar.php");
            exit();
        }
    }

    public function mostrarDisponibilidad() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fecha']) && isset($_POST['cancha_id'])) {
            $fecha = $_POST['fecha'];
            $cancha_id = $_POST['cancha_id'];

            // Obtener reservas para la fecha seleccionada y la cancha seleccionada
            $reservas = $this->model->getReservasPorFecha($cancha_id, $fecha);

            // Procesar las reservas para visualizar horarios ocupados
            $horarios_ocupados = [];
            foreach ($reservas as $reserva) {
                $hora_inicio = strtotime($reserva['Hora_Inicio']);
                $hora_fin = strtotime($reserva['Hora_Fin']);
                while ($hora_inicio < $hora_fin) {
                    $horarios_ocupados[date('H:i', $hora_inicio)] = true;
                    $hora_inicio += 1800; // Avanzar en intervalos de 30 minutos
                }
            }

            // Guardar la información en sesión
            $_SESSION['horarios_ocupados'] = $horarios_ocupados;
            $_SESSION['fecha'] = $fecha;
            $_SESSION['cancha_id'] = $cancha_id;

            // Redirigir a la página de disponibilidad
            header("Location: ../view/php/disponibilidad.php");
            exit();
        } else {
            // Si no hay fecha o cancha_id en el POST, redirigir a la página de reservas
            header("Location: ../view/php/reservar.php");
            exit();
        }
    }

    public function cancelarReservaUSU() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_reserva'])) {
            $id_reserva = $_POST['id_reserva'];
            $resultado = $this->model->cancelarReserva($id_reserva);
            
            // No redirigir a otra página, simplemente terminar la función
            // Podrías devolver un JSON o un mensaje si es necesario
            header("Location: ../view/php/horas_usuario.php");
            exit(); // Finaliza el script aquí
        }
    }

    public function cancelarReserva() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_reserva'])) {
            $id_reserva = $_POST['id_reserva'];
            $resultado = $this->model->cancelarReserva($id_reserva);
            
            // Generar mensaje para el usuario
            $mensaje = $resultado 
                ? "Reserva cancelada con éxito."
                : "Error al cancelar la reserva.";
            
            // Responder con un script de redirección y el mensaje emergente
            echo "<script>
                    alert('$mensaje');
                    window.location.href = '{$_SERVER['HTTP_REFERER']}';
                  </script>";
            exit; // Finaliza para evitar cualquier salida adicional
        }
    }
    
    public function enviarMensajeWhatsApp($telefono, $mensaje) {
        $credentials = getCredentials();
        $sid = $credentials['twilio_sid'];
        $token = $credentials['twilio_token'];
        $twilio = new Client($sid, $token);
    
        try {
            $twilio->messages->create(
                "whatsapp:" . $telefono, 
                [
                    'from' => "whatsapp:+14155238886", 
                    'body' => $mensaje
                ]
            );
            error_log("Mensaje enviado exitosamente a: " . $telefono);  // Log exitoso
            return true; 
        } catch (Exception $e) {
            error_log("Error al enviar mensaje: " . $e->getMessage()); // Log de error
            return false;
        }
    }
    

    public function confirmarReserva() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_reserva'])) {
            $id_reserva = $_POST['id_reserva'];
            $resultado = $this->model->confirmarReserva($id_reserva);
            
            // Generar mensaje para el usuario
            $mensaje = $resultado 
                ? "Reserva confirmada con éxito."
                : "Error al confirmar la reserva.";
            
            // Responder con un script de redirección y el mensaje emergente
            echo "<script>
                    alert('$mensaje');
                    window.location.href = '{$_SERVER['HTTP_REFERER']}';
                  </script>";
            exit; // Finaliza para evitar cualquier salida adicional
        }
    }

    public function mostrarHorasCanceladas() {
        // Obtener reservas canceladas desde el modelo
        $reservasCanceladas = $this->model->getReservasCanceladas();
        // Cargar la vista y pasarle las reservas canceladas
        require '../view/php/horas_canceladas.php'; // Asegúrate de que la ruta sea correcta
    }

    public function mostrarHorasUsuario() {
        if (isset($_SESSION['session_email'])) {
            $email = $_SESSION['session_email'];
    
            // Obtener las reservas del usuario
            $reservasUsuario = $this->model->getReservasUsuario($email);
    
            // Cargar la vista y pasarle las reservas del usuario
            require '../view/php/horas_usuario.php';
        } else {
            // Redirigir si no está autenticado
            echo "Debes iniciar sesión.";
            exit();
        }
    }
}


// Uso del controlador
$clientesController = new Clientes_controller();

// Acciones según las rutas
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    switch ($action) {
        case 'listar':
            $clientesController->cargarClientes();
            break;
        case 'agregar':
            $clientesController->mostrarFormulario();
            break;
        case 'insertar':
            $clientesController->agregarClientes();
            break;
        case 'actualizar':
            $clientesController->editarClientes();
            break;
        case 'verificar':
            $clientesController->verificarClientes();
            break;
        case 'cerrar':
            $clientesController->cerrarClientes();
            break;
        case 'reservar':
            $clientesController->reservar(); // Nueva acción para reservas
            break;
        case 'mostrar_reservas':
            $clientesController->mostrarReservas();
            break;
        case 'mostrar_horas_canceladas':
            $clientesController->mostrarHorasCanceladas();
            break;
        case 'mostrar_horas_usuario':
            $clientesController->mostrarHorasUsuario();
            break;
        case 'confirmar_reserva':
            $clientesController->confirmarReserva();
            break;
        case 'cancelar_reserva':
            $clientesController->cancelarReserva();
            break;case 'cancelar_reserva':
            $clientesController->cancelarReserva();
            break;
        case 'cancelar_reserva_usu':
            $clientesController->cancelarReservaUSU();
            break;
    }
} else {
    $clientesController->listarClientes();
}