# UPGRADE FROM 4.x.x TO 5.0.0

### Breaking Changes

Version 5 of this package no longer supports Laravel 8.
Since Laravel has not provided version 8 with updates since 24/01/2023.
Since Laravel 9+ requires PHP 8.1, this PHP version is also required in this library.
---

### Features

- This package can now be used with Laravel v9 and v10.
- Added Coveralls support into github actions.

### Patches

- The cache directory now points to the "/storage/cache" directory. 
