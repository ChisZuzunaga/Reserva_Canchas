<?php
require_once '../modelo/modelogod.php';

class Clientes_controller {
    private $model;

    public function __construct() {
        $this->model = new Clientes_Model();
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
        header("Location: ../vista/iniciar.php");
        exit();
    }

    public function agregarClientes() {
        session_start();
        // Obtiene los datos del formulario y los inserta en la base de datos
        $nombrer = $_POST['nombre'];
        $apellidor = $_POST['apellido'];
        $emailr = $_POST['email'];
        $claver = $_POST['clave'];
        $numeror = $_POST['numero'];
        // Manejo de la imagen subida
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $nombreImagen = $_FILES['imagen']['name'];
            $rutaTemporal = $_FILES['imagen']['tmp_name'];
            $directorioDestino = '../uploads/'.$nombreImagen;
    
            // Mover el archivo subido a la ubicación definitiva
            if (move_uploaded_file($rutaTemporal, $directorioDestino)) {
                $imagenRuta = $directorioDestino;
            } else {
                echo "Error al mover la imagen";
                return;
            }
        } else {
            echo "Error al subir la imagen";
            return;
        }
        
        $resultado = $this->model->insertClientes($nombrer, $apellidor, $emailr, $claver, $numeror, $imagenRuta);

        if ($resultado) {
            echo "Usuario agregado correctamente";
            $_SESSION['session_email']= $emailr;
            $_SESSION['session_nombre'] = $nombrer;
            $_SESSION['ruta_imagen'] = $imagenRuta;
            header("Location: ../vista/inicio.php");
        } else {
            echo "Error al crear Usuario";
        }
    }

    public function verificarClientes() {
        session_start();
        $email = $_POST['rer_email'];
        $clave = $_POST['rer_clave'];
    
        $cliente = $this->model->verifysClientes($email);
    
        if ($cliente) {
            if ($clave == $cliente['Clave']) {
                echo "Inicio de sesión exitoso";
                $_SESSION['session_email'] = $email;
                $_SESSION['session_nombre'] = $cliente['Nombre'];
                $_SESSION['ruta_imagen'] = $cliente['Imagen'];
                header("Location: ../vista/inicio.php");
            } else {
                echo "Error: Contraseña incorrecta";
            }
        } else {
            echo "Error: Usuario no encontrado";
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

            $resultado = $this->model->reservarCancha($cancha_id, $fecha, $hora_inicio, $duracion, $precio);

            echo $resultado ? "Reserva realizada con éxito." : "El horario no está disponible.";
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
            header("Location: ../vista/disponibilidad.php");
            exit();
        } else {
            // Si no hay fecha o cancha_id en el POST, redirigir a la página de reservas
            header("Location: ../vista/reservar.php");
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
            header("Location: ../vista/disponibilidad.php");
            exit();
        } else {
            // Si no hay fecha o cancha_id en el POST, redirigir a la página de reservas
            header("Location: ../vista/reservar.php");
            exit();
        }
    }

    public function cancelarReserva() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_reserva'])) {
            $id_reserva = $_POST['id_reserva'];
            $resultado = $this->model->cancelarReserva($id_reserva);
            
            echo $resultado ? "Reserva cancelada con éxito." : "Error al cancelar la reserva.";
        }
    }
    
    public function confirmarReserva() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_reserva'])) {
            $id_reserva = $_POST['id_reserva'];
            $resultado = $this->model->confirmarReserva($id_reserva);
            
            echo $resultado ? "Reserva confirmada con éxito." : "Error al confirmar la reserva.";
        }
    }

    public function mostrarHorasCanceladas() {
        // Obtener reservas canceladas desde el modelo
        $reservasCanceladas = $this->modelo->getReservasCanceladas();
        
        // Cargar la vista y pasarle las reservas canceladas
        require '../vista/horas_canceladas.php'; // Asegúrate de que la ruta sea correcta
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
            
    }
} else {
    $clientesController->listarClientes();
}