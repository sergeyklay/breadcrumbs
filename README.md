# Phalcon Breadcrumbs [![Build Status](https://travis-ci.org/phalcongelist/breadcrumbs.svg?branch=master)](https://travis-ci.org/phalcongelist/breadcrumbs)

![Breadcrumbs screenshot](https://github.com/phalcongelist/breadcrumbs/blob/master/docs/breadcrumbs.png)

Phalcon Breadcrumbs is a powerful and flexible component for building site breadcrumbs.
You can adapt it to your own needs or improve it if you want.

Please write us if you have any feedback.

Thanks!

## NOTE

The master branch will always contain the latest stable version. If you wish
to check older versions or newer ones currently under development, please
switch to the relevant branch/tag.

## Getting Started

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

### Define your breadcrumbs

We recommend registering it with your application's services for even easier use:

```php
use Phalcon\Breadcrumbs;

// Initialize the Breadcrumbs component.
$di->setShared('breadcrumbs', function () {
    return new Breadcrumbs;
});
```

**Adding a crumb with a link:**

```php
$this->breadcrumbs->add('Home', '/');
```

**Adding a crumb without a link (normally the last one):**

```php
$this->breadcrumbs->add('User', null, ['linked' => false]);
```

**Output crumbs:**

Php Engine
```php
<ol class="breadcrumb">
    <?php $this->breadcrumbs->output(); ?>
</ol>
```

Volt Engine
```volt
<ol class="breadcrumb">
  {{ breadcrumbs.output() }}
</ol>
```

**Change crumb separator:**

```php
$this->breadcrumbs->setSeparator(' &raquo; ');
```

**Delete a crumb (by url):**

```php
$this->breadcrumbs->remove('/admin/user/create');

// remove a crumb without an url
$this->breadcrumbs->remove(null);
```

**Add multi-language support:**

```php
use Phalcon\Translate\Adapter\NativeArray as Translator;
use Phalcon\Breadcrumbs;

$messages = [
    'crumb-home'     => 'Home',
    'crumb-user'     => 'User',
    'crumb-settings' => 'Settings',
    'crumb-profile'  => 'Profile',
];

// Initialize the Translate adapter.
$di->setShared('translate', function () {
    return new Translator(['content' => $messages]);
});

// Initialize the Breadcrumbs component.
$di->setShared('breadcrumbs', function () {
    return new Breadcrumbs;
});
```

**Custom logging when errors happen:**

```php
use Phalcon\Logger\Formatter\Line as FormatterLine;
use Phalcon\Logger\Adapter\File as FileLogger;
use Phalcon\Breadcrumbs;

// Initialize the Logger.
$di->setShared('logger', function ($filename = null, $format = null) use ($config) {
    $format   = $format ?: $config->get('logger')->format;
    $filename = trim($filename ?: $config->get('logger')->filename, '\\/');
    $path     = rtrim($config->get('logger')->path, '\\/') . DIRECTORY_SEPARATOR;

    $formatter = new FormatterLine($format, $config->get('logger')->date);
    $logger = new FileLogger($path . $filename);

    $logger->setFormatter($formatter);
    $logger->setLogLevel($config->get('logger')->logLevel);

    return $logger;
});

// Initialize the Breadcrumbs component.
$di->setShared('breadcrumbs', function () {
    return new Breadcrumbs;
});
```

## Copyright

Phalcon Breadcrumbs is open-sourced software licensed under the [New BSD License][:license:].
© Phalcon Framework Team and contributors

[:composer:]: https://getcomposer.org/
[:phalcon:]: https://github.com/phalcon/cphalcon/releases
[:license:]: https://github.com/phalcongelist/breadcrumbs/blob/master/docs/LICENSE.md
