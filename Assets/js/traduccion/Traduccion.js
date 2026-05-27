let traducciones = {};

async function cambiarIdioma(idioma) {
    try {
        const modulo = await import(`./locales/${idioma}.js`);
        traducciones = modulo.default;

        localStorage.setItem('userLanguage', idioma);
        document.documentElement.lang = idioma
        
        applicarTraduccion();
    } catch (error) {
        console.error(`Erro ao carregar o idioma: ${idioma}`, error);
        if (idioma !== 'es') cambiarIdioma('es');
    }
}

function applicarTraduccion() {
    document.querySelectorAll('[data-translate]').forEach(element => {
        const key = element.getAttribute('data-translate');
        if (traducciones[key]) {
            if (element.tagName === 'TITLE') {
                document.title = traducciones[key];
            } else {
                element.textContent = traducciones[key];
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    let idiomaElegido = localStorage.getItem('userLanguage');
    
    if (!idiomaElegido) {
        const idiomaNavegador = navigator.language || navigator.userLanguage;
        const shortidiomaNavegador = idiomaNavegador.split('-')[0];

        const idiomasSoportados = ['en', 'es', 'pt'];

        if (idiomasSoportados.includes(idiomaNavegador)) {
            idiomaElegido = idiomaNavegador;
        } else if (idiomasSoportados.includes(shortidiomaNavegador)) {
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