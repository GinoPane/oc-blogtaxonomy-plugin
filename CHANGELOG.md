# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

Types of changes

* **Features** for initial package features.
* **Added** for new features.
* **Changed** for changes in existing functionality.
* **Deprecated** for soon-to-be removed features.
* **Removed** for now removed features.
* **Fixed** for any bug fixes.
* **Security** in case of vulnerabilities.

## [Unreleased]

## 3.1.1 - 2021-05-19

### Fixed

* Fix validation rule for an old October CMS version

## 3.1.0 - 2021-05-16

### Fixed

* Extend allowed characters for model names and titles

## 3.0.5 - 2021-05-04

### Fixed
* Make group-by query conditional for the abstract model to fix the bug

## 3.0.4 - 2021-04-27

### Fixed
* Changed name in composer because October suddenly started to complain about it

## 3.0.3 - 2021-04-23

### Fixed
* Fixed a group by clause when sorting by a particular field on ModelAbstract

## 3.0.2 - 2021-04-11

### Fixed
* Try to fix an issue with unsupported json data type

## 3.0.1 - 2021-03-29

### Fixed
* Fixed a bug with undefined constant left after removal of deprecated code

## 3.0.0 - 2021-03-25

### Features
* Long live Belarus!

### Added
* Post types functionality
* Cover and featured images for categories, series, tags

### Removed
* Deprecated table

## 2.6.1 - 2020-11-11

### Fixed
* Removed usage of 'void' return type to support old PHP versions

## 2.6.0 - 2020-11-09

### Added
* Added 'include tagged posts' option for the 'Posts in the Series' component

## 2.5.0 - 2020-09-14

### Added
* Related posts can now be additionally filtered by post and/or category

## 2.4.0 - 2020-09-03

### Added
* Added ability to pass series slug into post URLs via the 'Posts in the Series' component

## 2.3.1 - 2020-08-21

### Fixed
* Fixed post URLs broken by Rainlab Blog 1.4.3

## 2.3.0 - 2020-06-11

### Added
* Added status support for series
* Added possibility to delete series from the series list page

## 2.2.1 - 2020-05-24

### Added
* Added missing `include categories` filter for Tags list and Series list

## 2.2.0 - 2020-05-21

### Added
* Added support of special characters for tag name

## 2.1.1 - 2020-05-19

### Fixed
* Fix trait name

## 2.1.0 - 2020-05-19

### Added
* Add filter by categories (included) for post lists (with tags/series)

### Changed
* Rename 'Exceptions' section to 'Filters' in component settings

## 2.0.3 - 2020-02-05

### Changed
* Non-ajax version of pagination is now a default
* Do not include the existing routing parameters when generating new links

## 2.0.2 - 2020-01-15

### Fixed
* Fix incorrect query being used for series model
* Remove accidental return operators from update files which prevented clean plugin installation

## 2.0.1 - 2020-01-14

### Fixed
* Set index name explicitly to prevent the generation of too long name

## 2.0.0 - 2020-01-12

### Features
* Taggable series

### Added
* 'Include series posts' option for the 'Posts With the Tag' component
* 'Include series tags' option for the 'Tag List' component
* 'Fetch series posts count' option for the 'Tag List' component

## 1.14.2 - 2019-12-23

### Fixed
* Added missing count of posts in the related series component

## 1.14.1 - 2019-09-02

### Fixed
* Fixed auto generated index being too long

## 1.14.0 - 2019-07-29

### Added
* Expose page parameter name for simple pagination

## 1.13.2 - 2019-06-30

### Fixed
* Added missing exclusions handling for series and tags

## 1.13.1 - 2019-06-25

### Fixed
* Fixed related posts component

## 1.13.0 - 2019-06-20

### Added
* Implemented exclusions by post/category for tag and series lists

## 1.12.0 - 2019-06-18

### Added
* Added functionality of migration from other plugins

## 1.11.1 - 2019-06-12

### Fixed
* Fixed migrations to prevent a ghost bug on update

## 1.11.0 - 2019-06-11

### Added
* Implemented related series
* Implemented exclusions by post/category for post lists

## 1.10.0 - 2019-05-17

### Fixed

* Fixed default templates for posts with tags and series to show correct empty message on wrong pagination

## Changed

* Updated mark.js

## 1.9.0 - 2019-05-11

### Added

* Allow fetching of all related posts for tags and series lists via a new option

### Fixed

* Count only published blog posts in series list

### Changed

* Internal directory structure was slightly changed

## 1.8.0 - 2019-04-17

### Added

* Exposed total tag count over the limit
* Implemented client tag filter for tag list

### Changed

* Micro refactorings and optimizations

## 1.7.0 - 2019-04-03

### Fixed

* Fixed a method to get real URL parameter names

## 1.6.0 - 2019-04-02

### Changed

* Stop using default slug value for TagList (to allow valid empty slug)
* Simplify internal query for posts relation

## 1.5.4 - 2019-03-14

### Fixed
* Improved missing partials references so the backend expander could work correctly

## 1.5.3 - 2019-03-10

### Fixed
* Improved partials references so the backend expander could work correctly

## 1.5.2 - 2019-02-27

### Fixed
* Make translatable implementation truly optional
* Allow any valid character in validation for titles, names, etc.

## 1.5.0 - 2019-01-29

### Added
* Added Translate plugin support
* Added localization support for different strings

### Changed
* Changed series description column type
* Loosen series title validation to allow punctuation
* Multiple internal enhancements

## 1.4.0 - 2019-01-25

### Added
* Featured images for series

### Changed
* Series edit page enhanced
* Several tweaks for tags and series lists

## 1.3.1 - 2019-01-23

### Fixed
* Added missing slug generation for tags

### Added
* Minor enhancements

## 1.3.0 - 2019-01-09

### Changed
* Use another style to list required plugins.

## 1.2.0 - 2019-01-07

### Changed
* Update tag name validation rules.

## 1.1.1 - 2018-12-18

### Changed
* Skip extending Post form fields for a nested form case.
 
## 1.1.0 - 2018-11-27

### Changed
* Degraded from PHP7.1 to PHP7.0 to support the minimal required version for October CMS.

## 1.0.0 - 2018-08-12

### Features
* The initial release of Blog Taxonomy.

[Unreleased]: https://github.com/GinoPane/oc-blogtaxonomy-plugin/compare/v3.1.0...HEAD
