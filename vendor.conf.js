const config = require('./bol-build.conf.js');

const bowerComponents = config.paths.bowerComponents;
const nodeModules = config.paths.nodeModules;

module.exports = {
    getStyles: function() {
        return [
            nodeModules + '/react-select2-wrapper/css/select2.css'
        ];
    },

    getScripts: function() {
        return [
        ];
    }
};
