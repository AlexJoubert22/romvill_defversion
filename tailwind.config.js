/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',
    content: [
        './**/*.php',
        './assets/js/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                'primary':           '#135bec',
                'primary-dark':      '#0d3c9e',
                'secondary':         '#D4AF37',
                'secondary-ink':     '#8A6B18',
                'background-light':  '#f8f9fc',
                'background-dark':   '#000000',
                'slate-dark':        '#141414',
                // Fondos oscuros del tema: negro de verdad, no azul marino.
                // Solo se pisan los tonos de FONDO; 50-600 (textos) siguen siendo slate.
                'slate': {
                    '700': '#2E2E2E',
                    '800': '#141414',
                    '900': '#0A0A0A',
                    '950': '#000000',
                },
            },
            fontFamily: {
                'display': ['Manrope', 'sans-serif'],
                'serif':   ['Playfair Display', 'serif'],
            },
            borderRadius: {
                DEFAULT: '0.25rem',
                lg:      '0.5rem',
                xl:      '0.75rem',
                full:    '9999px',
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
};
