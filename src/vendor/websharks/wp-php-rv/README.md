## WP PHP vX.x+ (Version Check w/ Dashboard Notice)

Stub for WordPress themes/plugins that require PHP vX.x+ (i.e. a minimum version that you define).

![](assets/screenshot.png)

---

### Example Usage in a Typical WordPress Theme/Plugin File

```php
<?php
/*
	Plugin Name: My Plugin
	Plugin URI: http://example.com/my-plugin
	Description: Example plugin.
	Author: Example Author.
	Version: 0.1-alpha
	Author URI: http://example.com
	Text Domain: my-plugin
*/
$GLOBALS['wp_php_rv'] = '5.3'; // Require PHP vX.x+ (you configure this).
if(require('wp-php-rv/src/includes/check.php')) // `TRUE` if running PHP vX.x+.
	require dirname(__FILE__).'/my-plugin-code.php'; // It's OK to load your plugin.
else wp_php_rv_notice(); // Creates a nice PHP vX.x+ dashboard notice for the site owner.
```

---

### Understanding `if(require('wp-php-rv/src/includes/check.php'))`

The `check.php` file will automatically return `TRUE` upon using `include()` or `require()` in your scripts; i.e., iff the installation site is running PHP vX.x+ (as configured by `$GLOBALS['wp_php_rv']`). Otherwise it returns `FALSE`. Therefore, the simplest way to run your check is to use `if(require('wp-php-rv/src/includes/check.php'))`. **However**, you could also choose to do it this way.

```php
<?php
$GLOBALS['wp_php_rv'] = '5.3'; // Require PHP vX.x+.
require 'wp-php-rv/src/includes/check.php'; // Include.

if(wp_php_rv()) // `TRUE` if running PHP vX.x+.
	require dirname(__FILE__).'/my-plugin-code.php'; // It's OK to load your plugin.
else wp_php_rv_notice(); // Creates a nice PHP vX.x+ dashboard notice for the site owner.
```

---

### Dashboard Notice that Calls your Software by Name

```php
<?php
$GLOBALS['wp_php_rv'] = '5.3'; // Require PHP vX.x+.
if(require('wp-php-rv/src/includes/check.php')) // `TRUE` if running PHP vX.x+.
	require dirname(__FILE__).'/my-plugin-code.php'; // It's OK to load your plugin.
else wp_php_rv_notice('My Plugin'); // Dashboard notice mentions your software specifically.
```

_NOTE: If you omit the `$software_name` argument, a default value is used instead. The default value is `ucwords('[calling file basedir]')`; e.g., if `/my-plugin/stub.php` calls `wp-php-rv/src/includes/check.php`, the default `$software_name` automatically becomes `My Plugin`. Nice!_

---

### Using a Custom Dashboard Notice

```php
<?php
$GLOBALS['wp_php_rv'] = '5.3'; // Require PHP vX.x+.
if(require('wp-php-rv/src/includes/check.php')) // `TRUE` if running PHP vX.x+.
	require dirname(__FILE__).'/my-plugin-code.php'; // It's OK to load your plugin.
else wp_php_rv_custom_notice('My Plugin requires PHP v5.3+'); // Custom Dashboard notice.
```

---

### What if Multiple Themes/Plugins Use This?

This is fine! :-) The `wp-php-rv/src/includes/check.php` file uses `function_exists()` as a wrapper; which allows it to be included any number of times, and by any number of plugins; and also from any number of locations. **The only thing to remember**, is that you MUST be sure to define `$GLOBALS['wp_php_rv']` each time; i.e., each time you `include('wp-php-rv/src/includes/check.php')` or `require('wp-php-rv/src/includes/check.php')`.

The point here, is that `$GLOBALS['wp_php_rv']` defines a PHP version that is specific to your plugin requirements, so it should be defined explicitly by each plugin developer before they `include('wp-php-rv/src/includes/check.php')` or `require('wp-php-rv/src/includes/check.php')`.

---

### Can this Just Go at the Top of My Existing Theme/Plugin File?

**No, there are two important things to remember.**

1. Don't forget to bundle a copy of the `websharks/wp-php-rv` repo with your theme/plugin. All you really need is the `/src` directory.
2. Don't leave your existing code in the same file. Use this in a stub file that checks for PHP vX.x+ first (as seen in the examples above), BEFORE loading your code which depends on PHP vX.x+. Why? If you put a PHP vX.x+ check at the top of an existing PHP file, and that particular PHP file happens to contain code which is only valid in PHP v5.2 (for instance), it may still trigger a syntax error. For this reason, you should move your code into a separate file and create a stub file that checks for the existence of PHP vX.x+ first.

---

### Can I Test for Required PHP Extensions Too?

Yes, `$GLOBALS['wp_php_rv']` can be either a string with a required version, or an array with both a required version and a nested array of required PHP extensions. The easiest way to show how this works is by example (as seen below). Note that your array of required PHP extensions must be compatible with PHP's [`extension_loaded()`](http://php.net/manual/en/function.extension-loaded.php) function.

```php
<?php
$GLOBALS['wp_php_rv']['rv'] = '5.3';
$GLOBALS['wp_php_rv']['re'] = array('curl', 'mbstring');

if(require('wp-php-rv/src/includes/check.php')) // `TRUE` if running PHP vX.x+ w/ all required extensions.
	require dirname(__FILE__).'/my-plugin-code.php'; // It's OK to load your plugin.
else wp_php_rv_notice('My Plugin'); // Dashboard notice mentions your software specifically.
```

_**Note**: with this technique, the dashboard notice may vary depending on the scenario. If only the required PHP version is missing, the dashboard notice will mention this only. If the required PHP version is satisified, but the PHP installation is missing one or more required PHP extensions, only those required extensions will be mentioned to a site owner. If neither can be satisfied (i.e. the required version of PHP is missing, AND one or more required PHP extensions are missing too), the dashboard notice will mention both the required version of PHP, and include a list of the required PHP extensions they are missing. ~ Also, when/if missing PHP extensions are listed for a site owner, they're automatically linked up, leading a site owner to the relevant section of the manual at PHP.net._

---

Copyright: © 2015 [WebSharks, Inc.](http://www.websharks-inc.com/bizdev/) (coded in the USA)

Released under the terms of the [GNU General Public License](http://www.gnu.org/licenses/gpl-3.0.html).
