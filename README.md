# Phalcon Breadcrumbs

[![Build Status](https://travis-ci.org/phalcongelist/breadcrumbs.svg?branch=master)](https://travis-ci.org/phalcongelist/breadcrumbs)

Phalcon Breadcrumbs is a powerful and flexible component for building site breadcrumbs.
You can adapt it to your own needs or improve it if you want.

Please write us if you have any feedback.

Thanks!

## NOTE

The master branch will always contain the latest stable version. If you wish
to check older versions or newer ones currently under development, please
switch to the relevant branch/tag.

## Get Started

### Requirements

To use this component, you need at least:

* [Composer][:composer:]
* PHP >= 5.4
* Latest stable [Phalcon Framework release][:phalcon:] extension enabled

### Installing

Install composer in a common location or in your project:

```sh
$ curl -s http://getcomposer.org/installer | php
```

Create the composer.json file as follows:

```json
{
    "require": {
        "phalcongelist/breadcrumbs": "dev-master"
    }
}
```

Run the composer installer:

```sh
$ php composer.phar install
```

### Usage

We recommend registering it with your application's services for even easier use:

```php
use Phalcon\Breadcrumbs;

// Initialize the Breadcrumbs component.
$di->setShared('breadcrumbs', function () {
    return new Breadcrumbs;
});
```

#### Adding a crumb with a link

```php
$this->breadcrumbs->add('/', 'Home');
```

#### Adding a crumb without a link (Normally the last one)

```php
$this->breadcrumbs->add(null, 'User', false);
```

#### Render crumbs

```php
$this->breadcrumbs->render();
```

## Copyright

Phalcon Breadcrumbs is open-sourced software licensed under the [New BSD License][:license:].
© Phalcon Framework Team and contributors

[:composer:]: https://getcomposer.org/
[:phalcon:]: https://github.com/phalcon/cphalcon/releases
[:license:]: https://github.com/phalcongelist/breadcrumbs/blob/master/docs/LICENSE.md
