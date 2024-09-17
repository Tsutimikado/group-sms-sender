/** @type {import('tailwindcss').Config} */

module.exports = {
  content: [
    "./src/**/*.{js,jsx,ts,tsx}",
  ],
  theme: {
    extend: {
      keyframes: {
        wiggle: {
          '0%, 50%': { transform: 'translateX(15px)', filter:'blur(1)' },
          '25%': { transform: 'translateX(-15px)', filter: 'blur(1)' },
          '100%': {transform: 'translateX(0)'}
          // '100%': { transform: 'translateX(0)' }
        },
        appear: {
          '0%' : {opacity: '0'},
          '100%' : {opacity: '1'}
        },
        disappear: {
          '0%': { opacity: '1' },
          '100%': { opacity: '0' }
        },
        appearDropUp: {
          '0%': {transform: 'translateY(35px)'},
          '100%': {transform: 'translateY(0)'}
        },
        appearDropDown: {
          '0%': { transform: 'translateY(-35px)' },
          '100%': { transform: 'translateY(0)' }
        },
        appearDropLeft: {
          '0%': { transform: 'translateX(27px)' },
          '100%': { transform: 'translateX(0)' }
        },
        appearDropRight: {
          '0%': { transform: 'translateX(-27px)' },
          '100%': { transform: 'translateX(0)' }
        },
        expandY: {
          '0%': {transform:'scaleY(0)'},
          '100%': { transform: 'scaleY(1)' }
        }
      },
      animation: {
        wiggle: 'wiggle 0.32s ease-out',
        appear: 'appear 0.35s  ease-in',
        disappear: 'disappear 0.2s ease-out',
        appearDropUp: 'appearDropUp 0.35s ease-in',
        appearDropDown: 'appearDropDown 0.35s ease-in',
        appearDropLeft: 'appearDropLeft 0.35s ease-in',
        appearDropRight: 'appearDropRight 0.35s ease-in',
        expandY: 'expandY 0.2s ease-out'
      }
    }
  },
  variants: {},
  plugins: [
  ],
}