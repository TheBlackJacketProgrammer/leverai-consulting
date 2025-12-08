/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "../../application/views/**/*.php",
    "../../application/views/**/*.js",
    "../../assets/css/**/*.css",
    "../../assets/scss/**/*.scss"
  ],
  safelist: [
    {
      pattern: /.*/, // This will include all classes
    },
  ],
  theme: {
    extend: {
      colors: {
        primary: '#007bff',
        secondary: '#6c757d',
        success: '#28a745',
        danger: '#dc3545',
        warning: '#ffc107',
        info: '#17a2b8',
        light: '#f8f9fa',
        dark: '#343a40',
      },
      fontFamily: {
        sans: ['Arial', 'sans-serif'],
        heading: ['Helvetica Neue', 'sans-serif'],
        mono: ['Courier New', 'monospace'],
      },
      spacing: {
        '0': '0',
        '1': '0.25rem',
        '2': '0.5rem',
        '3': '0.75rem',
        '4': '1rem',
        '5': '1.25rem',
        '6': '1.5rem',
        '8': '2rem',
        '10': '2.5rem',
        '12': '3rem',
        '16': '4rem',
      },
    },
  },
  plugins: [],
} 