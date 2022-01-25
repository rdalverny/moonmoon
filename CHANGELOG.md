# moonmoon Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).



## [Unreleased]

- Added: basic support for multiple instances hosting (849221e)
- Added: show cache size usage in admin page (3075019)


## [10.0.0-rc] - 2022-01-25

- Removed: support for PHP versions older than 7.2
- Added: test workflow (.github/workflow/php.yml)
- Added: atom feed is now cached too
- Added: support for PHP from 7.2 to 8.1
- Added: this changelog
- Added: PHP version support policy (see README.md)
- Added: Indonesian translation (from @arachvy, see moonmoon/moonmoon#107)
- Added: moonmoon version info in admin and page footer (fixes moonmoon/moonmoon#115)
- Added: a full OPML file may now be imported into the admin area (fixes moonmoon/moonmoon#67)
- Added: Makefile to help with common dev actions (test, format, lint, run, etc.)
- Fixed: German language id in install page (fixes moonmoon/moonmoon#116)
- Fixed: Cache include path  for index/atom feed
- Fixed: atom feed item `<guid>` now uses a unique id, not permalink (fixes moonmoon/moonmoon#58)
- Fixed: atom feed dates are now in UTC, and dateModified is now updated
- Changed: tests and coverage refactoring (`make test`)
- Changed: PSR2 enforcement (`make fmt`), some type hints added
- Changed: PlanetConfig refactored to handle more explicitly all config values
- Changed: all public scripts/elements are now in a dedicated `public/` folder
- Changed: path to public OPML is now `/opml/` and not `/custom/people.opml` (broken link)
- Changed: path to Atom feed is now `/feed/` (`/atom.php` redirects to it)
- Changed: all config now lives in `custom/config` directory;
  with migration support from old config location to new one;
- Changed: updated all lang files
- Security: Javascript content is removed (fixes moonmoon/moonmoon#111)
- Security: a stronger hash function is used for password storage (from/fixes moonmoon/moonmoon#10)


## [9.x] branch - 2022-01-25

Dedicated branch for 9.x support.

## [9.0.0-rc.3] - 2018-01-04

### Bugs

* Reverted 3510092, which introduced at least two serious bugs (1e7eb27).
* Added the folder custom/ to the release archive.
* Fixed autoloading issues with PHP 5.6.30 (98097b5).


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

[Unreleased]: https://github.com/rdalverny/moonmoon/compare/10.0.0-rc...HEAD
[10.0.0-rc]: https://github.com/rdalverny/moonmoon/releases/tag/10.0.0-rc
[9.x]: https://github.com/rdalverny/moonmoon/tree/9.x
[9.0.0-rc.3]: https://github.com/moonmoon/moonmoon/compare/9.0.0-rc.2...9.0.0-rc.3
[9.0.0-rc.2]: https://github.com/moonmoon/moonmoon/compare/9.0.0-rc...9.0.0-rc.2
[9.0.0-rc]: https://github.com/moonmoon/moonmoon/releases/tag/9.0.0-rc
[Pre-9]: https://github.com/moonmoon/moonmoon/compare/ec4326e4bab52c558d1f2564ab2fa0545f81b071...23267b401439199a8bf3d5c9733f70d5d0e3d3d1

---