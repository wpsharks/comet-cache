<?php
namespace WebSharks\CometCache\Classes\MenuPage\Options;

use WebSharks\CometCache\Classes;

/**
 * Options section.
 *
 * @since 17xxxx Refactor menu pages.
 */
class Expiration extends Classes\AbsBase
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
        echo '      <i class="si si-clock-o"></i> '.__('Cache Expiration Time', 'comet-cache')."\n";
        echo '   </a>'."\n";

        echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
        echo '      <i class="si si-clock-o si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
        echo '      <h3>'.__('Automatic Expiration Time (Max Age)', 'comet-cache').'</h3>'."\n";
        echo '      <p>'.__('If you don\'t update your site much, you could set this to <code>6 months</code> and optimize everything even further. The longer the Cache Expiration Time is, the greater your performance gain. Alternatively, the shorter the Expiration Time, the fresher everything will remain on your site. A default value of <code>7 days</code> (recommended); is a good conservative middle-ground.', 'comet-cache').'</p>'."\n";
        echo '      <p>'.sprintf(__('Keep in mind that your Expiration Time is only one part of the big picture. %1$s will also clear the cache automatically as changes are made to the site (i.e., you edit a post, someone comments on a post, you change your theme, you add a new navigation menu item, etc., etc.). Thus, your Expiration Time is really just a fallback; e.g., the maximum amount of time that a cache file could ever possibly live.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p>'.sprintf(__('All of that being said, you could set this to just <code>60 seconds</code> and you would still see huge differences in speed and performance. If you\'re just starting out with %1$s (perhaps a bit nervous about old cache files being served to your visitors); you could set this to something like <code>30 minutes</code> and experiment with it while you build confidence in %1$s. It\'s not necessary to do so, but many site owners have reported this makes them feel like they\'re more-in-control when the cache has a short expiration time. All-in-all, it\'s a matter of preference <i class="si si-smile-o"></i>.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_max_age]" value="'.esc_attr($this->plugin->options['cache_max_age']).'" /></p>'."\n";
        echo '      <p class="info">'.__('<strong>Tip:</strong> the value that you specify here MUST be compatible with PHP\'s <a href="http://php.net/manual/en/function.strtotime.php" target="_blank" style="text-decoration:none;"><code>strtotime()</code></a> function. Examples: <code>30 seconds</code>, <code>2 hours</code>, <code>7 days</code>, <code>6 months</code>, <code>1 year</code>.', 'comet-cache').'</p>'."\n";
        echo '      <p class="info">'.sprintf(__('<strong>Note:</strong> %1$s will never serve a cache file that is older than what you specify here (even if one exists in your cache directory; stale cache files are never used). In addition, a WP Cron job will automatically cleanup your cache directory (once per hour); purging expired cache files periodically. This prevents a HUGE cache from building up over time, creating a potential storage issue.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";

        echo '      <hr />'."\n";

        echo '      <h3>'.__('Cache Cleanup Schedule', 'comet-cache').'</h3>'."\n";
        echo '      <p>'.sprintf(__('If you have an extremely large site and you lower the default Cache Expiration Time of <code>7 days</code>, expired cache files can build up more quickly. By default, %1$s cleans up expired cache files via <a href="http://cometcache.com/r/wp_cron-functions/" target="_blank">WP Cron</a> at an <code>hourly</code> interval, but you can tell %1$s to use a custom Cache Cleanup Schedule below to run the cleanup process more or less frequently, depending on your specific needs.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_cleanup_schedule]">'."\n";
        foreach (wp_get_schedules() as $_wp_cron_schedule_key => $_wp_cron_schedule) {
            echo '       <option value="'.esc_attr($_wp_cron_schedule_key).'"'.selected($this->plugin->options['cache_cleanup_schedule'], $_wp_cron_schedule_key, false).'>'.esc_html($_wp_cron_schedule['display']).'</option>'."\n";
        } // This builds the list of options using WP_Cron schedules configured for this WP installation.
        unset($_wp_cron_schedule_key, $_wp_cron_schedule);
        echo '      </select></p>'."\n";

        if (IS_PRO || $this->plugin->isProPreview()) {
            $_sys_getloadavg_unavailable = ($this->plugin->isProPreview() ? false : !$this->plugin->sysLoadAverages());
            echo '  <div>'."\n";
            echo '      <hr />'."\n";
            echo '      <h3 data-pro-version-only="'.(!IS_PRO ? __('pro version only', 'comet-cache') : '').'" style="'.($_sys_getloadavg_unavailable ? 'opacity: 0.5;' : '').'">'.__('Disable Cache Expiration If Server Load Average is High?', 'comet-cache').'</h3>'."\n";
            echo '      <p style="'.($_sys_getloadavg_unavailable ? 'opacity: 0.5;' : '').'">'.sprintf(__('If you have high traffic at certain times of the day, %1$s can be told to check the current load average via <a href="http://cometcache.com/r/system-load-average-via-php/" target="_blank"><code>sys_getloadavg()</code></a>. If your server\'s load average has been high in the last 15 minutes or so, cache expiration is disabled automatically to help reduce stress on the server; i.e., to avoid generating a new version of the cache while the server is very busy.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            echo '      <p style="'.($_sys_getloadavg_unavailable ? 'opacity: 0.5;' : '').'">'.sprintf(__('To enable this functionality you should first determine what a high load average is for your server. If you log into your machine via SSH you can run the <code>top</code> command to get a feel for what a high load average looks like. Once you know the number, you can enter it in the field below; e.g., <code>1.05</code> might be a high load average for a server with one CPU. See also: <a href="http://cometcache.com/r/understanding-load-average/" target="_blank">Understanding Load Average</a>', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            echo '      <p><input '.($_sys_getloadavg_unavailable ? 'disabled' : '').' type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_max_age_disable_if_load_average_is_gte]" value="'.esc_attr($this->plugin->options['cache_max_age_disable_if_load_average_is_gte']).'" /></p>'."\n";
            if ($_sys_getloadavg_unavailable && mb_stripos(PHP_OS, 'win') === 0) { // See: <http://jas.xyz/1HZsZ9v>
                echo '  <p class="warning">'.__('<strong>Note:</strong> It appears that your server is running Windows. The <code>sys_getloadavg()</code> function has not been implemented in PHP for Windows servers yet.', 'comet-cache').'</p>'."\n";
            } elseif ($_sys_getloadavg_unavailable && mb_stripos(PHP_OS, 'win') !== 0) {
                echo '  <p class="warning">'.__('<strong>Note:</strong> <code>sys_getloadavg()</code> has been disabled by your web hosting company or is not available on your server.', 'comet-cache').'</p>'."\n";
            }
            echo '   </div>'."\n";
        }
        echo '   </div>'."\n";

        echo '</div>'."\n";
    }
}
