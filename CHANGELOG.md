# moonmoon Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).



## [Unreleased]

- Better support for enclosures
  (fixes [#100](https://github.com/moonmoon/moonmoon/issues/100))

## [10.0.0-rc.3] - 2022-05-06

### Enhancements

- New authentication system
  ([cd921cc](https://github.com/rdalverny/moonmoon/commit/cd921cc),
  from [#80](https://github.com/moonmoon/moonmoon/issues/80))
- Readjusted font sizes on front page

### Bugs

- Fixed `<audio>`/`<video>` behavior
  (fixes [#112](https://github.com/moonmoon/moonmoon/issues/112),
  which depended on upstream
  [simplepie/simplepie#716](https://github.com/simplepie/simplepie/issues/716))


## [10.0.0-rc.2] - 2022-02-05

### Added

- Basic support for multiple instances hosting
  ([849221e](https://github.com/rdalverny/moonmoon/commit/849221e))
- Cache size usage now is detailed in admin page
  ([3075019](https://github.com/rdalverny/moonmoon/commit/3075019))

### Bugs

- Fixed test workflow
- Fixed cache pruning

### Enhancements

- Clarified responsibilities between `OpmlManager`, `Opml`, `PlanetConfig`
- Clarified how `Planet::download()` and postload.php behave
- Rearranged time/author/source info under post title,
  with better localized date format


## [10.0.0-rc] - 2022-01-25

### Removed

- Support for PHP versions older than 7.2

### Added

- Test workflow (.github/workflow/php.yml)
- Atom feed is now cached too
- Support for PHP from 7.2 to 8.1
- This changelog
- PHP version support policy (see [README.md](README.md))
- Indonesian translation (from @arachvy,
  see [#107](https://github.com/moonmoon/moonmoon/issues/107))
- moonmoon version info is availabe in admin and page footer
  (fixes [#115](https://github.com/moonmoon/moonmoon/issues/115))
- A full OPML file may now be imported into the admin area
  (fixes [#67](https://github.com/moonmoon/moonmoon/issues/67))
- A Makefile to help with common dev actions (test, format, lint, run, etc.)

### Bugs

- German language id in install page
  (fixes [#116](https://github.com/moonmoon/moonmoon/issues/116))
- Cache include path for index/atom feed
- Atom feed item `<guid>` now uses a unique id, not permalink
  (fixes [#58](https://github.com/moonmoon/moonmoon/issues/58))
- Atom feed dates are now in UTC, and dateModified is now updated

### Enhancements

- Tests and coverage refactoring (`make test`)
- PSR2 enforcement (`make fmt`), some type hints added
- PlanetConfig refactored to handle more explicitly all config values
- All public scripts/elements are now in a dedicated `public/` folder
- Path to public OPML is now `/opml/` and not `/custom/people.opml` (broken link)
- Path to Atom feed is now `/feed/` (`/atom.php` redirects to it)
- All config now lives in `custom/config` directory;
  with migration support from old config location to new one;
- Updated all lang files

### Security

- Javascript content is removed
  (fixes [#111](https://github.com/moonmoon/moonmoon/issues/111))
- A stronger hash function is used for password storage
  (from/fixes [#10](https://github.com/moonmoon/moonmoon/issues/10))


## [9.x] branch - 2022-01-25

Dedicated branch for 9.x support.

## [9.0.0-rc.3] - 2018-01-04

### Bugs

* Reverted [3510092](https://github.com/moonmoon/moonmoon/commit/3510092),
  which introduced at least two serious bugs
  ([1e7eb27](https://github.com/moonmoon/moonmoon/commit/1e7eb27)).
* Added the folder custom/ to the release archive.
* Fixed autoloading issues with PHP 5.6.30
  ([98097b5](https://github.com/moonmoon/moonmoon/commit/98097b5)).


## [9.0.0-rc.2] - 2018-01-03

This is not really a rc and things still need to be fixed, but 9.0.0 should land soon!
Here are the (significant) changes since 9.0.0-rc:

### Enhancements

* The Spanish translation was already present but was not available during the install (#90) ; thanks @Emmafrs!
* Improved support of PHP 7 by using more recent versions of SimplePie (#81, #82) ; thanks @silvyn!
* Wrote a bit of documentation and improved coding style.
* Added various unit / integration tests, each commit is now automatically tested on Travis.

### Security

* Only allow to fetch feeds that were already added through the dashboard (#84).
* Added a mitigation against CSRF attacks (#98).


## [9.0.0-rc] - 2017-01-24

In this release candidate, we move away from year.month versioning to use semver.
The next version of moonmoon will be 9.0.0.
This release contains everything that is in master, including updates to Simplepie.
This should help for people using newer versions of PHP. There are no new features.

The RC will be available for a few weeks. If not bug is reported, it will become the official 9.0.0.


### [Pre-9] - Before 2017-01

---

[Unreleased]: https://github.com/rdalverny/moonmoon/compare/10.0.0-rc.3...10-dev
[10.0.0-rc.3]: https://github.com/rdalverny/moonmoon/compare/10.0.0-rc2....10.0.0-rc.3
[10.0.0-rc.2]: https://github.com/rdalverny/moonmoon/compare/10.0.0-rc...10.0.0-rc.2
[10.0.0-rc]: https://github.com/rdalverny/moonmoon/releases/tag/10.0.0-rc
[9.x]: https://github.com/rdalverny/moonmoon/tree/9.x
[9.0.0-rc.3]: https://github.com/moonmoon/moonmoon/compare/9.0.0-rc.2...9.0.0-rc.3
[9.0.0-rc.2]: https://github.com/moonmoon/moonmoon/compare/9.0.0-rc...9.0.0-rc.2
[9.0.0-rc]: https://github.com/moonmoon/moonmoon/releases/tag/9.0.0-rc
[Pre-9]: https://github.com/moonmoon/moonmoon/compare/ec4326e4bab52c558d1f2564ab2fa0545f81b071...23267b401439199a8bf3d5c9733f70d5d0e3d3d1

---