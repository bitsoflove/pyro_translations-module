const path = require('path');
const fs = require('fs');
const envConfig = fs.existsSync(path.join('.', '.env.js')) ? require('./.env.js') : {};

const publicPath = 'assets/bitsoflove/translations-module';

const publicFullPath = '../../../../public/' + publicPath;
const nodeModules =  './node_modules';
const bowerComponents = publicFullPath + '/bower_components';


module.exports = {
    host: envConfig.host,
    browserSync: {
        proxy: envConfig.proxy,
        port: envConfig.port,
        notify: false
    },
    clean: [
        publicFullPath + '/scripts',
        publicFullPath + '/styles'
    ],
    paths: {
        nodeModules: nodeModules,
        bowerComponents: bowerComponents,
        publicPath: publicPath,
        publicFullPath: publicFullPath
    },
    sass: {
        src: './resources/scss/**/*.scss',
        watch: './resources/scss/**/*.scss',
        includePaths: [
            bowerComponents,
            nodeModules
        ]
    },
    cdn: '',
    vendor: 'vendor.conf.js',
    destinations: {
        cwd: publicFullPath,
        scripts: 'scripts',
        styles: 'styles'
    },
    watch: {
        fullReloadPaths: [
            './resources/views/**/*.twig'
        ]
    },
    styleModules: {
        enabled: true,
        includePaths: [
          bowerComponents,
        ],
        sassResources: [
            path.resolve('resources/scss/_sass-resources.scss')
        ]
    },
    webpack: {
        dev: 'webpack-dev.conf.js',
        dist: 'webpack-dist.conf.js',
        hot: {
            outputPath: publicPath + '/scripts'
        },
        options: {
          include: 'resources',
        }
    }
};
