= v160103 =

- **Bug Fix**: Fixed an issue that was unexpectedly producing "Failed to update your /.htaccess file" error messages. The `.htaccess` routines are now more intelligent and take into consideration which plugin options are enabled and which options require updating the `.htaccess` file. This also improves performance by avoiding unnecessary read/writes to the `.htaccess` file. Props @patdumond. See [Issue #641](https://github.com/websharks/zencache/issues/641).
- **Bug Fix**: When `allow_url_fopen` is disabled via the PHP configuration, the Auto-Cache Engine is unable to read the XML Sitemap and was silently failing with only PHP Warning in the PHP error log. The Auto-Cache Engine currently requires [PHP URL-aware fopen wrappers](http://zencache.com/r/allow_url_fopen/). A new Dashboard notice displays an error message when `allow_url_fopen` is disabled and prevents the Auto-Cache Engine from attempting to run. See [Issue #644](https://github.com/websharks/zencache/issues/644).
- **Bug Fix**: Fixed an Auto-Cache Engine bug that was producing false-positive Dashboard errors related to timeouts: "Problematic XML Sitemap URL - WP_Http says: Operation timed out after 5001 milliseconds with 0 bytes received." Due to the way ZenCache was checking the XML Sitemap URL more than necessary, timeouts were more likely to occur. We now only check the URL repeatedly when a failure has been encountered. If the URL is confirmed as working, we don't check the URL again until the Auto-Cache Engine runs (every 15 minutes by default) or until the Plugin Options are saved. If you are still seeing timeout errors after this update, please see [this article](http://zencache.com/kb-article/why-am-i-seeing-auto-cache-engine-timeout-errors/). See [Issue #643](https://github.com/websharks/zencache/issues/643).
- **Bug Fix**: Fixed an Auto-Cache Engine bug where ZenCache would would check both the non-SSL (`http://`) and the SSL (`https://`) version of the XML Sitemap URL when the Site Address was configured to use SSL. See [Issue #643](https://github.com/websharks/zencache/issues/643).
- **Enhancement**: It's now possible to override the ZenCache Nonce exclusion globally (dangerous) or only for Logged-In Users (safer). Please see [this article](http://zencache.com/r/kb-article-what-are-wordpress-nonces-and-why-are-they-not-cache-compatible/) for full details. [Issue #637](https://github.com/websharks/zencache/issues/637).
- **Akismet Compatibility**: Fixed a bug with Akismet compatibility where ZenCache was not properly disabling the Akismet Comment Nonce, which resulted in pages being unnecessarily excluded from the cache due to the presence of the `akismet_comment_nonce` in the markup. Props @Kalfer. See [Issue #642](https://github.com/websharks/zencache/issues/642).

= v151220 =

- **New Feature!**: It's now possible to customize the Cache Cleanup Schedule and set your own schedule in _ZenCache → Plugin Options → Manual Cache Clearing → Cache Cleanup Schedule_. ZenCache uses [`wp_get_schedules()`](https://codex.wordpress.org/Function_Reference/wp_get_schedules) when generating the list of available schedules, which makes it possible to create your own custom cron schedule using a plugin like WP Crontrol and use that to define a custom Cache Cleanup Schedule. Props @kristineds. See [Issue #472](https://github.com/websharks/zencache/issues/472).
- **New Feature!** It's now possible to configure the Pro Plugin Updater to check for Beta versions (Release Candidates) of the plugin. See _ZenCache → Plugin Updater → Beta Testers_. We publish Release Candidates a week or so before official releases to allow for additional testing and early preview of upcoming features and bug fixes. If you're not already on our Beta Testers mailing list, you can signup [here](http://zencache.com/r/zencache-beta-testers-list/). Props @kristineds. See [Issue #352](https://github.com/websharks/zencache/issues/352).
- **New Feature!** It's now possible to clear [Expired WordPress Transients](https://codex.wordpress.org/Transients_API) from the Clear Cache Option Menu in the WordPress Admin Bar. The WordPress Transients API has no functionality to automatically clean up expired transients; doing so is left for plugin authors and we've found that very few plugins that use the Transient API actually clean up expired transients properly, which can lead to your database being full of expired transient data that is no longer used. Props @kristineds. See [Issue #459](https://github.com/websharks/zencache/issues/459).
- **New Feature!** Client-Side Caching now includes a new URI Exclusion Patterns for Client-Side Caching, allowing you to exclude specific URIs from being cached by a client-side browser when Client-Side Caching is enabled. Props @renzms. See [Issue #498](https://github.com/websharks/zencache/issues/498).
- **Bug Fix**: Fixed a bug that was generating a "Failed to update your `/.htaccess` file" error message. The routines that update your `.htaccess` file were failing if your `.htaccess` file already contained the word "ZenCache" (case-insensitive). See [Issue #617](https://github.com/websharks/zencache/issues/617) and [Issue #620](https://github.com/websharks/zencache/issues/620).
- **Bug Fix**: Fixed a bug that in some scenarios could cause custom `.htaccess` rules to be lost if your `.htaccess` file was using the exact same comment start and end markers as ZenCache (`# BEGIN ZenCache` and `# END ZenCache`). If you were using those exact same start and end markers, ZenCache was replacing whatever was between them with a copy of the ZenCache `.htaccess` rules. ZenCache now uses a unique marker (`WmVuQ2FjaGU`) to identify its own code blocks inside your `.htaccess` file. See [Issue #620](https://github.com/websharks/zencache/issues/620).
- **Bug Fix**: Fixed a bug with clearing cache files for paginated pages where the `pagination_base` had been changed from the default `page` to something else (e.g., a translated string). The WP Rewrite API is now used to include `pagination_base` and `comments_pagination_base` when building paths to cache files to determine which cache files should be cleared. Props @renzms. See [Issue #607](https://github.com/websharks/zencache/issues/607).
- **Bug Fix**: Fixed a bug with the Static CDN Filters that affected sites using a permalink structure that ended with `.htm` or `.html`. This bug was supposed to be fixed back in v151002 but another bug prevented the fix from working properly. This release fixes the issue once and for all. See [Issue #495](https://github.com/websharks/zencache/issues/495#issuecomment-160324313).
- **Bug Fix**: With Logged-In User caching enabled, there were some scenarios where the user cache was being cleared on every page load when a user was logged in. ZenCache now hooks into `updated_user_metadata` instead of `update_user_metadata` to clear a specific user cache. Props @kristineds. See [Issue #623](https://github.com/websharks/zencache/issues/623).
- **Bug Fix**: Fixed a bug where the Auto-Cache Engine cron event would disappear in some scenarios. Props @patdumond, @MarioKnight. See [Issue #613](https://github.com/websharks/zencache/issues/613).
- **Bug Fix**: ZenCache no longer caches any pages that contain `_wpnonce` in the markup. This ensures that pages containing time-sensitive `nonce` values are not cached. Props @kristineds and @jaswsinc. See [Issue #601](https://github.com/websharks/zencache/issues/601).
- **Bug Fix**: This release attempts to resolve reports of Error 500 and Internal Server Error issues when running with PHP 5.6 + OPcache. Instead of clearing the entire Opcode cache whenever the plugin options are saved, we only invalidate the `advanced-cache.php` file, which is rebuilt each time you update your configuration options. Props @jaswsinc. Also props to @MarioKnight and @CotswoldPhoto for helping us narrow this down. See [Issue #624](https://github.com/websharks/zencache/issues/624).
- **Bug Fix**: Fixed a bug where ZenCache could inadvertently corrupt the `.htaccess` file if it contained invalid UTF-8 characters. This has been fixed by first checking the `.htaccess` file for invalid UTF-8 characters via `wp_check_invalid_utf8()`. See [Issue #633](https://github.com/websharks/zencache/issues/633#issuecomment-165493039).
- **Enhancement**: Improved WP Cron-related configuration and validation of custom cron schedules. See [PR #197](https://github.com/websharks/zencache-pro/pull/197).
- **Enhancement**: ZenCache now clears the cache whenever a WordPress plugin is activated or deactivated. Many WordPress plugins change content on the front-end of the site, so this enhancement ensures that an old cache file is never served to visitors. If you want to disable this automatic behavior, see [this article](http://zencache.com/kb-article/how-do-i-prevent-zencache-from-clearing-the-cache-upon-plugin-activation-or-deactivation/). Props @renzms. See [Issue #424](https://github.com/websharks/zencache/issues/424).
- **Enhancement**: The Auto-Cache Engine diagnostic reporting for XML Sitemap-related errors has been greatly improved. The Dashboard notice now explains exactly what error is occurring when the Auto-Cache Engine attempts to verify that the XML Sitemap exists. Props @jaswsinc. See [Issue #615](https://github.com/websharks/zencache/issues/615) and [Issue #618](https://github.com/websharks/zencache/issues/618).
- **Enhancement**: Auto-Cache Engine XML Sitemap error checking is now more intelligent. When configured, the XML Sitemap URL is now checked upon every `admin_init` to verify that the XML Sitemap is accessible and valid. If a previous error has been fixed, the error message will disappear immediately instead of taking 15 minutes (the Auto-Cache Engine run cycle). See [Issue #616](https://github.com/websharks/zencache/issues/616).
- **Enhancement**: Static CDN Filters are no longer applied by default for Logged-In Users. This was causing some confusion because Logged-In User caching is disabled by default. There is no harm in applying Static CDN Filters to all users (logged-in or not logged-in), in fact we recommend it, however we now disable this functionality by default to avoid confusion. A new option allows you to control this behavior and enable Static CDN Filters for Logged-In users; see _ZenCache → Logged-In Users → Static CDN Filters Enabled for Logged-In Users & Comment Authors?_ Props @kristineds, @lkraav, and @isaumya. See [Issue #486](https://github.com/websharks/zencache/issues/486).
- **Enhancement**: The Static CDN Filters now attempt to obey the `ZENCACHE_ALLOWED` and `DONOTCACHEPAGE` constants so that Static CDN Filters are not applied when caching is disabled. However, due to the way Static CDN Filters are implemented this is not always reliable. If you need to programmatically disable Static CDN Filters, see [this article](http://zencache.com/kb-article/how-do-i-disable-static-cdn-filters-via-php/). See [Issue #628](https://github.com/websharks/zencache/issues/628).
- **Enhancement**: When activating ZenCache for the first time, a new Dashboard message now includes a helpful link to the options page to enable caching and review the options. Props @kristineds. See [Issue #112](https://github.com/websharks/zencache/issues/112).
- **Enhancement**: The automatic Cache Cleanup schedule that cleans up (deletes) expired/stale cache files has been changed from once every day to once every hour. Running the cleanup hourly makes ZenCache smarter when configured in certain ways and saves disk space. Props @kristineds. See [Issue #472](https://github.com/websharks/zencache/issues/472).
- **Enhancement:** The new default value for "Clear the PHP OPcache Too?" when clearing the cache manually is now disabled. Instead of having this option enabled by default, we automatically invalidate only the `advanced-cache.php` file in PHP's opcode cache whenever you update your configuration options, which allows ZenCache to function as expected. That was the most important reason for needing to clear the opcode cache in previous versions. Props @jaswsinc. Also props to @MarioKnight and @CotswoldPhoto for helping us narrow this down. See [Issue #624](https://github.com/websharks/zencache/issues/624).
- **Enhancement**: Improved `.htaccess` utilities so that an exclusive lock is acquired when reading+writing to the file. This helps avoid inadvertently corrupting the `.htaccess` file in scenarios where another process might be reading/writing to it at the same time. See [Issue #633](https://github.com/websharks/zencache/issues/633).
- **Multisite Enhancement**: The Auto-Cache Engine has a new configuration option when running on WP Multisite Networks that allows you to define whether or not the Auto-Cache Engine should look for XML Sitemaps on each of the child blogs. This now defaults to being off; i.e., by default, child blogs are no longer checked. Props @jaswsinc. See [Issue #618](https://github.com/websharks/zencache/issues/618#issuecomment-158695842).
- **Multisite Enhancement**: The Auto-Cache Engine XML Sitemap diagnostic reporting is now always disabled for child blogs and only errors related to a primary sitemap location are displayed on the Dashboard. Props @jaswsinc. See [Issue #618](https://github.com/websharks/zencache/issues/618#issuecomment-158695842).
- **bbPress Compatibility:** This release improves compatibility with bbPress when ZenCache Logged-In User caching is enabled. In this scenario, bbPress may generate links for Admins that contain time-sensitive `_wpnonce` values which could expire if cached and result in certain admin-related actions failing. ZenCache no longer caches pages that contain `_wpnonce` in the markup. This ensures that pages containing time-sensitive `nonce` values are not cached. Props @kristineds, @jaswsinc, and @clavaque. See [Issue #601](https://github.com/websharks/zencache/issues/601).
- **Akismet Compatibility:** ZenCache no longer caches pages that contain `akismet_comment_nonce` in the markup. This ensures that a page that contains a time-sensitive `nonce` value does not get cached. Props @kristineds and @jaswsinc. See [Issue #601](https://github.com/websharks/zencache/issues/601).
- **Akismet Compatibility:** ZenCache now automatically disables the Akismet Comment Nonce using [the `akismet_comment_nonce` filter](https://github.com/git-mirror/wordpress-akismet/blob/2.5.6/akismet.php#L333), which improves compatibility between Akismet and the page caching functionality provided by ZenCache. This ensures that pages do not contain time-sensitive `nonce` values that should not be cached. If you'd like to revert this behavior, please see [this article](http://zencache.com/kb-article/how-do-i-prevent-zencache-from-disabling-the-akismet-comment-nonce/). Props @kristineds and @jaswsinc. See [Issue #601](https://github.com/websharks/zencache/issues/601).
- **WooCommerce Compatibility:** This release improves compatibility with the WooCommerce Product Stock feature. When the Product Stock changes, ZenCache will now clear the cache file for the associated Product to ensure that the stock quantity that appears on the site remains up-to-date. See [Issue #597](https://github.com/websharks/zencache/issues/597).

= v151114 =

- **Announcement: The next version of ZenCache will require PHP 5.4+.** As of December 1st, 2015, the minimum PHP version required to run ZenCache will change to PHP 5.4+. This release of ZenCache will be the last version to support PHP 5.3. Please see announcement with further details: [New Minimum PHP Version: PHP 5.4](http://zencache.com/r/new-minimum-php-version-php-5-4/)
- **Announcement: The next version of ZenCache will not support the PHP APC Extension**. As of December 1st, 2015, ZenCache will no longer run with the PHP APC extension enabled. Please see announcement with further details: [PHP APC Extension No Longer Supported](http://zencache.com/r/php-apc-extension-no-longer-supported/)
- **New Feature!** The Clear Cache button in the Admin Bar now includes a sub-menu with several new options for clearing the cache from anywhere on your site. You can clear the cache for just the Home Page, the Current URL, a Specific URL, PHP's OPCache (if active), or the CDN Cache (when Static CDN Filters are configured). This menu comes in two flavors and can be customized (or disabled entirely) inside _ZenCache → Plugin Options → Manual Cache Clearing_. Props @jaswsinc. See [Issue #596](https://github.com/websharks/zencache/issues/596#issuecomment-152786080).
- **New Feature!** It's now possible to specify a list of Custom URLs whose cache files should be cleared whenever you update a Post/Page, approve a Comment, or make other changes where ZenCache detects that a Post/Page cache should be cleared to keep your site up-to-date. See _ZenCache → Plugin Options → Automatic Cache Clearing → Misc. Auto-Clear Options → Auto-Clear a List of Custom URLs Too?_ Props @kristineds. See [Issue #111](https://github.com/websharks/zencache/issues/111).
- **New Feature!** A new watered-down Regular Expression syntax is now supported in several existing ZenCache features, including the new list of Custom URLs to Auto-Clear, URI Exclusion Patterns, HTTP Referrer Exclusion Patterns, User-Agent Exclusion Patterns, and the HTML Compressor CSS Exclusion Patterns and JavaScript Exclusion Patterns. This new syntax greatly increases the power and flexibility of each of these features and makes things possible like the much-requested ability to Auto-Clear the Home Page or Posts Page of a site whenever a post cache is cleared. For more information on this new watered-down Regular Expression syntax, [this KB Article](http://zencache.com/r/watered-down-regex-syntax/). Props @kristineds @jaswsinc. See [Issue #191](https://github.com/websharks/zencache/issues/191).
- **Bug Fix**: Fixed a bug with Static CDN Filters and Cross-Origin Resource Sharing (CORS) that was generating a "Cross-Origin Request Blocked" error. ZenCache will now update the root `.htaccess` file to include a `Header set Access-Control-Allow-Origin "*"` rule for `ttf`, `ttc`, `otf`, `eot`, `woff`, `woff2`, `font.css`, `css`, and `js` files whenever the Static CDN Filters are enabled. This is necessary to avoid "Cross-Origin Request Blocked" errors. Note that if you are already experiencing this error, you should create and configure a new CDN hostname to resolve the issue. In our tests it appears that some CDNs are caching the initial header response received by the server, which means it's necessary to send the `Access-Control-Allow-Origin` header _before_ configuring the Static CDN Filters with a CDN hostname. See [Issue #427](https://github.com/websharks/zencache/issues/427#issuecomment-121774596).
- **Bug Fix**: Removed `eot,ttf,otf,woff` font extensions from the Static CDN Filter Blacklisted Extensions. These were added in a previous release in an attempt to resolve an issue with Cross-Origin Resource Sharing (CORS), however now that the HTML Compressor has been updated to work with Static CDN Filters, the CSS compressed by the HTML Compressor is now served from the CDN. Fonts are most likely to be referenced by CSS, which is static. So when Static CDN Filters are applied, the CSS is getting moved to the CDN and the fonts are then expected to live on the CDN too. By excluding them from the Static CDN Filter, we were creating a problem instead of solving one. This release removes the font extensions from the default Blacklisted Extensions so that fonts can be hosted on the CDN alongside the CSS that references them. See [Issue #427](https://github.com/websharks/zencache/issues/427#issuecomment-121777790).
- **Bug Fix**: Fixed a bug related to updating plugins with WP-CLI on a site that was running ZenCache Pro. While ZenCache Pro updates must still be done through the ZenCache Pro Updater inside the Dashboard, updating other plugins via WP-CLI was generating a harmless ZenCache exception: "Invalid argument; host token empty!". With this fix, ZenCache will properly detect when WP-CLI is running to avoid these errors. Props @MarioKnight @renzms. See [Issue #563](https://github.com/websharks/zencache/issues/563).
- **Bug Fix**: Fixed a bug where post previews were being cached when Logged-In User Caching and GET Request caching were both enabled (both are disabled by default). This release now detects previews in this scenario and excludes those requests from being cached. Props @clavaque @kristineds. See [Issue #583](https://github.com/websharks/zencache/issues/583).
- **Bug Fix**: Fixed an issue where a PHP Notice was generated when an inactive WordPress component was being upgraded. This issue did not have any adverse affect on the site, but this fix resolves the issue so that the notice won't appear in PHP logs. See [Issue #589](https://github.com/websharks/zencache/issues/589).
- **Bug Fix**: Fixed a bug where a commented-out `WP_CACHE` definition in `wp-config.php` (such as what WP Super Cache leaves behind) was being incorrectly ignored and resulted in caching being silently disabled. Props @davidfavor. See [Issue #591](https://github.com/websharks/zencache/issues/591).
- **Bug Fix**: Fixed a bug where in some scenarios a page might not be cached due to a stray `AUTH_COOKIE` or `SECURE_AUTH_COOKIE` cookie, even when the user is not logged in. Props @jaswsinc @renzms. See [Issue #592](https://github.com/websharks/zencache/issues/592).
- **Enhancement**: Excluded several unnecessary files from the plugin zip file that were being used during the build process but were not necessary to run the plugin. Props @bridgeport. See [Issue #579](https://github.com/websharks/zencache/issues/579).
- **Enhancement**: When the Cache Directory location is changed, ZenCache now cleans up the old Cache Directory and any old cache files instead of leaving them behind. Props @clavaque @renzms. See [Issue #580](https://github.com/websharks/zencache/issues/580).
- **Enhancement**: Added a new API Function that allows a site owner to clear the cache for a specific URL via `zencache::clearUrl($url);`. See [this article](http://zencache.com/kb-article/clearing-the-cache-dynamically/#toc-988085ad) for further details. Props @kristineds. See [Issue #590](https://github.com/websharks/zencache/issues/590).
- **Enhancement**: Improved the HTML Notes generated by ZenCache when s2Member (a membership plugin for WordPress) is specifically disabling caching. s2Member automatically disables caching on certain pages that are required to remain dynamic. The HTML Notes generated by ZenCache now explain when this is happening. Props @renzms. See [Issue #504](https://github.com/websharks/zencache/issues/504).
- **Enhancement**: In Logged-In User Caching, the "Yes, but DON'T maintain a separate cache for each user" option has been hidden because this particular option has the potential to create a security issue if not configured properly. If you were already using this option, it will not be hidden and it will continue to work. Otherwise, if you require this particular option you can now enable it using a filter (see [this comment](https://github.com/websharks/zencache/issues/497#issuecomment-146060440)). Props @renzms @jaswsinc. See [Issue #497](https://github.com/websharks/zencache/issues/497).
- **Enhancement**: The Auto-Cache Engine now detects when the configured XML Sitemap is not valid or is unreachable and displays an appropriate notice. Props @kristineds and @jaswsinc. See [Issue #555](https://github.com/websharks/zencache/issues/555).

= v151004 =

- **Bug Fix**: Fixed a bug introduced in the previous release that was resulting in a "Fatal error: Using $this when not in object context" for sites running PHP 5.3. (PHP 5.4+ sites were unaffected.) Props @jaswsinc. See [Issue #581](https://github.com/websharks/zencache/issues/581).

= v151002 =

- **New Feature!** Cache Statistics is a completely new ZenCache Pro feature that will help site owners better understand their WordPress site cache. An easy-to-access Cache Stats menu button in the Admin Bar is accompanied by a whole new page that shows Current Cache Totals (including total number of cache files and total size of cache on the disk), Current Disk Health (including total disk capacity and total available), Current System Health (including memory usage and load average), and two beautiful Polar Area pie charts that show you both current and historical data on Cache File Counts and Cache File Sizes with a 30-Day High, Current Total, Page Cache total, and HTML Compressor total for each chart. Props to @jaswsinc. See [Issue #83](https://github.com/websharks/zencache/issues/83).
- **New Feature!** It's now possible to specify which WordPress Roles/Capabilities are allowed to clear the cache from the WordPress Admin bar. A new option inside _Dashboard → ZenCache → Plugin Options → Manual Cache Clearing_ allows specifying a comma-delimited list of Roles and/or Capabilities under, "Also allow others to clear the cache from their Admin Bar?". If a user has an allowed Role/Capability, they will see the "Clear Cache" button in their Admin Bar. This feature is also compatible with WordPress Multisite Networks, allowing a Network Administrator to define which Child Site roles should allow a Child Site user to see the "Clear Cache" button for their Child Site. Props @danielmt2k @jaswsinc. See [Issue #68](https://github.com/websharks/zencache/issues/68).
- **New Feature!** It's now possible to "Disable Cache Expiration If Server Load Average is High" (see _Dashboard → ZenCache → Plugin Options → Cache Expiration Time_). This allows you to provide a specific load average that should cause ZenCache to disable cache expiration and help reduce stress on the server; i.e., to avoid generating a new version of the cache while the server is very busy. Props @jaswsinc. See [Issue #347](https://github.com/websharks/zencache/issues/347).
- **New Feature!** It's now possible to manually clear the CDN Cache when Static CDN Filters are enabled. Inside the Static CDN Filters options panel there's a new "Clear CDN Cache" button and there's also a new option ("Clear the CDN Cache Too?") inside _Dashboard → ZenCache → Plugin Options → Manual Cache Clearing_ that allows you to specify whether or not you'd like the CDN Cache to be cleared whenever the "Clear Cache" button is clicked (either from the Admin Bar or from inside the Plugin Options). Props @kristineds @jaswsinc. See [Issue #488](https://github.com/websharks/zencache/issues/488).
- **New Feature!** When your server has the [PHP OPCache Extension](http://zencache.com/r/php-opcache/) installed, ZenCache can now be configured to also clear the PHP opcode cache whenever you clear the cache manually using the ZenCache "Clear Cache" button. See _ZenCache Options → Manual Cache Clearing → Clear the PHP OPCache Too?_ (note that this option only appears if you have the OPCache Extension installed). Props @jaswsinc. See [Issue #489](https://github.com/websharks/zencache/issues/489).
- **Bug Fix**: Fixed an HTML Compressor bug related to CSS pseudo-classes where spaces between the class name and pseudo-class name were being removed when CSS was minified. Props @patdumond @jaswsinc. See [Issue #544](https://github.com/websharks/zencache/issues/544).
- **Bug Fix**: Fixed an HTML Compressor bug related to `<noscript>` tags. The HTML Compressor now makes no adjustments to anything inside `<noscript>` tags, and the same has always been true for IE conditional comments as well. Props @rtrevellyan @jaswsinc. See [Issue #65](https://github.com/websharks/html-compressor/issues/65).
- **Bug Fix**: Fixed an issue related to a popular NGINX server configuration (`try_files $uri $uri/ /index.php?q=$uri&$args;`) that was preventing the entire site from being cached. ZenCache disables caching by default for all requests that include a query string (see _Dashboard → ZenCache → Plugin Options → GET Requests_) and this particular NGINX configuration passes _all_ requests to WordPress with a `?q=` query variable, which was resulting in ZenCache disabling caching on all pages. This release implements better detection for NGINX and works around this scenario. Props @jaswsinc. See [Issue #561](https://github.com/websharks/zencache/issues/561).
- **Bug Fix**: Fixed a bug with the Static CDN Filters that affected sites using a permalink structure that ended with `.htm` or `.html`. Generally, files that end in `.htm` or `.html` are considered static files, hence the reason ZenCache was rewriting URLs with those extensions to point at the configured CDN. However, if a site uses `.htm` or `.html` in their permalink structure, all links to Posts/Pages within the site will appear to be static files when they are in fact dynamic and therefore should not be rewritten. ZenCache now checks the permalink structure and excludes `.htm` and `.html` from the allowed extensions when the permalink structure is using one of these. Props @jaswsinc. See [Issue #495](https://github.com/websharks/zencache/issues/495).
- **Bug Fix**: Fixed an SSL issue with the HTML Compressor that was causing problems in some hosting environments where the hosting server was incorrectly setting `$_SERVER['REQUEST_SCHEME']` to `http` even when the WordPress Site URL and Home URL were set to use `https://`. As a result of this improper server configuration, the combined CSS/JS files generated by the HTML Compressor were being served over HTTP even when a site was configured to use HTTPS. This release applies a workaround for this improper server configuration that no longer looks at `$_SERVER['REQUEST_SCHEME']`. See [Issue #413](https://github.com/websharks/zencache/issues/413) and [Issue #73](https://github.com/websharks/html-compressor/issues/73).
- **Multisite Bug Fix**: Fixed a bug in the Auto-Cache Engine that was resulting in duplicate Cron Jobs being created for each Child Site. The Auto-Cache Engine now only runs from the Main Site in a network, as it should. When the Auto-Cache Engine runs on the Main Site, it will also run for each of the Child Blogs (see [this article](http://zencache.com/r/kb-article-how-does-the-auto-cache-engine-cache-child-blogs-in-a-multisite-network/) for more information). Props @jaswsinc. See [Issue #543](https://github.com/websharks/zencache/issues/543).
- **Enhancement**: Improved HTML Compressor HTTP connection handling, timeouts, protocol, BOM markers, exceptions, `Referer:` and `User-Agent:` headers. Props @LittleBastard77 @jaswsinc. See [Issue #391](https://github.com/websharks/zencache/issues/391) and [Issue #69](https://github.com/websharks/html-compressor/issues/69).
- **Enhancement**: Manual Cache Clearing options have now been separated from Automatic Cache Clearing options inside the ZenCache Plugin Options to improve the differentiation between these.
- **Enhancement**: New icons in the ZenCache Plugin Options help improve the visual representation of each panel.
- **Enhancement**: The installed plugin version is now shown at the top of the ZenCache Options Page. When a newer version of the plugin is available, an "update available" link appears. Props @renzms. See [Issue #502](https://github.com/websharks/zencache/issues/502).
- **Enhancement**: New transition in/out effects on the Cache Cleared Dashboard notifications. Props @jaswsinc. See [Issue #538](https://github.com/websharks/zencache/issues/538).

= v150821 =

- **Multisite Domain Mapping Compatibility**: ZenCache Pro is now fully compatible with the [WordPress MU Domain Mapping plugin](https://wordpress.org/plugins/wordpress-mu-domain-mapping/), including Multisite Networks using domain mapping on top-level domains, sub-domains, and sub-directories. Multiple variations of each site spread across an unlimited number of domain mappings and/or the original blog domain/path is also supported. All other features of ZenCache Pro, including the Auto-Cache Engine, Static CDN Filters, and HTML Compression are also now compatible with domain mapping. Props @jaswsinc. See [Issue #339](https://github.com/websharks/zencache/issues/339).
- **bbPress Compatibility**: This release greatly improves compatibility with bbPress. Events like creating a new Forum, creating a new Topic, and posting a reply to a Topic (including threaded replies), now properly clear the necessary cache files to ensure that cached bbPress pages remain up-to-date. Props @jaswsinc. See [Issue #168](https://github.com/websharks/zencache/issues/168).
- **Bug Fix**: Fixed a bug where, in some rare cases, `wp-config.php` would end up with two `WP_CACHE` definitions. Props @jaswsinc. See [Issue #509](https://github.com/websharks/zencache/issues/509).
- **Bug Fix**: Saving a Post as a Draft was incorrectly purging XML Sitemap cache files. Props @jaswsinc. See [Issue #368](https://github.com/websharks/zencache/issues/368).
- **Bug Fix:** The HTML Compressor now strips UTF-8  Byte Order Markers (BOMs) from CSS files generated by preprocessors such as Sass. Files consolidated by the HTML Compressor include an `@charset` rule already, making a BOM unnecessary. In fact, if BOMs are not stripped, some browsers will choke on portions of a consolidated CSS file, because a BOM could potentially appear in the middle of the file; i.e., at the _wrong_ place. Symptoms included mysterious missing styles in portions of the site, fonts not loading at all times, or font-based icons (e.g., FontAwesome) to render mystery glyphs instead of the icons. Props @jaswsinc. See [Issue #52](https://github.com/websharks/html-compressor/issues/52).
- **Bug Fix:** Improved HTML Compressor strict data type comparison when analyzing `$_SERVER` environment variables to detect an `https://` connection URL. We now detect `(integer)443` and `(string)443`. In addition, we can now pick up `$_SERVER['HTTPS']` being any of `1|on|yes|true` (caSe insensitive). Props @jaswsinc. See [Issue #61](https://github.com/websharks/html-compressor/issues/61).
- **Multisite Bug Fix**: Fixed a bug where the Clear Cache button wouldn't clear Child-Site Logged-In User Home Page cache files on WordPress Multisite Networks. Props @jaswsinc. See [Issue #409](https://github.com/websharks/zencache/issues/409)
- **Multisite Bug Fix**: Fixed a bug where the Home Page cache was not clearing on Child Sites in a Multisite Network. Props @jaswsinc. See [Issue #409](https://github.com/websharks/zencache/issues/409)
- **Multisite Bug Fix**: Fixed an issue where the Auto-Cache Engine would fail to auto-cache child blogs in a multisite network whenever the network was being served from a sub-directory. Props @jaswsinc. See [Issue #537](https://github.com/websharks/zencache/issues/537).
- **Multisite Bug Fix**: Fixed a bug with 404 Request caching on Multisite Networks where ZenCache Pro was not considering that each child blog in a multisite network will have its own 404 error page. Props @jaswsinc. See [Issue #539](https://github.com/websharks/zencache/issues/539).
- **Multisite Bug Fix**: Fixed a bug where clearing the cache from the main site of a multisite network, when there are child blogs in sub-directories, resulted in all child blogs being cleared from the cache, not just the main site as would be expected. This has been resolved and only the main site is cleared when clicking the Clear Cache button. Note that the Wipe Cache button can still be used to clear the cache for all sites in a Multisite Network. Props @jaswsinc. See [Issue #540](https://github.com/websharks/zencache/issues/540).
- **Multisite Bug Fix**: Fixed a bug where Wiping the cache on a multisite network resulted in the very next page view being cached incorrectly. Props @jaswsinc. See [Issue #541](https://github.com/websharks/zencache/issues/541).
- **Enhancement**: Improved compatibility with any Custom Post Type that uses hierarchies. Props @jaswsinc.

= v150716 =

- **Bug Fix**: Fixed a fatal error that was occurring on some sites after upgrading to v150629. We discovered the fatal error was related to PHP's outdated and buggy APC extension, which is often active on sites running PHP 5.3 and PHP 5.4. We now prevent activation of ZenCache on systems with the APC extension enabled and show a warning message instead. We've written [a KB Article that further explains the APC Extension Warning](http://zencache.com/kb-article/why-does-zencache-show-an-apc-extension-warning/) and offers advice to site owners who are currently running APC. See also [Issue #511](https://github.com/websharks/zencache/issues/511).
- **Bug Fix**: Fixed a bug in the Dynamic Version Salt filter that was generating PHP notices and warnings. See [Issue #522](https://github.com/websharks/zencache/issues/522).
- **Bug Fix**: Fixed an issue with backwards compatibility that was preventing some AC Plugins from working properly. See [Issue #514](https://github.com/websharks/zencache/issues/514).
- **Enhancement**: Added a stats pinger, similar to the WordPress stats collector, that reports the server OS, PHP version, MySQL version, WordPress version, and the ZenCache version, securely and anonymously to WebSharks. See [this KB Article](http://zencache.com/kb-article/what-information-does-zencache-pro-report-to-websharks/) for more information.

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
