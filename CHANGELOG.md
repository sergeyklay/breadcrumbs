# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
## [1.4.2] - 2019-09-26
### Removed
- Removed not used dev dependencies and empty tests structure

## [1.4.1] - 2019-09-26
### Added
- Allow adding multiple crumbs without a link [#23](https://github.com/sergeyklay/breadcrumbs/pull/23)

### Changed
- Changed ownership

## [1.4.0] - 2018-05-24
### Fixed
- Fixed, last element in chain has no link always

### Changed
- Changed `CHANGELOG.md`

## [1.3.4] - 2018-01-20
### Added
- Enabled support for PHP 7.2

### Changed
- Used latest Phalcon
- Updated dev dependencies

## [1.3.3] - 2017-10-21
### Changed
- Used latest Phalcon
- Updated dev dependencies

## [1.3.2] - 2017-09-10
### Changed
- Used latest Phalcon
- Updated tests
- Updated docs

## [1.3.1] - 2017-04-18
### Fixed
- Fixed invalid converting `$id` to `":null:"` if `$url` is not null in update function.

## [1.3.0] - 2017-04-10
### Changed
- Changed organization
- Minor `composer.json` improvements

## [1.2.1] - 2016-12-21
### Changed
- Updated dev-dependencies
- Minor grammar improvements
- Refactored test environment

## [1.2.0] - 2016-03-26
### Added
- Added `Breadcrumbs::count`

### Deprecated
- PHP 5.4 is now fully deprecated

### Fixed
- Fixed building with Phalcon 2.1.x

## [1.1.1] - 2016-03-12
### Added
- Added Codeception support

### Changed
- Cleanup documentation

## [1.1.0] - 2016-02-22
### Added
- Added support of events
- Added `Breadcrumbs::update` to update an existing crumb
- Added the events: `breadcrumbs:beforeUpdate` and `breadcrumbs:afterUpdate`
- Introduced domain exceptions
- Detect empty `Breadcrumbs::$elements` on update or remove
- Added `Breadcrumbs::setTemplate` to set rendering template
- Added the events: `breadcrumbs:beforeSetTemplate` and `breadcrumbs:afterSetTemplate`

### Changed
- Updated `Breadcrumbs::log` in order to add the ability to catch the exception in your custom listener

## 1.0.0 - 2016-02-22
### Added
- Initial release

[Unreleased]: https://github.com/sergeyklay/breadcrumbs/compare/v1.4.2...HEAD
[1.4.2]: https://github.com/sergeyklay/breadcrumbs/compare/v1.4.1...v1.4.2
[1.4.1]: https://github.com/sergeyklay/breadcrumbs/compare/v1.4.0...v1.4.1
[1.4.0]: https://github.com/sergeyklay/breadcrumbs/compare/v1.3.4...v1.4.0
[1.3.4]: https://github.com/sergeyklay/breadcrumbs/compare/v1.3.3...v1.3.4
[1.3.3]: https://github.com/sergeyklay/breadcrumbs/compare/v1.3.2...v1.3.3
[1.3.2]: https://github.com/sergeyklay/breadcrumbs/compare/v1.3.1...v1.3.2
[1.3.1]: https://github.com/sergeyklay/breadcrumbs/compare/v1.3.0...v1.3.1
[1.3.0]: https://github.com/sergeyklay/breadcrumbs/compare/v1.2.1...v1.3.0
[1.2.1]: https://github.com/sergeyklay/breadcrumbs/compare/v1.2.0...v1.2.1
[1.2.0]: https://github.com/sergeyklay/breadcrumbs/compare/v1.1.1...v1.2.0
[1.1.1]: https://github.com/sergeyklay/breadcrumbs/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/sergeyklay/breadcrumbs/compare/v1.0.0...v1.1.0
