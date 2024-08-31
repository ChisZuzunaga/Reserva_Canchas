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
