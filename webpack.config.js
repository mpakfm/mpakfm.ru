/* подключим плагин */
var Encore = require('@symfony/webpack-encore');
var CopyWebpackPlugin = require('copy-webpack-plugin'); // this line tell to webpack to use the plugin


Encore
    /* Установим путь куда будет осуществляться сборка */
    .setOutputPath('public/build/')
    /* Укажем web путь до каталога web/build */
    .setPublicPath('/build')
    /* Каждый раз перед сборкой будем очищать каталог /build */
    .cleanupOutputBeforeBuild()
    /* --- Добавим основной JavaScript в сборку --- */
    .addEntry('scripts', './assets/app.js')
    /* Добавим наш главный файл ресурсов в сборку */
    .addStyleEntry('styles', './assets/app.scss')
    /* Включим поддержку sass/scss файлов */
    .enableSassLoader()
    /**/
    .disableSingleRuntimeChunk()
    /**/
    .autoProvidejQuery()
    /**/
    /*
    .addPlugin(new CopyWebpackPlugin([
        { from: './assets/dist/images', to: 'images' }
    ]))
    */
    .addPlugin(new CopyWebpackPlugin({
        patterns: [
            { from: './assets/dist/images', to: 'images' }
        ]
    }))

    /* В режиме разработки будем генерировать карту ресурсов */
    .enableSourceMaps(!Encore.isProduction());

/* Экспортируем финальную конфигурацию */
module.exports = Encore.getWebpackConfig();
