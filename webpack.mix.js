let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.webpackConfig({
  output: {
    publicPath: "/",
    chunkFilename: 'js/[name].[chunkhash].js'
  },
  resolve: {
    alias: {
      'components': 'assets/js/components',
      'config': 'assets/js/config',
      'plugins': 'assets/js/plugins',
      'views': 'assets/js/views',
      'styles': 'assets/js/styles'
    },
    modules: [
      'node_modules',
      path.resolve(__dirname, "resources")
    ]
  },
});


mix.js('resources/assets/js/admin.js', 'public/js/admin/admin.js').combine([
	'node_modules/font-awesome/css/font-awesome.min.css',
	'node_modules/ionicons/dist/css/ionicons.min.css',
	'node_modules/element-ui/lib/theme-default/index.css',
	'resources/assets/sass/app.scss'
	],'public/css/admin.css');