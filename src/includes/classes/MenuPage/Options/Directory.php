<?php
namespace WebSharks\CometCache\Classes\MenuPage\Options;

use WebSharks\CometCache\Classes;

/**
 * Options section.
 *
 * @since 17xxxx Refactor menu pages.
 */
class Directory extends Classes\AbsBase
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
        echo '      <i class="si si-folder-open"></i> '.__('Cache Directory', 'comet-cache')."\n";
        echo '   </a>'."\n";

        echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
        echo '      <h3>'.__('Base Cache Directory (Must be Writable; i.e., <a href="http://cometcache.com/r/wp-file-permissions/" target="_blank">Permissions</a> <code>755</code> or Higher)', 'comet-cache').'</h3>'."\n";
        echo '      <p>'.sprintf(__('This is where %1$s will store the cached version of your site. If you\'re not sure how to deal with directory permissions, don\'t worry too much about this. If there is a problem, %1$s will let you know about it. By default, this directory is created by %1$s and the permissions are setup automatically. In most cases there is nothing more you need to do.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
        echo '      <table style="width:100%;"><tr><td style="width:1px; font-weight:bold; white-space:pre;">'.esc_html(WP_CONTENT_DIR).'/</td><td><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][base_dir]" value="'.esc_attr($this->plugin->options['base_dir']).'" /></td><td style="width:1px; font-weight:bold; white-space:pre;">/</td></tr></table>'."\n";
        echo '   </div>'."\n";

        echo '</div>'."\n";
    }
}
