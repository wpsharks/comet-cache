<?php
namespace WebSharks\CometCache\Classes\MenuPage\Options;

use WebSharks\CometCache\Classes;

/**
 * Options section.
 *
 * @since 17xxxx Refactor menu pages.
 */
class ProUpdater extends Classes\AbsBase
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
            echo '<div class="plugin-menu-page-panel" id="'.esc_attr(SLUG_TD.'-configure-pro-updater').'">'."\n";

            echo '   <a href="#" class="plugin-menu-page-panel-heading'.(!empty($_REQUEST[GLOBAL_NS.'_configure_pro_updater']) ? ' open' : '').'" data-pro-version-only="'.(!IS_PRO ? __('pro version only', 'comet-cache') : '').'">'."\n";
            echo '      <i class="si si-sign-in"></i> '.__('Update Credentials', 'comet-cache')."\n";
            echo '   </a>'."\n";

            echo '   <div class="plugin-menu-page-panel-body'.(!empty($_REQUEST[GLOBAL_NS.'_configure_pro_updater']) ? ' open' : '').' clearfix">'."\n";

            echo '      <i class="si si-user si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";

            echo '      <h3>'.__('Authentication for Automatic Updates', 'comet-cache').'</h3>'."\n";
            echo '      <p>'.sprintf(__('%1$s Pro is a premium product available for purchase @ <a href="http://cometcache.com/prices/" target="_blank">cometcache.com</a>. In order to connect with our update servers, you must supply your License Key. Your License Key is located under "My Account" when you log in @ <a href="http://cometcache.com/" target="_blank">cometcache.com</a>. This will authenticate your copy of %1$s Pro; providing you with access to the latest version. You only need to enter these credentials once. %1$s Pro will save them in your WordPress database; making future upgrades even easier. <i class="si si-smile-o"></i> If you prefer to upgrade manually, see <a href="https://cometcache.com/r/kb-article-how-to-manually-upgrade-comet-cache-pro/">this article</a>.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            echo '      <hr />'."\n";

            echo '      <h3>'.sprintf(__('Username', 'comet-cache'), esc_html(NAME)).'</h3>'."\n";
            echo '      <p><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][pro_update_username]" value="'.esc_attr($this->plugin->options['pro_update_username']).'" autocomplete="new-password" /></p>'."\n";
            echo '      <h3>'.sprintf(__('License Key', 'comet-cache'), esc_html(NAME)).'</h3>'."\n";
            echo '      <p><input type="password" name="'.esc_attr(GLOBAL_NS).'[saveOptions][pro_update_password]" value="'.esc_attr($this->plugin->options['pro_update_password']).'" autocomplete="new-password" /></p>'."\n";

            if ((!defined('DISALLOW_FILE_MODS') || !DISALLOW_FILE_MODS) && !apply_filters('automatic_updater_disabled', defined('AUTOMATIC_UPDATER_DISABLED') && AUTOMATIC_UPDATER_DISABLED)) {
                // See: <http://jas.xyz/2gFcnPd> and note that `automatic_updater_disabled` is a core filter.
                echo '      <hr />'."\n";
                echo '      <h3>'.__('Automatic Background Updates', 'comet-cache').'</h3>'."\n";
                echo '      <p>'.sprintf(__('Enable this if you\'d like %1$s to download and install bug fixes and security updates automatically in the background. Requires a valid license key in the field above.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
                echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][pro_auto_update_enable]" autocomplete="off">'."\n";
                echo '            <option value="0"'.selected($this->plugin->options['pro_auto_update_enable'], '0', false).'>'.sprintf(__('No, I\'ll wait for an update notification in my dashboard.', 'comet-cache'), esc_html(NAME)).'</option>'."\n";
                echo '            <option value="1"'.selected($this->plugin->options['pro_auto_update_enable'], '1', false).'>'.sprintf(__('Yes, enable automatic background updates.', 'comet-cache'), esc_html(NAME)).'</option>'."\n";
                echo '         </select></p>'."\n";
            }
            echo '      <hr />'."\n";
            echo '      <h3>'.__('Beta Program', 'comet-cache').'</h3>'."\n";
            echo '      <p>'.sprintf(__('If you would like to participate in our beta program and receive new features and bug fixes before they are released to the public, %1$s can include Release Candidates when checking for automatic updates. Release Candidates are almost-ready-for-production and have already been through many internal test runs. Our team runs the latest Release Candidate on all of our production sites, but that doesn\'t mean you\'ll want to do the same. :-) Please report any issues with Release Candidates on <a href="https://github.com/websharks/comet-cache/issues/" target="_blank">GitHub</a>.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][pro_update_check_stable]" autocomplete="off">'."\n";
            echo '            <option value="1"'.selected($this->plugin->options['pro_update_check_stable'], '1', false).'>'.sprintf(__('No, do not check for Release Candidates; I only want public releases.', 'comet-cache'), esc_html(NAME)).'</option>'."\n";
            echo '            <option value="0"'.selected($this->plugin->options['pro_update_check_stable'], '0', false).'>'.sprintf(__('Yes, check for Release Candidates; I want to help with testing.', 'comet-cache'), esc_html(NAME)).'</option>'."\n";
            echo '         </select></p>'."\n";
            echo '         <p class="info" style="display:block;">'.__('<strong>How will I know that I\'m running a Release Candidate?</strong><br />If you\'re running a Release Candidate, the version ends with <code>-RC</code>, e.g., Comet Cache™ Pro v151201-RC.', 'comet-cache').'</p>'."\n";
            echo '         <p class="info" style="display:block;">'.__('<strong>Email Alternative:</strong> Instead, if you\'d just like to receive updates about Release Candidates (via email), including a Release Candidate changelog, please sign up for the <a href="http://cometcache.com/r/comet-cache-beta-testers-list/" target="_blank">beta testers mailing list</a>.', 'comet-cache').'</p>'."\n";

            echo '   </div>'."\n";

            echo '</div>'."\n";
        }
    }
}
