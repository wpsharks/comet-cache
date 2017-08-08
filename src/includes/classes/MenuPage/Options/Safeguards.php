<?php
namespace WebSharks\CometCache\Classes\MenuPage\Options;

use WebSharks\CometCache\Classes;

/**
 * Options section.
 *
 * @since 17xxxx Refactor menu pages.
 */
class Safeguards extends Classes\AbsBase
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
        echo '      <i class="si si-shield"></i> '.__('Plugin Deletion Safeguards', 'comet-cache')."\n";
        echo '   </a>'."\n";

        echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
        echo '      <i class="si si-shield si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
        echo '      <h3>'.__('Uninstall on Plugin Deletion; or Safeguard Options?', 'comet-cache').'</h3>'."\n";
        echo '      <p>'.sprintf(__('<strong>Tip:</strong> By default, if you delete %1$s using the plugins menu in WordPress, nothing is lost. However, if you want to completely uninstall %1$s you should set this to <code>Yes</code> and <strong>THEN</strong> deactivate &amp; delete %1$s from the plugins menu in WordPress. This way %1$s will erase your options for the plugin, erase directories/files created by the plugin, remove the <code>advanced-cache.php</code> file, terminate CRON jobs, etc. It erases itself from existence completely.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][uninstall_on_deletion]">'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['uninstall_on_deletion'], '0', false).'>'.__('Safeguard my options and the cache (recommended).', 'comet-cache').'</option>'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['uninstall_on_deletion'], '1', false).'>'.sprintf(__('Yes, uninstall (completely erase) %1$s on plugin deletion.', 'comet-cache'), esc_html(NAME)).'</option>'."\n";
        echo '         </select></p>'."\n";
        echo '   </div>'."\n";

        echo '</div>'."\n";
    }
}
