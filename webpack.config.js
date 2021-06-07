var Encore = require('@symfony/webpack-encore');

// Let PHPStorm load the webpack configuration correctly
if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps()
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // Enable it for all builds with the
    // default hash algorithm (sha384)
    .enableIntegrityHashes(Encore.isProduction)

    // uncomment to define the assets of the project
    .addEntry('app', [
      './assets/js/app.js',
      './assets/js/_fos_js_routes.js'
    ])
    .addEntry('double-column', './assets/js/doubleColumn.js')
    .addEntry('content', [
      './assets/js/content.js',
      'symfony-collection/jquery.collection.js'
    ])
    .addEntry('analytics', [
      './assets/js/analytics/main.js'
    ])
    .addEntry('ckeditorPatches', [
      './assets/js/ckeditorPatches.js'
    ])
    .addEntry('ckeditorContents', [
      './assets/css/ckeditor/ckeditor.scss'
    ])

    .addEntry('vendor', './assets/js/vendor.js')

    // Elfinder dedicated assets
    .copyFiles([
      {
        from: './node_modules/requirejs/',
        to: 'elfinder/requirejs/[path][name].[hash:8].[ext]',
        pattern: /require\.js$/
      },
      {from: './node_modules/jquery/dist', to: 'els/jquery/[path][name].[ext]', pattern: /jquery\.min\.js$/},
      {
        from: './node_modules/jquery-ui-dist',
        to: 'els/jquery-ui/[path][name].[ext]',
        pattern: /jquery-ui\.min\.(js|css)$/
      },
      {
        from: './node_modules/jquery-ui-themes/themes/smoothness',
        to: 'els/jquery-ui/themes/smoothness/[path][name].[ext]'
      },
      {from: './vendor/studio-42/elfinder/css', to: 'els/css/[path][name].[ext]'},
      {from: './vendor/studio-42/elfinder/img', to: 'els/img/[path][name].[ext]'},
      {from: './vendor/studio-42/elfinder/js', to: 'els/js/[path][name].[ext]'},
      {from: './vendor/studio-42/elfinder/sounds', to: 'els/sounds/[path][name].[ext]'}
    ])

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    // Enable typescript
    .enableTypeScriptLoader()

    // uncomment if you use Sass/SCSS files
    .enableSassLoader()

    // uncomment for legacy applications that require $/jQuery as a global variable
    .autoProvidejQuery()

    // Provide popper global var for bootstrap
    .autoProvideVariables({
      Popper: ['popper.js', 'default']
    })

    .configureDevServerOptions(function (options) {
      options.disableHostCheck = true;
    })

    // Fixes CSS HMR
    // See https://github.com/symfony/webpack-encore/issues/348
    .disableCssExtraction(Encore.isDevServer())
;

module.exports = Encore.getWebpackConfig();
