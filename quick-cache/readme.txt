=== Quick Cache (Speed Without Compromise) ===

Stable tag: 131031
Requires at least: 3.3
Tested up to: 3.7.1
Text Domain: quick-cache

License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Contributors: WebSharks, PriMoThemes
Donate link: http://www.websharks-inc.com/r/wp-theme-plugin-donation/
Tags: cache, quick cache, quick-cache, quickcache, speed, performance, loading, generation, execution, benchmark, benchmarking, debug, debugging, caching, cash, caching, cacheing, super cache, advanced cache, advanced-cache, wp-cache, wp cache, options panel included, websharks framework, w3c validated code, includes extensive documentation, highly extensible

Speed up your site ~ BIG Time! - If you care about the speed of your site, Quick Cache is a plugin that you absolutely MUST have installed :-)

== Installation ==

**Quick Tip:** WordPress® can only deal with one cache plugin being activated at a time. Please uninstall any existing cache plugins that you've tried in the past. In other words, if you've installed W3 Total Cache, WP Super Cache, DB Cache Reloaded, or any other caching plugin, uninstall them all before installing Quick Cache. One way to check, is to make sure this file: `/wp-content/advanced-cache.php` is NOT present; and if it does exist, delete it before installing Quick Cache. That file will ONLY be present if you have a cache plugin already installed. If you don't see it, you're good.

**Quick Cache is very easy to install (follow these instructions):**

1. Upload the `/quick-cache` folder to your `/wp-content/plugins/` directory.
2. Activate the plugin through the Plugins menu in WordPress®.
3. Navigate to the Quick Cache panel & enable it.

**How do I know that Quick Cache is working?**

First of all, make sure that you've enabled Quick Cache. After you activate the plugin, go to the Quick Cache options panel and enable it, then scroll to the bottom and click Save All Changes. All of the other options on that page are already pre-configured for typical usage. Skip them all for now. You can go back through all of them later and fine-tune things the way you like them.

Once Quick Cache has been enabled, **you'll need to log out**. Cache files are NOT served to visitors who are logged in, and that includes you too :-) Cache files are NOT served to recent commenters either. If you've commented (or replied to a comment lately); please clear your browser cookies before testing.

To verify that Quick Cache is working, navigate your site like a normal visitor would. Right-click on any page (choose View Source), then scroll to the very bottom of the document. At the bottom, you'll find comments that show Quick Cache stats and information. You should also notice that page-to-page navigation is lightning fast compared to what you experienced prior to installing Quick Cache.

**Running Quick Cache On A WordPress® Multisite Installation**

WordPress® Multisite Networking is a special consideration in WordPress®. If Quick Cache is installed under a Multisite Network installation, it will be enabled for ALL blogs the same way. The centralized config options for Quick Cache, can only be modified by a Super Administrator operating on the main site. Quick Cache has internal processing routines that prevent configuration changes, including menu displays; for anyone other than a Super Administrator operating on the main site.

== Description ==

If you care about the speed of your site, Quick Cache is one of those plugins that you absolutely MUST have installed :-) Quick Cache takes a real-time snapshot (building a cache) of every Page, Post, Category, Link, etc. These snapshots are then stored (cached) intuitively, so they can be referenced later, in order to save all of that processing time that has been dragging your site down and costing you money.

The Quick Cache plugin uses configuration options that you select from the options panel. See: `Quick Cache -› Options` in your Dashboard. Once a file has been cached, Quick Cache uses advanced techniques that allow it to recognize when it should and should not serve a cached version of the file. The decision engine that drives these techniques is under your complete control through options on the back-end. By default, Quick Cache does not serve cached pages to users who are logged in, or to users who have left comments recently. Quick Cache also excludes administrative pages, login pages, POST/PUT/GET requests, CLI processes, and any additional User-Agents; or other special pattern matches that you want to add.

== Screenshots ==

1. Quick Cache Screenshot #1
2. Quick Cache Screenshot #2
3. Quick Cache Screenshot #3
4. Quick Cache Screenshot #4
5. Quick Cache Screenshot #5
6. Quick Cache Screenshot #6
7. Quick Cache Screenshot #7

== So Why Does WordPress® Need To Be Cached? ==

To understand how Quick Cache works, first you have to understand what a cached file is, and why it is absolutely necessary for your site and every visitor that comes to it. WordPress® (by its very definition) is a database-driven publishing platform. That means you have all these great tools on the back-end of your site to work with, but it also means that every time a Post/Page/Category is accessed on your site, dozens of connections to the database have to be made, and literally thousands of PHP routines run in harmony behind-the-scenes to make everything jive. The problem is, for every request that a browser sends to your site, all of these routines and connections have to be made (yes, every single time). Geesh, what a waste of processing power, memory, and other system resources. After all, most of the content on your site remains the same for at least a few minutes at a time. If you've been using WordPress® for very long, you've probably noticed that (on average) your site does not load up as fast as other sites on the web. Now you know why!

== The Definition Of A Cached File (from the Wikipedia) ==

In computer science, a cache (pronounced /kash/) is a collection of data duplicating original values stored elsewhere or computed earlier, where the original data is expensive to fetch (owing to longer access time) or to compute, compared to the cost of reading the cache. In other words, a cache is a temporary storage area where frequently accessed data can be stored for rapid access. Once the data is stored in the cache, it can be used in the future by accessing the cached copy rather than re-fetching or recomputing the original data.

== Prepare To Be Amazed / It's Time To Speed Things Up ==

Quick Cache is extremely reliable, because it runs completely in PHP code, and does not hand important decisions off to the `mod_rewrite` engine or browser cache; also making Quick Cache MUCH easier to setup and configure.

In addition, Quick Cache actually sends a no-cache header (yes, a no-cache header); which allows it to remain in control at all times. It might seem weird that a caching plugin would send a no-cache header :-). Well, no-cache headers are a key component in this plugin, and they will NOT affect performance negatively. On the contrary, this is how the system can accurately serve cache files to public users vs. users who are logged-in, commenters, etc.

If you care about the speed of your site, Quick Cache is one of those plugins that you absolutely MUST have installed :-) Quick Cache takes a real-time snapshot (building a cache) of every Post, Page, Category, Link, etc. These snapshots are then stored (cached) intuitively, so they can be referenced later, in order to save all of that processing time that has been dragging your site down and costing you money.

The Quick Cache plugin uses configuration options that you select from the options panel. See: `Quick Cache -› Options` in your Dashboard. Once a file has been cached, Quick Cache uses advanced techniques that allow it to recognize when it should and should not serve a cached version of the file. The decision engine that drives these techniques is under your complete control through options on the back-end. By default, Quick Cache does not serve cached pages to users who are logged in, or to users who have left comments recently. Quick Cache also excludes administrative pages, login pages, POST/PUT/GET requests, CLI processes, and any additional User-Agents; or other special pattern matches that you want to add.

== Running Quick Cache On A WordPress® Multisite Installation ==

WordPress® Multisite Networking is a special consideration in WordPress®. If Quick Cache is installed under a Multisite Network installation, it will be enabled for ALL blogs the same way. The centralized config options for Quick Cache, can only be modified by a Super Administrator operating on the main site. Quick Cache has internal processing routines that prevent configuration changes, including menu displays; for anyone other than a Super Administrator operating on the main site.

== How To Enable GZIP Compression for Even Greater Speeds ==

You don't have to use an `.htaccess` file to enjoy the performance enhancements provided by this plugin; caching is handled by WordPress®/PHP alone. That being said, if you want to take advantage of GZIP compression (and we do recommend this), then you WILL need an `.htaccess` file to accomplish that part. This plugin fully supports GZIP compression on its output. However, it does not handle GZIP compression directly. We purposely left GZIP compression out of this plugin, because GZIP compression is something that should really be enabled at the Apache level or inside your `php.ini` file. GZIP compression can be used for things like JavaScript and CSS files as well, so why bother turning it on for only WordPress-generated pages when you can enable GZIP at the server level and cover all the bases!

If you want to enable GZIP, create an `.htaccess` file in your WordPress® installation directory and put the following few lines in it. Alternatively, if you already have an `.htaccess` file, just add these lines to it, and that is all there is to it. GZIP is now enabled!

	<IfModule mod_deflate.c>
		<IfModule mod_filter.c>
			AddOutputFilterByType DEFLATE text/plain text/html application/x-httpd-php-source
			AddOutputFilterByType DEFLATE text/xml application/xml application/xhtml+xml application/xml-dtd
			AddOutputFilterByType DEFLATE application/rdf+xml application/rss+xml application/atom+xml image/svg+xml
			AddOutputFilterByType DEFLATE text/css text/javascript application/javascript application/x-javascript
			AddOutputFilterByType DEFLATE font/truetype application/x-font-ttf font/opentype application/x-font-otf
		</IfModule>
	</IfModule>

If your installation of Apache does not have `mod_deflate` installed. You can also enable GZIP compression using PHP configuration alone. In your `php.ini` file, you can simply add the following line anywhere: `zlib.output_compression = on`

== Frequently Asked Questions ==

= How do I know that Quick Cache is working the way it should be? =
First of all, make sure that you've enabled Quick Cache. After you activate the plugin, go to the Quick Cache options panel and enable it, then scroll to the bottom and click Save All Changes. All of the other options on that page are already pre-configured for typical usage. Skip them all for now. You can go back through all of them later and fine-tune things the way you like them.

Once Quick Cache has been enabled, **you'll need to log out**. Cache files are NOT served to visitors who are logged in, and that includes you too :-) Cache files are NOT served to recent commenters either. If you've commented (or replied to a comment lately); please clear your browser cookies before testing.

To verify that Quick Cache is working, navigate your site like a normal visitor would. Right-click on any page (choose View Source), then scroll to the very bottom of the document. At the bottom, you'll find comments that show Quick Cache stats and information. You should also notice that page-to-page navigation is lightning fast compared to what you experienced prior to installing Quick Cache.

= What is the down side to running Quick Cache? =
There is NOT one! Quick Cache is a MUST HAVE for every WordPress® powered site. In fact, we really can't think of any site running WordPress® that would want to be without it. To put it another way, the WordPress® software itself comes with a built in action reference for an `advanced-cache.php` file, because WordPress® developers realize the importance of such as plugin. The `/wp-content/advanced-cache.php` file is named as such, because the WordPress® developers expect it to be there when caching is enabled by a plugin. If you don't have the `/wp-content/advanced-cache.php` file yet, it is because you have not enabled Quick Cache from the options panel yet.

= So why does WordPress® need to be cached? =
To understand how Quick Cache works, first you have to understand what a cached file is, and why it is absolutely necessary for your site and every visitor that comes to it. WordPress® (by its very definition) is a database-driven publishing platform. That means you have all these great tools on the back-end of your site to work with, but it also means that every time a Post/Page/Category is accessed on your site, dozens of connections to the database have to be made, and literally thousands of PHP routines run in harmony behind-the-scenes to make everything jive. The problem is, for every request that a browser sends to your site, all of these routines and connections have to be made (yes, every single time). Geesh, what a waste of processing power, memory, and other system resources. After all, most of the content on your site remains the same for at least a few minutes at a time. If you've been using WordPress® for very long, you've probably noticed that (on average) your site does not load up as fast as other sites on the web. Now you know why!

In computer science, a cache (pronounced /kash/) is a collection of data duplicating original values stored elsewhere or computed earlier, where the original data is expensive to fetch (owing to longer access time) or to compute, compared to the cost of reading the cache. In other words, a cache is a temporary storage area where frequently accessed data can be stored for rapid access. Once the data is stored in the cache, it can be used in the future by accessing the cached copy rather than re-fetching or recomputing the original data.

= Where & why are the cache files stored on my server? =
The cache files are stored in a special directory: `/wp-content/cache/`. This directory needs to remain writable, just like the `/wp-content/uploads` directory on many WordPress® installations. The `/cache` directory is where MD5 hash files reside. These files are named (with an MD5 hash) according to your MD5 Version Salt and the `HTTP_HOST/REQUEST_URI`. Please see: `Quick Cache -› Options -› MD5 Version Salt` for further details.

Whenever a request comes in from someone on the web, Quick Cache checks to see if it can serve a cached file, it looks at your MD5 Version Salt, it looks at the `HTTP_HOST/REQUEST_URI`, then it checks the `/cache` directory. If a cache file has been built already, and it matches your `Salt.HTTP_HOST.REQUEST_URI` combination, and it is not too old (see: `Quick Cache -› Options -› Expiration`), then it will serve that file instead of asking WordPress® to regenerate it. This adds tremendous speed to your site and reduces server load.

If you have GZIP compression enabled, then the cache file is also sent to the browser with compression (recommended). Modern web browsers that support this technique will definitely take advantage of it. After all, if it is easier to email a zip file, it's also easier to download a web page that way. That is why on-the-fly GZIP compression for web pages is recommended. This is supported by all modern browsers.

If you want to enable GZIP, create an `.htaccess` file in your WordPress® installation directory and put the following few lines in it. Alternatively, if you already have an `.htaccess` file, just add these lines to it, and that is all there is to it. GZIP is now enabled!

	<IfModule mod_deflate.c>
		<IfModule mod_filter.c>
			AddOutputFilterByType DEFLATE text/plain text/html application/x-httpd-php-source
			AddOutputFilterByType DEFLATE text/xml application/xml application/xhtml+xml application/xml-dtd
			AddOutputFilterByType DEFLATE application/rdf+xml application/rss+xml application/atom+xml image/svg+xml
			AddOutputFilterByType DEFLATE text/css text/javascript application/javascript application/x-javascript
			AddOutputFilterByType DEFLATE font/truetype application/x-font-ttf font/opentype application/x-font-otf
		</IfModule>
	</IfModule>

If your installation of Apache does not have `mod_deflate` installed. You can also enable GZIP compression using PHP configuration alone. In your `php.ini` file, you can simply add the following line anywhere: `zlib.output_compression = on`

= What happens if a user logs in? Are cache files used then? =
The decision engine that drives these techniques is under your complete control through options on the back-end. By default, Quick Cache does not serve cached pages to users who are logged in, or users who have left comments recently. Quick Cache also excludes administrative pages, login pages, POST/PUT/GET requests, CLI processes, and any additional User-Agents; or special pattern matches that you want to add. POST requests should never be cached. A CLI request is one that comes from the command line; commonly used by CRON jobs and other automated routines.

= Will comments and other dynamic parts of my blog update immediately? =
It depends on your configuration of Quick Cache. There is an automatic expiration system (the garbage collector), which runs through WordPress® behind-the-scene, according to your Expiration setting (see: `Quick Cache -› Options -› Expiration`). There is also a built-in expiration time on existing files that is checked before any cache file is served up, which also uses your Expiration setting. In addition; whenever you update a Post or a Page, Quick Cache can automatically prune that particular file from the cache so it instantly becomes fresh again. Otherwise, your visitors would need to wait for the previous cached version to expire. (see: `Quick Cache -› Options -› Dynamic Cache Pruning`).

By default, Quick Cache does not serve cached pages to users who are logged in, or to users who have left comments recently. Quick Cache also excludes administrative pages, login pages, POST/PUT/GET requests, CLI processes, and any additional User-Agents; or special pattern matches that you want to add. POST requests should never be cached. A CLI request is one that comes from the command line; commonly used by CRON jobs and other automated routines.

= Can I customize the way cache files are stored & served up? =
Quick Cache provides you with the ability to customize the Salt used in MD5 hash generation for cache storage, and that directly affects the way they are served also. The ability to customize the Salt used in cache storage is important to advanced webmasters. Some sites offer unique services and serve special versions of certain files across different devices. The ability to control how different versions of pages are cached, is critical to advanced webmasters that need to tweak everything and customize the caching engine to their specific needs. See: `Quick Cache -› Options -› MD5 Version Salt` for further details. If you don't understand what a Salt is, or what an MD5 hash is, that is 100% ok :-) If you don't understand what it is, you probably don't need it. That simple :-) Using a custom Salt is a very advanced technique and it is NOT required to benefit from speed enhancements provided by Quick Cache.

= How do I enable GZIP compression? Is GZIP supported? =
There is no need to use an `.htaccess` file with this plugin; caching is handled by WordPress®/PHP alone. That being said, if you also want to take advantage of GZIP compression (and we do recommend this), then you WILL need an `.htaccess` file to accomplish that part. This plugin fully supports GZIP compression on its output. However, it does not handle GZIP compression directly. We purposely left GZIP compression out of this plugin, because GZIP compression is something that should really be enabled at the Apache level or inside your `php.ini` file. GZIP compression can be used for things like JavaScript and CSS files as well, so why bother turning it on for only WordPress-generated pages when you can enable GZIP at the server level and cover all the bases!

If you want to enable GZIP, create an `.htaccess` file in your WordPress® installation directory and put the following few lines in it. Alternatively, if you already have an `.htaccess` file, just add these lines to it, and that is all there is to it. GZIP is now enabled!

	<IfModule mod_deflate.c>
		<IfModule mod_filter.c>
			AddOutputFilterByType DEFLATE text/plain text/html application/x-httpd-php-source
			AddOutputFilterByType DEFLATE text/xml application/xml application/xhtml+xml application/xml-dtd
			AddOutputFilterByType DEFLATE application/rdf+xml application/rss+xml application/atom+xml image/svg+xml
			AddOutputFilterByType DEFLATE text/css text/javascript application/javascript application/x-javascript
			AddOutputFilterByType DEFLATE font/truetype application/x-font-ttf font/opentype application/x-font-otf
		</IfModule>
	</IfModule>

If your installation of Apache does not have `mod_deflate` installed. You can also enable gzip compression using PHP configuration alone. In your `php.ini` file, you can simply add the following line anywhere: `zlib.output_compression = on`

= How can I serve a different set of cache files to iPhone users? =
Set your MD5 Version Salt to the following:

	(stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') !== FALSE) ? 'IPHONES' : '')

This effectively creates two versions of the cache. When iPhones are detected, Quick Cache will prepend `IPHONES` to the `HTTP_HOST.REQUEST_URI`, before it generates the MD5 hash for storage.

= How can I serve a different set of cache files based on a cookie? =
Set your MD5 Version Salt to the following:

	(!empty($_COOKIE['my_cookie']) && stripos($_COOKIE['my_cookie'], 'BlueLizards') !== FALSE) ? 'BlueLizards' : '')

This effectively creates two versions of the cache. When `my_cookie` contains `BlueLizards`, Quick Cache will prepend `BlueLizards` to the `HTTP_HOST.REQUEST_URI`, before it generates the MD5 hash for storage. Another, even simpler way to handle this, would be to use the value of a specific cookie to generate multiple variations of the cache. So instead of the ternary expression shown above, you would simply set your Version Salt to:

`(string)@$_COOKIE['my_cookie']`

The value of `$_COOKIE['my_cookie']` is what would be used as your Version Salt. It would even be OK if `$_COOKIE['my_cookie']` was equal to an empty string. In that case the default version of the cache would be used.

= I'm a plugin developer. How can I prevent certain files from being cached? =
	define('QUICK_CACHE_ALLOWED', FALSE); // The easiest way.
	// or $_SERVER['QUICK_CACHE_ALLOWED'] = FALSE; // Also very easy.
	// or define('DONOTCACHEPAGE', TRUE); // For compatibility with other cache plugins.

When your script finishes execution, Quick Cache will know that it should NOT cache that particular page. It does not matter where or when you define this Constant; e.g. `define('QUICK_CACHE_ALLOWED', FALSE);` because Quick Cache is the last thing to run during execution. So as long as you define this Constant at some point in your routines, everything will be fine.

Quick Cache also provides support for `define('DONOTCACHEPAGE', TRUE)`, which is used by the WP Super Cache plugin as well. Another option is: `$_SERVER['QUICK_CACHE_ALLOWED'] = FALSE`. The `$_SERVER` array method is useful if you need to disable caching at the Apache level using `mod_rewrite`. The `$_SERVER` array is filled with all environment variables, so if you use `mod_rewrite` to set the `QUICK_CACHE_ALLOWED` environment variable, that will end up in `$_SERVER['QUICK_CACHE_ALLOWED']`. All of these methods have the same end result, so it's up to you which one you'd like to use.

= What should my expiration setting be? =
If you don't update your site much, you could set this to `6 months`; optimizing everything even further. The longer the cache expiration time is, the greater your performance gain. Alternatively, the shorter the expiration time, the fresher everything will remain on your site. A default value of `7 days` (recommended expiration time), is a good conservative middle-ground.

Keep in mind that your expiration setting is only one part of the big picture. Quick Cache will also purge the cache automatically as changes are made to the site (i.e. you edit a post, someone comments on a post, you change your theme, you add a new navigation menu item, etc., etc.). Thus, your expiration time is really just a fallback; e.g. the maximum amount of time that a cache file could ever possibly live.

That being said, you could set this to just `60 seconds` and you would still see huge differences in speed and performance. If you're just starting out with Quick Cache (perhaps a bit nervous about old cache files being served to your visitors); you could set this to something like `30 minutes` and experiment with it while you build confidence in Quick Cache. It's not necessary, but many site owners have reported this makes them feel like they're more-in-control when the cache has a short expiration time. All-in-all, it's a matter of preference :-)

== Upgrade Notice ==

= v131031 =
The latest version is a complete rewrite. The same functionality, but with significant changes to the underlying codebase.

== Changelog ==

= 131031 =
* A complete rewrite. The same functionality, but with significant changes to the underlying codebase.

= 111203 =
* Updated to support WordPress® v3.3. Backward compatibily remains for WordPress® v3.2.x.

= 110720 =
* Bug fix. Corrected XSS security issue associated with the handling of ``$_SERVER["REQUEST_URI"]`` inside the comment lines that Quick Cache introduces at the bottom of the source code.
* Bug fix. Corrected cosmetic issue in WordPress v3.2 related to the positioning of the Clear Cache button.

= 110709 =
* Routine maintenance. No signifigant changes.

= 110708 =
* Routine maintenance. No signifigant changes.
* Compatibility with WordPress v3.2.

= 110523 =
* **Versioning.** Starting with this release, versions will follow this format: `yymmdd`. The version for this release is: `110523`.
* Routine maintenance. No signifigant changes.

= 2.3.6 =
* Routine maintenance. No signifigant changes.

= 2.3.5 =
* Bug fix. Under the right scenario, errors regarding the function `is_user_logged_in()` in the second phase of `advanced-cache.php` have been resolved in this release of Quick Cache.
* Compatibility. Quick Cache is now capable of dealing with themes/plugins that attempt to use `ob_start("ob_gzhandler")` inside a `header.php` file, or in other places that may create a problem in the nesting order of output buffers. For instance, this release of Quick Cache resolves some incompatiblities with Headway themes for WordPress®. Please note that GZIP should be enabled at the Apache level ( i.e. with an .htaccess file ), or in PHP using `zlib.output_compression = on`. Both of these methods are preferred over `ob_start("ob_gzhandler")`. If you must use `ob_start("ob_gzhandler")`, please make this declaration inside your `/wp-config.php` file, and NOT inside `/header.php`, as this creates a problem that Quick Cache must work around, and could ultimately prevent GZIP from working at all if you do it this way. For further details on how to enable GZIP with Quick Cache, please see the included `/readme.txt` file.

= 2.3.2 =
* Compatiblity. References to `dirname()` that were processed by the Quick Cache `/advanced-cache.php` handler should have been using `WP_CONTENT_DIR` for improved compatibility with WordPress® installations that may use non-standardized installation directories and/or symlinks.
* New Filter available for developers. Multisite Super Admins can now give their Child Blog owners the ability to manually clear the cache for their own site in the Network. Quick Cache accomplishes this by making the "Clear Cache" button visible in the administrative header for Child Blog owners. If you wish to enable this, you can use this Filter: `add_filter("ws_plugin__qcache_ms_user_can_see_admin_header_controls", "__return_true");`. This button is always visible to Super Admins. Adding this Filter makes it visible to all child Blog Owners as well.

= 2.3.1 =
* Framework updated; general cleanup.
* Optimizations. Further internal optimizations applied through configuration checksums that allow Quick Cache to load with even less overhead now.
* Bug fix. Quick Cache was suffering from a bug regression related to stale Last-Modified headers being sent with cached copies. This has been resolved in Quick Cache v2.3.1+.

= 2.3 =
* Framework updated; general cleanup.
* Updated with static class methods. Quick Cache now uses PHP's SPL autoload functionality to further optimize all of its routines.

= 2.2.8 =
* Framework updated; general cleanup.
* Updated for compatibility with WordPress® 3.1.

= 2.2.7 =
* Framework updated. General cleanup.

= 2.2.6 =
* Updated to disable caching on database failures that do not trigger a `5xx` error code. Quick Cache is now capable of disabling the cache engine dynamically on all database connection failures within WordPress®.

= 2.2.5 =
* Updated to support all `5xx` error codes. Quick Cache now monitors the `status_header` function for `5xx` error codes. If a `5xx` status header is detected, caching is automatically disabled, as it should be.

= 2.2.3 =
* Framework updated. General cleanup.

= 2.2.2 =
* Minor updates to the Ajax clearing routines that were implemented in v2.2.1.
* This update also adds compatibility with (offline) localhost installations of WordPress® (WAMP/MAMP).

= 2.2.1 =
* Support for `glob()` has been added to Quick Cache. In previous versions, it was impossible to pinpoint a specific cache file through Dynamic Pruning routines ( at least, not with 100% accuracy ). This was because an MD5 Version Salt *could* have been generated; based on arbitrary conditionals, set by the site owner. Quick Cache now stores its cache files with three MD5 hash strings, producing longer file names; but with the added benefit of improved Multisite compatibility, and improvements in optimization overall. Quick Cache can now handle dynamic pruning with 100% accuracy. Even supporting complex Multisite installations, with or without `SUBDOMAIN_INSTALL`.
* New feature. Quick Cache now integrates a `Clear Quick Cache` button into the WordPress® Dashboard. This makes it easy to force a "cache reset, via <code>ajax</code>", without having to navigate through the Quick Cache menu for this simple task. Another great benefit to this new button, is that it works in all Dashboard views, even in a Multisite installation across different backends. If you're running a Multisite installation, you can use this new button to clear the cache for a particular site/blog in your network, without interrupting others.
* Bug fix. The Constant `QUICK_CACHE_ALLOWED` was being defined too early in the buffering routine. This has been resolved in v2.2.1.
* Optimization of `advanced-cache.php`. A few things have been streamlined even further.
* Added compatibility for the [WP Maintenance Mode](http://wordpress.org/extend/plugins/wp-maintenance-mode/) plugin, and also the [Maintenance Mode](http://wordpress.org/extend/plugins/maintenance-mode/) plugin. Quick Cache will disable itself when these plugins are enabled for maintenance.
* Added compatibility for other Maintenance Mode plugins that are capable of sending a `Status: 503` header, or a `Retry-After:` header.
* Added compatibility for plugins that create PHP sessions. Quick Cache will automatically disable itself when a PHP session is active.
* Added compatiblity for web hosts that insert a port number into the `$_SERVER["HTTP_HOST"]` variable. Quick Cache is now capable of handling this gracefully.
* Improvement. Removed references to `$blog_id = 1` in favor of `is_main_site()`; providing support for Multisite Mode, where there are multiple sites, instead of just multiple blogs.
* Updated Dynamic Pruning Hooks for Custom Post Types, and Custom Taxonomies in WordPress® 3.0+.
* Extended compatiblity for Quick Cache on SSL enabled blogs.

= 2.1.9 =
* Framework updated; general cleanup.
* Updated minimum requirements to WordPress® 3.0.

= 2.1.8 =
* Framework updated to WS-P-3.0.

= 2.1.7 =
* Bug fix. A bug related to gzinflate variations handled by the WP_Http class has been resolved. This was preventing Quick Cache from validating a custom MD5 Version Salt on some servers.
* Framework updated to WS-P-2.3.

= 2.1.6 =
* Auto-Cache Engine. References to `ws_plugin__qcache_curl_get()`, have been replaced by `c_ws_plugin__qcache_utils_urls::remote()`, which makes use of `wp_remote_request()` through the WP_Http class. This removes an absolute dependency on the cURL extension for PHP. This also gives Quick Cache/WordPress® the ability to decide with method of communication to use for HTTP requests; based on what the installation server has available. Note: this only affects the Auto-Cache Engine for Quick Cache, which is completely optional.
* Compatibility. Quick Cache is now smarter about the way it reports errors. For example, when/if there are directory permission issues with your `wp-content` directory; Quick Cache can help with this, in a more intuitive fashion.
* Compatibility. Support has been added for WordPress® 3.0 with Multisite/Networking enabled.
* Updated minimum requirements to WordPress® 2.9.2.
* Framework updated to WS-P-2.2.

= 2.1.5 =
* A new option for Dynamic Cache Pruning was added. You can now choose `Single + Front Page`. This makes it possible to Create or Edit a Post/Page, and have the cache automatically updated for that specific Post/Page. And.. in addition, your Front Page ( aka: Home Page ) will also be refreshed at the same time.
* A minor bug was fixed in the Dynamic Cache Pruning routines. This bug was originally introduced in Quick Cache v2.1.1, and has now been corrected in v2.1.5. This bug, under certain circumstances, was preventing Quick Cache from locating an expired md5 cache file, for some Posts/Pages being updated.
* Advanced feature addition. Quick Cache now comes bundled with a robust Auto-Cache Engine. This is an optional feature, for VERY advanced users. You'll find the new Auto-Cache Engine listed right along with all of the other Quick Cache options. This works in conjunction with an XML Sitemap.

= 2.1.4 =
* Advanced feature addition. You can now prevent caching dynamically whenever pages on your site receive traffic from specific URLs, specific domains, or even specific word fragments found within the HTTP_REFERER. This feature is very advanced, and will NOT impact your site unless you decide to use it for one reason or another.

= 2.1.3 =
* Added `De-Activation Safeguards` to the Quick Cache options panel.
* Updated the Quick Cache options panel. It's been given a make-over.
* Stable tag updated in support of tagged releases within the repository at WordPress.org.

= 2.1.2 =
* WebSharks Framework for Plugins has been updated to P-2.1.
* Updated caching routines in support of hosting providers running with CGI/FastCGI. Quick Cache has been tested with VPS.net, HostGator, BlueHost, (mt) Media Temple (gs) and (dv), The Rackspace Cloud, and several dedicated servers ( including some Amazon EC2 instances ) running with Apache; including support for both `mod_php` and also `CGI/FastCGI` implementations. Quick Cache should work fine with any Apache/PHP combination. Please report all bugs through the [Support Forum](http://www.primothemes.com/forums/viewforum.php?f=5).
* An issue was discovered with WordPress® MU `/files/` being accessed through `htaccess/mod_rewrite`. Quick Cache has been updated to exclude all `/files/` served under WordPress® MU, which is the way it should be. Requests that contain `/files/` are a reference to WordPress® Media, and there is no reason, to cache, or send no-cache headers, for Media. Quick Cache now ignores all references to `/files/` under WordPress® MU. This problem was not affecting all installations of WPMU, because there already are/were scans in place for Content-Type headers. However, under some CGI/FastCGI implementations, this was not getting picked on WMPU with `mod_rewrite` rules. This has been resolved in v2.1.2.

= 2.1.1 =
* A WPMU bug was corrected in Quick Cache v2.1.1. This bug was related to `HTTP_HOST` detection under WordPress® MU installations that were using sub-domains. Please thank `QuickSander` for reporting this important issue.

= 2.1 =
* Quick Cache has added further support for themes and plugins that dynamically set `Content-Type` headers through PHP routines. Quick Cache is now smart enough to automatically disable itself whenever a theme or plugin sends a `Content-Type` header that would be incompatible with Quick Cache. In other words, any `Content-Type` header that is not a variation of `HTML, XHTML or XML`.
* Quick Cache has also been upgraded to support the preservation of scripted headers sent by PHP routines. If a plugin or theme sends scripted headers ( using the `header()` function in PHP ), those headers will be preserved. They'll be stored along with the cache. This allows them to be sent back to the browser whenever a cached version is served on subsequent visits to the original file.
* Compatability checked against WordPress.org 2.9.1, 2.9.2 &amp; WordPress MU 2.9.1, 2.9.2. Everything looks good. No changes required.

= 2.0 =
* A few tweaks to the options panel.
* Documentation updated, several small improvements in error reporting.
* Additional error checking to support an even wider range of hosting providers.
* Added automation routines for safe re-activation after an upgrade is performed.

= 1.9 =
* Additional support added for WordPress® MU 2.8.6+.
* Security file `quick-cache-mu.php` added specifically for MU installations. WordPress® MU is a special ( multi-user ) version of WordPress®. If you're running WordPress® MU, check the [readme.txt] file for WordPress® MU notations.

= 1.8 =
* Re-organized core framework. Updated to: P-2.0.
* Updated to support WP 2.9+.

= 1.7 =
* Updated documentation. Added some additional code samples.
* Tested with WP 2.8.5. Everything ok.

= 1.6 =
* We've added the ability to enable Double-Caching ( client-side caching ). Full documentation is provided in the Quick Cache options panel. This feature is for those of you who just want blazing fast speed and are not concerned as much about reliability and control. We don't recommend turning this on unless you realize what you're doing.

= 1.5 =
* Support for Dynamic Cache Pruning has been improved. Full documentation is provided in the Quick Cache options panel.
* Additional feature-specific documentation has been added to assist novice webmasters during configuration.

= 1.4 =
* Garbage collection has been further optimized for speed and performance on extremely high traffic sites.
* PHP Ternary expressions are now supported in your Version Salt. This takes your Version Salt to a whole new level.
* Additional code samples have been provided for Version Salts; showing you how to deal with mobile devices and other tricky situations.

= 1.3 =
* We've implemented both Semaphore ( `sem_get` ) and `flock()` mutex. If you're on a Cloud Computing Model ( such as the Rackspace® Cloud ), then you'll want to go with flock() unless they tell you otherwise. In all other cases we recommend the use of Semaphores over Flock because it is generally more reliable. The folks over at Rackspace® have suggested the use of flock() because of the way their Cloud handles multi-threading. In either case, flock() will be fully functional in any hosting environment, so it makes a great fallback in case you experience any problems.

= 1.2 =
* We've implemented a way for plugin developers to disallow caching during certain routines or on specific pages. You can set the following PHP Constant at runtime to disable caching. `define("QUICK_CACHE_ALLOWED", false)`. We have also added backward compatibility for WP Super Cache, so that `define("DONOTCACHEPAGE", true)` will also be supported by plugins that have previously been written for compatibility with Super Cache. In other words, Quick Cache looks for either of these two Constants.

= 1.1 =
* Added the ability to create a Version Salt. This is a feature offered ONLY by Quick Cache. Full documentation is provided in the Quick Cache options panel. This can become very useful for sites that provide membership services or have lots and lots of plugins installed that makes their site incompatible with WP Super Cache. With Quick Cache, you'll now have more control over the entire caching process using a custom Version Salt tailored to your specific needs.

= 1.0 =
* Initial release.