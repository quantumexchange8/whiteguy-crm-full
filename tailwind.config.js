const defaultTheme = require('tailwindcss/defaultTheme')

/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.{vue,js,jsx}',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                dark: {
                    'eval-0': '#151823',
                    'eval-1': '#222738',
                    'eval-2': '#2A2F42',
                    'eval-3': '#2C3142',
                },
            },
        },
        screens: {
          '2xs': '320px',
          // => @media (min-width: 320px) { ... }

          'xs': '475px',
          // => @media (min-width: 475px) { ... }

          'sm': '640px',
          // => @media (min-width: 640px) { ... }
    
          'md': '768px',
          // => @media (min-width: 768px) { ... }
    
          'lg': '1024px',
          // => @media (min-width: 1024px) { ... }
    
          'xl': '1280px',
          // => @media (min-width: 1280px) { ... }
    
          '2xl': '1536px',
          // => @media (min-width: 1536px) { ... }
        },
        keyframes: {
            scaleAnimation: {
                '0%': { 
                    opacity: 0,
                    transform: 'scale(1.5)',
                },
                '100%': { 
                    opacity: 1,
                    transform: 'scale(1)',
                },
            },
            fadeIn: {
                '0%': { 
                    opacity: 0,
                },
                '100%': { 
                    opacity: 1,
                },
            },
            fadeOut: {
                '0%': { 
                    opacity: 1,
                },
                '100%': { 
                    opacity: 0,
                },
            },
            drawCircle: {
                '0%': { 
                    strokeDashoffset: '151',
                },
                '100%': { 
                    strokeDashoffset: '0',
                },
            },
            drawCheck: {
                '0%': { 
                    strokeDashoffset: '36',
                },
                '100%': { 
                    strokeDashoffset: '0',
                },
            },
            drawX: {
                '0%': { 
                    strokeDashoffset: '0',
                },
                '100%': { 
                    strokeDashoffset: '1000',
                },
            },
            shake: {
                '0%': {
                    transform: 'rotate(5deg)',
                },
                '25%': {
                    transform: 'rotate(-6deg)',
                },
                '50%': {
                    transform: 'rotate(5deg)',
                },
                '75%': {
                    transform: '-rotate(-6deg)',
                },
                '100%': {
                    transform: 'rotate(5deg)',
                },
            },
        },
        animation: {
            scaleAnimation: 'scaleAnimation 1s ease-in-out',
            fadeIn: 'fadeIn 1s ease-in-out',
            fadeOut: 'fadeOut 1s ease-in-out',
            drawCircle: 'drawCircle 1.5s ease-in-out',
            drawCheck: 'drawCheck 1.3s ease-in',
            drawX: 'drawX 1.3s ease-in',
            shake: 'shake 1s linear infinite',
        },
    },

    plugins: [require('@tailwindcss/forms')],
}
