<?php
namespace WebSharks\CometCache\Classes\MenuPage\Options;

use WebSharks\CometCache\Classes;

/**
 * Options section.
 *
 * @since 17xxxx Refactor menu pages.
 */
class Nf404Requests extends Classes\AbsBase
{
    /**
     * Constructor.
     *
     * @since 17xxxx Refactor menu pages.
     */
    public function __construct()
    {
        parent::__construct(); // Parent constructor.

        echo '<div class="plugin-menu-page-panel">'."\n";

        echo '   <a href="#" class="plugin-menu-page-panel-heading">'."\n";
        echo '      <i class="si si-chain-broken"></i> '.__('404 Requests', 'comet-cache')."\n";
        echo '   </a>'."\n";

        echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
        echo '      <i class="si si-question-circle si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
        echo '      <h3>'.__('Caching Enabled for 404 Requests?', 'comet-cache').'</h3>'."\n";
        echo '      <p>'.sprintf(__('When this is set to <code>No</code>, %1$s will ignore all 404 requests and no cache file will be served. While this is fine for most site owners, caching the 404 page on a high-traffic site may further reduce server load. When this is set to <code>Yes</code>, %1$s will cache the 404 page (see <a href="https://codex.wordpress.org/Creating_an_Error_404_Page" target="_blank">Creating an Error 404 Page</a>) and then serve that single cache file to all future 404 requests.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_404_requests]">'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['cache_404_requests'], '0', false).'>'.__('No, do NOT cache (or serve a cache file) for 404 requests.', 'comet-cache').'</option>'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['cache_404_requests'], '1', false).'>'.__('Yes, I would like to cache the 404 page and serve the cached file for 404 requests.', 'comet-cache').'</option>'."\n";
        echo '         </select></p>'."\n";
        echo '      <p class="info">'.sprintf(__('<strong>How does %1$s cache 404 requests?</strong> %1$s will create a special cache file (<code>----404----.html</code>, see Advanced Tip below) for the first 404 request and then <a href="http://www.php.net/manual/en/function.symlink.php" target="_blank">symlink</a> future 404 requests to this special cache file. That way you don\'t end up with lots of 404 cache files that all contain the same thing (the contents of the 404 page). Instead, you\'ll have one 404 cache file and then several symlinks (i.e., references) to that 404 cache file.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p class="info">'.__('<strong>Advanced Tip:</strong> The default 404 cache filename (<code>----404----.html</code>) is designed to minimize the chance of a collision with a cache file for a real page with the same name. However, if you want to override this default and define your own 404 cache filename, you can do so by adding <code>define(\'COMET_CACHE_404_CACHE_FILENAME\', \'your-404-cache-filename\');</code> to your <code>wp-config.php</code> file (note that the <code>.html</code> extension should be excluded when defining a new filename).', 'comet-cache').'</p>'."\n";
        echo '   </div>'."\n";

        echo '</div>'."\n";
    }
}
