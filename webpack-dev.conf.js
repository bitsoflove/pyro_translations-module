const path = require('path');
const config = require('./bol-build.conf.js');

module.exports = {
    cache: true,
    devtool: 'eval',
    watch: true,
    entry: {
          translations: './resources/js/src/index.js'
    },
    output: {
        publicPath: config.paths.publicPath,
        filename: '[name].js',
        chunkFilename: '[chunkhash].js'
    },
    module: {
        noParse: [],
        loaders: [{
            test: /\.jsx?$/,
            include: path.resolve('./resources/js/src'),
            loader: 'babel',
            query: {}
        }]
    },
    resolve: {
        extensions: ['', '.jsx', '.js'],
        alias: {}
    },
    plugins: []
};
