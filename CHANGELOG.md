# [1.2.0](https://github.com/phalcongelist/breadcrumbs/releases/tag/v1.2.0) (2016-03-26)
* PHP 5.4 is now fully deprecated
* Added `Breadcrumbs::count`
* Fixed building with Phalcon 2.1.x

# [1.1.1](https://github.com/phalcongelist/breadcrumbs/releases/tag/v1.1.1) (2016-03-12)
* Added Codeception support
* Cleanup documentation

# [1.1.0](https://github.com/phalcongelist/breadcrumbs/releases/tag/v1.1.0) (2016-02-22)
* Added support of events
* Added `Breadcrumbs::update` to update an existing crumb
* Added the events: `breadcrumbs:beforeUpdate` and `breadcrumbs:afterUpdate`
* Updated `Breadcrumbs::log` in order to add the ability to catch the exception in your custom listener
* Detect empty `Breadcrumbs::$elements` on update or remove
* Added `Breadcrumbs::setTemplate` to set rendering template
* Added the events: `breadcrumbs:beforeSetTemplate` and `breadcrumbs:afterSetTemplate`
* Introduced domain exceptions

# [1.0.0](https://github.com/phalcongelist/breadcrumbs/releases/tag/v1.0.0) (2016-02-21)
* Initial release
