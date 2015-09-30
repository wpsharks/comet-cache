= v150930 =

- **Bug Fix**: Fixed a bug where, in some rare cases, `wp-config.php` would end up with two `WP_CACHE` definitions. See [Issue #509](https://github.com/websharks/zencache/issues/509).
- **Enhancement**: Updated Text Domain throughout codebase in preparation for plugin import to translate.wordpress.org. See [Issue #576](https://github.com/websharks/zencache/issues/576).

= v150718 =

- **Bug Fix**: Fixed a Fatal Error when upgrading from v150629 to the latest version of ZenCache. This fatal error only occurred on sites that had `WP_DEBUG` enabled, at which point ZenCache failed to properly update `advanced-cache.php`. When `WP_DEBUG` was disabled, the new version of ZenCache was able to properly update `advanced-cache.php` and therefore the temporary failure was silent and inconsequential. This bug fix ensures that no fatal error will be produced when upgrading from v150629, even if `WP_DEBUG` is enabled. See [Issue #524](https://github.com/websharks/zencache/issues/524).

= v150709 =

- **Bug Fix**: This update reverts the codebase restructure released in the previous version (v150626). That release received several reports of Fatal Errors, however those issues only affected a small subset of ZenCache users. After extensive research, we discovered that this issue was related to a bug in PHP's APC Extension (which is commonly used on systems running PHP 5.3 and PHP 5.4). The restructured codebase in the previous release utilized a newer PHP feature (Closures) and that increased the likelihood of exposing that existing PHP bug. Since we can't fix the PHP bug directly and we want to continue supporting PHP 5.3.2+ and PHP 5.4 for now, we're reverting the ZenCache Lite codebase restructure to the previous version (v150409). The new ZenCache Lite features that were included in the previous release are still available in this release--only the structure of the codebase itself has changed. To read more about the PHP APC bug, see [Issue #511](https://github.com/websharks/zencache/issues/511).
- **Enhancement**: The Pro Preview feature has been enhanced to improve usability. We also updated the Static CDN Filters section of the Pro Preview to include several new Static CDN Filter features.

= v150626 =

- **Restructured Codebase**: The entire ZenCache codebase has been restructured to improve performance, enhance flexibility, and make it easier to build in new features!
- **New Feature!** The free version of ZenCache now supports several new options that were previously only available in the Pro version. You can now toggle the Auto-Clear Cache routines for the Home Page, Posts Page, Author Page, Category Archives, Tag Archives, Custom Term Archives, RSS/RDF/Atom Feeds, and XML Sitemaps. This gives you more control over exactly when ZenCache purges the cache for these parts of your site. See _ZenCache → Plugin Options → Clearing the Cache_ for further details.
- **New Feature!** URI Exclusion Patterns are now available in ZenCache Lite! This previously Pro-only feature is now available in the free version of ZenCache and allows you to exclude a list of URIs from being cached by ZenCache. See _ZenCache → Plugin Options → URI Exclusion Patterns_ for further details.
- **New Feature!** HTTP Referrer Exclusion Patterns are now available in ZenCache Lite! This previously Pro-only feature is now available in the free version of ZenCache and allows you to define a list of referring URLs or domains that send you traffic. When ZenCache sees a request coming from one of those URLs or domains, it will not cache that particular request. See _ZenCache → Plugin Options → HTTP Referrer Exclusion Patterns_ for further details.
- **New Pro Feature!**: HTML Compression now supports compressing JSON (in addition to the already supported HTML, JavaScript, and CSS compression). Props @jaswsinc. See [Issue #469](https://github.com/websharks/zencache/issues/469).
- **New Pro Feature!**: Static CDN Filters now supports multiple CDN hostnames. This allows you to configure more than one CDN hostname, also referred to as Domain Sharding. This makes it possible for site owners to work around web browser concurrency limits, allowing the browser to download many resources simultaneously, which increases overall speed. Props to @isaumya and @jaswsinc. See [Issue #468](https://github.com/websharks/zencache/issues/468).
- **Enhancement** (Pro): Static CDN Filters now includes proper support for WordPress Multisite Networks, including support for subdomains (full support for Domain Mapping coming in the next release). If you're running a WordPress Multisite Network and want to configure a CDN, see [this KB Article](http://zencache.com/kb-article/static-cdn-filters-for-wordpress-multisite-networks/) for further details.
- **Enhancement** (Pro): Static CDN Filters now also apply to any static files that are referenced inside CSS files. Props @jaswsinc. See [Issue #461](https://github.com/websharks/zencache/issues/461).
- **Enhancement**: Completed a major restructure of the entire codebase to improve modularity and dependency management. Props @jaswsinc.
- **Enhancement** (Pro): Static CDN Filters now supports the ability to configure separate CDN hostname(s) for each domain (or subdomain) that you run in a WordPress Multisite Network. Props @jaswsinc. See [Issue #475](https://github.com/websharks/zencache/issues/475).
- **Enhancement** (Pro): Static CDN Filters now support subdomains when ZenCache is running inside a WordPress Multisite Network. Props @jaswsinc. See [Issue #439](https://github.com/websharks/zencache/issues/439).
- **Bug Fix** (Pro): Static CDN Filters were not being applied to the primary site on WP Multisite installations that used subdomains. Props to @isaumya for discovering this bug. See [Issue #470](https://github.com/websharks/zencache/issues/470).

= v150409 =

- **Enhancement (includes improved CloudFlare support)**: Improvements to IP address detection, including added support for CloudFlare IP forwarding, multiple IPs in a single header, and the ability to customize the lookup order and/or add/remove sources that are searched when looking for the current IP address. It's also possible to revert to the old IP address detection behavior (see [How do I customize remote IP detection?](http://zencache.com/kb-article/how-do-i-customize-ip-address-detection/)). Props @jaswsinc. [Issue #449](https://github.com/websharks/zencache/pull/449)
- **Enhancement** (Pro): Files being served by the HTML Compressor were being sent without a `Vary: Accept-Encoding` header, which caused some page speed testing services to give a lower rating to sites using ZenCache. ZenCache now ensures this header is sent via an `.htaccess` file inside the HTML Compressor cache directory (requires Apache 2.1+). Props @jaswsinc. [Issue #436](https://github.com/websharks/zencache/issues/436).
- **Enhancement** (Pro): Static CDN Filters now also filter any static resources inside Text Widgets, so that those resources can be cached by your CDN. Props @jaswsinc. [Issue #430](https://github.com/websharks/zencache/issues/430)
- **Enhancement** (Pro): Static CDN Filters now apply to minified and compressed CSS/JS files generated by the HTML Compressor; these files will now be cached by your CDN. Props @jaswsinc. [Issue #429](https://github.com/websharks/zencache/issues/429)
- **Bug Fix**: Fixed a bug related to the Quick Cache migration that resulted in caching being disabled despite ZenCache being enabled. Uninstalling Quick Cache was removing `define('WP_CACHE', TRUE);` from the `wp-config.php` file. ZenCache now makes sure that caching remains enabled after uninstalling Quick Cache during the ZenCache migration process. [Issue #450](https://github.com/websharks/zencache/issues/450).
- **Bug Fix**: Fixed a minor UI issue where the ZenCache Dashboard icon would occasionally flash to a black color when refreshing the Dashboard. Props @jaswsinc. [Issue #453](https://github.com/websharks/zencache/issues/453).
- **Bug Fix**: When ZenCache was running on an installation of PHP with `open_basedir` restrictions applied, calls to `is_dir()` were triggering a PHP Warning while looking for a writable temporary directory. This bug has been fixed. [Issue #456](https://github.com/websharks/zencache/issues/456).
- **Bug Fix**: Fixed a bug where changing the permalink for a published post would result in the cache file for the old permalink being left behind and as a result both the old and the new permalink would be accessible, instead of WordPress redirecting the old permalink to the new one. This has been fixed and ZenCache now properly clears the old cache file when changing the permalink on a published post. [Issue #359](https://github.com/websharks/zencache/issues/359).
- **Bug Fix**: Fixed a bug where transitioning a Published post back to Pending or Draft would not automatically clear the cache file. This resulted in the post remaining accessible on the frontend despite being set as Pending or Draft. ZenCache now properly clears the cache file automatically when transitioning from Published to Pending or Draft, which prevents access to the post as expected. [Issue #441](https://github.com/websharks/zencache/issues/441).
- **Bug Fix** (Pro): Some users reported seeing `Warning: trim() expects parameter 1 to be string`. This was produced by a low-impact bug that has been fixed in this release. [Issue #455](https://github.com/websharks/zencache/issues/455).
- **Bug Fix** (Pro): Some users running the HTML Compressor reported seeing 403 Forbidden errors related to loading the compressed/minified files. This was a permissions-related issue that has been resolved in this release. Props @superian and @jaswsinc. See [Issue #50](https://github.com/websharks/html-compressor/issues/50).
- **Bug Fix** (Pro): Some HTTP requests made by the HTML Compressor to servers configured to reject HTTP requests that don't include a User-Agent were failing. A User-Agent is now always used in all requests. Props @jaswsinc. See [Issue #49](https://github.com/websharks/html-compressor/issues/49).
- **Minimum Required PHP Version Bumped to PHP v5.3.2+:** The minimum PHP version required for ZenCache has been bumped up to PHP v5.3.2+ (from PHP v5.3+). The ZenCache cache locking mechanism, specifically the use of `flock()`, requires behavior introduced in PHP v5.3.2. [Issue #444](https://github.com/websharks/zencache/issues/444).

= v150314 =

- **Bug Fix**: Fixed a bug in the Quick Cache back compat. method `\zencache\share\apply_wp_filters()`. It was not passing the ZenCache-filtered value through the Quick Cache back compat. routine. This bug affected sites that had disabled automatic cache clearing routines via a filter.
- **Bug Fix** (Pro): Updated Static CDN Filters whitelist to include font file extensions `eot,ttf,otf,woff`, as site owners may wish to serve fonts with these extensions from the CDN (this would require a custom CORS configuration; see [this article](http://davidwalsh.name/cdn-fonts) for instructions). See [#427](https://github.com/websharks/zencache/issues/427).
- **Bug Fix** (Pro): Updated Static CDN Filters blacklist to exclude font file extensions `eot,ttf,otf,woff` by default, as they require custom CORS configuration to display properly. See [#427](https://github.com/websharks/zencache/issues/427).
- **Bug Fix** (Pro): The Static CDN Filters feature was calling a protected method that was requiring PHP v5.4+ but now the Static CDN Filters feature works with PHP v5.3+. See [#426](https://github.com/websharks/zencache/issues/426).
- **Bug Fix**: Fixed a zero-byte `advanced-cache.php` bug related to migrating from Quick Cache. When you deleted/uninstalled Quick Cache after upgrading to ZenCache, the Quick Cache uninstallation process would empty the `advanced-cache.php` file, resulting in the site no longer being cached, despite ZenCache being active. ZenCache now continuously checks to make sure that `advanced-cache.php` is installed properly. See [#432](https://github.com/websharks/zencache/issues/432).

= v150218 =

**Announcing ZenCache, formerly Quick Cache!**

We are very excited to announce the release of [ZenCache](http://zencache.com), an advanced WordPress caching plugin inspired by simplicity. ZenCache is the successor to Quick Cache, a very popular WordPress caching plugin that has been downloaded over 1 million times and won acclaim for its speed, simplicity, and ease of configuration. [Read the full announcement here](http://zencache.com/announcing-zencache-formerly-quick-cache/).

ZenCache is an evolution of Quick Cache. It comes with a completely refactored codebase, subtle UI enhancements, internal optimizations; along with full backward compatibility with all previous versions of Quick Cache Lite and Quick Cache Pro. In fact, installing ZenCache on a site that was previously running Quick Cache is a piece of cake! Watch [this video](http://zencache.com/kb-article/how-to-migrate-from-quick-cache-lite-to-zencache-lite/) to learn more. All configuration options are preserved.

- **New Pro Feature: CDN Integration:** With the announcement and release of ZenCache we are excited to also announce a new, highly-requested feature: Static CDN Filters. This feature allows you to serve some and/or ALL static files on your site from a CDN of your choosing. See also: [Introduction to Static CDN Filters](http://zencache.com/kb-article/introduction-to-static-cdn-filters/).
- **Full Backwards Compatibility with Quick Cache and Quick Cache Pro:** ZenCache is fully backwards compatible with all previous versions of Quick Cache Lite and Quick Cache Pro. Simply install ZenCache and your existing Quick Cache options will preserved by ZenCache.

= v150129 =

- **Bug Fix** (Pro): Fixed a bug where the Pro Updater would fail when FTP or SFTP details via the WordPress Dashboard are required to perform updates. Props @jaswsinc. See [#389](https://github.com/websharks/quick-cache/issues/389).
- **Bug Fix**: Several fixes for a stubborn bug related to "Fatal Error: 'Unable to clear dir'" error messages and errors referencing "SplFileInfo::getMTime(): stat failed". Props @jaswsinc. See [#397](https://github.com/websharks/quick-cache/issues/397).
- **Bug Fix** (Pro): Fixed a bug where the `qcAC` variable (used to force-enable/disable GET Request caching) was not respected properly whenever a URL contained a query string and a user was currently logged into the site. Props @jaswsinc. See [#401](https://github.com/websharks/quick-cache/issues/401).

= v141231 =

- **Bug Fix**: Addressed another issue related to "Fatal Error: 'Unable to clear dir'" and tmp directories that don't get cleared by Quick Cache. This fix discards iteration references before renaming the tmp directories. Props @jaswsinc. See [#288](https://github.com/websharks/quick-cache/issues/288).
- **Bug Fix**: We have had a few reports of getMTime Stat failing when clearing the cache. This fix clears the Stat Cache before iteration when deleting files from the cache directory. Props @jaswsinc. See [#385](https://github.com/websharks/quick-cache/issues/385).
- **Enhancement**: Added a new filter (`quick_cache\\share_disable_cache_locking`) to allow disabling cache locking. Simply return boolean `TRUE` to this filter to disable cache locking. This may be useful for site owners who are experiencing issues with cache locking on web hosting platforms with filesystems that don't properly support locking. Note that this filter must be applied using an Advanced Cache Plugin (see **Dashboard → Quick Cache → Plugin Options → Theme/Plugin Developers**). See also [#387](https://github.com/websharks/quick-cache/issues/387).
- **Enhancement**: Added a new filter that allows forcing Semaphore cache locking on hosting platforms where `sem_get()` is available and would result in improved performance. Return `sem` to the `quick_cache\\share::cache_lock_lock_type` filter to force Semaphore cache locking, or `flock` to use the default method that uses `flock()`. Note that this filter must be applied using an Advanced Cache Plugin (see **Dashboard → Quick Cache → Plugin Options → Theme/Plugin Developers**). See also [#387](https://github.com/websharks/quick-cache/issues/387).

= v141205 =

- **Bug Fix**: Addressed another issue with "Fatal Error: 'Unable to clear dir'" messages by adding new blocking methods for cache lock and unlock, making it so that cache writes (including clearing, purging, wiping) all gain an exclusive lock on the cache directory while work is underway. Props @jaswsinc. See [#288](https://github.com/websharks/quick-cache/issues/288).
- **Bug Fix**: Fixed a Home Page clearing bug that arose in the previous release as the result of an extra leading `\/` in one of our regex patterns. Props @jaswsinc. See [#365](https://github.com/websharks/quick-cache/issues/365).

= v141110 =

- **Quick Cache is changing its name to ZenCache!** See [the blog post](http://www.websharks-inc.com/post/quick-cache-is-changing-its-name/) for full details.
- **Enhancement**: All dashboard notices will now include the number of files cleared for each notice.  Props @jaswsinc. See [#351](https://github.com/websharks/quick-cache/pull/351).
- **Enhancement**:  Quick Cache is now capable of doing atomic clearing/purging/wiping. On a Multisite network, this is now accomplished on a blog-specific basis, without needing to scan the entire network-wide cache directory. This should improve performance considerably on large networks (i.e. those with VERY large cache directories). Props @jaswsinc. See [#288](https://github.com/websharks/quick-cache/issues/288) and [#351](https://github.com/websharks/quick-cache/pull/351).
- **Enhancement**: Added a new class file (`/includes/utils-feed.php`) and refactored the XML feed clearing routine. With these utilities in place, the `auto_clear_xml_feeds_cache()` method is now much easier to deal with and comprehend. Props @jaswsinc. See [#351](https://github.com/websharks/quick-cache/pull/351).
- **Enhancement**: Quick Cache now scans each scheme sub-directory, i.e. `/http/` and `/https/`, separately to help break apart what was previously a much larger directory scan for sites that serve pages over both schemes. This will improve performance on both standard and Multisite network installs. See [#351](https://github.com/websharks/quick-cache/pull/351).
- **Enhancement**: Refactored codebase to improve modularity by creating new methods in `includes/share.php` for network-wide clearing/purging/wiping, host/blog-specific clearing/purging/wiping, recursively deleting a directory, assisting in translations, and several new string utilities. Props @jaswsinc. See [#351](https://github.com/websharks/quick-cache/pull/351).
- **Enhancement**: Renamed all `\quick_cache\plugin::auto_purge_*` methods, giving them the proper prefix of `auto_clear_*` instead to reflect proper Wipe/Clear/Purge terminology. Along with this, all of the `auto_purge_` option keys have been renamed as well. See new [Wipe/Clear/Purge wiki article](http://www.websharks-inc.com/r/quick-cache-wipeclearpurge-terminology-wiki/) for a description of these terms. Props @jaswsinc. See [#351](https://github.com/websharks/quick-cache/pull/351).
- **Enhancement**: All URI and Referrer exclusion patterns are now caSe insensitive. WordPress v4.0 will serve URIs without cAse sensitivity. All of the Quick Cache codebase has been updated to support caSe insensitive clearing/purging/wiping; along with caSe insensitive pattern matching against URIs. Props @jaswsinc. See [#351](https://github.com/websharks/quick-cache/pull/351).
- **Bug Fix (Pro)**: Fixed a bug with the Auto-Clear Author Cache routine that was presenting dashboard notices even when `change_notifications_enable` was off. Props @jaswsinc. See [#351](https://github.com/websharks/quick-cache/pull/351).
- **Bug Fix (Pro)**: Fixed a bug with the HTML Compressor where `style`, `link` and/or `script` tags could become out of order in certain scenarios. See: [#45](https://github.com/websharks/html-compressor/issues/45)
- **Bug Fix (Pro)**: Several HTML Compressor bug fixes related to JavaScript compression routines. See [#38](https://github.com/websharks/html-compressor/issues/38)
- **Bug Fix**: Fixed a bug that was occasionally generating "Fatal Error: 'Unable to clear dir'" messages. The root cause of this is believed to be non-atomic purging of cache directories which, on a busy site, could result in new cache files being created before a purging routine was finished clearing the directory. Clearing/purging/wiping is now atomic in all scenarios. Props @jaswsinc. See [#288](https://github.com/websharks/quick-cache/issues/288) and [#351](https://github.com/websharks/quick-cache/pull/351).
- **Bug Fix**: Fixed a bug in the Auto-Clear Custom Post Type Archive Cache that was not checking for the `$post_id` in `$this->static[__FUNCTION__]`. Props @jaswsinc. See [#351](https://github.com/websharks/quick-cache/pull/351).
- **Bug Fix**: Fixed a bug with the uninstaller whereby attempting to uninstall after receiving a notice that PHP 5.3+ is required would result in a blank screen and require manual removal of the plugin. See [#334](https://github.com/websharks/quick-cache/issues/334).
- **Bug Fix**: Fixed an issue with detecting cacheable requests that was, in rare instances, resulting in blank white pages for some site owners. See [#279](https://github.com/websharks/quick-cache/issues/279).

= v141001 =

- **Enhancement**: Improved Dashboard messaging for the `auto_clear_cache()` routine. See [#328](https://github.com/websharks/quick-cache/issues/328).
- **Enhancement (Pro)**: Improved consistency of Auto-Cache Engine User-Agent string by removing WordPress-version-specific identifier. See [#315](https://github.com/websharks/quick-cache/issues/315).
- **Enhancement (Pro)**: It is now possible to disable the automatic clear and wipe cache routines. If you have a very large site with many cache files, this feature allows you manual control over when the cache gets cleared or wiped. For complete documentation on this feature, see [Quick Cache Wiki - Clear and Wipe Cache Routines](https://github.com/websharks/quick-cache/wiki/Clear-Cache-and-Wipe-Cache-Routines). See also [#23](https://github.com/websharks/quick-cache/issues/23).
- **Bug Fix**: Fixes an issue with the Pro Preview mode where saving the plugin options may inadvertently save Pro-only options that could later cause issues if the plugin is upgraded to the Pro version. See [#311](https://github.com/websharks/quick-cache/pull/311#issuecomment-54491922).
- **Bug Fix**: Fixes an edge-case where the proper cache files do not get purged when a user with the Editor role publishes a new post with both a category and a tag associated with the post. See [#313](https://github.com/websharks/quick-cache/issues/313).
- **Bug Fix**: When Quick Cache purges/resets the cache for a post with a Custom Post Type, any cache files for the associated Custom Post Type archive view are now auto-purged, along with any associated XML feed cache files. See [#280](https://github.com/websharks/quick-cache/issues/280).
- **Bug Fix**: Fixed missing trailing slash in Directory / Expiration Time configuration panel. This was purely a visual inconsistency and had no effect on Quick Cache's functionality. See [#267](https://github.com/websharks/quick-cache/issues/267).
- **Bug Fix**: When saving custom CSS using JetPack's Custom CSS module, Quick Cache now properly purges the site cache to ensure that outdated cache files are not served to visitors. See [#246](https://github.com/websharks/quick-cache/issues/246).
- **Bug Fix**: When changes are made to WordPress General, Reading, Discussion, or Permalink settings (Dashboard -> Settings), Quick Cache now clears the cache to prevent an outdated cache file from being served to visitors. See [#223](https://github.com/websharks/quick-cache/issues/223).
- **Bug Fix**: When an active plugin is updated, or when an active theme or the parent theme for an active child theme is updated, or when WordPress Core is updated, Quick Cache now properly clears the cache to ensure that an outdated cache file does not get served to a visitor. See [#145](https://github.com/websharks/quick-cache/issues/145) and [#327](https://github.com/websharks/quick-cache/issues/327).
- **Bug Fix**: When a new comment is posted, Quick Cache now properly purges the cache files for any paginated comment pages. See [#336](https://github.com/websharks/quick-cache/issues/336).
- **Bug Fix (Pro)**: Fixed a bug where the HTML Compressor, when enabled with the "remove extra whitespace in the final HTML" option, would incorrectly remove `<!DOCTYPE html>`. See [#299](https://github.com/websharks/quick-cache/issues/299).
- **Bug Fix (Pro)**: Fixed a bug in the HTML Compressor that would, in certain scenarios, leave behind fragments of HTML comments. See [#295](https://github.com/websharks/quick-cache/issues/295).
- **Bug Fix (Pro)**: The Auto-Cache Engine now has an option to configure a delay between each request when pre-caching the site. There were some reports of the Auto-Cache Engine causing load issues with large sites on servers that sometimes had trouble handling many requests. See [#294](https://github.com/websharks/quick-cache/issues/294).
- **Bug Fix (Pro)**: Fixes an Auto-Cache Engine scheduling issue that may, in some scenarios, prevent it from running as expected. See [#291](https://github.com/websharks/quick-cache/issues/291).
- **Bug Fix (Pro)**: Fixed a bug with the HTML Compressor where `style`, `link` and/or `script` tags could end up out of order in certain scenarios. See [#45](https://github.com/websharks/html-compressor/issues/45).
- **Bug Fix (Pro)**: Fixed a bug in the HTML Compressor related to JavaScript compression routines. See [#38](https://github.com/websharks/html-compressor/issues/38).

= v140829 =

- **SECURITY FIX - Please upgrade immediately**: Fixes a security related to cached cookies sent in the header of a request. This only affects sites running plugins that might send cookie data via the header. See [#253](https://github.com/websharks/zencache/issues/253)
- **Enhancement**: Auto-Purge RSS Feeds. ZenCache will now automatically purge the cache for RSS/RDF/Atom Feeds when Feed Caching is enabled. This new option will purge the cache for the master feed, the master comments feed, feeds associated with comments on a Post/Page, term-related feeds (including mixed term-related feeds), and author-related feeds when you update a Post/Page, approve a Comment, or make other changes where ZenCache can detect that certain types of Feeds should be purged. See [#182](https://github.com/websharks/zencache/issues/182)
- **Enhancement**: Improve handling of symlink creation for 404 cache files by using atomic symlink creation to decrease the possibility of encountering a race condition. See [#242](https://github.com/websharks/zencache/issues/242).
- **Enhancement**: Improved portability of `advanced-cache.php`. This will help reduce configuration overhead for site owners when migrating a WordPress installation from one server to another. See [#258](https://github.com/websharks/zencache/issues/258).
- **Enhancement**: Option Panels now have proper HTML anchor tags so that they work better with browser extensions that rely on anchor tags being available. See [#260.](https://github.com/websharks/zencache/issues/260)
- **Enhancement**: The Plugin Deactivation Safeguards option has been renamed to Plugin Deletion Safeguards. When Plugin Deletion Safeguards are disabled, deactivating and deleting the plugin will now erase your options for the plugin, erase directories/files created by the plugin, remove the advanced-cache.php file, terminate CRON jobs, etc. It completely erases itself, but only when you disable Plugin Deletion Safeguards (enabled by default to prevent accidental loss of data). See [#261](https://github.com/websharks/zencache/issues/261).
- **Enhancement (Pro)**: HTML Compressor now includes FOPEN as transport layer fallback in case cURL is not available. See [#15](https://github.com/websharks/html-compressor/issues/15)
- **Enhancement (Pro)**: HTML Compressor now writes files atomically; this will help avoid race conditions when writing cache files. See [#273](https://github.com/websharks/zencache/issues/273)
- **Enhancement (Pro)**: Improved error handling for the Auto-Cache Engine. There were some scenarios where `XMLReader()` would fail with a PHP Warning notice when it was unable to properly parse the sitemap. See [#250](https://github.com/websharks/zencache/issues/250).
- **Bug Fix**: The cache directory is now properly removed when deleting the plugin from the WordPress Dashboard plugins list. See [#261](https://github.com/websharks/zencache/issues/261).
- **Bug Fix**: WooCommerce compatibility fix for a bug where cart session data appeared to get cached across sessions. See [#253](https://github.com/websharks/zencache/issues/253)
- **Bug Fix (Pro)**: The plugin upgrade notice no longer appears on Child Blogs in a Multisite Network. There was no security risk here; while the upgrade notice was shown, Child Blog admins who did not have permission to upgrade Network-activated plugins were unable to do anything with the message. See [#259](https://github.com/websharks/zencache/issues/259).
- **Bug Fix (Pro)**: Fixed a bug where, in certain scenarios, a WordPress Plugin may break the JavaScript that controls the Clear Cache button on the Dashboard. See [#272](https://github.com/websharks/zencache/issues/259).
- **Bug Fix (Pro)**: CSS files are now excluded from compression by the HTML Compressor when included inside conditional comments. See [#35](https://github.com/websharks/html-compressor/issues/35)
- **Bug Fix (Pro)**: HTML Compressor now preserves whitespace inside CSS `calc()` statements. See [#286](https://github.com/websharks/zencache/issues/286).

= v140725 =

- **Enhancement**: Improved overall performance by optimizing the auto-purge routines. See also: [#130](https://github.com/websharks/zencache/issues/130)
- **Enhancement**: The "GET Requests" UI Panel now explains that you can use `?zcAC=0` to disable caching when you ARE caching GET Requests. See also: [#210](https://github.com/websharks/zencache/issues/210).
- **New Pro Feature: Auto-Purge XML Sitemaps**. If you're generating XML Sitemaps with a plugin like Google XML Sitemaps, you can now tell ZenCache to automatically purge any cached sitemap files whenever it purges a Post/Page cache. You may also specify a list of XML Sitemap patterns to clear, if you have multiple sitemap files. See also: [#169](https://github.com/websharks/zencache/issues/169)
- **Enhancement (Pro)**: The ZenCache Pro Updater now accepts a License Key in place of the WebSharks password.
- **Enhancement (Pro)**: In a Multisite Network, the Auto-Cache Engine will now also auto-cache each child blog. See also: [#169](https://github.com/websharks/zencache/issues/169)
- **Bug Fix**: Fixed a bug that was causing unapproved, spam, and trash comments to unnecessarily purge the cache. See also: [#159](https://github.com/websharks/zencache/issues/159)
- **Bug Fix**: A custom `WP_CONTENT_DIR` is now obeyed in the scenario where it's set to a path outside of `ABSPATH`. See also: [#95](https://github.com/websharks/zencache/issues/95)
- **Bug Fix**: The UI now correctly displays custom `WP_CONTENT_DIR` in the "Directory/Expiration Time" options panel. See also: [#206](https://github.com/websharks/zencache/issues/206)
- **Bug Fix**: ZenCache LITE now correctly sets the `ZENCACHE_PRO` constant to false. See also: [#229](https://github.com/websharks/zencache/issues/229)
- **Bug Fix**: Workaround for broken page navigation on the front page of some sites. This is a WordPress `redirect_canonical()` bug workaround. See also: [#209](https://github.com/websharks/zencache/issues/209)
- **Bug Fix (Pro)**: 404 Caching now properly returns a 404 HTTP Status code when serving a cached 404 page. See also: [#197](https://github.com/websharks/zencache/issues/197)
- **Bug Fix (Pro)**: The HTML Compressor now properly preserves `[]` character whitespace during CSS compression. See also: [#25](https://github.com/websharks/html-compressor/issues/25)
- **Bug Fix (Pro)**: The Pro Updater upgrade link now points to the correction location when displayed from a Child Blog in a Multisite Network. See also: [#205](https://github.com/websharks/zencache/issues/205)
- **Bug Fix (Pro)**: The Auto-Cache Engine now correctly handles the sitemap when `home_url()` differs from `site_url()`.
- **Bug Fix (Pro)**: The "Dynamic Version Salt" options panel now correctly displays the last saved value. See also: [#231](https://github.com/websharks/zencache/issues/231)

= v140605 =

- **New Feature**: Branched Cache Structure. Cache files are now written to the cache directory using a more intuitive format of `PROTOCOL`/`HOSTNAME`/`PERMALINK` (e.g., `http/example-com/sample-page.html`). For more details, please see <http://www.websharks-inc.com/r/zencache-branched-cache-structure-wiki/>
- **New Feature**: 404 Page caching. It's now possible to enable/disable the caching of 404 requests. Enabling this feature generates a single cache file for your 404 Page and then symlinks future 404 requests to that cache file. See *Dashboard -> ZenCache -> Plugin Options -> 404 Requests*.
- **New Feature (Pro)**: HTML Compressor (experimental). This new experimental feature automatically combines and compresses CSS/JS/HTML code. See *Dashboard -> ZenCache -> Plugin Options -> HTML Compressor*. For more details about how this feature works, please see <https://github.com/websharks/HTML-Compressor>
- **New Feature (Pro)**: Auto-Cache Engine. When enabled, the Auto-Cache Engine will pre-cache your site at 15-minute intervals, rebuilding cache files when necessary (it will not rebuild cache files until they have expired). This helps eliminate the slowness a user may experience when visiting a page on your site that has not yet been cached. See *Dashboard -> ZenCache -> Plugin Options -> Auto-Cache Engine*.
- **New Feature**: Auto-Purge "Author Page". When a single Post/Page is changed in some way, ZenCache can also purge any existing cache files for the associated Author Page. See *Dashboard -> ZenCache -> Plugin Options -> Clearing the Cache*. (This option is enabled by default; disabling this requires ZenCache Pro.)
- **New Feature**: Auto-Purge "Category Archives". When a single Post/Page is changed in some way, ZenCache can also purge any existing cache files for the associated Category archive views. See *Dashboard -> ZenCache -> Plugin Options -> Clearing the Cache*. (This option is enabled by default; disabling this requires ZenCache Pro.)
- **New Feature**: Auto-Purge "Tag Archives". When a single Post/Page is changed in some way, ZenCache can also purge any existing cache files for the associated Tag archive views. See *Dashboard -> ZenCache -> Plugin Options -> Clearing the Cache*. (This option is enabled by default; disabling this requires ZenCache Pro.)
- **New Feature**: Auto-Purge "Custom Term Archives". When a single Post/Page is changed in some way, ZenCache can also purge any custom Terms that may have their own Term archive views. See *Dashboard -> ZenCache -> Plugin Options -> Clearing the Cache*. (This option is enabled by default; disabling this requires ZenCache Pro.)
- **Enhancement**: Improved conflict handling of other plugins using `ob_start()`. See <https://github.com/websharks/zencache/issues/97>
- **Enhancement**: Added a postload filter for `status_header` so that ZenCache can properly detect calls to the WP core function `status_header()`
- **Enhancement**: The ZenCache cache directory has been changed to `wp-content/cache/zencache/` to provide better organization of cache files and avoid interfering with another plugin that may also be writing to the `wp-content/cache/` directory. See <https://github.com/websharks/zencache/issues/123>
- **Enhancement**: New detailed debugging notes (see *Dashboard -> ZenCache -> Plugin Options -> Enable/Disable*). There is now an extra option to show detailed debugging information in addition to the ZenCache notes in the HTML source. For now, this feature only applies when the HTML Compressor is enabled.
- **Enhancement**: Better Debugging Notices. If ZenCache is not caching a particular page (such as when a logged-in user visits the site and logged-in user caching is not enabled), ZenCache will now report why that page is not being cached in the HTML notes.
- **Enhancement**: Improved compatibility with the Nav Menu Roles plugin. See <https://github.com/websharks/zencache/issues/164>
- **Bug Fix**: Obey custom content directories. If you have customized your `WP_CONTENT_DIR` and `WP_CONTENT_URL` constants to point somewhere other than the default, ZenCache will now obey those and use your custom directory for storing cache files. See <https://github.com/websharks/zencache/issues/95>
- **Bug Fix**: Scheduled posts now trigger the clearing of any associated archive views when those posts go live (assuming you have those archive views set to Auto-Purge in *Dashboard -> ZenCache -> Plugin Options -> Clearing the Cache*). See <https://github.com/websharks/zencache/issues/26>
- **Bug Fix**: Fixed a bug where saving a post as `draft` would trigger the Auto-Purge Post routine and clear the cache for that post. Now only purges post status `publish` and `private` and when transitioning from `publish` or `private` post status to `draft`, `future`, or `private`. See <https://github.com/websharks/zencache/issues/43>
- **Bug Fix**: Split/paginated comments and multi-page Posts/Page cache files are now purged properly when the post cache is purged. See <https://github.com/websharks/zencache/issues/75>

= v140104 =

* **New Options for Feed Caching**. It's now possible to control RSS, RDF, and Atom Feed caching. The new default is for feed caching to be disabled, which resolves an issue where new posts don't show up in the feed until the cache is cleared. This version of ZenCache disables feed caching to prevent this from happening. If you wish to cache feeds, you can enable feed caching in the options. See: <https://github.com/websharks/zencache/issues/44>
* **New Automatic Updater for ZenCache Pro**. ZenCache Pro now includes an automatic updater which lets you to keep ZenCache Pro updated right from within your WordPress Dashboard. To upgrade to a new version of ZenCache Pro using the Automatic Updater, simply fill in your WebSharks-Inc.com credentials in the new Plugin Updater sub-panel (**ZenCache Pro -› Plugin Updater**). See: <https://github.com/websharks/zencache/issues/21>

= v131224 =

* **New Lite Enhancement**. The Home Page cache and Posts Page cache are now automatically purged when necessary (such as when a new post is published). See: <https://github.com/websharks/zencache/issues/40>
* Improved ZenCache version check notice.
* Improved ZenCache options validation.
* **Bug Fix**. ZenCache was previously not properly excluding systematic WordPress areas reliably, e.g. any file that begins with `wp-` and/or the `xmlrpc` file. These are now properly auto-excluded. On Multisite installations, `/files/` is also auto-excluded from being cached. This bug required fixing incorrect instances of `[?$]` in regex patterns. See: <https://github.com/websharks/zencache/issues/41>
* **Multisite Enhancement**. When running ZenCache on Multisite Network installation, only allow the plugin to be "Network Activated" (becuase that is how ZenCache is designed to work). See: <https://github.com/websharks/zencache/issues/50>
* **Multisite Enhancement**. New 'Wipe' button allows a site owner to clear (wipe) the cache for all sites in a Multisite Network at once. See: <https://github.com/websharks/zencache/issues/48>
* **Multisite Bug Fix**. Clearing the cache on a Multisite Network configured to use sub-directories now works properly. See: <https://github.com/websharks/zencache/issues/39>
* **Multisite Bug Fix**. Fixed unmatched closing parenthesis in regex. See: <https://github.com/websharks/zencache/issues/37>
* **Multisite Bug Fix**. Added support for `PATH_CURRENT_SITE` and `$GLOBALS['base']`.
* **Multisite Bug Fix**. Removed depreciated VHOST code that was causing issues with clearing the cache.

= v131206 =

* **New Pro Feature**. It's now possible for developers to add custom PHP code to the cache clearing routines (e.g. custom code which might consider things like APC or memcache also). This requires [ZenCache Pro](http://www.websharks-inc.com/product/zencache/). Please check your Dashboard under: **ZenCache Pro -› Clearing the Cache**. See also: [this screenshot](https://f.cloud.github.com/assets/1563559/1692324/7ae902c4-5e78-11e3-98ba-acbb08b30585.png).
* **Multisite Bug Fix**. Unable to clear the cache when running sub-directories. See: <https://github.com/websharks/zencache/issues/30>
* **Multisite Bug Fix**. The "Clear Cache" button was not displayed for child blogs in a network. Fixed in this release.

= v131205 =

* Added hook to `wp_set_comment_status` to purge the comment cache when a comment status changes.
* Ignore `set_time_limit()` errors in case function is disabled in PHP configuration. This is a temporary fix and will be handled more appropriately in a future maintenance release. See also: <https://github.com/websharks/zencache/issues/20>
* Added Raam Dev to the contributors list. Raam will now be leading the development of ZenCache and ZenCache Pro.

= v131128 =

* **New Plugin Architecture for ZenCache.** This release introduces a new way for theme/plugin developers to modify the way ZenCache operates at the `advanced-cache.php` level (e.g. very early-on). For further details on this, please check your Dashboard under: `ZenCache -› Theme/Plugin Developers`. See also: <https://github.com/websharks/zencache/issues/17>
* **Compatibility.** This release further improves PHP v5.3 detection. ZenCache will now generate an administrative notice instead of a PHP exception; allowing the plugin to be activated, but without actually loading the plugin under this scenario. A notice to the site owner is helpful in cases where the plugin is NOT being updated through the Dashboard. This will remove the risk of crashing a site that's attempting to run ZenCache w/o PHP v5.3+ installed. See also: <https://github.com/websharks/zencache/issues/13>

= v131127 =

* **Compatibility.** This release improves PHP v5.3 detection. ZenCache will now generate an administrative notice instead of a PHP exception; allowing the plugin to be activated, but without actually loading the plugin under this scenario. A notice to the site owner is helpful in cases where the plugin is NOT being updated through the Dashboard. This will remove the risk of crashing a site that's attempting to run ZenCache w/o PHP v5.3+ installed. See also: <https://github.com/websharks/zencache/issues/13>
* **New Pro Feature.** Clear Home Page (and/or Posts Page) on auto-purge. See: <https://github.com/websharks/zencache/issues/11>
* **Bug Fix (Options -Indexes).** Removing unnecessary `.htaccess` file from the `/wp-content/plugins/zencache/` directory that prevented directory indexing, as this is not compatible in all hosting environents. See: <https://github.com/websharks/zencache/issues/9>
* **Bug Fix (ABSPATH).** Incorrect detection of the `/wp-config.php` file on sites that move this file up one directory. Fixed in this release. See: <https://github.com/websharks/zencache/issues/7>
* **Bug Fix (Parse Error).** Correcting code that deals with an edge case where the `/wp-config.php` file may become corrupted upon deactivation of the ZenCache plugin through the WP Dashboard. Fixed in this release. See: <https://github.com/websharks/zencache/issues/6>
* **Bug Fix (Error Reporting).** Improving error message via Dashboard whenever permissions are an issue in one specific scenario. See: <https://github.com/websharks/zencache/issues/16>
* **Enhancement (Pro Preview).** Adding a more visible way to disable Pro Preview mode in the Lite version of ZenCache. See: <https://github.com/websharks/zencache/issues/2>
* **Emergency Scenario** Adding notes in several sections of the `reamde.txt` file regarding "what to do in an emergency scenario".
* **See Also** <https://github.com/websharks/zencache/issues?page=1&state=closed>

= v131121 =

* Updated to support all features and functionality of WordPress v3.7+ (this new release of ZenCache requires WordPress v3.7+). The ZenCache plugin is now being actively maintained and future updates and improvements will be released periodically by lead developer Jason Caldwell. The popularity of this plugin and recent acknowldegments at WordCamp in Boston have inspired Jason to revamp ZenCache!
* The latest version of ZenCache is a complete rewrite (OOP design). Faster! and even more dependable. NOTE: the free version of ZenCache (this new LITE version); while it remains fully functional and is more-than-adequate for most sites; is now limited in some ways. The following advanced features from the previous release are no longer available in the lite version: a custom MD5 Version Salt; custom Exclusion Patterns; the Clear Cache button in the admin bar. These, and MANY other brand new features are now available only in the pro version of the plugin. For further details, please see: <http://www.websharks-inc.com/product/zencache/>.
* Bug fix. ZenCache now considers the `HTTPS` evironment variable in order to prevent cache collisions on sites that serve pages over SSL. Nothing to configure, this is now built into the ZenCache engine.
* UI updates. An improved user interface makes configuring this plugin a dream! ZenCache got an awesome makeover in this release.
* Improved support for multisite networks. It's never been easier to run ZenCache on a multisite network. For further details, please see: **Dashboard -› Network Admin -› ZenCache** when/if you have Multisite Networking enabled in WordPress.
* Update; PUT and DELETE requests are now considered by ZenCache. By default, ZenCache does NOT serve cached pages to users who are logged in, or to users who have left comments recently. ZenCache also excludes administrative pages, login pages, POST/PUT/DELETE/GET(w/ query string) requests and/or CLI processes.
* Dropping support for `ob_gzhandler()`; and the like. ZenCache will now throw PHP exceptions to warn you about this should it be an issue in your hosting environment. If you want to enable GZIP, please follow the instructions provided by ZenCache and avoid the use of `ob_gzhandler()` as this is not a recommended way to enable GZIP on any hosting platform.
* Truly atomic cache file write updates. Removing support for SEM vs. FLOCK for file locking. ZenCache no longer needs a mutex file. Cache file updates are written to a temp file and renamed for the best reliability and improved speed too!
* Localization. ZenCache is now translatable. This release adds support for gettext translations, a very popular method for translating WordPress plugins. All parts of the ZenCache plugin can be localized now. The source code was updated with calls to the `__` function and a new text domain was added: `zencache`. PO translation files should be placed in your plugins directory, example: `/wp-content/plugins/zencache-en_US.mo`; or in `WP_LANG_DIR/plugins/zencache-en_US.mo`.
* Capability requirement. This release of ZenCache requires that an Administrator be logged-in with the Capability of `activate_plugins`. This is a default Capability that comes with the Administrator Role in WordPress. So, unless you've modified your WordPress Roles/Capabilities in some extremely creative way, this should not impact you; just something to be aware of.
* **(Pro Version)** There is now a pro version of this plugin available. Please see: <http://www.websharks-inc.com/product/zencache/>. The initial set of pro features include: the ability to cache logged-in users too! (VERY powerful, particularly for membership sites); a new improved "Clear Cache" button in the admin bar (along with an option to enable/disable this feature); the ability to disable Dashboard notifications related to automatic clearing/purging on change detections; Import/Export functionality for ZenCache configuration files; URI exclusion patterns (now supporting wildcards too); User-Agent exclusion patterns (now supporting wildcards too); HTTP referrer exclusion patterns (now supporting wildcards too); an MD5 Version Salt; and rockstar support for all ZenCache features.
* **(Pro Version)** Regarding URI/User-Agent/HTTP Referrer exclusion patterns. If you configured any of these options in the previous release and would like to continue to use them in this release, please upgrade to the pro version or contact lead developer Jason Caldwell for assistance. Note: if you had these options configured in the previous release, once you upgrade to the pro version they will come back just like they were. Either that, or you may choose to continue using the previous version of ZenCache where this functionality still exists.
* Lite version source code now available on GitHub also: <https://github.com/websharks/zencache>.

= v111203 =

* Updated to support WordPress® v3.3. Backward compatibily remains for WordPress® v3.2.x.

= v110720 =

* Bug fix. Corrected XSS security issue associated with the handling of ``$_SERVER["REQUEST_URI"]`` inside the comment lines that ZenCache introduces at the bottom of the source code.
* Bug fix. Corrected cosmetic issue in WordPress v3.2 related to the positioning of the Clear Cache button.

= v110709 =

* Routine maintenance. No signifigant changes.

= v110708 =

* Routine maintenance. No signifigant changes.
* Compatibility with WordPress v3.2.

= v110523 =

* **Versioning.** Starting with this release, versions will follow this format: `yymmdd`. The version for this release is: `110523`.
* Routine maintenance. No signifigant changes.

= v2.3.6 =

* Routine maintenance. No signifigant changes.

= v2.3.5 =

* Bug fix. Under the right scenario, errors regarding the function `is_user_logged_in()` in the second phase of `advanced-cache.php` have been resolved in this release of ZenCache.
* Compatibility. ZenCache is now capable of dealing with themes/plugins that attempt to use `ob_start("ob_gzhandler")` inside a `header.php` file, or in other places that may create a problem in the nesting order of output buffers. For instance, this release of ZenCache resolves some incompatiblities with Headway themes for WordPress®. Please note that GZIP should be enabled at the Apache level ( i.e. with an .htaccess file ), or in PHP using `zlib.output_compression = on`. Both of these methods are preferred over `ob_start("ob_gzhandler")`. If you must use `ob_start("ob_gzhandler")`, please make this declaration inside your `/wp-config.php` file, and NOT inside `/header.php`, as this creates a problem that ZenCache must work around, and could ultimately prevent GZIP from working at all if you do it this way. For further details on how to enable GZIP with ZenCache, please see the included `/readme.txt` file.

= v2.3.2 =

* Compatiblity. References to `dirname()` that were processed by the ZenCache `/advanced-cache.php` handler should have been using `WP_CONTENT_DIR` for improved compatibility with WordPress® installations that may use non-standardized installation directories and/or symlinks.
* New Filter available for developers. Multisite Super Admins can now give their Child Blog owners the ability to manually clear the cache for their own site in the Network. ZenCache accomplishes this by making the "Clear Cache" button visible in the administrative header for Child Blog owners. If you wish to enable this, you can use this Filter: `add_filter("ws_plugin__qcache_ms_user_can_see_admin_header_controls", "__return_true");`. This button is always visible to Super Admins. Adding this Filter makes it visible to all child Blog Owners as well.

= v2.3.1 =

* Framework updated; general cleanup.
* Optimizations. Further internal optimizations applied through configuration checksums that allow ZenCache to load with even less overhead now.
* Bug fix. ZenCache was suffering from a bug regression related to stale Last-Modified headers being sent with cached copies. This has been resolved in ZenCache v2.3.1+.

= v2.3 =

* Framework updated; general cleanup.
* Updated with static class methods. ZenCache now uses PHP's SPL autoload functionality to further optimize all of its routines.

= v2.2.8 =

* Framework updated; general cleanup.
* Updated for compatibility with WordPress® 3.1.

= v2.2.7 =

* Framework updated. General cleanup.

= v2.2.6 =

* Updated to disable caching on database failures that do not trigger a `5xx` error code. ZenCache is now capable of disabling the cache engine dynamically on all database connection failures within WordPress®.

= v2.2.5 =

* Updated to support all `5xx` error codes. ZenCache now monitors the `status_header` function for `5xx` error codes. If a `5xx` status header is detected, caching is automatically disabled, as it should be.

= v2.2.3 =

* Framework updated. General cleanup.

= v2.2.2 =

* Minor updates to the Ajax clearing routines that were implemented in v2.2.1.
* This update also adds compatibility with (offline) localhost installations of WordPress® (WAMP/MAMP).

= v2.2.1 =

* Support for `glob()` has been added to ZenCache. In previous versions, it was impossible to pinpoint a specific cache file through Dynamic Pruning routines ( at least, not with 100% accuracy ). This was because an MD5 Version Salt *could* have been generated; based on arbitrary conditionals, set by the site owner. ZenCache now stores its cache files with three MD5 hash strings, producing longer file names; but with the added benefit of improved Multisite compatibility, and improvements in optimization overall. ZenCache can now handle dynamic pruning with 100% accuracy. Even supporting complex Multisite installations, with or without `SUBDOMAIN_INSTALL`.
* New feature. ZenCache now integrates a `Clear ZenCache` button into the WordPress® Dashboard. This makes it easy to force a "cache reset, via <code>ajax</code>", without having to navigate through the ZenCache menu for this simple task. Another great benefit to this new button, is that it works in all Dashboard views, even in a Multisite installation across different backends. If you're running a Multisite installation, you can use this new button to clear the cache for a particular site/blog in your network, without interrupting others.
* Bug fix. The Constant `ZENCACHE_ALLOWED` was being defined too early in the buffering routine. This has been resolved in v2.2.1.
* Optimization of `advanced-cache.php`. A few things have been streamlined even further.
* Added compatibility for the [WP Maintenance Mode](http://wordpress.org/extend/plugins/wp-maintenance-mode/) plugin, and also the [Maintenance Mode](http://wordpress.org/extend/plugins/maintenance-mode/) plugin. ZenCache will disable itself when these plugins are enabled for maintenance.
* Added compatibility for other Maintenance Mode plugins that are capable of sending a `Status: 503` header, or a `Retry-After:` header.
* Added compatibility for plugins that create PHP sessions. ZenCache will automatically disable itself when a PHP session is active.
* Added compatiblity for web hosts that insert a port number into the `$_SERVER["HTTP_HOST"]` variable. ZenCache is now capable of handling this gracefully.
* Improvement. Removed references to `$blog_id = 1` in favor of `is_main_site()`; providing support for Multisite Mode, where there are multiple sites, instead of just multiple blogs.
* Updated Dynamic Pruning Hooks for Custom Post Types, and Custom Taxonomies in WordPress® 3.0+.
* Extended compatiblity for ZenCache on SSL enabled blogs.

= v2.1.9 =

* Framework updated; general cleanup.
* Updated minimum requirements to WordPress® 3.0.

= v2.1.8 =

* Framework updated to WS-P-3.0.

= v2.1.7 =

* Bug fix. A bug related to gzinflate variations handled by the WP_Http class has been resolved. This was preventing ZenCache from validating a custom MD5 Version Salt on some servers.
* Framework updated to WS-P-2.3.

= v2.1.6 =

* Auto-Cache Engine. References to `ws_plugin__qcache_curl_get()`, have been replaced by `c_ws_plugin__qcache_utils_urls::remote()`, which makes use of `wp_remote_request()` through the WP_Http class. This removes an absolute dependency on the cURL extension for PHP. This also gives ZenCache/WordPress® the ability to decide with method of communication to use for HTTP requests; based on what the installation server has available. Note: this only affects the Auto-Cache Engine for ZenCache, which is completely optional.
* Compatibility. ZenCache is now smarter about the way it reports errors. For example, when/if there are directory permission issues with your `wp-content` directory; ZenCache can help with this, in a more intuitive fashion.
* Compatibility. Support has been added for WordPress® 3.0 with Multisite/Networking enabled.
* Updated minimum requirements to WordPress® 2.9.2.
* Framework updated to WS-P-2.2.

= v2.1.5 =

* A new option for Dynamic Cache Pruning was added. You can now choose `Single + Front Page`. This makes it possible to Create or Edit a Post/Page, and have the cache automatically updated for that specific Post/Page. And.. in addition, your Front Page ( aka: Home Page ) will also be refreshed at the same time.
* A minor bug was fixed in the Dynamic Cache Pruning routines. This bug was originally introduced in ZenCache v2.1.1, and has now been corrected in v2.1.5. This bug, under certain circumstances, was preventing ZenCache from locating an expired md5 cache file, for some Posts/Pages being updated.
* Advanced feature addition. ZenCache now comes bundled with a robust Auto-Cache Engine. This is an optional feature, for VERY advanced users. You'll find the new Auto-Cache Engine listed right along with all of the other ZenCache options. This works in conjunction with an XML Sitemap.

= v2.1.4 =

* Advanced feature addition. You can now prevent caching dynamically whenever pages on your site receive traffic from specific URLs, specific domains, or even specific word fragments found within the HTTP_REFERER. This feature is very advanced, and will NOT impact your site unless you decide to use it for one reason or another.

= v2.1.3 =

* Added `De-Activation Safeguards` to the ZenCache options panel.
* Updated the ZenCache options panel. It's been given a make-over.
* Stable tag updated in support of tagged releases within the repository at WordPress.org.

= v2.1.2 =

* WebSharks Framework for Plugins has been updated to P-2.1.
* Updated caching routines in support of hosting providers running with CGI/FastCGI. ZenCache has been tested with VPS.net, HostGator, BlueHost, (mt) Media Temple (gs) and (dv), The Rackspace Cloud, and several dedicated servers ( including some Amazon EC2 instances ) running with Apache; including support for both `mod_php` and also `CGI/FastCGI` implementations. ZenCache should work fine with any Apache/PHP combination. Please report all bugs through the [Support Forum](http://www.primothemes.com/forums/viewforum.php?f=5).
* An issue was discovered with WordPress® MU `/files/` being accessed through `htaccess/mod_rewrite`. ZenCache has been updated to exclude all `/files/` served under WordPress® MU, which is the way it should be. Requests that contain `/files/` are a reference to WordPress® Media, and there is no reason, to cache, or send no-cache headers, for Media. ZenCache now ignores all references to `/files/` under WordPress® MU. This problem was not affecting all installations of WPMU, because there already are/were scans in place for Content-Type headers. However, under some CGI/FastCGI implementations, this was not getting picked on WMPU with `mod_rewrite` rules. This has been resolved in v2.1.2.

= v2.1.1 =

* A WPMU bug was corrected in ZenCache v2.1.1. This bug was related to `HTTP_HOST` detection under WordPress® MU installations that were using sub-domains. Please thank `QuickSander` for reporting this important issue.

= v2.1 =

* ZenCache has added further support for themes and plugins that dynamically set `Content-Type` headers through PHP routines. ZenCache is now smart enough to automatically disable itself whenever a theme or plugin sends a `Content-Type` header that would be incompatible with ZenCache. In other words, any `Content-Type` header that is not a variation of `HTML, XHTML or XML`.
* ZenCache has also been upgraded to support the preservation of scripted headers sent by PHP routines. If a plugin or theme sends scripted headers ( using the `header()` function in PHP ), those headers will be preserved. They'll be stored along with the cache. This allows them to be sent back to the browser whenever a cached version is served on subsequent visits to the original file.
* Compatability checked against WordPress.org 2.9.1, 2.9.2 &amp; WordPress MU 2.9.1, 2.9.2. Everything looks good. No changes required.

= v2.0 =

* A few tweaks to the options panel.
* Documentation updated, several small improvements in error reporting.
* Additional error checking to support an even wider range of hosting providers.
* Added automation routines for safe re-activation after an upgrade is performed.

= v1.9 =

* Additional support added for WordPress® MU 2.8.6+.
* Security file `zencache-mu.php` added specifically for MU installations. WordPress® MU is a special ( multi-user ) version of WordPress®. If you're running WordPress® MU, check the [readme.txt] file for WordPress® MU notations.

= v1.8 =

* Re-organized core framework. Updated to: P-2.0.
* Updated to support WP 2.9+.

= v1.7 =

* Updated documentation. Added some additional code samples.
* Tested with WP 2.8.5. Everything ok.

= v1.6 =

* We've added the ability to enable Double-Caching ( client-side caching ). Full documentation is provided in the ZenCache options panel. This feature is for those of you who just want blazing fast speed and are not concerned as much about reliability and control. We don't recommend turning this on unless you realize what you're doing.

= v1.5 =

* Support for Dynamic Cache Pruning has been improved. Full documentation is provided in the ZenCache options panel.
* Additional feature-specific documentation has been added to assist novice webmasters during configuration.

= v1.4 =

* Garbage collection has been further optimized for speed and performance on extremely high traffic sites.
* PHP Ternary expressions are now supported in your Version Salt. This takes your Version Salt to a whole new level.
* Additional code samples have been provided for Version Salts; showing you how to deal with mobile devices and other tricky situations.

= v1.3 =

* We've implemented both Semaphore ( `sem_get` ) and `flock()` mutex. If you're on a Cloud Computing Model ( such as the Rackspace® Cloud ), then you'll want to go with flock() unless they tell you otherwise. In all other cases we recommend the use of Semaphores over Flock because it is generally more reliable. The folks over at Rackspace® have suggested the use of flock() because of the way their Cloud handles multi-threading. In either case, flock() will be fully functional in any hosting environment, so it makes a great fallback in case you experience any problems.

= v1.2 =

* We've implemented a way for plugin developers to disallow caching during certain routines or on specific pages. You can set the following PHP Constant at runtime to disable caching. `define("ZENCACHE_ALLOWED", false)`. We have also added backward compatibility for WP Super Cache, so that `define("DONOTCACHEPAGE", true)` will also be supported by plugins that have previously been written for compatibility with Super Cache. In other words, ZenCache looks for either of these two Constants.

= v1.1 =

* Added the ability to create a Version Salt. This is a feature offered ONLY by ZenCache. Full documentation is provided in the ZenCache options panel. This can become very useful for sites that provide membership services or have lots and lots of plugins installed that makes their site incompatible with WP Super Cache. With ZenCache, you'll now have more control over the entire caching process using a custom Version Salt tailored to your specific needs.

= v1.0 =

* Initial release.
