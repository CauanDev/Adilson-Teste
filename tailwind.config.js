/** @type {import('tailwindcss').Config} */
const withMT = require("@material-tailwind/html/utils/withMT");

module.exports = withMT({
  content: [
    "./resources/**/*.vue",
    "./resources/**/*.js",
    "./resources/**/*.html",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
})
