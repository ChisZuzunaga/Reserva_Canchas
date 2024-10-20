// Obtén la imagen y el input de archivo
var imagen = document.getElementById('f-perfil');
var inputFile = document.getElementById('input-imagen');

// Asocia el clic en la imagen al input file
imagen.addEventListener('click', function() {
    inputFile.click();
});

// Cambia la imagen mostrada cuando el usuario selecciona un archivo
inputFile.addEventListener('change', function() {
    if (inputFile.files && inputFile.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            imagen.src = e.target.result; // Cambia la fuente de la imagen a la imagen seleccionada
        };

        reader.readAsDataURL(inputFile.files[0]); // Lee el archivo como una URL de datos
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.getElementById('chk');
    const signupForm = document.getElementById('signupForm');
    const loginForm = document.getElementById('loginForm');

    // Función para deshabilitar todos los inputs de un formulario
    function disableForm(form, disable) {
        const inputs = form.querySelectorAll('input');
        inputs.forEach(input => {
            input.disabled = disable;
        });
    }

    // Escucha cambios en el checkbox
    checkbox.addEventListener('change', function() {
        if (checkbox.checked) {
            // Checkbox activado: Mostrar login, bloquear registro
            disableForm(signupForm, true);
            disableForm(loginForm, false);
        } else {
            // Checkbox desactivado: Mostrar registro, bloquear login
            disableForm(signupForm, false);
            disableForm(loginForm, true);
        }
    });

    // Inicialmente bloquea los inputs del formulario que no está activo
    if (checkbox.checked) {
        disableForm(signupForm, true);
        disableForm(loginForm, false);
    } else {
        disableForm(signupForm, false);
        disableForm(loginForm, true);
    }
});