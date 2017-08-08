<?php
namespace WebSharks\CometCache\Classes\MenuPage\Options;

use WebSharks\CometCache\Classes;

/**
 * Options section.
 *
 * @since 17xxxx Refactor menu pages.
 */
class FeedRequests extends Classes\AbsBase
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
        echo '      <i class="si si-feed"></i> '.__('Feed Caching', 'comet-cache')."\n";
        echo '   </a>'."\n";

        echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
        echo '      <i class="si si-question-circle si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
        echo '      <h3>'.__('Caching Enabled for RSS, RDF, Atom Feeds?', 'comet-cache').'</h3>'."\n";
        echo '      <p>'.__('This should almost ALWAYS be set to <code>No</code>. UNLESS, you\'re sure that you want to cache your feeds. If you use a web feed management provider like Google® Feedburner and you set this option to <code>Yes</code>, you may experience delays in the detection of new posts. <strong>NOTE:</strong> If you do enable this, it is highly recommended that you also enable automatic Feed Clearing too. Please see the section above: "Automatic Cache Clearing". Find the sub-section titled: "Auto-Clear RSS/RDF/ATOM Feeds".', 'comet-cache').'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][feeds_enable]" class="-no-if-enabled">'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['feeds_enable'], '0', false).'>'.__('No, do NOT cache (or serve a cache file) when displaying a feed.', 'comet-cache').'</option>'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['feeds_enable'], '1', false).'>'.__('Yes, I would like to cache feed URLs.', 'comet-cache').'</option>'."\n";
        echo '         </select></p>'."\n";
        echo '      <p class="info">'.__('<strong>Note:</strong> This option affects all feeds served by WordPress, including the site feed, the site comment feed, post-specific comment feeds, author feeds, search feeds, and category and tag feeds. See also: <a href="http://codex.wordpress.org/WordPress_Feeds" target="_blank">WordPress Feeds</a>.', 'comet-cache').'</p>'."\n";
        echo '   </div>'."\n";

        echo '</div>'."\n";
    }
}
