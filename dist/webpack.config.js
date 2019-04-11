const Encore = require('@symfony/webpack-encore');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const WebpackBuildNotifierPlugin = require('webpack-build-notifier');
const jsonImporter = require('node-sass-json-importer');
Encore
// the project directory where all compiled assets will be stored
.setOutputPath('webroot/build/')

// the public path used by the web server to access the previous directory
.setPublicPath('/build')

.addEntry('script', './assets/js/script.js')

.addStyleEntry('css/style', './assets/css/style.scss')

// fixes modules that expect jQuery to be global
.autoProvidejQuery()

.addPlugin(new CopyWebpackPlugin([
	// copies to {output}/static
	{
		from: './assets/static',
		to: 'static'
	}
]))

.addPlugin(new WebpackBuildNotifierPlugin({
	title: "My CakePHP Project",
	//logo: path.resolve("./img/my-cakephp-project.png"),
	//  successIcon: path.resolve("./img/my-cakephp-project.png"),
	warningSound: false
}))

.enableSassLoader(function(options){
	options.importer = jsonImporter()
})

.enableSourceMaps(!Encore.isProduction())

.cleanupOutputBeforeBuild()

.enableVersioning(!Encore.isDevServer())
;
module.exports = Encore.getWebpackConfig();
