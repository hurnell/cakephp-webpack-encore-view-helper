# WebpackEncoreViewHelper plugin for CakePHP

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require hurnell/cakephp-webpack-encore-view-helper
```

Make sure that composer.json of your CakePHP installation contains:
```
"minimum-stability": "dev",
"prefer-stable": true
```

## Initialize

In App/View/AppView add use statement and update initialize method:

```
use Hurnell\WebpackEncoreViewHelper\View\Helper\EncoreHelper

$this->loadHelper('Encore', [
    'className'=> EncoreHelper::class
]);
```

## Usage

In your .ctp files just add:
```
    <?= $this->Encore->load('build/js/entry.js') ?>
    <?= $this->Encore->load('build/js/entry.css') ?>
    <?= $this->Encore->load('build/css/main.css') ?>
```

These entrypoints are defined in your webpack.config.js file (see dist directory).

I have also included my package.json file in the dist directory to shows the packages I have installed to support this version of webpack.config.js. 

Note that the only way that I was able to get hot module replacement (auto reload web-page when you update asset files) to work was with:
```
yarn encore dev-server --port 8080 --disableHostCheck=true
```
There is a shortcut defined under scripts in package.json:
```
yarn dev-server
```

Check out [webpack-visualizer](https://chrisbateman.github.io/webpack-visualizer/) to help optimise your imports. Just run ```yarn stats ``` and upload the resulting stats.json file to this website.
