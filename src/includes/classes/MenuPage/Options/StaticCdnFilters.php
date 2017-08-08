<?php
namespace WebSharks\CometCache\Classes\MenuPage\Options;

use WebSharks\CometCache\Classes;

/**
 * Options section.
 *
 * @since 17xxxx Refactor menu pages.
 */
class StaticCdnFilters extends Classes\AbsBase
{
    /**
     * Constructor.
     *
     * @since 17xxxx Refactor menu pages.
     */
    public function __construct()
    {
        parent::__construct(); // Parent constructor.

        if (IS_PRO || $this->plugin->isProPreview()) {
            echo '<div class="plugin-menu-page-panel'.(!IS_PRO ? ' pro-preview' : '').'">'."\n";

            echo '   <a href="#" class="plugin-menu-page-panel-heading" data-pro-version-only="'.(!IS_PRO ? __('pro version only', 'comet-cache') : '').'">'."\n";
            echo '      <i class="si si-cloud"></i> '.__('Static CDN Filters', 'comet-cache')."\n";
            echo '   </a>'."\n";

            echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
            echo '   <button type="button" class="plugin-menu-page-clear-cdn-cache" style="float:right; margin:0 0 1em 1em;" title="'.esc_attr(__('Clear CDN Cache (Bump CDN Invalidation Counter)', 'comet-cache')).'">'.__('Clear CDN Cache', 'comet-cache').' <img src="'.esc_attr($this->plugin->url('/src/client-s/images/clear.png')).'" style="width:16px; height:16px; display:inline-block;" /></button>'."\n";
            echo '      <h3>'.__('Enable Static CDN Filters (e.g., MaxCDN/CloudFront)?', 'comet-cache').'</h3>'."\n";
            echo '      <p>'.sprintf(__('This feature allows you to serve some and/or ALL static files on your site from a CDN of your choosing. This is made possible through content/URL filters exposed by WordPress and implemented by %1$s. All it requires is that you setup a CDN hostname sourced by your WordPress installation domain. You enter that CDN hostname below and %1$s will do the rest! Super easy, and it doesn\'t require any DNS changes either. :-) Please <a href="http://cometcache.com/r/static-cdn-filters-general-instructions/" target="_blank">click here</a> for a general set of instructions.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            echo '      <p>'.__('<strong>What\'s a CDN?</strong> It\'s a Content Delivery Network (i.e., a network of optimized servers) designed to cache static resources served from your site (e.g., JS/CSS/images and other static files) onto it\'s own servers, which are located strategically in various geographic areas around the world. Integrating a CDN for static files can dramatically improve the speed and performance of your site, lower the burden on your own server, and reduce latency associated with visitors attempting to access your site from geographic areas of the world that might be very far away from the primary location of your own web servers.', 'comet-cache').'</p>'."\n";
            if ($this->plugin->isNginx() && $this->plugin->applyWpFilters(GLOBAL_NS.'_wp_htaccess_nginx_notice', true) && (!isset($_SERVER['WP_NGINX_CONFIG']) || $_SERVER['WP_NGINX_CONFIG'] !== 'done')) {
                echo '<div class="plugin-menu-page-notice error">'."\n";
                echo '   <i class="si si-thumbs-down"></i> '.__('It appears that your server is running NGINX and does not support <code>.htaccess</code> rules. Please <a href="http://cometcache.com/r/kb-article-recommended-nginx-server-configuration/" target="_new">update your server configuration manually</a>. Note that updating your NGINX server configuration <em>before</em> enabling Static CDN Filters is recommended to prevent any <a href="http://cometcache.com/r/kb-article-what-are-cross-origin-request-blocked-cors-errors/" target="_new">CORS errors</a> with your CDN. If you\'ve already updated your NGINX configuration, you can safely <a href="http://cometcache.com/r/kb-article-how-do-i-disable-the-nginx-htaccess-notice/" target="_new">ignore this message</a>.', 'comet-cache')."\n";
                echo '</div>'."\n";
            }
            echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cdn_enable]" data-target=".-static-cdn-filter-options">'."\n";
            echo '            <option value="0"'.(!IS_PRO ? '' : selected($this->plugin->options['cdn_enable'], '0', false)).'>'.__('No, I do NOT want CDN filters applied at runtime.', 'comet-cache').'</option>'."\n";
            echo '            <option value="1"'.(!IS_PRO ? ' selected' : selected($this->plugin->options['cdn_enable'], '1', false)).'>'.__('Yes, I want CDN filters applied w/ my configuration below.', 'comet-cache').'</option>'."\n";
            echo '         </select></p>'."\n";
            if ($this->plugin->isApache() && $this->plugin->options['cdn_enable'] && !$this->plugin->options['htaccess_access_control_allow_origin']) {
                echo '        <p class="warning" style="display:block;">'.__('<strong>Warning:</strong> Static CDN Filters are enabled above but the <strong>Comet Cache → Plugin Options → Apache Optimizations → Send Access-Control-Allow-Origin Header</strong> option has been disabled. We recommend sending the <code>Access-Control-Allow-Origin</code> header to avoid <a href="https://cometcache.com/r/kb-article-what-are-cross-origin-request-blocked-cors-errors/" target="_blank">CORS errors</a> when a CDN is configured.', 'comet-cache').'</p>'."\n";
            }
            echo '      <hr />'."\n";

            echo '      <div class="plugin-menu-page-panel-if-enabled -static-cdn-filter-options">'."\n";

            echo '         <h3>'.__('CDN Hostname (Required)', 'comet-cache').'</h3>'."\n";

            echo '         <p class="info" style="display:block;">'.// This note includes three graphics. One for MaxCDN; another for CloudFront, and another for KeyCDN.
             '              <a href="http://cometcache.com/r/keycdn/" target="_blank"><img src="'.esc_attr($this->plugin->url('/src/client-s/images/keycdn-logo.png')).'" style="width:90px; float:right; margin: 18px 10px 0 18px;" /></a>'.
             '              <a href="http://cometcache.com/r/amazon-cloudfront/" target="_blank"><img src="'.esc_attr($this->plugin->url('/src/client-s/images/cloudfront-logo.png')).'" style="width:75px; float:right; margin: 8px 10px 0 25px;" /></a>'.
             '              <a href="http://cometcache.com/r/maxcdn/" target="_blank"><img src="'.esc_attr($this->plugin->url('/src/client-s/images/maxcdn-logo.png')).'" style="width:125px; float:right; margin: 20px 0 0 25px;" /></a>'.
             '            '.__('This field is really all that\'s necessary to get Static CDN Filters working! However, it does requires a little bit of work on your part. You need to setup and configure a CDN before you can fill in this field. Once you configure a CDN, you\'ll receive a hostname (provided by your CDN), which you\'ll enter here; e.g., <code>js9dgjsl4llqpp.cloudfront.net</code>. We recommend <a href="http://cometcache.com/r/maxcdn/" target="_blank">MaxCDN</a>, <a href="http://cometcache.com/r/amazon-cloudfront/" target="_blank">Amazon CloudFront</a>, <a href="http://cometcache.com/r/keycdn/" target="_blank">KeyCDN</a>, and/or <a href="http://cometcache.com/r/cdn77/" target="_blank">CDN77</a> but this should work with many of the most popular CDNs. Please read <a href="http://cometcache.com/r/static-cdn-filters-general-instructions/" target="_blank">this article</a> for a general set of instructions. We also have a <a href="http://cometcache.com/r/static-cdn-filters-maxcdn/" target="_blank">MaxCDN tutorial</a>, <a href="http://cometcache.com/r/static-cdn-filters-cloudfront/" target="_blank">CloudFront tutorial</a>, <a href="http://cometcache.com/r/static-cdn-filters-keycdn/" target="_blank">KeyCDN tutorial</a>, and a <a href="http://cometcache.com/r/static-cdn-filters-cdn77/" target="_blank">CDN77 tutorial</a> to walk you through the process.', 'comet-cache').'</p>'."\n";
            echo '         <p><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][cdn_host]" value="'.esc_attr($this->plugin->options['cdn_hosts'] ? '' : $this->plugin->options['cdn_host']).'"'.($this->plugin->options['cdn_hosts'] ? ' disabled="disabled"' : '').' /></p>'."\n";

            echo '         <hr />'."\n";

            echo '         <h3>'.__('Multiple CDN Hostnames for Domain Sharding and Multisite Networks (Optional)', 'comet-cache').'</h3>'."\n";
            echo '         <p>'.sprintf(__('%1$s also supports multiple CDN Hostnames for any given domain. Using multiple CDN Hostnames (instead of just one, as seen above) is referred to as <strong><a href="http://cometcache.com/r/domain-sharding/" target="_blank">Domain Sharding</a></strong> (<a href="http://cometcache.com/r/domain-sharding/" target="_blank">click here to learn more</a>). If you configure multiple CDN Hostnames (i.e., if you implement Domain Sharding), %1$s will use the first one that you list for static resources loaded in the HTML <code>&lt;head&gt;</code> section, the last one for static resources loaded in the footer, and it will choose one at random for all other static resource locations. Configuring multiple CDN Hostnames can improve speed! This is a way for advanced site owners to work around concurrency limits in popular browsers; i.e., making it possible for browsers to download many more resources simultaneously, resulting in a faster overall completion time. In short, this tells the browser that your website will not be overloaded by concurrent requests, because static resources are in fact being served by a content-delivery network (i.e., multiple CDN hostnames). If you use this functionality for Domain Sharding, we suggest that you setup one CDN Distribution (aka: Pull Zone), and then create multiple CNAME records pointing to that distribution. You can enter each of your CNAMES in the field below, as instructed.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            echo '         <p class="info" style="display:block;">'.sprintf(__('<strong>On WordPress Multisite Network installations</strong>, this field also allows you to configure different CDN Hostnames for each domain (or sub-domain) that you run from a single installation of WordPress. For more information about configuring Static CDN Filters on a WordPress Multisite Network, see this tutorial: <a href="http://cometcache.com/r/static-cdn-filters-for-wordpress-multisite-networks/" target="_blank">Static CDN Filters for WordPress Multisite Networks</a>.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            echo '         <p style="margin-bottom:0;"><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][cdn_hosts]" rows="5" spellcheck="false" autocomplete="off" placeholder="'.esc_attr('e.g., '.$this->plugin->hostToken(false, true).' = cdn1.'.$this->plugin->hostToken(false, true).', cdn2.'.$this->plugin->hostToken(false, true).', cdn3.'.$this->plugin->hostToken(false, true)).'" wrap="off" style="white-space:pre;">'.esc_textarea($this->plugin->options['cdn_hosts']).'</textarea></p>'."\n";
            echo '         <p style="margin-top:0;">'.sprintf(__('<strong>↑ Syntax:</strong> This is a line-delimited list of domain mappings. Each line should start with your WordPress domain name (e.g., <code>%1$s</code>), followed by an <code>=</code> sign, followed by a comma-delimited list of CDN Hostnames associated with the domain in that line. If you\'re running a Multisite Network installation of WordPress, you might have multiple configuration lines. Otherwise, you should only need one line to configure multiple CDN Hostnames for a standard WordPress installation.', 'comet-cache'), esc_html($this->plugin->hostToken(false, true))).'</p>'."\n";

            echo '         <hr />'."\n";

            echo '         <h3>'.__('CDN Supports HTTPS Connections?', 'comet-cache').'</h3>'."\n";
            echo '         <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cdn_over_ssl]" autocomplete="off">'."\n";
            echo '                  <option value="0"'.selected($this->plugin->options['cdn_over_ssl'], '0', false).'>'.__('No, I don\'t serve content over https://; or I haven\'t configured my CDN w/ an SSL certificate.', 'comet-cache').'</option>'."\n";
            echo '                  <option value="1"'.selected($this->plugin->options['cdn_over_ssl'], '1', false).'>'.__('Yes, I\'ve configured my CDN w/ an SSL certificate; I need https:// enabled.', 'comet-cache').'</option>'."\n";
            echo '            </select></p>'."\n";

            echo '         <hr />'."\n";

            echo '         <h3 style="margin-bottom:0;">'.
                                '<a href="#" class="dotted" data-toggle-target=".'.esc_attr(GLOBAL_NS.'-static-cdn-filters--more-options').'">'.
                                    '<i class="si si-eye"></i> '.__('Additional Options (For Advanced Users)', 'comet-cache').' <i class="si si-eye"></i>'.
                                '</a>'.
                           '</h3>'."\n";

            echo '         <div class="'.esc_attr(GLOBAL_NS.'-static-cdn-filters--more-options').'" style="'.(!IS_PRO ? '' : 'display:none; ').'margin-top:1em;">'."\n";
            echo '              <p class="info" style="display:block;">'.__('Everything else below is 100% completely optional; i.e., not required to enjoy the benefits of Static CDN Filters.', 'comet-cache').'</p>'."\n";

            echo '              <hr />'."\n";

            echo '              <h3>'.__('Whitelisted File Extensions (Optional; Comma-Delimited)', 'comet-cache').'</h3>'."\n";
            echo '              <p><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][cdn_whitelisted_extensions]" value="'.esc_attr($this->plugin->options['cdn_whitelisted_extensions']).'" /></p>'."\n";
            echo '              <p>'.__('If you leave this empty a default set of extensions are taken from WordPress itself. The default set of whitelisted file extensions includes everything supported by the WordPress media library.', 'comet-cache').(IS_PRO ? ' '.__('This includes the following: <code style="white-space:normal; word-wrap:break-word;">'.esc_html(implode(',', Classes\CdnFilters::defaultWhitelistedExtensions())).'</code>', 'comet-cache') : '').'</p>'."\n";

            echo '              <h3>'.__('Blacklisted File Extensions (Optional; Comma-Delimited)', 'comet-cache').'</h3>'."\n";
            echo '              <p><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][cdn_blacklisted_extensions]" value="'.esc_attr($this->plugin->options['cdn_blacklisted_extensions']).'" /></p>'."\n";
            echo '              <p>'.__('With or without a whitelist, you can force exclusions by explicitly blacklisting certain file extensions of your choosing. Please note, the <code>php</code> extension will never be considered a static resource; i.e., it is automatically blacklisted at all times.', 'comet-cache').'</p>'."\n";

            echo '              <hr />'."\n";

            echo '              <h3>'.__('Whitelisted URI Inclusion Patterns (Optional; One Per Line)', 'comet-cache').'</h3>'."\n";
            echo '              <p><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][cdn_whitelisted_uri_patterns]" rows="5" spellcheck="false" class="monospace">'.format_to_edit($this->plugin->options['cdn_whitelisted_uri_patterns']).'</textarea></p>'."\n";
            echo '              <p class="info" style="display:block;">'.__('<strong>Note:</strong> please remember that your entries here should be formatted as a line-delimited list; e.g., one inclusion pattern per line.', 'comet-cache').'</p>'."\n";
            echo '              <p>'.__('If provided, only local URIs matching one of the patterns you list here will be served from your CDN Hostname. URI patterns are caSe-insensitive. A wildcard <code>*</code> will match zero or more characters in any of your patterns. A caret <code>^</code> symbol will match zero or more characters that are NOT the <code>/</code> character. For instance, <code>*/wp-content/*</code> here would indicate that you only want to filter URLs that lead to files located inside the <code>wp-content</code> directory. Adding an additional line with <code>*/wp-includes/*</code> would filter URLs in the <code>wp-includes</code> directory also. <strong>If you leave this empty</strong>, ALL files matching a static file extension will be served from your CDN; i.e., the default behavior.', 'comet-cache').'</p>'."\n";
            echo '              <p>'.__('Please note that URI patterns are tested against a file\'s path (i.e., a file\'s URI, and NOT its full URL). A URI always starts with a leading <code>/</code>. To clarify, a URI is the portion of the URL which comes after the hostname. For instance, given the following URL: <code>http://example.com/path/to/style.css?ver=3</code>, the URI you are matching against would be: <code>/path/to/style.css?ver=3</code>. To whitelist this URI, you could use a line that contains something like this: <code>/path/to/*.css*</code>', 'comet-cache').'</p>'."\n";

            echo '              <h3>'.__('Blacklisted URI Exclusion Patterns (Optional; One Per Line)', 'comet-cache').'</h3>'."\n";
            echo '              <p><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][cdn_blacklisted_uri_patterns]" rows="5" spellcheck="false" class="monospace">'.format_to_edit($this->plugin->options['cdn_blacklisted_uri_patterns']).'</textarea></p>'."\n";
            echo '              <p>'.__('With or without a whitelist, you can force exclusions by explicitly blacklisting certain URI patterns. URI patterns are caSe-insensitive. A wildcard <code>*</code> will match zero or more characters in any of your patterns. A caret <code>^</code> symbol will match zero or more characters that are NOT the <code>/</code> character. For instance, <code>*/wp-content/*/dynamic.pdf*</code> would exclude a file with the name <code>dynamic.pdf</code> located anywhere inside a sub-directory of <code>wp-content</code>.', 'comet-cache').'</p>'."\n";
            echo '              <p class="info" style="display:block;">'.__('<strong>Note:</strong> please remember that your entries here should be formatted as a line-delimited list; e.g., one exclusion pattern per line.', 'comet-cache').'</p>'."\n";

            echo '              <hr />'."\n";

            echo '              <h3>'.__('Query String Invalidation Variable Name', 'comet-cache').'</h3>'."\n";
            echo '              <p><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][cdn_invalidation_var]" value="'.esc_attr($this->plugin->options['cdn_invalidation_var']).'" /></p>'."\n";
            echo '              <p>'.sprintf(__('Each filtered URL (which then leads to your CDN) will include this query string variable as an easy way to invalidate the CDN cache at any time. Invalidating the CDN cache is simply a matter of changing the global invalidation counter (i.e., the value assigned to this query string variable). %1$s manages invalidations automatically; i.e., %1$s will automatically bump an internal counter each time you upgrade a WordPress component (e.g., a plugin, theme, or WP itself). Or, if you ask %1$s to invalidate the CDN cache (e.g., a manual clearing of the CDN cache); the internal counter is bumped then too. In short, %1$s handles cache invalidations for you reliably. This option simply allows you to customize the query string variable name which makes cache invalidations possible. <strong>Please note, the default value is adequate for most sites. You can change this if you like, but it\'s not necessary.</strong>', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            echo '              <p class="info" style="display:block;">'.sprintf(__('<strong>Tip:</strong> You can also tell %1$s to automatically bump the CDN Invalidation Counter whenever you clear the cache manually. See: <strong>%1$s → Manual Cache Clearing → Clear the CDN Cache Too?</strong>', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            echo '              <p class="info" style="display:block;">'.sprintf(__('<strong>Note:</strong> If you empty this field, it will effectively disable the %1$s invalidation system for Static CDN Filters; i.e., the query string variable will NOT be included if you do not supply a variable name.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            echo '         </div>'."\n";
            echo '      </div>'."\n";
            echo '   </div>'."\n";

            echo '</div>'."\n";
        }
    }
}
