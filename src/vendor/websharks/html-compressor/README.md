# HTML-Compressor (Beta)

<img src="assets/html-compressor-logo.png" width="300px" align="right" />

Combines & compresses CSS/JS/HTML code.

[![](https://img.shields.io/github/license/websharks/html-compressor.svg)](https://github.com/websharks/html-compressor/blob/HEAD/LICENSE.txt)

[![](https://img.shields.io/badge/made-w%2F_100%25_pure_awesome_sauce-AB815F.svg?label=made)](http://websharks-inc.com/)
[![](https://img.shields.io/badge/by-WebSharks_Inc.-656598.svg?label=by)](http://www.websharks-inc.com/team/)
[![](https://img.shields.io/github/release/websharks/html-compressor.svg?label=latest)](https://github.com/websharks/html-compressor/releases)
[![](https://img.shields.io/packagist/v/websharks/html-compressor.svg?label=packagist)](https://packagist.org/packages/websharks/html-compressor)
[![](https://img.shields.io/github/issues/websharks/html-compressor.svg?label=issues)](https://github.com/websharks/html-compressor/issues)
[![](https://img.shields.io/github/forks/websharks/html-compressor.svg?label=forks)](https://github.com/websharks/html-compressor/network)
[![](https://img.shields.io/github/stars/websharks/html-compressor.svg?label=stars)](https://github.com/websharks/html-compressor/stargazers)
[![](https://img.shields.io/github/downloads/websharks/html-compressor/latest/total.svg?label=downloads)](https://github.com/websharks/html-compressor/releases)
[![](https://img.shields.io/packagist/dt/websharks/html-compressor.svg?label=packagist)](https://packagist.org/packages/websharks/html-compressor)

```php
<?php
require_once 'html-compressor.phar';
$html_compressor = new WebSharks\HtmlCompressor\Core();
ob_start(array($html_compressor, 'compress'));
```

----

## Installation Instructions (Two Options)

1. As a [Composer](https://packagist.org/packages/websharks/html-compressor) Dependency

  ```json
  {
      "require": {
          "websharks/html-compressor": "dev-master"
      }
  }
  ```

2. Or, Download the PHAR Binary
  See: https://github.com/websharks/html-compressor/releases

## Where do I get the PHAR file?

A PHAR binary is made available for each official release. See: [releases](https://github.com/websharks/html-compressor/releases).

## Why did we create the HTML Compressor?

The HTML Compressor class was developed because all of us here at WebSharks™ are growing tired of seeing WordPress installations out-in-the-wild that are running many different plugins; where each plugin may add a new set of CSS/JS files. This creates a slow-loading site, even if a page caching plugin is active.

**For example**, if you look at the HTML source code for most sites powered by a publishing platform such as WordPress (or audit one in a web developer console), you will find **a complete mess like this**...

    <link rel="stylesheet" href="theme.css" type="text/css" />
    <link rel="stylesheet" href="child-theme.css" type="text/css" />
    <link rel="stylesheet" href="theme-variation.css" type="text/css" />
    <link rel="stylesheet" href="plugin1.css" type="text/css" />
    <link rel="stylesheet" href="plugin2.css" type="text/css" />
    <link rel="stylesheet" href="plugin3.css" type="text/css" />
    <link rel="stylesheet" href="plugin4.css" type="text/css" />
    <link rel="stylesheet" href="plugin5.css" type="text/css" />
    ... and on, and on, and on ...

    <script type="text/javascript" src="jquery.js"></script>
    <script type="text/javascript" src="jquery-migrate.min.js"></script>
    <script type="text/javascript" src="tabs.min.js"></script>
    <script type="text/javascript" src="custom.js"></script>
    <script type="text/javascript" src="plugin1.js"></script>
    <script type="text/javascript" src="plugin2.js"></script>
    <script type="text/javascript" src="plugin3.js"></script>
    <script type="text/javascript" src="plugin4.js"></script>
    <script type="text/javascript" src="plugin5.js"></script>
    ... and on, and on, and on ...

#### ↑ The Problem Here?

Instead of a single CSS and/or JS file (i.e., one or two HTTP connections); the browser needs to make *several* requests; and it needs to download each of these resources separately. This is not a problem that impacts WordPress alone, we see this issue across many publishing platforms where plugins are brought into the mix.

Ideally, your publishing platform (or theme) would minimize the number of external resources that it depends on by consolidating those external resources (i.e., CSS/JS files) into just one or two files; and then compress them too. However, not all themes do this. In fact, this is not always possible (even when a theme/plugin developer is aware of the issue).

For instance, if a theme/plugin developer is working within a set of PHP framework standards (e.g., doing things "the WordPress way"), the end result may not always be optimized in an ideal fashion. We know first-hand that this really bugs developers. Experienced developers don't create a mess by choice, it's just how the framework pulls everything together that can sometimes produce a mess. Also, when a site owner adds plugins to the mix later; where the publishing platform (or theme) is being supplemented by CSS/JS files that are plugin-specific—this is where things can really get crazy; e.g., a new CSS and/or JS file for each plugin.

## Solution, the WebSharks™ HTML Compressor!

**The WebSharks™ HTML Compressor works as an additional layer of functionality that can come in after your publishing platform pieces everything together.** The WebSharks™ HTML Compressor analyzes each page of your site in real-time; i.e., as it's being loaded; inspecting each line of HTML code.

CSS/JS files are combined (where possible) and compressed (where possible); then it can optimize the HTML code and any inline JavaScript/CSS too. The goal is to speed things up for your visitors and to reduce the number of HTTP connections that your server processes.

#### Step-by-Step (Detailed Explanation)

All of these compression options are enabled by default, but you can modify this behavior as you see fit. Toward the bottom of this file you will find a list of all possible configuration options.

**1.** The HTML Compressor starts by inspecting the `<head>` and `<body>` of the HTML document. An attempt is made to recursively combine all CSS resources (including inline styles, and all remote resources too) into a single CSS file. If `compress_css_code` is enabled (on by default), the code in this single file is also compressed (i.e., extra whitespace is removed, hex color codes are optimized, etc, etc).

   *A few NOTES regarding step `1`.*

   - **NOTE:** The HTML Compressor does its best to remain standards-compliant during this process; thereby reducing the chance of a conflict to an absolute minimum.

   - **NOTE:** An attempt is made to fetch remote resources (e.g., externally hosted resources) or `@import` rules. Any remote resources are simply added to the single CSS file to eliminate any possibility of more than one HTTP connection being required to load the consolidated CSS. The HTML Compressor is also capable of resolving file paths and `url()` references to the proper location during this process.

   - **NOTE:** The HTML Compressor avoids cascading conflicts by intelligently skipping over any exclusions that you define and/or anything that is impossible to consolidate. In some cases 10 files might become 1; in other cases 10 files might become 2 or 3. This could vary from site to site—or from page-to-page even. If 1 file is possible, that's what you'll get. Otherwise, you will get whatever compression is possible.

   - **NOTE:** Under no circumstance will the HTML Compression alter the loading order of the underlying CSS resources. Even when files are combined in multiple sets (or certain exclusions are skipped over), the loading order is always preserved so that CSS conflicts are not introduced by this process.

   - **NOTE:** All CSS `@` rules are preserved by the HTML Compressor too. This includes `@media` specifications (whether they be defined in an `@import` or `<link media="">` tag; or with an embedded `@media` rule). There is only one exception. The `@charset` rule is always forced to a value of `@charset "UTF-8";` (recommended anyway).

   - **NOTE:** Conditional CSS (e.g., `<!--if[]` tags) are always excluded from consolidation so their behavior is not altered. Cascading order is preserved when exclusions are encountered; this goes for `<!--if[]` tags too of course.

**2.** Next, we inspect the `<head>` of the HTML document. An attempt is made to combine all JS resources in the `<head>` into a single JS file. If `compress_js_code` is enabled (on by default), the code in this single file is also compressed (i.e., extra whitespace is removed, variable names are optimized, etc, etc).

**3.** Next, we inspect a special area of the source code that can be flagged for compression by wrapping a section with `<!--footer-scripts--><!--footer-scripts-->`. This flagging is only necessary if you have scripts that you intentionally place in the footer. If the HTML Compressor finds a `<!--footer-scripts--><!--footer-scripts-->` section; an attempt is made to combine all JS resources into a single JS file. If `compress_js_code` is enabled (on by default), the code in this single file is also compressed (i.e., extra whitespace is removed, variable names are optimized, etc, etc).

   *A few NOTES regarding steps `2` and `3`.*

   - **NOTE:** The HTML Compressor does its best to remain standards-compliant during this process; thereby reducing the chance of a conflict to an absolute minimum.

   - **NOTE:** An attempt is made to fetch remote resources (e.g., externally hosted JavaScript resources). Any remote resources are simply added to the single JS file to eliminate any possibility of more than one HTTP connection being required to load the consolidated JS. The HTML Compressor is also capable of resolving file paths and references to the proper location during this process.

   - **NOTE:** The HTML Compressor avoids loading order conflicts by intelligently skipping over any exclusions that you define and/or anything that is impossible to consolidate. In some cases 10 files might become 1; in other cases 10 files might become 2 or 3. This could vary from site to site; or from page-to-page even. If 1 file is possible, that's what you'll get. Otherwise, you will get whatever compression is possible.

   - **NOTE:** Under no circumstance will the HTML Compression alter the loading order of the underlying JS resources. Even when files are combined in multiple sets (or certain exclusions are skipped over), the loading order is always preserved so that JS conflicts are not introduced by this process.

   - **NOTE:** All JavaScript `async` indicators are preserved during this process. Asynchronous loading is detected by the `async` and/or `defer` attribute in a `<script>` tag.

   - **NOTE:** Conditional JS (e.g., `<!--if[]` tags) are always excluded from consolidation so their behavior is not altered. Loading order is preserved when exclusions are encountered; this goes for `<!--if[]` tags too of course.

**4.** Next, we look at the `<body>` for any inline `<script>` tags. While it is not possible  to consolidate inline JS; if `compress_inline_js_code` is enabled (on by default) an attempt is made to compress the JavaScript code in these inline code snippets to reduce the amount of overhead they might add.

**5.** Last, we compress the HTML code itself (i.e., extra whitespace is removed). Care is taken to preserve special tags where raw formatting is important; but you should end up with a much smaller HTML file; and the external resources it depends on will have certainly be reduced to a bare minimum.

----

## Some Usage Examples

### 1. HTML Compressor As An Output Buffer
*This code snippet should be processed BEFORE any other output occurs.*

```php
<?php
require_once 'html-compressor.phar';
$html_compressor = new WebSharks\HtmlCompressor\Core();
ob_start(array($html_compressor, 'compress'));
```

**TIP:** The `php.ini` directive [auto_prepend_file](http://www.php.net/manual/en/ini.core.php#ini.auto-prepend-file) is a nice/clean way to integrate the HTML Compressor. You could create a file as seen in this example and specify that as the `auto_prepend_file` to enable compression of every HTML file that you serve; noting that the HTML Compressor will simply pass any non-HTML code through it's buffer without compressing it. The HTML Compressor only attempts to compress data which contains a closing `</html>` tag.

**IMPORTANT NOTE:** One thing to keep in mind is that the WebSharks™ HTML Compressor works best when it's integrated together with a page caching plugin like ZenCache for WordPress; or another page caching plugin that you might prefer. **Why use a page caching plugin?** The HTML Compressor can be used on any site powered by PHP; but ideally you would cache the optimized HTML that it outputs, thereby removing the need for the HTML Compressor to analyze every single request. Of course, you *can* analyze every single request if you want to *(and the HTML Compressor has a cache of it's own to help keep things sane)*, but it's always better to store (cache) the compressed HTML output by this class. This will reduce server load and make your site even faster.

**FAQ:** Is `html-compressor.phar` the only file that I need? Yes. The other files that you see in the GitHub repo are already compressed into the PHAR file. The only file you need is the `html-compressor.phar`. A PHAR binary is made available for each official release. See: [releases](https://github.com/websharks/html-compressor/releases).

### 2. HTML Compressor as an Output Buffer (w/ Options)
*This just demonstrates how to specify an array of options.*

```php
<?php
require_once 'html-compressor.phar';
$html_compressor_options = array(

    'css_exclusions' => array(),
    'js_exclusions' => array('.php?'),

    'cache_expiration_time' => '14 days',
    'cache_dir_public' => '/var/www/public_html/htmlc/cache/public',
    'cache_dir_url_public' => 'http://example.com/htmlc/cache/public',
    'cache_dir_private' => '/var/www/public_html/htmlc/cache/private',

    'compress_combine_head_body_css' => TRUE,
    'compress_combine_head_js' => TRUE,
    'compress_combine_footer_js' => TRUE,
    'compress_inline_js_code' => TRUE,
    'compress_css_code' => TRUE,
    'compress_js_code' => TRUE,
    'compress_html_code' => TRUE,

    'benchmark' => FALSE,
    'product_title' => 'HTML Compressor',
    'vendor_css_prefixes' => array('moz','webkit','khtml','ms','o')
);
$html_compressor = new WebSharks\HtmlCompressor\Core($html_compressor_options);
ob_start(array($html_compressor, 'compress'));
```

### 3. HTML Compressor on raw HTML Code
*This demonstrates how to run the compressor against arbitrary HTML code.*

```php
<?php
require_once 'html-compressor.phar';
$html_compressor_options = array(

    'css_exclusions' => array(),
    'js_exclusions' => array('.php?'),

    'cache_expiration_time' => '14 days',
    'cache_dir_public' => '/var/www/public_html/htmlc/cache/public',
    'cache_dir_url_public' => 'http://example.com/htmlc/cache/public',
    'cache_dir_private' => '/var/www/public_html/htmlc/cache/private',

    'current_url_scheme' => 'http',
    'current_url_host' => 'www.example.com',
    'current_url_uri' => '/raw/file/test.html?one=1&two=2',

    'compress_combine_head_body_css' => TRUE,
    'compress_combine_head_js' => TRUE,
    'compress_combine_footer_js' => TRUE,
    'compress_inline_js_code' => TRUE,
    'compress_css_code' => TRUE,
    'compress_js_code' => TRUE,
    'compress_html_code' => TRUE,

    'benchmark' => FALSE,
    'product_title' => 'HTML Compressor',
    'vendor_css_prefixes' => array('moz','webkit','khtml','ms','o')
);
$html = '<html> ... </html>';
$html_compressor = new WebSharks\HtmlCompressor\Core($html_compressor_options);
$html = $html_compressor->compress($html);
```

----

## Class Constructor Options

e.g., `new WebSharks\HtmlCompressor\Core($options);`

*where `$options` is an associative array with one or more keys listed below.*

#### Current List of All Possible Options

##### The following options allow you to exclude certain CSS/JS files and/or inline snippets.

 *NOTE: these options only apply if compression is enabled for CSS/JS files.*

- (array)`css_exclusions` Defaults to `array()`. If you have some CSS files (or inline styles) that should NOT be included by compression routines,
 please specify an array of search tokens to exclude. Search tokens are compared to external CSS file `href` values (i.e., URLs or paths).
 Search tokens are also compared to the contents of any inline `<style></style>` tags.

- (array)`js_exclusions` Defaults to `array()`. If you have some JS files (or inline scripts) that should NOT be included by compression routines,
 please specify an array of search tokens to exclude. Search tokens are compared to external JS file `src` values (i.e., URLs or paths).
 Search tokens are also compared to the contents of any inline `<script></script>` tags in cases where compression is possible.

  *NOTE: Search tokens should be string literals. The HTML Compressor currently does NOT support wildcards or regex in search tokens.
 If you need to use regex patterns instead of search tokens, please use a `regex_` prefix when you define the option key; e.g., `'regex_js_exclusions' => '/\special\-script\.js/i'`. This works for `regex_css_exclusions` too.*

----

- (boolean)`disable_built_in_css_exclusions` Defaults to `FALSE`. There are a few built-in exclusion patterns that are updated (improved) with each new release of the HTML Compressor. It is recommended that you leave these enabled at all times.
 However, if you prefer to turn them off (i.e., to *only* exclude the patterns you specify), you can set this to a `TRUE` value to disable all of those which are built-in.
 Built-in exclusions deal with things that should almost always be excluded; for any site. If you'd like to see the list of built-in patterns
 you can read the `$built_in_regex_css_exclusion_patterns` class property. It's an array of regular expressions that are compared to external CSS file
 `href` values (i.e., URLs or paths); and they are also compared to the contents of any inline `<style></style>` tags.

- (boolean)`disable_built_in_js_exclusions` Defaults to `FALSE`. There are a few built-in exclusion patterns that are updated (improved) with each new release of the HTML Compressor. It is recommended that you leave these enabled at all times.
 However, if you prefer to turn them off (i.e., to *only* exclude the patterns you specify), you can set this to a `TRUE` value to disable all of those which are built-in.
 Built-in exclusions deal with things that should almost always be excluded; for any site. If you'd like to see the list of built-in patterns
 you can read the `$built_in_regex_js_exclusion_patterns` class property. It's an array of regular expressions that are compared to external JS file
 `src` values (i.e., URLs or paths); and they are also compared to the contents of any inline `<script></script>` tags.

----

##### The following options can be used to setup custom cache directories/URLs.

*NOTE: under most circumstances, the built-in default values will do just fine.*

- (string)`cache_expiration_time` Defaults to a value of `14 days`. You can use anything compatible with PHP's `strtotime()` function.
 NOTE: This expiration time is mostly irrelevant, because the HTML Compressor uses an internal checksum, and it also checks `filemtime()` before
 using an existing cache file. The HTML Compressor class also handles the automatic cleanup of your cache directories to keep it from growing too large over time.
 Therefore, unless you have VERY little disk space there is no reason to set this to a lower value (even if your site changes dynamically quite often).
 If anything, you might like to increase this value which could help to further reduce server load.

  **There are two scenarios where a cache regeneration occurs.**

  1. When a cache file expires (based on your expiration time).
  2. A cache file MUST be regenerated because there is a checksum mis-match (e.g., the content changed dynamically).

  In short, `cache_expiration_time` controls the first scenario; i.e., the absolute maximum amount of time that a cache file can ever live (or be used); and this will impact the automatic cleanup routine too of course.

- (string)`cache_dir_public` Absolute server path to a local cache directory that is available over HTTP (i.e., publicly accessible).
 If you exclude this, there are two default handlers. If `WP_CONTENT_DIR` is defined (i.e., you are running this within WordPress),
 then your public cache directory will be located under: `wp-content/htmlc/cache/public`. Otherwise, this will default to
 a value of `$_SERVER['DOCUMENT_ROOT']/htmlc/cache/public`.

- (string)`cache_dir_url_public` A publicly available URL which leads to your `cache_dir_public`.
 If you exclude this option, an automatic detection is used (i.e., a best guess based on `cache_dir_public`).

- (string)`cache_dir_private` Absolute server path to a local cache directory that is NOT available over HTTP (i.e., private/hidden).
 If you exclude this, there are two default handlers. If `WP_CONTENT_DIR` is defined (i.e., you are running this within WordPress),
 then your private cache directory will be located under: `wp-content/htmlc/cache/private`. Otherwise, this will default to
 a value of `$_SERVER['DOCUMENT_ROOT']/htmlc/cache/private`.

- (string)`cache_dir_url_private` Not applicable. This option exists internally but URLs to the private cache directory
 are never generated. Therefore, under normal circumstances you can ignore this option value all together.

- (boolean)`cleanup_cache_dirs` Defaults to TRUE. By default, cache directories are cleaned up automatically
 over time; i.e., at semi-random intervals and also based on your `cache_expiration_time`. If you would prefer to cleanup
 the cache on your own, you can set this to a FALSE value.

----

##### The following options can be used to specify the current URL.

*NOTE: it is normally NOT necessary to supply any of these values.*

- (string)`current_url_scheme` When this class is running as a web application, we can detect this value automatically.
 That said, if you intend to run this class outside of a web server environment you will need to tell the compressor
 what the current URL scheme would be if the file you are compressing was being served as a web page.
 This should be set to one of `https` or `http`.

- (string)`current_url_host` When this class is running as a web application, we can detect this value automatically.
 That said, if you intend to run this class outside of a web server environment you will need to tell the compressor
 what the current URL host would be if the file you are compressing was being served as a web page.
 This should be set to something like `www.example.com`.

- (string)`current_url_uri` When this class is running as a web application, we can detect this value automatically.
 That said, if you intend to run this class outside of a web server environment you will need to tell the compressor
 what the current URI (i.e., path and query string) would be if the file you are compressing was being served as a web page.
 This should be set to something like `/path/to/page/?one=1&two=2`.

----

##### The following options control compression behavior.

*NOTE: compression routines are applied in the same order as these options are listed below.*

- (boolean)`compress_combine_head_body_css` TRUE by default. If you prefer NOT to combine CSS files into a single HTTP connection,
 please set this to a FALSE value. This can be helpful if your site (for whatever reason) is incompatible with the CSS compress-combine routines.
 NOTE: if you disable this due to an incompatibility, please report it via GitHub so the issue can be resolved for everyone.

- (boolean)`compress_combine_head_js` TRUE by default. If you prefer NOT to combine JS files into a single HTTP connection,
 please set this to a FALSE value. This can be helpful if your site (for whatever reason) is incompatible with the JS compress-combine routines.
 NOTE: if you disable this due to an incompatibility, please report it via GitHub so the issue can be resolved for everyone.

- (boolean)`compress_combine_footer_js` TRUE by default. If you prefer NOT to combine JS files in the footer into a single HTTP connection,
 please set this to a FALSE value. This can be helpful if your site (for whatever reason) is incompatible with the JS compress-combine routines.
 NOTE: if you disable this due to an incompatibility, please report it via GitHub so the issue can be resolved for everyone.

- (boolean)`compress_combine_remote_css_js` TRUE by default. If you prefer NOT to combine CSS/JS files from remote resource locations
 please set this to a FALSE value. By default, the options: `compress_combine_head_body_css`, `compress_combine_head_js`, `compress_combine_footer_js` will recursively combine all resources (including those from remote locations).
 If you set this to a FALSE value, all remote (externally hosted resources; e.g., those from CDNs or other remote URLs) will be excluded automatically to prevent remote off-site connections from taking place.

- (boolean)`compress_inline_js_code` TRUE by default. If you prefer NOT to compress inline JS code (i.e., minify the contents of inline `<script>` tags),
 please set this to a FALSE value. This can be helpful if your site (for whatever reason) is incompatible with the inline JS compression routines.
 NOTE: if you disable this due to an incompatibility, please report it via GitHub so the issue can be resolved for everyone.

- (boolean)`compress_css_code` TRUE by default. If you prefer NOT to compress CSS files (i.e., minify the underlying CSS code),
 please set this to a FALSE value. This can be helpful if your site (for whatever reason) is incompatible with the CSS compression routines.
 NOTE: if you disable this due to an incompatibility, please report it via GitHub so the issue can be resolved for everyone.

- (boolean)`compress_js_code` TRUE by default. If you prefer NOT to compress JS files (i.e., minify the underlying JS code),
 please set this to a FALSE value. This can be helpful if your site (for whatever reason) is incompatible with the JS compression routines.
 NOTE: if you disable this due to an incompatibility, please report it via GitHub so the issue can be resolved for everyone.

- (boolean)`compress_html_code` TRUE by default. If you prefer NOT to compress HTML markup (i.e., the removal of extra whitespace, etc),
 please set this to a FALSE value. This can be helpful if your site (for whatever reason) is incompatible with the HTML compression routines.
 NOTE: if you disable this due to an incompatibility, please report it via GitHub so the issue can be resolved for everyone.

----

##### Other misc. options. These don't really fall into any specific category yet.

- (boolean|string)`benchmark` Off by default. If enabled, HTML comments are added to the final output with some useful information. If you set this to a `TRUE` value, it simply enables an overall time calculation. If you're debugging, and you need to see many other details too; you can set this to the string: `details` (not recommended in a production environment).

- (string)`product_title` Define this if you'd like to change the title of a particular HTML Compressor instance (i.e., to change the name from the default "HTML Compressor" to something else you like better). If undefined, this defaults to `HTML Compressor`. Note: this impacts the title of the product displayed in the HTML comments; assuming that `benchmark` is enabled where comments are included in the final output.

- (array)`vendor_css_prefixes` An array of known vendor-specific CSS prefixes. Defaults to `array('moz','webkit','khtml','ms','o')`.

----

## License

Copyright: © 2014 [WebSharks, Inc.](http://websharks-inc.com/) (coded in the USA)

Released under the terms of the [GNU General Public License](http://www.gnu.org/licenses/gpl-3.0.html).
