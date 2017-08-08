<?php
namespace WebSharks\CometCache\Classes\MenuPage\Options;

use WebSharks\CometCache\Classes;

/**
 * Options section.
 *
 * @since 17xxxx Refactor menu pages.
 */
class Statistics extends Classes\AbsBase
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
            echo '<div class="plugin-menu-page-panel">'."\n";

            echo '   <a href="#" class="plugin-menu-page-panel-heading" data-pro-version-only="'.(!IS_PRO ? __('pro version only', 'comet-cache') : '').'">'."\n";
            echo '      <i class="si si-pie-chart"></i> '.__('Cache-Related Statistics', 'comet-cache')."\n";
            echo '   </a>'."\n";

            echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
            if ($this->plugin->isProPreview()) {
                echo '      <img src="'.esc_attr($this->plugin->url('/src/client-s/images/stats-preview.png')).'" style="width:100%;border: 1px dashed #cac9c9;margin-bottom: 20px;">';
            }
            echo '      <i class="si si-pie-chart si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
            echo '      <h3>'.__('Enable Cache-Related Stats &amp; Charts?', 'comet-cache').'</h3>'."\n";
            echo '      <p>'.sprintf(__('%1$s can collect and display cache-related statistics (including charts). Stats are displayed in the WordPress Admin Bar, and also in your Dashboard under: <strong>%1$s → Stats/Charts</strong>. Cache-related stats provide you with a quick look at what\'s happening behind-the-scenes. Your site grows faster and faster as the cache grows larger in size.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][stats_enable]" data-target=".-stats-admin-bar-enable">'."\n";
            echo '            <option value="1"'.selected($this->plugin->options['stats_enable'], '1', false).'>'.__('Yes, enable stats collection &amp; the menu page in WordPress for viewing stats.', 'comet-cache').'</option>'."\n";
            echo '            <option value="0"'.selected($this->plugin->options['stats_enable'], '0', false).'>'.__('No, I have a VERY large site and I want to avoid any unnecessary directory scans.', 'comet-cache').'</option>'."\n";
            echo '         </select></p>'."\n";
            echo '      <p class="info">'.sprintf(__('<strong>Note:</strong> %1$s does a great job of collecting stats, in ways that don\'t cause a performance issue. In addition, as your cache grows larger than several hundred files in total size, statistics are collected less often and at longer intervals. All of that being said, if you run a VERY large site (e.g., more than 20K posts), you might want to disable stats collection in favor of blazing fast speeds not impeded by any directory scans needed to collect stats.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            echo '      <hr />'."\n";

            echo '      <div class="plugin-menu-page-panel-if-enabled -stats-admin-bar-enable">'."\n";
            echo '          <h3>'.__('Show Stats in the WordPress Admin Bar?', 'comet-cache').'</h3>'."\n";
            echo '          <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][stats_admin_bar_enable]" data-target=".-stats-admin-bar-roles-caps">'."\n";
            echo '              <option value="1"'.selected($this->plugin->options['stats_admin_bar_enable'], '1', false).'>'.__('Yes, enable stats in the WordPress admin bar.', 'comet-cache').'</option>'."\n";
            echo '              <option value="0"'.selected($this->plugin->options['stats_admin_bar_enable'], '0', false).'>'.__('No, I\'ll review stats from the menu page in WordPress if I need to.', 'comet-cache').'</option>'."\n";
            echo '          </select></p>'."\n";
            if (is_multisite()) {
                echo '      <div class="plugin-menu-page-panel-if-enabled -stats-admin-bar-roles-caps">'."\n";
                echo '          <h4 style="margin-bottom:0;">'.__('Allow Child Sites in a Network to See Stats in Admin Bar?', 'comet-cache').'</h4>'."\n";
                echo '          <p style="margin-top:2px;">'.sprintf(__('In a Multisite Network, each child site has stats of its own. If you want child sites to see cache-related stats in their WordPress Admin Bar, you can specify a comma-delimited list of <a href="http://cometcache.com/r/wp-roles-caps/" target="_blank">Roles and/or Capabilities</a> that are allowed to see stats. For example, if I want the Administrator to see stats in their Admin Bar, I could enter <code>administrator</code> here. If I also want to show stats to Editors, I can use a comma-delimited list: <code>administrator,editor</code>. Or, I could use a single Capability of: <code>edit_others_posts</code>; which covers both Administrators &amp; Editors at the same time.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
                echo '          <p style="margin-bottom:0;"><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][stats_admin_bar_roles_caps]" value="'.esc_attr($this->plugin->options['stats_admin_bar_roles_caps']).'" /></p>'."\n";
                echo '          <p style="margin-top:0;">'.sprintf(__('<strong>Note:</strong> As a security measure, in addition to the Role(s) and/or Capabilities that you list here, each child site owner must also have the ability to <code>%1$s</code>.', 'comet-cache'), esc_html(IS_PRO ? $this->plugin->stats_min_cap : 'edit_posts')).'</p>'."\n";
                echo '      </div>'."\n";
            } else {
                echo '      <div class="plugin-menu-page-panel-if-enabled -stats-admin-bar-roles-caps">'."\n";
                echo '          <h4 style="margin-bottom:0;">'.__('Allow Others to See Stats in Admin Bar?', 'comet-cache').'</h4>'."\n";
                echo '          <p style="margin-top:2px;">'.sprintf(__('If you want others to see cache-related stats in their WordPress Admin Bar, you can specify a comma-delimited list of <a href="http://cometcache.com/r/wp-roles-caps/" target="_blank">Roles and/or Capabilities</a> that are allowed to see stats. For example, if I want Editors to see stats in their Admin Bar, I could enter <code>editor</code> here. If I also want to show stats to Authors, I can use a comma-delimited list: <code>editor,author</code>. Or, I could use a single Capability of: <code>publish_posts</code>; which covers both Editors &amp; Authors at the same time.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
                echo '          <p style="margin-bottom:0;"><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][stats_admin_bar_roles_caps]" value="'.esc_attr($this->plugin->options['stats_admin_bar_roles_caps']).'" /></p>'."\n";
                echo '          <p style="margin-top:0;">'.sprintf(__('<strong>Note:</strong> As a security measure, in addition to the Role(s) and/or Capabilities that you list here, each user must also have the ability to <code>%1$s</code>.', 'comet-cache'), esc_html(IS_PRO ? $this->plugin->stats_min_cap : 'edit_posts')).'</p>'."\n";
                echo '      </div>'."\n";
            }
            echo '      </div>'."\n";
            echo '   </div>'."\n";

            echo '</div>'."\n";
        }
    }
}
