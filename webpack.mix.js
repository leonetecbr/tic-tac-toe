const mix = require('laravel-mix');

mix.webpackConfig({
    stats: {
        children: true,
    },
});

mix.copy('node_modules/bootstrap/dist/css/bootstrap.min.css', '/css/bootstrap.min.css')
    .copy('node_modules/bootstrap/dist/css/bootstrap.min.css.map', 'css/bootstrap.min.css.map')
    .copy('node_modules/bootstrap-icons/font/bootstrap-icons.css', 'css/bootstrap-icons.css').sourceMaps()
    .copy('node_modules/bootstrap-icons/font/fonts/bootstrap-icons.woff', 'css/fonts/bootstrap-icons.woff')
    .css('resources/css/index.css', 'css/index.min.css').sourceMaps()
    .copy('node_modules/jquery/dist/jquery.min.js', 'js/jquery.min.js')
    .copy('node_modules/bootstrap/dist/js/bootstrap.bundle.min.js', 'js/bootstrap.bundle.min.js')
    .copy('node_modules/bootstrap/dist/js/bootstrap.bundle.min.js.map', 'js/bootstrap.bundle.min.js.map')
    .js('resources/js/index.js', 'js/index.min.js').sourceMaps()
    .disableNotifications();