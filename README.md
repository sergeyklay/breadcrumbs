# Phalcon Breadcrumbs [![Build Status](https://travis-ci.org/phalcon/breadcrumbs.svg?branch=master)](https://travis-ci.org/phalcon/breadcrumbs)

![Breadcrumbs Screenshot](https://github.com/phalcon/breadcrumbs/blob/master/docs/breadcrumbs.png)

Phalcon Breadcrumbs is a powerful and flexible component for building site breadcrumbs.
You can adapt it to your own needs or improve it if you want.

Please write us if you have any feedback.

Thanks!

## NOTE

The `master` branch will always contain the latest stable version. If you wish
to check older versions or newer ones currently under development, please
switch to the relevant branch/tag.

## Getting Started

### Requirements

To use this component, you need at least:

* [Composer][:composer:]
* PHP >= 5.5
* Latest stable [Phalcon Framework release][:phalcon:] extension enabled

**NOTE:** Support for legacy PHP versions (down to 5.6) is provided on a best-effort basis.

### Installing

If you have Composer [globally installed](https://getcomposer.org/doc/00-intro.md#globally), this command will suffice:

```sh
$ composer require phalcon/breadcrumbs
```

Otherwise, install Composer in a common location or in your project:

```sh
$ curl -s http://getcomposer.org/installer | php
```

Create the `composer.json` file as follows:

```json
{
    "require": {
        "phalcon/breadcrumbs": "^1.3"
    }
}
```

Then run the composer installer:

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

**Update an existing crumb:**

```php
$this->breadcrumbs->update('/admin/user/remove', ['label' => '<strong class="red">Remove</strong>']);
```

**Count crumbs:**
```php
$this->breadcrumbs->count();
```

**Sets rendering template:**

```php
$this->breadcrumbs->setTemplate(
    '<li><a href="{{link}}">{{icon}}{{label}}</a></li>', // linked
    '<li class="active">{{icon}}{{label}}</li>',         // not linked
    '<i class="fa fa-dashboard"></i>'                    // first icon
);
```

**Multi-language support:**

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
$di->setShared('translate', function () use ($messages) {
    return new Translator(['content' => $messages]);
});

// Initialize the Breadcrumbs component.
$di->setShared('breadcrumbs', function () {
    return new Breadcrumbs;
});
```

**Errors logging:**

```php
use Phalcon\Logger\Formatter\Line as FormatterLine;
use Phalcon\Logger\Adapter\File as FileLogger;
use Phalcon\Breadcrumbs;

// Initialize the Logger.
$di->setShared('logger', function ($filename = null, $format = null) use ($config) {
    $formatter = new FormatterLine($config->get('logger')->format, $config->get('logger')->date);
    $logger = new FileLogger($config->get('logger')->path . $config->get('logger')->filename);

    $logger->setFormatter($formatter);
    $logger->setLogLevel($config->get('logger')->logLevel);

    return $logger;
});

// Initialize the Breadcrumbs component.
$di->setShared('breadcrumbs', function () {
    return new Breadcrumbs;
});
```

### Events

```php
use Phalcon\Breadcrumbs;
use Phalcon\Events\Manager as EventsManager;

// Initialize the Events Manager.
$di->setShared('eventsManager', function () {
    return new EventsManager;
});

// Initialize the Breadcrumbs component.
$di->setShared('breadcrumbs', function () use ($di) {
    $manager = $di->getShared('eventsManager');
    $manager->attach('breadcrumbs', function ($event, $connection) {
        // We stop the event if it is cancelable
        if ($event->isCancelable()) {
            // Stop the event, so other listeners will not be notified about this
            $event->stop();
        }

        // Receiving the data from the event context
        print_r($event->getData());
    });

    $breadcrumbs = new Breadcrumbs;
    $breadcrumbs->setEventsManager($manager);

    return $breadcrumbs;
});
```

Available events:

```
breadcrumbs:beforeAdd
breadcrumbs:afterAdd
breadcrumbs:beforeOutput
breadcrumbs:afterOutput
breadcrumbs:beforeTranslate
breadcrumbs:afterTranslate
breadcrumbs:beforeLogging
breadcrumbs:afterLogging
breadcrumbs:beforeRemove
breadcrumbs:afterRemove
breadcrumbs:beforeUpdate
breadcrumbs:afterUpdate
breadcrumbs:beforeSetTemplate
breadcrumbs:afterSetTemplate
```

## Copyright

Phalcon Breadcrumbs is open-sourced software licensed under the [New BSD License][:license:].
© Phalcon Framework Team and contributors

[:composer:]: https://getcomposer.org/
[:phalcon:]: https://github.com/phalcon/cphalcon/releases
[:license:]: https://github.com/phalcon/breadcrumbs/blob/master/LICENSE.txt
