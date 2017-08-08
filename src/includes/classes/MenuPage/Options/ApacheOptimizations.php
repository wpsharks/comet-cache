<?php
namespace WebSharks\CometCache\Classes\MenuPage\Options;

use WebSharks\CometCache\Classes;

/**
 * Options section.
 *
 * @since 17xxxx Refactor menu pages.
 */
class ApacheOptimizations extends Classes\AbsBase
{
    /**
     * Constructor.
     *
     * @since 17xxxx Refactor menu pages.
     */
    public function __construct()
    {
        parent::__construct(); // Parent constructor.

        if ($this->plugin->isApache() || $this->plugin->isProPreview()) {
            echo '<div class="plugin-menu-page-panel'.(!IS_PRO && $this->plugin->isProPreview() ? ' pro-preview' : '').'">'."\n";

            echo '   <a href="#" class="plugin-menu-page-panel-heading" data-additional-pro-features="'.(!IS_PRO && $this->plugin->isProPreview() ? __('additional pro features', 'comet-cache') : '').'">'."\n";
            echo '      <i class="si si-server"></i> '.__('Apache Optimizations', 'comet-cache')."\n";
            echo '   </a>'."\n";

            echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
            echo '      <img src="'.esc_attr($this->plugin->url('/src/client-s/images/apache.png')).'" class="screenshot" />'."\n";
            echo '      <h3>'.__('Apache Performance Tuning (Optional; Highly Recommended)', 'comet-cache').'</h3>'."\n";
            echo '      <p>'.__('You don\'t need to use an <code>.htaccess</code> file to enjoy the performance enhancements provided by this plugin; caching is handled automatically by WordPress/PHP alone. That being said, if you want to take advantage of additional speed enhancements by optimizing the Apache web server to achieve maximize performance (and we do recommend this), then you WILL need an <code>.htaccess</code> file to accomplish that part.', 'comet-cache').'</p>'."\n";
            echo '      <p>'.__('WordPress itself uses the <code>.htaccess</code> file to create Apache rewrite rules when you enable fancy Permalinks, so there\'s a good chance you already have an <code>.htaccess</code> file. The options below allow for additional performance tuning using recommendations provided by Comet Cache.', 'comet-cache').'</p>'."\n";
            echo '      <p>'.__('When you enable one of the options below, Comet Cache will attempt to automatically insert the appropriate configuration into your <code>.htaccess</code> file (or remove it automatically if you are disabling an option). If Comet Cache is unable to update the file, or if you would prefer to add the configuration yourself, the recommended configuration to add to the file can be viewed at the bottom of each option.', 'comet-cache').'</p>'."\n";
            echo '              <p class="info" style="display:block;">'.__('<strong>Note:</strong> The <code>.htaccess</code> file is parsed by the web server directly, before WordPress is even loaded. For that reason, if something goes wrong in the file you can end up with a broken site. We recommend creating a backup of your current <code>.htaccess</code> file before making any modifications.', 'comet-cache').'</p>'."\n";
            echo '      <hr />'."\n";
            echo '      <h3>'.__('Enable GZIP Compression?', 'comet-cache').'</h3>'."\n";
            echo '      <p>'.__('<a href="https://cometcache.com/r/google-developers-gzip-compression/" target="_blank">GZIP compression</a> is highly recommended. It\'s not uncommon to achieve compression rates as high as 70-90%, which is a huge savings in the amount of data that needs to be transferred with each visit to your site.', 'comet-cache').'</p>'."\n";
            echo '      <p>'.sprintf(__('%1$s fully supports GZIP compression on its output. However, it does not handle GZIP compression directly like some caching plugins. We purposely left GZIP compression out of this plugin because GZIP compression is something that should really be enabled at the Apache level or inside your <code>php.ini</code> file. GZIP compression can be used for things like JavaScript and CSS files as well, so why bother turning it on for only WordPress-generated pages when you can enable GZIP at the server level and cover all the bases!', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][htaccess_gzip_enable]" data-target=".-htaccess-gzip-enable-options">'."\n";
            echo '            <option value="0"'.selected($this->plugin->options['htaccess_gzip_enable'], '0', false).'>'.__('No, do NOT enable GZIP Compression (or I\'ll update my configuration manually; see below)', 'comet-cache').'</option>'."\n";
            echo '            <option value="1"'.selected($this->plugin->options['htaccess_gzip_enable'], '1', false).'>'.__('Yes, enable GZIP Compression (recommended)', 'comet-cache').'</option>'."\n";
            echo '         </select></p>'."\n";
            echo '      <p>'.__('Or, you can update your configuration manually: [<a href="#" data-toggle-target=".'.esc_attr(GLOBAL_NS.'-apache-optimizations--gzip-configuration').'"><i class="si si-eye"></i> .htaccess configuration <i class="si si-eye"></i></a>]', 'comet-cache').'</p>'."\n";
            echo '      <div class="'.esc_attr(GLOBAL_NS.'-apache-optimizations--gzip-configuration').'" style="display:none; margin-top:1em;">'."\n";
            echo '        <p>'.__('<strong>To enable GZIP compression:</strong> Create or edit the <code>.htaccess</code> file in your WordPress installation directory and add the following lines to the top:', 'comet-cache').'</p>'."\n";
            echo '        <pre class="code"><code>'.esc_html($this->plugin->fillReplacementCodes(file_get_contents(dirname(__DIR__).'/templates/htaccess/gzip-enable.txt'))).'</code></pre>'."\n";
            echo '        <p class="info" style="display:block;">'.__('<strong>Or</strong>, if your server is missing <code>mod_deflate</code>/<code>mod_filter</code>; open your <code>php.ini</code> file and add this line: <a href="http://php.net/manual/en/zlib.configuration.php" target="_blank" style="text-decoration:none;"><code>zlib.output_compression = on</code></a>', 'comet-cache').'</p>'."\n";
            echo '      </div>'."\n";

            if ((!IS_PRO && $this->plugin->isApache()) && !$this->plugin->isProPreview()) {
                echo '      <hr />'."\n";
                echo '      <p class="warning" style="display:block;">'.sprintf(__('<a href="%1$s">Enable the Pro Preview</a> to see <strong>Leverage Browser Caching</strong>, <strong>Enforce Canonical URLs</strong>, and more!', 'comet-cache'), esc_attr(add_query_arg(urlencode_deep(['page' => GLOBAL_NS, GLOBAL_NS.'_pro_preview' => '1']), self_admin_url('/admin.php')))).'</p>'."\n";
            }
            if (IS_PRO || $this->plugin->isProPreview()) {
                echo '      <hr />'."\n";
                echo '      <h3 data-pro-version-only="'.(!IS_PRO ? __('pro version only', 'comet-cache') : '').'">'.__('Leverage Browser Caching?', 'comet-cache').'</h3>'."\n";
                echo '      <p>'.__('<a href="https://cometcache.com/r/google-developers-http-caching/" target="_blank">Browser Caching</a> is highly recommended. When loading a single page, downloading all of the resources for that page may require multiple roundtrips between the browser and server, which delays processing and may block rendering of page content. This also incurs data costs for the visitor. With browser caching, your server tells the visitor\'s browser that it is allowed to cache static resources for a certain amount of time (Google recommends 1 week and that\'s what Comet Cache uses).', 'comet-cache').'</p>'."\n";
                echo '      <p>'.__('In WordPress, \'Page Caching\' is all about server-side performance (reducing the amount of time it takes the server to generate the page content). With Comet Cache installed, you\'re drastically reducing page generation time. However, you can make a visitor\'s experience ​<em>even faster</em>​ when you leverage browser caching too. When this option is enabled, the visitor\'s browser will cache static resources from each page and reuse those cached resources on subsequent page loads. In this way, future visits to the same page will not require additional connections to your site to download static resources that the visitor\'s browser has already cached.', 'comet-cache').'</p>'."\n";
                echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][htaccess_browser_caching_enable]" data-target=".-htaccess-browser-caching-enable-options">'."\n";
                echo '            <option value="0"'.(!IS_PRO ? '' : selected($this->plugin->options['htaccess_browser_caching_enable'], '0', false)).'>'.__('No, do NOT enable Browser Caching (or I\'ll update my configuration manually; see below)', 'comet-cache').'</option>'."\n";
                echo '            <option value="1"'.(!IS_PRO ? 'selected' : selected($this->plugin->options['htaccess_browser_caching_enable'], '1', false)).'>'.__('Yes, enable Browser Caching for static resources (recommended)', 'comet-cache').'</option>'."\n";
                echo '         </select></p>'."\n";
                echo '      <p>'.__('Or, you can update your configuration manually: [<a href="#" data-toggle-target=".'.esc_attr(GLOBAL_NS.'-apache-optimizations--leverage-browser-caching').'"><i class="si si-eye"></i> .htaccess configuration <i class="si si-eye"></i></a>]', 'comet-cache').'</p>'."\n";
                echo '      <div class="'.esc_attr(GLOBAL_NS.'-apache-optimizations--leverage-browser-caching').'" style="display:none; margin-top:1em;">'."\n";
                echo '        <p>'.__('<strong>To enable Browser Caching:</strong> Create or edit the <code>.htaccess</code> file in your WordPress installation directory and add the following lines to the top:', 'comet-cache').'</p>'."\n";
                echo '        <pre class="code"><code>'.esc_html($this->plugin->fillReplacementCodes(file_get_contents(dirname(__DIR__).'/templates/htaccess/browser-caching-enable.txt'))).'</code></pre>'."\n";
                echo '      </div>'."\n";
            }
            if (IS_PRO || $this->plugin->isProPreview()) {
                echo '      <hr />'."\n";
                echo '      <h3 data-pro-version-only="'.(!IS_PRO ? __('pro version only', 'comet-cache') : '').'">'.__('Enforce an Exact Hostname?', 'comet-cache').'</h3>'."\n";
                echo '      <p>'.sprintf(__('By enforcing an exact hostname you avoid duplicate cache files, which saves disk space and improves cache performance. For example, if a bot or crawler accesses your site using your server\'s IP address instead of using your domain name (e.g., <code>http://123.456.789/path</code>), this results in duplicate cache files, because the host was an IP address. The \'host\' being an important factor in any cache storage system. The same would be true if a visitor attempted to access your site using a made-up sub-domain; e.g., <code>http://foo.bar.%1$s/path</code>. This sort of thing can be avoided by explicitly enforcing an exact hostname in the request. One that matches exactly what you\'ve configured in <strong>WordPress Settings → General</strong>.', 'comet-cache'), esc_html(parse_url(network_home_url(), PHP_URL_HOST))).'</p>'."\n";
                echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][htaccess_enforce_exact_host_name]" data-target=".-htaccess-enforce-exact-host-name-options">'."\n";
                echo '            <option value="0"'.(!IS_PRO ? '' : selected($this->plugin->options['htaccess_enforce_exact_host_name'], '0', false)).'>'.__('No, do NOT enforce an exact hostname (or I\'ll update my configuration manually; see below)', 'comet-cache').'</option>'."\n";
                echo '            <option value="1"'.(!IS_PRO ? 'selected' : selected($this->plugin->options['htaccess_enforce_exact_host_name'], '1', false)).'>'.sprintf(__('Yes, enforce the exact hostname: %1$s', 'comet-cache'), esc_html(parse_url(network_home_url(), PHP_URL_HOST))).'</option>'."\n";
                echo '         </select></p>'."\n";
                echo '      <p>'.__('Or, you can update your configuration manually: [<a href="#" data-toggle-target=".'.esc_attr(GLOBAL_NS.'-apache-optimizations--enforce-exact-host-name').'"><i class="si si-eye"></i> .htaccess configuration <i class="si si-eye"></i></a>]', 'comet-cache').'</p>'."\n";
                echo '      <div class="'.esc_attr(GLOBAL_NS.'-apache-optimizations--enforce-exact-host-name').'" style="display:none; margin-top:1em;">'."\n";
                echo '        <p>'.__('<strong>To enforce an exact hostname:</strong> Create or edit the <code>.htaccess</code> file in your WordPress installation directory and add the following lines to the top:', 'comet-cache').'</p>'."\n";
                echo '        <pre class="code"><code>'.esc_html($this->plugin->fillReplacementCodes(file_get_contents(dirname(__DIR__).'/templates/htaccess/enforce-exact-host-name.txt'))).'</code></pre>'."\n";
                echo '      </div>'."\n";
            }
            if ((IS_PRO && !empty($GLOBALS['wp_rewrite']->permalink_structure)) || $this->plugin->isProPreview()) {
                echo '      <hr />'."\n";
                echo '      <h3 data-pro-version-only="'.(!IS_PRO ? __('pro version only', 'comet-cache') : '').'">'.__('Enforce Canonical URLs?', 'comet-cache').'</h3>'."\n";
                echo '      <p>'.__('Permalinks (URLs) leading to Posts/Pages on your site (based on your WordPress Permalink Settings) '.($GLOBALS['wp_rewrite']->use_trailing_slashes ? 'require a <code>.../trailing-slash/</code>' : 'do not require a <code>.../trailing-slash</code>').'. Ordinarily, WordPress enforces this by redirecting a request for '.($GLOBALS['wp_rewrite']->use_trailing_slashes ? '<code>.../something</code>' : '<code>.../something/</code>').', to '.($GLOBALS['wp_rewrite']->use_trailing_slashes ? '<code>.../something/</code>' : '<code>.../something</code>').', thereby forcing the final location to match your Permalink configuration. However, whenever you install a plugin like Comet Cache, much of WordPress (including this automatic redirection) is out of the picture when the cached copy of a page is being served. So enabling this option will add rules to your <code>.htaccess</code> file that make Apache aware of your WordPess Permalink configuration. Apache can do what WordPress normally would, only much more efficiently.', 'comet-cache').'</p>'."\n";
                echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][htaccess_enforce_canonical_urls]" data-target=".-htaccess-enforce-canonical-urls-options">'."\n";
                echo '            <option value="0"'.(!IS_PRO ? '' : selected($this->plugin->options['htaccess_enforce_canonical_urls'], '0', false)).'>'.__('No, do NOT enforce canonical URLs (or I\'ll update my configuration manually; see below)', 'comet-cache').'</option>'."\n";
                echo '            <option value="1"'.(!IS_PRO ? 'selected' : selected($this->plugin->options['htaccess_enforce_canonical_urls'], '1', false)).'>'.__('Yes, enforce canonical URLs (recommended)', 'comet-cache').'</option>'."\n";
                echo '         </select></p>'."\n";
                echo '      <p>'.__('Or, you can update your configuration manually: [<a href="#" data-toggle-target=".'.esc_attr(GLOBAL_NS.'-apache-optimizations--enforce-cononical-urls').'"><i class="si si-eye"></i> .htaccess configuration <i class="si si-eye"></i></a>]', 'comet-cache').'</p>'."\n";
                echo '      <div class="'.esc_attr(GLOBAL_NS.'-apache-optimizations--enforce-cononical-urls').'" style="display:none; margin-top:1em;">'."\n";
                echo '        <p>'.__('<strong>To enforce Canonical URLs:</strong> Create or edit the <code>.htaccess</code> file in your WordPress installation directory and add the following lines to the top:', 'comet-cache').'</p>'."\n";
                if ($GLOBALS['wp_rewrite']->use_trailing_slashes) {
                    echo '        <pre class="code"><code>'.esc_html($this->plugin->fillReplacementCodes(file_get_contents(dirname(__DIR__).'/templates/htaccess/canonical-urls-ts-enable.txt'))).'</code></pre>'."\n";
                } else {
                    echo '        <pre class="code"><code>'.esc_html($this->plugin->fillReplacementCodes(file_get_contents(dirname(__DIR__).'/templates/htaccess/canonical-urls-no-ts-enable.txt'))).'</code></pre>'."\n";
                }
                echo '      </div>'."\n";
            }
            if ((IS_PRO && $this->plugin->options['cdn_enable']) || $this->plugin->isProPreview()) {
                echo '      <hr />'."\n";
                echo '      <h3 data-pro-version-only="'.(!IS_PRO ? __('pro version only', 'comet-cache') : '').'">'.__('Send Access-Control-Allow-Origin Header?', 'comet-cache').'</h3>'."\n";
                if ($this->plugin->options['cdn_enable'] && !$this->plugin->options['htaccess_access_control_allow_origin']) {
                    echo '        <p class="warning" style="display:block;">'.__('<strong>Warning:</strong> Send Access-Control-Allow-Origin Header has been disabled below but <strong>Comet Cache → Plugin Options → Static CDN Filters</strong> are enabled. We recommend configuring your server to send the <code>Access-Control-Allow-Origin</code> header to avoid <a href="https://cometcache.com/r/kb-article-what-are-cross-origin-request-blocked-cors-errors/" target="_blank">CORS errors</a> when a CDN is configured.', 'comet-cache').'</p>'."\n";
                }
                echo '      <p>'.__('If you are using Static CDN Filters to load resources for your site from another domain, it\'s important that your server sends an <code>Access-Control-Allow-Origin</code> header to prevent Cross Origin Resource Sharing (CORS) errors. This option is enabled automatically when you enable Static CDN Filters. For more information, see <a href="https://cometcache.com/r/kb-article-what-are-cross-origin-request-blocked-cors-errors/" target="_blank">this article</a>.', 'comet-cache').'</p>'."\n";
                echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][htaccess_access_control_allow_origin]" data-target=".-htaccess-access-control-allow-origin-options">'."\n";
                echo '            <option value="0"'.(!IS_PRO ? '' : selected($this->plugin->options['htaccess_access_control_allow_origin'], '0', false)).'>'.__('No, do NOT send the Access-Control-Allow-Origin header (or I\'ll update my configuration manually; see below)', 'comet-cache').'</option>'."\n";
                echo '            <option value="1"'.(!IS_PRO ? 'selected' : selected($this->plugin->options['htaccess_access_control_allow_origin'], '1', false)).'>'.__('Yes, send the Access-Control-Allow-Origin header (recommended for Static CDN Filters)', 'comet-cache').'</option>'."\n";
                echo '         </select></p>'."\n";
                echo '      <p>'.__('Or, you can update your configuration manually: [<a href="#" data-toggle-target=".'.esc_attr(GLOBAL_NS.'-apache-optimizations--access-control-allow-origin').'"><i class="si si-eye"></i> .htaccess configuration <i class="si si-eye"></i></a>]', 'comet-cache').'</p>'."\n";
                echo '      <div class="'.esc_attr(GLOBAL_NS.'-apache-optimizations--access-control-allow-origin').'" style="display:none; margin-top:1em;">'."\n";
                echo '        <p>'.__('<strong>To send the Access-Control-Allow-Origin header:</strong> Create or edit the <code>.htaccess</code> file in your WordPress installation directory and add the following lines to the top:', 'comet-cache').'</p>'."\n";
                echo '        <pre class="code"><code>'.esc_html($this->plugin->fillReplacementCodes(file_get_contents(dirname(__DIR__).'/templates/htaccess/access-control-allow-origin-enable.txt'))).'</code></pre>'."\n";
                echo '      </div>'."\n";
            }
            echo '   </div>'."\n";
            echo '</div>'."\n";
        }
    }
}
