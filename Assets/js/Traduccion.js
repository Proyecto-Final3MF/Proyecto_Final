const traducciones = {
    'en': {
        startNow: "Start Now",
        p: "We help you find a technician to fix your device in record time.",
        h1: "Welcome to COSMOS",
        signin: "Sign In",
        signup: "Sign Up",
        startPage: "Start Page",
        contact: "Contact"
    },
    'es': {
        startNow: "Empezar Ahora",
        p: "Te ayudamos a encontrar un tecnico para arreglar tu dispositivo en tiempo record",
        h1: "Bienvenido a COSMOS",
        signin: "Iniciar Session",
        signup: "Registrarse",
        startPage: "Inicio",
        contact: "Contacto"
    }
};

function applicarTraduccion(lang) {
    document.querySelectorAll('[data-translate]').forEach(element => {
        const key = element.getAttribute('data-translate');
        if (traducciones[lang] && traducciones[lang][key]) {
            if (element.tagName === 'TITLE') {
                document.title = traducciones[lang][key];
            } else {
                element.textContent = traducciones[lang][key];
            }
        }
    });
}

function cambiarIdioma(lang) {
    localStorage.setItem('userLanguage', lang);
    document.documentElement.lang = lang;
    applicarTraduccion(lang);
}

document.addEventListener('DOMContentLoaded', () => {
    let idiomaElegido = localStorage.getItem('userLanguage');
    if (!idiomaElegido) {
        const idiomaNavegador = navigator.language || navigator.userLanguage;
        const shortidiomaNavegador = idiomaNavegador.split('-')[0];

        if (traducciones[idiomaNavegador]) {
            idiomaElegido = idiomaNavegador;
        } else if (traducciones[shortidiomaNavegador]) {
            idiomaElegido = shortidiomaNavegador;
        } else {
            idiomaElegido = 'es';
        }
    }

    cambiarIdioma(idiomaElegido);

    const SeleccionarIdioma = document.getElementById('language-select');
    if (SeleccionarIdioma) {
        SeleccionarIdioma.value = idiomaElegido;
    }
});

const SeleccionarIdioma = document.getElementById('language-select');
if (SeleccionarIdioma) {
    SeleccionarIdioma.addEventListener('change', (event) => {
        const IdiomaSeleccionado = event.target.value;
        cambiarIdioma(IdiomaSeleccionado);
    });
}
