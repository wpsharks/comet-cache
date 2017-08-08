<?php
namespace WebSharks\CometCache\Classes\MenuPage\Options;

use WebSharks\CometCache\Classes;

/**
 * Options section.
 *
 * @since 17xxxx Refactor menu pages.
 */
class Developers extends Classes\AbsBase
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
        echo '      <i class="si si-octi-plug"></i> '.__('Theme/Plugin Developers', 'comet-cache')."\n";
        echo '   </a>'."\n";

        echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
        echo '      <i class="si si-puzzle-piece si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
        echo '      <h3>'.__('Developing a Theme or Plugin for WordPress?', 'comet-cache').'</h3>'."\n";
        echo '      <p>'.sprintf(__('<strong>Tip:</strong> %1$s can be disabled temporarily. If you\'re a theme/plugin developer, you can set a flag within your PHP code to disable the cache engine at runtime. Perhaps on a specific page, or in a specific scenario. In your PHP script, set: <code>$_SERVER[\'COMET_CACHE_ALLOWED\'] = FALSE;</code> or <code>define(\'COMET_CACHE_ALLOWED\', FALSE)</code>. %1$s is also compatible with: <code>define(\'DONOTCACHEPAGE\', TRUE)</code>. It does\'t matter where or when you define one of these, because %1$s is the last thing to run before script execution ends.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
        echo '      <hr />'."\n";
        echo '      <h3>'.sprintf(__('Writing "Advanced Cache" Plugins Specifically for %1$s', 'comet-cache'), esc_html(NAME)).'</h3>'."\n";
        echo '      <p>'.sprintf(__('Theme/plugin developers can take advantage of the %1$s plugin architecture by creating PHP files inside this special directory: <code>/wp-content/ac-plugins/</code>. There is an <a href="http://cometcache.com/r/ac-plugin-example/" target="_blank">example plugin file @ GitHub</a> (please review it carefully and ask questions). If you develop a plugin for %1$s, please share it with the community by publishing it in the plugins respository at WordPress.org.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p class="info">'.sprintf(__('<strong>Why does %1$s have it\'s own plugin architecture?</strong> WordPress loads the <code>advanced-cache.php</code> drop-in file (for caching purposes) very early-on; before any other plugins or a theme. For this reason, %1$s implements it\'s own watered-down version of functions like <code>add_action()</code>, <code>do_action()</code>, <code>add_filter()</code>, <code>apply_filters()</code>.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
        echo '   </div>'."\n";

        echo '</div>'."\n";
    }
}
