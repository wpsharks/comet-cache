<?php
namespace WebSharks\CometCache\Classes\MenuPage\Options;

use WebSharks\CometCache\Classes;

/**
 * Options section.
 *
 * @since 17xxxx Refactor menu pages.
 */
class Heading extends Classes\AbsBase
{
    /**
     * Constructor.
     *
     * @since 17xxxx Refactor menu pages.
     */
    public function __construct()
    {
        parent::__construct(); // Parent constructor.

        echo '<div class="plugin-menu-page-heading">'."\n";

        if (is_multisite()) {
            echo '<button type="button" class="plugin-menu-page-wipe-cache" style="float:right; margin-left:15px;" title="'.esc_attr(__('Wipe Cache (Start Fresh); clears the cache for all sites in this network at once!', 'comet-cache')).'"'.
                 '  data-action="'.esc_attr(add_query_arg(urlencode_deep(['page' => GLOBAL_NS, '_wpnonce' => wp_create_nonce(), GLOBAL_NS => ['wipeCache' => '1']]), self_admin_url('/admin.php'))).'">'.
                 '  '.__('Wipe', 'comet-cache').' <img src="'.esc_attr($this->plugin->url('/src/client-s/images/wipe.png')).'" style="width:16px; height:16px; display:inline-block;" /></button>'."\n";
        }
        echo '   <button type="button" class="plugin-menu-page-clear-cache" style="float:right;" title="'.esc_attr(__('Clear Cache (Start Fresh)', 'comet-cache').((is_multisite()) ? __('; affects the current site only.', 'comet-cache') : '')).'"'.
             '      data-action="'.esc_attr(add_query_arg(urlencode_deep(['page' => GLOBAL_NS, '_wpnonce' => wp_create_nonce(), GLOBAL_NS => ['clearCache' => '1']]), self_admin_url('/admin.php'))).'">'.
             '      '.__('Clear', 'comet-cache').' <img src="'.esc_attr($this->plugin->url('/src/client-s/images/clear.png')).'" style="width:16px; height:16px; display:inline-block;" /></button>'."\n";

        echo '   <button type="button" class="plugin-menu-page-restore-defaults"'.// Restores default options.
             '      data-confirmation="'.esc_attr(__('Restore default plugin options? You will lose all of your current settings! Are you absolutely sure about this?', 'comet-cache')).'"'.
             '      data-action="'.esc_attr(add_query_arg(urlencode_deep(['page' => GLOBAL_NS, '_wpnonce' => wp_create_nonce(), GLOBAL_NS => ['restoreDefaultOptions' => '1']]), self_admin_url('/admin.php'))).'">'.
             '      '.__('Restore', 'comet-cache').' <i class="si si-ambulance"></i></button>'."\n";

        echo '   <div class="plugin-menu-page-panel-togglers" title="'.esc_attr(__('All Panels', 'comet-cache')).'">'."\n";
        echo '      <button type="button" class="plugin-menu-page-panels-open"><i class="si si-chevron-down"></i></button>'."\n";
        echo '      <button type="button" class="plugin-menu-page-panels-close"><i class="si si-chevron-up"></i></button>'."\n";
        echo '   </div>'."\n";

        echo '   <div class="plugin-menu-page-upsells">'."\n";
        if (IS_PRO && current_user_can($this->plugin->update_cap)) {
            echo '<a href="'.esc_attr('http://cometcache.com/r/comet-cache-subscribe/').'" target="_blank"><i class="si si-envelope"></i> '.__('Newsletter', 'comet-cache').'</a>'."\n";
            echo '<a href="'.esc_attr('http://cometcache.com/r/comet-cache-beta-testers-list/').'" target="_blank"><i class="si si-envelope"></i> '.__('Beta Testers', 'comet-cache').'</a>'."\n";
        }
        if (!IS_PRO) {
            echo '  <a href="'.esc_attr(add_query_arg(urlencode_deep(['page' => GLOBAL_NS, GLOBAL_NS.'_pro_preview' => '1']), self_admin_url('/admin.php'))).'"><i class="si si-eye"></i> '.__('Preview Pro Features', 'comet-cache').'</a>'."\n";
            echo '  <a href="'.esc_attr('http://cometcache.com/prices/').'" target="_blank"><i class="si si-heart-o"></i> '.__('Pro Upgrade', 'comet-cache').'</a>'."\n";
        }
        echo '   </div>'."\n";

        echo '  <div class="plugin-menu-page-support-links">'."\n";
        if (IS_PRO) {
            echo '  <a href="'.esc_attr('http://cometcache.com/support/').'" target="_blank"><i class="si si-life-bouy"></i> '.__('Support', 'comet-cache').'</a>'."\n";
        }
        if (!IS_PRO) {
            echo '  <a href="'.esc_attr('https://cometcache.com/r/community-forum/').'" target="_blank"><i class="si si-comment"></i> '.__('Community Forum', 'comet-cache').'</a>'."\n";
        }
        echo '      <a href="'.esc_attr('http://cometcache.com/kb/').'" target="_blank"><i class="si si-book"></i> '.__('Knowledge Base', 'comet-cache').'</a>'."\n";
        echo '      <a href="'.esc_attr('http://cometcache.com/blog/').'" target="_blank"><i class="si si-rss-square"></i> '.__('Blog', 'comet-cache').'</a>'."\n";
        echo '   </div>'."\n";

        echo '  <div class="plugin-menu-page-mailing-list-links">'."\n";
        if (!IS_PRO) { // We show these above in the Pro version
            echo '      <a href="'.esc_attr('http://cometcache.com/r/comet-cache-subscribe/').'" target="_blank"><i class="si si-envelope"></i> '.__('Newsletter', 'comet-cache').'</a>'."\n";
            echo '      <a href="'.esc_attr('http://cometcache.com/r/comet-cache-beta-testers-list/').'" target="_blank"><i class="si si-envelope"></i> '.__('Beta Testers', 'comet-cache').'</a>'."\n";
        }
        echo '      <a href="'.esc_attr('https://twitter.com/cometcache/').'" target="_blank"><i class="si si-twitter"></i> '.__('Twitter', 'comet-cache').'</a>'."\n";
        echo '      <a href="'.esc_attr('https://www.facebook.com/cometcache/').'" target="_blank"><i class="si si-facebook"></i> '.__('Facebook', 'comet-cache').'</a>'."\n";
        echo '   </div>'."\n";

        if (IS_PRO) {
            echo '<div class="plugin-menu-page-version">'."\n";
            echo    sprintf(__('%1$s&trade; Pro v%2$s', 'comet-cache'), esc_html(NAME), esc_html(VERSION))."\n";
            echo    '(<a href="'.esc_attr('https://cometcache.com/changelog/').'" target="_blank">'.__('changelog', 'comet-cache').'</a>)'."\n";
            echo '</div>'."\n";
        } else { // For the lite version (default behavior).
            echo '<div class="plugin-menu-page-version">'."\n";
            echo    sprintf(__('%1$s&trade; v%2$s', 'comet-cache'), esc_html(NAME), esc_html(VERSION))."\n";
            echo    '(<a href="'.esc_attr('http://cometcache.com/changelog-lite/').'" target="_blank">'.__('changelog', 'comet-cache').'</a>)'."\n";
            echo '</div>'."\n";
        }
        echo '    <img src="'.$this->plugin->url('/src/client-s/images/options-'.(IS_PRO ? 'pro' : 'lite').'.png').'" alt="'.esc_attr(__('Plugin Options', 'comet-cache')).'" />'."\n";

        echo '<div style="clear:both;"></div>'."\n";

        echo '</div>'."\n";

        echo '<hr />'."\n";

        new Notices();
    }
}
