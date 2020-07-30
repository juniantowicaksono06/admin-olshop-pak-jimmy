require('babel-polyfill');
var path = require('path');
var outputDir = path.resolve(__dirname, "assets/js/script");

module.exports = {
    "mode": "development",
    "entry": {
        "main" : ["babel-polyfill", path.resolve(__dirname, 'src/main.js')]
    },
    "output": {
        "path": outputDir,
        "filename": '[name].bundle.js'
    },
    "module": {
        "rules": [{
            "test": /\.js$/,
            "exclude": /node_modules/,
            "use": ['babel-loader']
        }]
    }
}