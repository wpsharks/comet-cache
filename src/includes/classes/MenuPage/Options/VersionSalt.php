<?php
namespace WebSharks\CometCache\Classes\MenuPage\Options;

use WebSharks\CometCache\Classes;

/**
 * Options section.
 *
 * @since 17xxxx Refactor menu pages.
 */
class VersionSalt extends Classes\AbsBase
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
            echo '      <i class="si si-octi-versions"></i> '.__('Dynamic Version Salt', 'comet-cache')."\n";
            echo '   </a>'."\n";

            echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
            echo '      <img src="'.esc_attr($this->plugin->url('/src/client-s/images/salt.png')).'" class="screenshot" />'."\n";
            echo '      <h3>'.__('<i class="si si-flask"></i> <span style="display:inline-block; padding:5px; border-radius:3px; background:#FFFFFF; color:#354913;"><span style="font-weight:bold; font-size:80%;">GEEK ALERT</span></span> This is for VERY advanced users only...', 'comet-cache').'</h3>'."\n";
            echo '      <p>'.sprintf(__('<em>Note: Understanding the %1$s <a href="http://cometcache.com/r/kb-branched-cache-structure/" target="_blank">Branched Cache Structure</a> is a prerequisite to understanding how Dynamic Version Salts are added to the mix.</em>', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            echo '      <p>'.__('A Version Salt gives you the ability to dynamically create multiple variations of the cache, and those dynamic variations will be served on subsequent visits; e.g., if a visitor has a specific cookie (of a certain value) they will see pages which were cached with that version (i.e., w/ that Version Salt: the value of the cookie). A Version Salt can really be anything.', 'comet-cache').'</p>'."\n";
            echo '      <p>'.__('A Version Salt can be a single variable like <code>$_COOKIE[\'my_cookie\']</code>, or it can be a combination of multiple variables, like <code>$_COOKIE[\'my_cookie\'].$_COOKIE[\'my_other_cookie\']</code>. (When using multiple variables, please separate them with a dot, as shown in the example.)', 'comet-cache').'</p>'."\n";
            echo '      <p>'.__('Experts could even use PHP ternary expressions that evaluate into something. For example: <code>((preg_match(\'/iPhone/i\', $_SERVER[\'HTTP_USER_AGENT\'])) ? \'iPhones\' : \'\')</code>. This would force a separate version of the cache to be created for iPhones (e.g., <code>PROTOCOL.HOST.URI[...]v/iPhones.html</code>).', 'comet-cache').'</p>'."\n";
            echo '      <p>'.__('For more documentation, please see <a href="http://cometcache.com/r/kb-dynamic-version-salts/" target="_blank">Dynamic Version Salts</a>.', 'comet-cache').'</p>'."\n";
            echo '      <hr />'."\n";
            echo '      <h3>'.sprintf(__('Create a Dynamic Version Salt For %1$s? &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-size:90%%; opacity:0.5;">150%% OPTIONAL</span>', 'comet-cache'), esc_html(NAME)).'</h3>'."\n";
            echo '      <table style="width:100%;"><tr><td style="width:1px; font-weight:bold; white-space:pre;">PROTOCOL.HOST.URI.v.</td><td><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][version_salt]" value="'.esc_attr($this->plugin->options['version_salt']).'" class="monospace" placeholder="$_COOKIE[\'my_cookie\']" /></td><td style="width:1px; font-weight:bold; white-space:pre;"></td></tr></table>'."\n";
            echo '      <p class="info" style="display:block;">'.__('<a href="http://php.net/manual/en/language.variables.superglobals.php" target="_blank">Super Globals</a> work here; <a href="http://codex.wordpress.org/Editing_wp-config.php#table_prefix" target="_blank"><code>$GLOBALS[\'table_prefix\']</code></a> is a popular one.<br />Or, perhaps a PHP Constant defined in <code>/wp-config.php</code>; such as <code>WPLANG</code> or <code>DB_HOST</code>.', 'comet-cache').'</p>'."\n";
            echo '      <p class="notice" style="display:block;">'.__('<strong>Important:</strong> your Version Salt is scanned for PHP syntax errors via <a href="http://phpcodechecker.com/" target="_blank"><code>phpCodeChecker.com</code></a>. If errors are found, you\'ll receive a notice in the Dashboard.', 'comet-cache').'</p>'."\n";
            echo '      <p class="info" style="display:block;">'.__('If you\'ve enabled a separate cache for each user (optional) that\'s perfectly OK. A Version Salt works with user caching too.', 'comet-cache').'</p>'."\n";
            echo '   </div>'."\n";

            echo '</div>'."\n";
        }
    }
}
