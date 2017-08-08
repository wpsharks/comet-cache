<?php
namespace WebSharks\CometCache\Classes\MenuPage\Options;

use WebSharks\CometCache\Classes;

/**
 * Options section.
 *
 * @since 17xxxx Refactor menu pages.
 */
class GetRequests extends Classes\AbsBase
{
    /**
     * Constructor.
     *
     * @since 17xxxx Refactor menu pages.
     */
    public function __construct()
    {
        parent::__construct(); // Parent constructor.

        echo '<div class="plugin-menu-page-panel'.(!IS_PRO && $this->plugin->isProPreview() ? ' pro-preview' : '').'">'."\n";

        echo '   <a href="#" class="plugin-menu-page-panel-heading" data-additional-pro-features="'.(!IS_PRO && $this->plugin->isProPreview() ? __('additional pro features', 'comet-cache') : '').'">'."\n";
        echo '      <i class="si si-question-circle"></i> '.__('GET Requests', 'comet-cache')."\n";
        echo '   </a>'."\n";

        echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";

        echo '      <i class="si si-question-circle si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
        echo '      <h3>'.__('Caching Enabled for GET (Query String) Requests?', 'comet-cache').'</h3>'."\n";
        echo '      <p>'.__('This should almost always be set to <code>No</code>. UNLESS, you\'re using unfriendly Permalinks; i.e., if all of your URLs contain a query string (like <code>?p=123</code>). In such a case, you should set this option to <code>Yes</code>. However, it\'s better to update your Permalink options and use friendly Permalinks, which also optimizes your site for search engines. Again, if you\'re using friendly Permalinks (recommended) you can leave this at the default value of <code>No</code>.', 'comet-cache').'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][get_requests]">'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['get_requests'], '0', false).'>'.__('No, do NOT cache (or serve a cache file) when a query string is present.', 'comet-cache').'</option>'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['get_requests'], '1', false).'>'.__('Yes, I would like to cache URLs that contain a query string.', 'comet-cache').'</option>'."\n";
        echo '         </select></p>'."\n";
        echo '      <p class="info">'.sprintf(__('<strong>Advanced Tip:</strong> If you are not caching GET requests (recommended), but you <em>do</em> want to allow some special URLs that include query string parameters to be cached, you can add this special parameter to any URL <code>?%2$sAC=1</code>. This tells %1$s that it\'s OK to cache that particular URL, even though it contains query string arguments. If you <em>are</em> caching GET requests and you want to force %1$s to <em>not</em> cache a specific request, you can add this special parameter to any URL <code>?%2$sAC=0</code>.', 'comet-cache'), esc_html(NAME), esc_html(mb_strtolower(SHORT_NAME))).'</p>'."\n";
        echo '      <p style="font-style:italic;">'.__('<strong>Other Request Types:</strong> POST requests (i.e., forms with <code>method=&quot;post&quot;</code>) are always excluded from the cache, which is the way it should be. Any <a href="http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html" target="_blank">POST/PUT/DELETE</a> request should never, ever be cached. CLI and self-serve requests are also excluded from the cache automatically. A CLI request is one that comes from the command line; commonly used by CRON jobs and other automated routines. A self-serve request is an HTTP connection established from your site, to your site. For instance, a WP Cron job, or any other HTTP request that is spawned not by a user, but by the server itself.', 'comet-cache').'</p>'."\n";

        if (IS_PRO || $this->plugin->isProPreview()) {
            echo '<div>'."\n";
            echo    '<hr />'."\n";
            echo    '<h3 data-pro-version-only="'.(!IS_PRO ? __('pro version only', 'comet-cache') : '').'">'.__('List of GET Variable Names to Ignore', 'comet-cache').'</h3>'."\n";
            echo    '<p>'.__('You can enter one variable name per line. Each of the variable names that you list here will be ignored entirely; i.e., not considered when caching any given page, and not considered when serving any page that is already cached. For example, many sites use Google Analytics and there are <a href="https://cometcache.com/r/google-analytics-variables/" target="_blank" rel="external">GET request variables used by Google Analytics</a>, which are read by client-side JavaScript only. Those GET variables can be ignored altogether when it comes to the cache algorithm — speeding up your site even further.', 'comet-cache').'</p>'."\n";
            echo    '<p><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][ignore_get_request_vars]" rows="5" spellcheck="false" class="monospace">'.format_to_edit($this->plugin->options['ignore_get_request_vars']).'</textarea></p>'."\n";
            echo    '<p style="font-style:italic;">'.__('A wildcard <code>*</code> character can also be used when necessary; e.g., <code>utm_*</code> (where <code>*</code> = 0 or more characters that are NOT a slash <code>/</code>). To learn more about this syntax, please see <a href ="http://cometcache.com/r/watered-down-regex-syntax/" target="_blank">this KB article</a>.', 'comet-cache').'</p>'."\n";
            echo '</div>'."\n";
        }
        echo '   </div>'."\n";

        echo '</div>'."\n";
    }
}
