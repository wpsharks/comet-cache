## WP i18n Tools

Modified slightly by WebSharks, Inc.

#### Differences from original:

- Adding shebang line to the top of `makepot.php`
- Setting executable bit on `makepot.php` and preserving that with Git.
- Updated to support PHP/translations embedded into `.js` files too.
- Disabling line wrapping from final output to ensure better consistency.
- Adding support for plugin directories that use a `plugin.php` file instead of `[slug].php`.
- Adding `composer.json` and package on Packagist.org.
- Auto-exclude `(?:.+?/)?vendor/.*` in themes/plugins.
