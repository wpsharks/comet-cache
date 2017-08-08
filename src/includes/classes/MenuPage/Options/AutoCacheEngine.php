<?php
namespace WebSharks\CometCache\Classes\MenuPage\Options;

use WebSharks\CometCache\Classes;

/**
 * Options section.
 *
 * @since 17xxxx Refactor menu pages.
 */
class AutoCacheEngine extends Classes\AbsBase
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
            echo '      <i class="si si-sitemap"></i> '.__('Auto-Cache Engine', 'comet-cache')."\n";
            echo '   </a>'."\n";

            echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
            echo '      <i class="si si-question-circle si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
            echo '      <h3>'.__('Enable the Auto-Cache Engine?', 'comet-cache').'</h3>'."\n";
            echo '      <p>'.sprintf(__('After using %1$s for awhile (or any other page caching plugin, for that matter); it becomes obvious that at some point (based on your configured Expiration Time) %1$s has to refresh itself. It does this by ditching its cached version of a page, reloading the database-driven content, and then recreating the cache with the latest data. This is a never ending regeneration cycle that is based entirely on your configured Expiration Time.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            echo '      <p>'.__('Understanding this, you can see that 99% of your visitors are going to receive a lightning fast response from your server. However, there will always be around 1% of your visitors that land on a page for the very first time (before it\'s been cached), or land on a page that needs to have its cache regenerated, because the existing cache has become outdated. We refer to this as a <em>First-Come Slow-Load Issue</em>. Not a huge problem, but if you\'re optimizing your site for every ounce of speed possible, the Auto-Cache Engine can help with this. The Auto-Cache Engine has been designed to combat this issue by taking on the responsibility of being that first visitor to a page that has not yet been cached, or has an expired cache. The Auto-Cache Engine is powered, in part, by <a href="http://codex.wordpress.org/Category:WP-Cron_Functions" target="_blank">WP-Cron</a> (already built into WordPress). The Auto-Cache Engine runs at 15-minute intervals via WP-Cron. It also uses the <a href="http://core.trac.wordpress.org/browser/trunk/wp-includes/http.php" target="_blank">WP_Http</a> class, which is also built into WordPress already.', 'comet-cache').'</p>'."\n";
            echo '      <p>'.__('The Auto-Cache Engine obtains its list of URLs to auto-cache, from two different sources. It can read an <a href="http://wordpress.org/extend/plugins/google-sitemap-generator/" target="_blank">XML Sitemap</a> and/or a list of specific URLs that you supply. If you supply both sources, it will use both sources collectively. The Auto-Cache Engine takes ALL of your other configuration options into consideration too, including your Expiration Time, as well as any cache exclusion rules.', 'comet-cache').'</p>'."\n";
            echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][auto_cache_enable]" data-target=".-auto-cache-options">'."\n";
            echo '            <option value="0"'.(!IS_PRO ? '' : selected($this->plugin->options['auto_cache_enable'], '0', false)).'>'.__('No, leave the Auto-Cache Engine disabled please.', 'comet-cache').'</option>'."\n";
            echo '            <option value="1"'.(!IS_PRO ? ' selected' : selected($this->plugin->options['auto_cache_enable'], '1', false)).'>'.__('Yes, I want the Auto-Cache Engine to keep pages cached automatically.', 'comet-cache').'</option>'."\n";
            echo '         </select></p>'."\n";

            echo '      <hr />'."\n";

            echo '      <div class="plugin-menu-page-panel-if-enabled -auto-cache-options">'."\n";
            echo '         <h3>'.__('XML Sitemap URL (or an XML Sitemap Index)', 'comet-cache').'</h3>'."\n";
            echo '         <table style="width:100%;"><tr><td style="width:1px; font-weight:bold; white-space:pre;">'.esc_html(home_url('/')).'</td><td><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][auto_cache_sitemap_url]" value="'.esc_attr($this->plugin->options['auto_cache_sitemap_url']).'" /></td></tr></table>'."\n";
            if (is_multisite()) {
                echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][auto_cache_ms_children_too]">'."\n";
                echo '            <option value="0"'.selected($this->plugin->options['auto_cache_ms_children_too'], '0', false).'>'.__('All URLs in this network are in the sitemap for the main site.', 'comet-cache').'</option>'."\n";
                echo '            <option value="1"'.selected($this->plugin->options['auto_cache_ms_children_too'], '1', false).'>'.__('Using the path I\'ve given, look for blog-specific sitemaps in each child blog also.', 'comet-cache').'</option>'."\n";
                echo '         </select></p>'."\n";
                echo '      <p class="info" style="display:block; margin-top:0;">'.sprintf(__('<strong>↑</strong> If enabled here, each child blog can be auto-cached too. %1$s will dynamically change the leading <code>%2$s</code> as necessary; for each child blog in the network. %1$s supports both sub-directory &amp; sub-domain networks, including domain mapping plugins. For more information about how the Auto-Cache Engine caches child blogs, see <a href="http://cometcache.com/r/kb-article-how-does-the-auto-cache-engine-cache-child-blogs-in-a-multisite-network/" target="_blank">this article</a>.', 'comet-cache'), esc_html(NAME), esc_html(home_url('/'))).'</p>'."\n";
            }
            echo '         <hr />'."\n";

            echo '         <h3>'.__('And/Or; a List of URLs to Auto-Cache (One Per Line)', 'comet-cache').'</h3>'."\n";
            echo '         <p><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][auto_cache_other_urls]" rows="5" spellcheck="false" class="monospace">'.format_to_edit($this->plugin->options['auto_cache_other_urls']).'</textarea></p>'."\n";
            echo '         <p class="info" style="display:block; margin-top:-5px;">'.__('<strong>Note:</strong> Wildcards are NOT supported here. If you are going to supply a list of URLs above, each line must contain one full URL for the Auto-Cache Engine to auto-cache. If you have many URLs, we recommend using an <a href="https://en.wikipedia.org/wiki/Sitemaps" target="_blank">XML Sitemap</a>.', 'comet-cache').'</p>'."\n";

            echo '         <hr />'."\n";

            echo '         <h3>'.__('Auto-Cache Delay Timer (in Milliseconds)', 'comet-cache').'</h3>'."\n";
            echo '         <p>'.__('As the Auto-Cache Engine runs through each URL, you can tell it to wait X number of milliseconds between each connection that it makes. It is strongly suggested that you DO have some small delay here. Otherwise, you run the risk of hammering your own web server with multiple repeated connections whenever the Auto-Cache Engine is running. This is especially true on very large sites; where there is the potential for hundreds of repeated connections as the Auto-Cache Engine goes through a long list of URLs. Adding a delay between each connection will prevent the Auto-Cache Engine from placing a heavy load on the processor that powers your web server. A value of <code>500</code> milliseconds is suggested here (half a second). If you experience problems, you can bump this up a little at a time, in increments of <code>500</code> milliseconds; until you find a happy place for your server. <em>Please note that <code>1000</code> milliseconds = <code>1</code> full second.</em>', 'comet-cache').'</p>'."\n";
            echo '         <p><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][auto_cache_delay]" value="'.esc_attr($this->plugin->options['auto_cache_delay']).'" /></p>'."\n";

            echo '         <hr />'."\n";

            echo '         <h3>'.__('Auto-Cache User-Agent String', 'comet-cache').'</h3>'."\n";
            echo '         <table style="width:100%;"><tr><td><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][auto_cache_user_agent]" value="'.esc_attr($this->plugin->options['auto_cache_user_agent']).'" /></td><td style="width:1px; font-weight:bold; white-space:pre;">; '.esc_html(GLOBAL_NS.' '.VERSION).'</td></tr></table>'."\n";
            echo '         <p class="info" style="display:block;">'.__('This is how the Auto-Cache Engine identifies itself when connecting to URLs. See <a href="http://en.wikipedia.org/wiki/User_agent" target="_blank">User Agent</a> in the Wikipedia.', 'comet-cache').'</p>'."\n";
            echo '      </div>'."\n";
            echo '   </div>'."\n";

            echo '</div>'."\n";
        }
    }
}
