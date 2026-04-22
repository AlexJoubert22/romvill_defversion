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
                'secondary':         '#BFA15F',
                'background-light':  '#f8f9fc',
                'background-dark':   '#101622',
                'slate-dark':        '#1e293b',
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
