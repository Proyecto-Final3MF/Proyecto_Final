document.addEventListener("DOMContentLoaded", () => {
  // Hacemos fade in al cargar la página
  document.body.classList.add("fade-in");

  // Seleccionamos todos los enlaces internos
  const links = document.querySelectorAll("a");

  links.forEach(link => {
    link.addEventListener("click", e => {
      // Solo para enlaces que llevan a otra página de tu sitio
      if (link.hostname === window.location.hostname) {
        e.preventDefault(); // evitamos navegación inmediata
        document.body.classList.remove("fade-in");
        document.body.classList.add("fade-out");

        // Esperamos la duración del fade y luego cambiamos de página
        setTimeout(() => {
          window.location.href = link.href;
        }, 500); // coincide con el transition en CSS
      }
    });
  });
});

// 🔹 Importante: cuando vuelves atrás/adelante con el navegador
window.addEventListener("pageshow", (event) => {
  if (event.persisted) { 
    // Si la página viene de la caché del navegador, forzamos el fade-in
    document.body.classList.remove("fade-out");
    document.body.classList.add("fade-in");
  }
});

// 🔹 NUEVO: Animación al volver con el botón personalizado
const btnVolver = document.getElementById("btnVolver");

if (btnVolver) { // comprobamos que exista en la página
  btnVolver.addEventListener("click", (e) => {
    e.preventDefault();
    document.body.classList.remove("fade-in");
    document.body.classList.add("fade-out");

    setTimeout(() => {
      history.back();
    }, 500); // debe coincidir con la duración de tu transición CSS
  });
}