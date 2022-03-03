/**
 * Файл финальной версии JS для версии для разработчиков
 * @type {merge}
 */
const merge  = require('webpack-merge');
const common = require('./webpack.common.js');

module.exports = merge(common, {
   mode: 'development',
   devtool: 'inline-source-map'
});