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
                'primary':           '#F0C24A',
                'primary-dark':      '#B8862B',
                'secondary':         '#F0C24A',   // oro brillante (sobre negro)
                'secondary-ink':     '#8A6B18',
                'background-light':  '#f8f9fc',
                'background-dark':   '#000000',
                'slate-dark':        '#0A0A0A',
                // Fondos oscuros del tema: negro de verdad, no azul marino.
                // Solo se pisan los tonos de FONDO; 50-600 (textos) siguen siendo slate.
                'slate': {
                    '50':  '#FAF9F7',
                    '100': '#F3F1ED',
                    '200': '#E6E3DD',
                    '700': '#1C1C1C',
                    '800': '#0A0A0A',
                    '900': '#000000',
                    '950': '#000000',
                },
            },
            fontFamily: {
                'display': ['Onest', 'ui-sans-serif', 'system-ui', '-apple-system', 'Segoe UI', 'sans-serif'],
                'serif':   ['Cormorant Garamond', 'Georgia', 'Times New Roman', 'serif'],
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
