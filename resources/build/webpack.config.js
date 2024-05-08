const path = require('path');

module.exports = {
  // Set the mode to 'development' or 'production'
  mode: 'development',

  // Entry point of your application
  entry: '../js/index.js',

  // Output configuration
  output: {
    filename: 'bundle.js', // Output file name
    path: path.resolve(__dirname, 'dist'), // Output directory
  },

  // Configuration for development server
  devServer: {
    static: {
      directory: path.join(__dirname, 'dist'),
    },
    compress: true,
    port: 9000,
  },
};
