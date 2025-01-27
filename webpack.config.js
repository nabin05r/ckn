const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
    entry: {
        main: './assets/js/ckn-main.js', // Entry point for JavaScript
        styles: './assets/css/style.css', // Entry point for CSS
    },
    output: {
        path: path.resolve(__dirname, 'dist'),
        filename: 'js/[name].min.js',
        clean: true, // Automatically clean the output directory
    },
    module: {
        rules: [
        {
            test: /\.css$/,
            use: [
            MiniCssExtractPlugin.loader, // Extract CSS into separate files
            'css-loader', // Turns CSS into JS modules
            ],
        },
        {
            test: /\.scss$/,
            use: [
            MiniCssExtractPlugin.loader,
            'css-loader',
            'sass-loader', // Compiles Sass to CSS
            ],
        },
        ],
    },
    plugins: [
    new MiniCssExtractPlugin({
        filename: 'css/[name].min.css', // Output for CSS
    }),
  ],
mode: 'development',
};
