
const path = require('path');
const webpack = global.webpack;
const config = global.webpackDevConfig;

config.plugins.push(
    new webpack.optimize.OccurenceOrderPlugin(),

    new webpack.optimize.UglifyJsPlugin({
        compressor: {
            warnings: false
        }
    }),

    new webpack.DefinePlugin({
        'process.env': {
            NODE_ENV: JSON.stringify('production')
        }
    })
);

config.watch = false;
config.devtool = false;

config.externals = {
    react: 'React',
    'react-dom': 'ReactDOM',
};

config.output.filename = '[name].js';

module.exports = config;
