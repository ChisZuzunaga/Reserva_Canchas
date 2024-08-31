// Función para obtener los próximos 10 días desde la fecha actual
function obtenerProximosDias() {
    const dias = [];
    const hoy = new Date();
    for (let i = 0; i < 10; i++) {
        const dia = new Date();
        dia.setDate(hoy.getDate() + i);
        const diaString = dia.toISOString().split('T')[0];
        dias.push(diaString);
    }
    return dias;
}

// Función para cargar las fechas como botones
function cargarFechas() {
    const listadoFechas = document.getElementById('listadoFechas');
    const dias = obtenerProximosDias();

    dias.forEach(dia => {
        const botonFecha = document.createElement('button');
        botonFecha.textContent = dia;
        botonFecha.onclick = function() {
            mostrarHorasDisponibles(dia);
        };
        listadoFechas.appendChild(botonFecha);
    });
}

// Función para mostrar las horas disponibles para la fecha seleccionada
function mostrarHorasDisponibles(fecha) {
    const horasDisponibles = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'];
    const listaHoras = document.getElementById('listaHoras');
    const horasDiv = document.getElementById('horasDisponibles');

    listaHoras.innerHTML = '';  // Limpiar horas anteriores
    horasDisponibles.forEach(hora => {
        const horaItem = document.createElement('li');
        horaItem.textContent = hora;
        horaItem.className = 'hora-item';
        listaHoras.appendChild(horaItem);
    });

    horasDiv.style.display = 'block';
}

// Cargar fechas al inicio
cargarFechas();
