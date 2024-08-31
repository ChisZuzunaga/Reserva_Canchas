<?php
require_once '../modelo/modelogod.php';

class Clientes_controller {
    private $model;

    public function __construct() {
        $this->model = new Clientes_Model();
    }

    public function cargarClientes() {
        // Obtiene la lista de profesores y la muestra en la vista correspondiente
        $clientes = $this->model->getClientes();
        include 'profesor_view2.php';
    }

    public function mostrarFormulario() {
        // Muestra el formulario para agregar un nuevo profesor
        include 'crear_profesor.php';
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
    }
} else {
    $clientesController->listarClientes();
}
