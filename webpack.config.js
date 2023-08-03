const Encore = require('@symfony/webpack-encore');
const path = require('path');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
// This is used by PHPStorm
if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */

    // uncomment to define the assets of the project
    .addEntry('app', [
      './assets/js/app.js'
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
    .addEntry('openapi', './assets/js/openapi.js')
    .addEntry('ckeditor', './assets/js/ckeditor.js')

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
      {from: './vendor/studio-42/elfinder/sounds', to: 'els/sounds/[path][name].[ext]'},
      {
        from: './node_modules/@utwente/dotron-app/lib/',
        to: 'dotron/[name].[hash:8].[ext]'
      }
    ])

    // See https://symfony.com/bundles/FOSCKEditorBundle/current/installation.html#using-webpack-encore
    .copyFiles([
      {from: './node_modules/ckeditor4/', to: 'ckeditor/[path][name].[ext]', pattern: /\.(js|css)$/, includeSubdirectories: false},
      {from: './node_modules/ckeditor4/adapters', to: 'ckeditor/adapters/[path][name].[ext]'},
      {from: './node_modules/ckeditor4/lang', to: 'ckeditor/lang/[path][name].[ext]'},
      {from: './node_modules/ckeditor4/plugins', to: 'ckeditor/plugins/[path][name].[ext]'},
      {from: './node_modules/ckeditor4/skins', to: 'ckeditor/skins/[path][name].[ext]'},
      {from: './node_modules/ckeditor4/vendor', to: 'ckeditor/vendor/[path][name].[ext]'}
    ])

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    // .enableBuildNotifications()
    .enableSourceMaps()
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())
    // Enable typescript
    .enableTypeScriptLoader()

    // uncomment if you use Sass/SCSS files
    .enableSassLoader()

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

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    .enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()
;

const webpackConfig = Encore.getWebpackConfig();

// Add aliases
webpackConfig.resolve.alias['@'] = path.resolve(__dirname, 'assets/js');
webpackConfig.resolve.alias['@fos'] = path.resolve(__dirname, 'vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js');

module.exports = webpackConfig;
