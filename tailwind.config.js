/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./**/*.php"],
  theme: {
    extend: {
      colors: {
        terracotta: {
          light: '#FAF3EC', /* Let's keep it clean */
          DEFAULT: '#D96E48',
          dark: '#B8522E',
        },
        clay: {
          DEFAULT: '#E77A53',
        },
        mustard: {
          light: '#F0C765',
          DEFAULT: '#E0A838',
          dark: '#B08020',
        },
        beige: {
          light: '#FCFAF7',
          DEFAULT: '#FAF3EC',
          dark: '#F0E6D8',
        },
        charcoal: {
          DEFAULT: '#1E2922',
        }
      },
      fontFamily: {
        serif: ['"Playfair Display"', 'Georgia', 'serif'],
        sans: ['"Outfit"', 'Inter', 'sans-serif'],
      }
    },
  },
  plugins: [],
}
