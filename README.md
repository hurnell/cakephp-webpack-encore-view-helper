# WebpackEncoreViewHelper plugin for CakePHP

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require hurnell/cakephp-webpack-encore-view-helper
```

## Initialize

Update initialize function in App/View/AppView:

```
$this->loadHelper('Encore', [
    'className'=>'Hurnell\WebpackEncoreViewHelper\View\Helper\EncoreHelper'
]);
```

## Usage