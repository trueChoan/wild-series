module.exports = {
  content: [
    'templates/**/*.html.twig',
    'assets/js/**/*.js',
    "./node_modules/flowbite/**/*.js",

  ],
  theme: {
    extend: {
      colors:  {
        'yellow' : '#FBD160',
        'dark' : '#16181E',
        'green': '#00B9AE',
        'light-grey' : '#E0E0E0',
      },
      fontFamily: {
        'bangers': ['Bangers', 'cursive'],
        'baloo': ['"Baloo 2"', 'cursive']
      },
      maxWidth: {
        'xs': '15rem',
      },
      width: {
        '1/1': '75%',
      }
    },
  },
  plugins: [
    require('flowbite/plugin')
  ],
}
