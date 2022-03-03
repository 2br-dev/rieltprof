const path = require('path');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');

//Наши основные сущности для левой панели
var app_entries = [
    './src/mobilemenu_source.js'
];

module.exports = {
    mode: 'production',
    entry: {
        app: app_entries
    },
    output: {
        filename: 'mobilemenu.js',
        path: path.resolve(__dirname, 'dist')
    },
    plugins: [
        new CleanWebpackPlugin()
    ],
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: "babel-loader",
                    options: {
                        presets: [
                            ['@babel/preset-env', {
                                useBuiltIns: "entry", // do I need this?
                                modules: false,
                                targets: {
                                    browsers: [
                                        "last 2 versions",
                                        "IE >= 11"
                                    ],
                                },
                            }],
                        ],
                    }
                }
            }
        ]
    }
};
