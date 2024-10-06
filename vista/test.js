document.addEventListener("DOMContentLoaded", function() {
    const toggles = document.querySelectorAll(".toggle-icon");

    toggles.forEach(function(toggle) {
        toggle.addEventListener("click", function() {
            // Encuentra el contenido relacionado con el ícono
            const contenrt = this.parentElement.nextElementSibling;

            // Alterna la visibilidad del contenido
            contenrt.classList.toggle("hidden");

            // Alterna la dirección del ícono
            this.classList.toggle("fa-chevron-down");

            // Encuentra la tarjeta y cambia su tamaño
            const card = this.closest('.card1, .card2, .card3');
            card.classList.toggle("card-small");
        });
    });
});
