/**
 * Файл финальной версии JS для рабочей версии
 * @type {merge}
 */
const merge  = require('webpack-merge');
const common = require('./webpack.common.js');
const TerserPlugin = require('terser-webpack-plugin');
const CompressionPlugin = require('compression-webpack-plugin');

module.exports = merge(common, {
   mode: 'production',
   devtool: 'source-map',
   optimization: {
      minimizer: [new TerserPlugin({
         parallel: true,
         extractComments: true,
         sourceMap: true
      })]
   }
});

module.exports.plugins.push(new CompressionPlugin());