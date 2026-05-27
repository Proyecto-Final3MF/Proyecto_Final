const translations = {
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

function applyTranslations(lang) {
    document.querySelectorAll('[data-translate]').forEach(element => {
        const key = element.getAttribute('data-translate');
        if (translations[lang] && translations[lang][key]) {
            if (element.tagName === 'TITLE') {
                document.title = translations[lang][key];
            } else {
                element.textContent = translations[lang][key];
            }
        }
    });
}

function changeLanguage(lang) {
    localStorage.setItem('userLanguage', lang);
    document.documentElement.lang = lang;
    applyTranslations(lang);
}

document.addEventListener('DOMContentLoaded', () => {
    let userPreferredLanguage = localStorage.getItem('userLanguage');
    if (!userPreferredLanguage) {
        const browserLanguage = navigator.language || navigator.userLanguage;
        const shortBrowserLanguage = browserLanguage.split('-')[0];

        if (translations[browserLanguage]) {
            userPreferredLanguage = browserLanguage;
        } else if (translations[shortBrowserLanguage]) {
            userPreferredLanguage = shortBrowserLanguage;
        } else {
            userPreferredLanguage = 'es';
        }
    }

    changeLanguage(userPreferredLanguage);

    const languageSelect = document.getElementById('language-select');
    if (languageSelect) {
        languageSelect.value = userPreferredLanguage;
    }
});

const languageSelect = document.getElementById('language-select');
if (languageSelect) {
    languageSelect.addEventListener('change', (event) => {
        const selectedLanguage = event.target.value;
        changeLanguage(selectedLanguage);
    });
}