<?php
namespace WebSharks\CometCache\Classes\MenuPage\Options;

use WebSharks\CometCache\Classes;

/**
 * Options section.
 *
 * @since 17xxxx Refactor menu pages.
 */
class ImportExport extends Classes\AbsBase
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
            echo '      <i class="si si-arrow-circle-o-up"></i> '.__('Import/Export Options', 'comet-cache')."\n";
            echo '   </a>'."\n";

            echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
            echo '      <i class="si si-arrow-circle-o-up si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
            echo '      <h3>'.sprintf(__('Import Options from Another %1$s Installation?', 'comet-cache'), esc_html(NAME)).'</h3>'."\n";
            echo '      <p>'.sprintf(__('Upload your <code>%1$s-options.json</code> file and click "Save All Changes" below. The options provided by your import file will override any that exist currently.', 'comet-cache'), GLOBAL_NS).'</p>'."\n";
            echo '      <p><input type="file" name="'.esc_attr(GLOBAL_NS).'[import_options]" /></p>'."\n";
            echo '      <hr />'."\n";
            echo '      <h3>'.sprintf(__('Export Existing Options from this %1$s Installation?', 'comet-cache'), esc_html(NAME)).'</h3>'."\n";
            echo '      <button type="button" class="plugin-menu-page-export-options" style="float:right; margin: 0 0 0 25px;"'.// Exports existing options from this installation.
             '         data-action="'.esc_attr(add_query_arg(urlencode_deep(['page' => GLOBAL_NS, '_wpnonce' => wp_create_nonce(), GLOBAL_NS => ['exportOptions' => '1']]), self_admin_url('/admin.php'))).'">'.
             '         '.__('options.json', 'comet-cache').' <i class="si si-arrow-circle-o-down"></i></button>'."\n";
            echo '      <p>'.sprintf(__('Download your existing options and import them all into another %1$s installation; saves time on future installs.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            echo '   </div>'."\n";

            echo '</div>'."\n";
        }
    }
}
