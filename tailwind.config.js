const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
        },

        colors:{
            'green': '#0C4B05',
            'bar': '#989494',
            'white': '#FFFFFF',
            'yellow': '#FFCD00',
            'nav1': '#696767',
            'gray': '#D9D9D9',
            'red': '#E52B50',
            'black': '#000000',
        },
    },


};
