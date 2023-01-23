# UPGRADE FROM 3.x.x TO 4.0.0

### Breaking Changes

Support for PHP 7.4 ends with version 4.0. PHP 7.4 is no longer supported by the
no longer supported by the PHP core team as of November 28, 2022.
The minimum PHP version is now 8.0.
---
- `config/laravel-jms-serializer.php` has two new parameters `add_default_handlers: bool` and `custom_handlers: array<int, Dropelikeit\LaravelJmsSerializer\Contracts\CustomHandlerConfiguration>`
If you have upgraded this package from a previous version to 4.0, you need to add the two parameters manually.


- `Dropelikeit\LaravelJmsSerializer\Contracts\Config` has two new methods `shouldAddDefaultHeaders` and `getCustomHandlers`.

### Features
- `Dropelikeit\LaravelJmsSerializer\Serializer\Factory` can now handle custom handlers if the config has custom handlers.
- Add better documentation.

### Patches

- The ServiceProvider class be optimize
- Add ServiceProvider test
- Add test for `Dropelikeit\LaravelJmsSerializer\Serializer\Factory`.
