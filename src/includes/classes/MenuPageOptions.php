<?php
namespace WebSharks\ZenCache;

/**
 * Options Page.
 *
 * @since 150422 Rewrite.
 */
class MenuPageOptions extends MenuPage
{
    /**
     * Constructor.
     *
     * @since 150422 Rewrite.
     */
    public function __construct()
    {
        parent::__construct(); // Parent constructor.

        global $is_nginx; // WP global for web server checks below.

        echo '<form id="plugin-menu-page" class="plugin-menu-page" method="post" enctype="multipart/form-data"'.
             ' action="'.esc_attr(add_query_arg(urlencode_deep(array('page' => GLOBAL_NS, '_wpnonce' => wp_create_nonce())), self_admin_url('/admin.php'))).'">'."\n";

        /* ----------------------------------------------------------------------------------------- */

        echo '<div class="plugin-menu-page-heading">'."\n";

        if (is_multisite()) {
            echo '<button type="button" class="plugin-menu-page-wipe-cache" style="float:right; margin-left:15px;" title="'.esc_attr(__('Wipe Cache (Start Fresh); clears the cache for all sites in this network at once!', 'zencache')).'"'.
                 '  data-action="'.esc_attr(add_query_arg(urlencode_deep(array('page' => GLOBAL_NS, '_wpnonce' => wp_create_nonce(), GLOBAL_NS => array('wipeCache' => '1'))), self_admin_url('/admin.php'))).'">'.
                 '  '.__('Wipe', 'zencache').' <img src="'.esc_attr($this->plugin->url('/src/client-s/images/wipe.png')).'" style="width:16px; height:16px;" /></button>'."\n";
        }
        echo '   <button type="button" class="plugin-menu-page-clear-cache" style="float:right;" title="'.esc_attr(__('Clear Cache (Start Fresh)', 'zencache').((is_multisite()) ? __('; affects the current site only.', 'zencache') : '')).'"'.
             '      data-action="'.esc_attr(add_query_arg(urlencode_deep(array('page' => GLOBAL_NS, '_wpnonce' => wp_create_nonce(), GLOBAL_NS => array('clearCache' => '1'))), self_admin_url('/admin.php'))).'">'.
             '      '.__('Clear', 'zencache').' <img src="'.esc_attr($this->plugin->url('/src/client-s/images/clear.png')).'" style="width:16px; height:16px;" /></button>'."\n";

        echo '   <button type="button" class="plugin-menu-page-restore-defaults"'.// Restores default options.
             '      data-confirmation="'.esc_attr(__('Restore default plugin options? You will lose all of your current settings! Are you absolutely sure about this?', 'zencache')).'"'.
             '      data-action="'.esc_attr(add_query_arg(urlencode_deep(array('page' => GLOBAL_NS, '_wpnonce' => wp_create_nonce(), GLOBAL_NS => array('restoreDefaultOptions' => '1'))), self_admin_url('/admin.php'))).'">'.
             '      '.__('Restore', 'zencache').' <i class="si si-ambulance"></i></button>'."\n";

        echo '   <div class="plugin-menu-page-panel-togglers" title="'.esc_attr(__('All Panels', 'zencache')).'">'."\n";
        echo '      <button type="button" class="plugin-menu-page-panels-open"><i class="si si-chevron-down"></i></button>'."\n";
        echo '      <button type="button" class="plugin-menu-page-panels-close"><i class="si si-chevron-up"></i></button>'."\n";
        echo '   </div>'."\n";

        echo '   <div class="plugin-menu-page-upsells">'."\n";
        if (IS_PRO && current_user_can($this->plugin->update_cap)) {
            echo '<a href="'.esc_attr(add_query_arg(urlencode_deep(array('page' => GLOBAL_NS.'-pro-updater')), self_admin_url('/admin.php'))).'"><i class="si si-magic"></i> '.__('Pro Updater', 'zencache').'</a>'."\n";
            echo '<a href="'.esc_attr('http://zencache.com/r/zencache-subscribe/').'" target="_blank"><i class="si si-envelope"></i> '.__('Newsletter', 'zencache').'</a>'."\n";
            echo '<a href="'.esc_attr('http://zencache.com/r/zencache-beta-testers-list/').'" target="_blank"><i class="si si-envelope"></i> '.__('Beta Testers', 'zencache').'</a>'."\n";
        }
        if (!IS_PRO) {
            echo '  <a href="'.esc_attr(add_query_arg(urlencode_deep(array('page' => GLOBAL_NS, GLOBAL_NS.'_pro_preview' => '1')), self_admin_url('/admin.php'))).'"><i class="si si-eye"></i> '.__('Preview Pro Features', 'zencache').'</a>'."\n";
            echo '  <a href="'.esc_attr('http://zencache.com/prices/').'" target="_blank"><i class="si si-heart-o"></i> '.__('Pro Upgrade', 'zencache').'</a>'."\n";
        }
        echo '   </div>'."\n";

        echo '  <div class="plugin-menu-page-support-links">'."\n";
        if (IS_PRO) {
            echo '  <a href="'.esc_attr('http://zencache.com/support/').'" target="_blank"><i class="si si-life-bouy"></i> '.__('Support', 'zencache').'</a>'."\n";
        }
        if (!IS_PRO) {
            echo '  <a href="'.esc_attr('https://wordpress.org/support/plugin/zencache').'" target="_blank"><i class="si si-comment"></i> '.__('Community Forum', 'zencache').'</a>'."\n";
        }
        echo '      <a href="'.esc_attr('http://zencache.com/kb/').'" target="_blank"><i class="si si-book"></i> '.__('Knowledge Base', 'zencache').'</a>'."\n";
        echo '      <a href="'.esc_attr('http://zencache.com/blog/').'" target="_blank"><i class="si si-rss-square"></i> '.__('Blog', 'zencache').'</a>'."\n";
        echo '   </div>'."\n";

        if (!IS_PRO) { // We show these with the Pro Updater above in the Pro version
            echo '   <div class="plugin-menu-page-mailing-list-links">'."\n";
            echo '      <a href="'.esc_attr('http://zencache.com/r/zencache-subscribe/').'" target="_blank"><i class="si si-envelope"></i> '.__('Newsletter', 'zencache').'</a>'."\n";
            echo '      <a href="'.esc_attr('http://zencache.com/r/zencache-beta-testers-list/').'" target="_blank"><i class="si si-envelope"></i> '.__('Beta Testers', 'zencache').'</a>'."\n";
            echo '   </div>'."\n";
        }

        if (IS_PRO) {
            echo '<div class="plugin-menu-page-version">'."\n";
            echo '  '.sprintf(__('%1$s&trade; Pro v%2$s', 'zencache'), esc_html(NAME), esc_html(VERSION))."\n";

            if ($this->plugin->options['latest_pro_version'] && version_compare(VERSION, $this->plugin->options['latest_pro_version'], '<')) {
                echo '(<a href="'.esc_attr(add_query_arg(urlencode_deep(array('page' => GLOBAL_NS.'-pro-updater')), self_admin_url('/admin.php'))).'" style="font-weight:bold;">'.__('update available', 'zencache').'</a>)'."\n";
            } else {
                echo '(<a href="'.esc_attr('https://zencache.com/changelog/').'" target="_blank">'.__('changelog', 'zencache').'</a>)'."\n";
            }
            echo '</div>'."\n";
        } else { // For the lite version (default behavior).
            echo '<div class="plugin-menu-page-version">'."\n";
            echo '  '.sprintf(__('%1$s&trade; v%2$s', 'zencache'), esc_html(NAME), esc_html(VERSION))."\n";

            if ($this->plugin->options['latest_lite_version'] && version_compare(VERSION, $this->plugin->options['latest_lite_version'], '<')) {
                echo '(<a href="'.esc_attr(self_admin_url('/plugins.php')).'" style="font-weight:bold;">'.__('update available', 'zencache').'</a>)'."\n";
            } else {
                echo '(<a href="'.esc_attr('http://zencache.com/changelog-lite/').'" target="_blank">'.__('changelog', 'zencache').'</a>)'."\n";
            }
            echo '</div>'."\n";
        }
        echo '   <img src="'.$this->plugin->url('/src/client-s/images/options-'.(IS_PRO ? 'pro' : 'lite').'.png').'" alt="'.esc_attr(__('Plugin Options', 'zencache')).'" />'."\n";
        echo '<div style="clear:both;"></div>'."\n";
        echo '</div>'."\n";

        echo '<div class="plugin-menu-page-notice warning"><p>'.sprintf(__('<strong>Important %1$s Announcement:</strong> %1$s is changing its name to <a href="https://cometcache.com/r/announcing-comet-cache-formerly-zencache/" target="_blank"><strong>Comet Cache</a></strong>! Learn more about this upcoming change <a href="https://cometcache.com/r/announcing-comet-cache-formerly-zencache/" target="_blank">here</a>.', 'zencache'), esc_html(NAME)).'</p></div>'."\n";

        /* ----------------------------------------------------------------------------------------- */

        echo '<hr />'."\n";

        /* ----------------------------------------------------------------------------------------- */

        if (!empty($_REQUEST[GLOBAL_NS.'_updated'])) {
            echo '<div class="plugin-menu-page-notice notice">'."\n";
            echo '   <i class="si si-thumbs-up"></i> '.__('Options updated successfully.', 'zencache')."\n";
            echo '</div>'."\n";
        }
        if (!empty($_REQUEST[GLOBAL_NS.'_restored'])) {
            echo '<div class="plugin-menu-page-notice notice">'."\n";
            echo '   <i class="si si-thumbs-up"></i> '.__('Default options successfully restored.', 'zencache')."\n";
            echo '</div>'."\n";
        }
        if (!empty($_REQUEST[GLOBAL_NS.'_cache_wiped'])) {
            echo '<div class="plugin-menu-page-notice notice">'."\n";
            echo '   <img src="'.esc_attr($this->plugin->url('/src/client-s/images/wipe.png')).'" /> '.__('Cache wiped across all sites; recreation will occur automatically over time.', 'zencache')."\n";
            echo '</div>'."\n";
        }
        if (!empty($_REQUEST[GLOBAL_NS.'_cache_cleared'])) {
            echo '<div class="plugin-menu-page-notice notice">'."\n";
            if (is_multisite() && is_main_site()) {
                echo '<img src="'.esc_attr($this->plugin->url('/src/client-s/images/clear.png')).'" /> '.__('Cache cleared for main site; recreation will occur automatically over time.', 'zencache')."\n";
            } else {
                echo '<img src="'.esc_attr($this->plugin->url('/src/client-s/images/clear.png')).'" /> '.__('Cache cleared for this site; recreation will occur automatically over time.', 'zencache')."\n";
            }
            echo '</div>'."\n";
        }
        if (!empty($_REQUEST[GLOBAL_NS.'_wp_htaccess_add_failure'])) {
            echo '<div class="plugin-menu-page-notice error">'."\n";
            echo '   <i class="si si-thumbs-down"></i> '.sprintf(__('Failed to update your <code>/.htaccess</code> file automatically. Most likely a permissions error. Please make sure it has permissions <code>644</code> or higher (perhaps <code>666</code>). Once you\'ve done this, please try saving the %1$s options again.', 'zencache'), esc_html(NAME))."\n";
            echo '</div>'."\n";
        }
        if (!empty($_REQUEST[GLOBAL_NS.'_wp_htaccess_remove_failure'])) {
            echo '<div class="plugin-menu-page-notice error">'."\n";
            echo '   <i class="si si-thumbs-down"></i> '.sprintf(__('Failed to update your <code>/.htaccess</code> file automatically. Most likely a permissions error. Please make sure it has permissions <code>644</code> or higher (perhaps <code>666</code>). Once you\'ve done this, please try saving the %1$s options again.', 'zencache'), esc_html(NAME))."\n";
            echo '</div>'."\n";
        }
        if (!empty($_REQUEST[GLOBAL_NS.'_wp_htaccess_nginx_notice'])) {
            echo '<div class="plugin-menu-page-notice error">'."\n";
            echo '   <i class="si si-thumbs-down"></i> '.__('It appears that your server is running NGINX and does not support <code>.htaccess</code> rules. Please <a href="http://zencache.com/r/kb-article-recommended-nginx-server-configuration/" target="_new">update your server configuration manually</a>. If you\'ve already updated your NGINX configuration, you can safely <a href="http://zencache.com/r/kb-article-how-do-i-disable-the-nginx-htaccess-notice/" target="_new">ignore this message</a>.', 'zencache')."\n";
            echo '</div>'."\n";
        }
        if (!empty($_REQUEST[GLOBAL_NS.'_wp_config_wp_cache_add_failure'])) {
            echo '<div class="plugin-menu-page-notice error">'."\n";
            echo '   <i class="si si-thumbs-down"></i> '.__('Failed to update your <code>/wp-config.php</code> file automatically. Please add the following line to your <code>/wp-config.php</code> file (right after the opening <code>&lt;?php</code> tag; on it\'s own line). <pre class="code"><code>&lt;?php<br />define(\'WP_CACHE\', TRUE);</code></pre>', 'zencache')."\n";
            echo '</div>'."\n";
        }
        if (!empty($_REQUEST[GLOBAL_NS.'_wp_config_wp_cache_remove_failure'])) {
            echo '<div class="plugin-menu-page-notice error">'."\n";
            echo '   <i class="si si-thumbs-down"></i> '.__('Failed to update your <code>/wp-config.php</code> file automatically. Please remove the following line from your <code>/wp-config.php</code> file, or set <code>WP_CACHE</code> to a <code>FALSE</code> value. <pre class="code"><code>define(\'WP_CACHE\', TRUE);</code></pre>', 'zencache')."\n";
            echo '</div>'."\n";
        }
        if (!empty($_REQUEST[GLOBAL_NS.'_advanced_cache_add_failure'])) {
            echo '<div class="plugin-menu-page-notice error">'."\n";
            if ($_REQUEST[GLOBAL_NS.'_advanced_cache_add_failure'] === 'advanced-cache') {
                echo '<i class="si si-thumbs-down"></i> '.sprintf(__('Failed to update your <code>/wp-content/advanced-cache.php</code> file. Cannot write stat file: <code>%1$s/%2$s-advanced-cache</code>. Please be sure this directory exists (and that it\'s writable): <code>%1$s</code>. Please use directory permissions <code>755</code> or higher (perhaps <code>777</code>). Once you\'ve done this, please try again.', 'zencache'), esc_html($this->plugin->cacheDir()), esc_html(strtolower(SHORT_NAME)))."\n";
            } else {
                echo '<i class="si si-thumbs-down"></i> '.__('Failed to update your <code>/wp-content/advanced-cache.php</code> file. Most likely a permissions error. Please create an empty file here: <code>/wp-content/advanced-cache.php</code> (just an empty PHP file, with nothing in it); give it permissions <code>644</code> or higher (perhaps <code>666</code>). Once you\'ve done this, please try again.', 'zencache')."\n";
            }
            echo '</div>'."\n";
        }
        if (!empty($_REQUEST[GLOBAL_NS.'_advanced_cache_remove_failure'])) {
            echo '<div class="plugin-menu-page-notice error">'."\n";
            echo '   <i class="si si-thumbs-down"></i> '.__('Failed to remove your <code>/wp-content/advanced-cache.php</code> file. Most likely a permissions error. Please delete (or empty the contents of) this file: <code>/wp-content/advanced-cache.php</code>.', 'zencache')."\n";
            echo '</div>'."\n";
        }
        if (!IS_PRO && $this->plugin->isProPreview()) {
            echo '<div class="plugin-menu-page-notice info">'."\n";
            echo '<a href="'.add_query_arg(urlencode_deep(array('page' => GLOBAL_NS)), self_admin_url('/admin.php')).'" class="pull-right" style="margin:0 0 15px 25px; float:right; font-variant:small-caps; text-decoration:none;">'.__('close', 'zencache').' <i class="si si-eye-slash"></i></a>'."\n";
            echo '   <i class="si si-eye"></i> '.sprintf(__('<strong>Pro Features (Preview)</strong> ~ New option panels below. Please explore before <a href="http://zencache.com/prices/" target="_blank">upgrading <i class="si si-heart-o"></i></a>.<br /><small>NOTE: the free version of %1$s (this lite version) is more-than-adequate for most sites. Please upgrade only if you desire advanced features or would like to support the developer.</small>', 'zencache'), esc_html(NAME))."\n";
            echo '</div>'."\n";
        }
        if (!$this->plugin->options['enable']) {
            echo '<div class="plugin-menu-page-notice warning">'."\n";
            echo '   <i class="si si-warning"></i> '.sprintf(__('%1$s is currently disabled; please review options below.', 'zencache'), esc_html(NAME))."\n";
            echo '</div>'."\n";
        }
        /* ----------------------------------------------------------------------------------------- */

        echo '<div class="plugin-menu-page-body">'."\n";

        /* ----------------------------------------------------------------------------------------- */

        echo '<h2 class="plugin-menu-page-section-heading">'.
             '  '.__('Basic Configuration (Required)', 'zencache').
             '  <small><span>'.sprintf(__('Review these basic options and %1$s&trade; will be ready-to-go!', 'zencache'), esc_html(NAME)).'</span></small>'.
             '</h2>';

        /* --------------------------------------------------------------------------------------------------------------------------------------------------------------------------- */

        echo '<div class="plugin-menu-page-panel">'."\n";

        echo '   <a href="#" class="plugin-menu-page-panel-heading'.((!$this->plugin->options['enable']) ? ' open' : '').'">'."\n";
        echo '      <i class="si si-enty-gauge"></i> '.__('Enable/Disable', 'zencache')."\n";
        echo '   </a>'."\n";

        echo '   <div class="plugin-menu-page-panel-body'.((!$this->plugin->options['enable']) ? ' open' : '').' clearfix">'."\n";
        echo '      <img src="'.esc_attr($this->plugin->url('/src/client-s/images/tach.png')).'" style="float:right; width:100px; margin-left:1em;" />'."\n";
        echo '      <p style="float:right; font-size:120%; font-weight:bold;">'.sprintf(__('%1$s&trade; = SPEED<em>!!</em>', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><label class="switch-primary"><input type="radio" name="'.esc_attr(GLOBAL_NS).'[saveOptions][enable]" value="1"'.checked($this->plugin->options['enable'], '1', false).' /> '.sprintf(__('Yes, enable %1$s&trade;', 'zencache'), esc_html(NAME)).' <i class="si si-magic si-flip-horizontal"></i></label> &nbsp;&nbsp;&nbsp; <label><input type="radio" name="'.esc_attr(GLOBAL_NS).'[saveOptions][enable]" value="0"'.checked($this->plugin->options['enable'], '0', false).' /> '.__('No, disable.', 'zencache').'</label></p>'."\n";
        echo '      <p class="info" style="font-family:\'Georgia\', serif; font-size:110%; margin-top:1.5em;">'.sprintf(__('<strong>HUGE Time-Saver:</strong> Approx. 95%% of all WordPress sites running %1$s, simply enable it here; and that\'s it :-) <strong>No further configuration is necessary (really).</strong> All of the other options (down below) are already tuned for the BEST performance on a typical WordPress installation. Simply enable %1$s here and click "Save All Changes". If you get any warnings please follow the instructions given. Otherwise, you\'re good <i class="si si-smile-o"></i>. This plugin is designed to run just fine like it is. Take it for a spin right away; you can always fine-tune things later if you deem necessary.', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '      <hr />'."\n";
        echo '      <img src="'.esc_attr($this->plugin->url('/src/client-s/images/source-code-ss.png')).'" class="screenshot" />'."\n";
        echo '      <h3>'.sprintf(__('How Can I Tell %1$s is Working?', 'zencache'), esc_html(NAME)).'</h3>'."\n";
        echo '      <p>'.sprintf(__('First of all, please make sure that you\'ve enabled %1$s here; then scroll down to the bottom of this page and click "Save All Changes". All of the other options (below) are already pre-configured for typical usage. Feel free to skip them all for now. You can go back through all of these later and fine-tune things the way you like them.', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p>'.sprintf(__('Once %1$s has been enabled, <strong>you\'ll need to log out (and/or clear browser cookies)</strong>. By default, cache files are NOT served to visitors who are logged-in, and that includes you too ;-) Cache files are NOT served to recent comment authors either. If you\'ve commented (or replied to a comment lately); please clear your browser cookies before testing.', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p>'.sprintf(__('<strong>To verify that %1$s is working</strong>, navigate your site like a normal visitor would. Right-click on any page (choose View Source), then scroll to the very bottom of the document. At the bottom, you\'ll find comments that show %1$s stats and information. You should also notice that page-to-page navigation is <i class="si si-flash"></i> <strong>lightning fast</strong> now that %1$s is running; and it gets faster over time!', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][debugging_enable]" class="-no-if-enabled">'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['debugging_enable'], '1', false).'>'.__('Yes, enable notes in the source code so I can see it\'s working (recommended).', 'zencache').'</option>'."\n";
        echo '            <option value="2"'.selected($this->plugin->options['debugging_enable'], '2', false).'>'.__('Yes, enable notes in the source code AND show debugging details (not recommended for production).', 'zencache').'</option>'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['debugging_enable'], '0', false).'>'.__('No, I don\'t want my source code to contain any of these notes.', 'zencache').'</option>'."\n";
        echo '         </select></p>'."\n";
        echo '   </div>'."\n";

        echo '</div>'."\n";

        /* ----------------------------------------------------------------------------------------- */

        echo '<div class="plugin-menu-page-panel">'."\n";

        echo '   <a href="#" class="plugin-menu-page-panel-heading">'."\n";
        echo '      <i class="si si-shield"></i> '.__('Plugin Deletion Safeguards', 'zencache')."\n";
        echo '   </a>'."\n";

        echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
        echo '      <i class="si si-shield si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
        echo '      <h3>'.__('Uninstall on Plugin Deletion; or Safeguard Options?', 'zencache').'</h3>'."\n";
        echo '      <p>'.sprintf(__('<strong>Tip:</strong> By default, if you delete %1$s using the plugins menu in WordPress, nothing is lost. However, if you want to completely uninstall %1$s you should set this to <code>Yes</code> and <strong>THEN</strong> deactivate &amp; delete %1$s from the plugins menu in WordPress. This way %1$s will erase your options for the plugin, erase directories/files created by the plugin, remove the <code>advanced-cache.php</code> file, terminate CRON jobs, etc. It erases itself from existence completely.', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][uninstall_on_deletion]">'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['uninstall_on_deletion'], '0', false).'>'.__('Safeguard my options and the cache (recommended).', 'zencache').'</option>'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['uninstall_on_deletion'], '1', false).'>'.sprintf(__('Yes, uninstall (completely erase) %1$s on plugin deletion.', 'zencache'), esc_html(NAME)).'</option>'."\n";
        echo '         </select></p>'."\n";
        echo '   </div>'."\n";

        echo '</div>'."\n";

        /* ----------------------------------------------------------------------------------------- */

        echo '<h2 class="plugin-menu-page-section-heading">'.
             '  '.__('Advanced Configuration (All Optional)', 'zencache').
             '  <small>'.__('Recommended for advanced site owners only; already pre-configured for most WP installs.', 'zencache').'</small>'.
             '</h2>';

        /* --------------------------------------------------------------------------------------------------------------------------------------------------------------------------- */

        if (IS_PRO || $this->plugin->isProPreview()) {
            echo '<div class="plugin-menu-page-panel'.(!IS_PRO ? ' pro-preview' : '').'">'."\n";

            echo '   <a href="#" class="plugin-menu-page-panel-heading">'."\n";
            echo '      <i class="si si-broom"></i> '.__('Manual Cache Clearing', 'zencache')."\n";
            echo '   </a>'."\n";

            echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
            echo '      <h3>'.__('Clearing the Cache Manually', 'zencache').'</h3>'."\n";
            echo '      <img src="'.esc_attr($this->plugin->url('/src/client-s/images/clear-cache-ops1-ss.png')).'" class="-clear-cache-ops-ss screenshot" />'."\n";
            echo '      <p>'.sprintf(__('Once %1$s is enabled, you will find this new option in your WordPress Admin Bar (screenshot on right). Clicking this button will clear the cache and you can start fresh at anytime (e.g., you can do this manually; and as often as you wish).', 'zencache'), esc_html(NAME)).'</p>'."\n";
            echo '      <p>'.sprintf(__('Depending on the structure of your site, there could be many reasons to clear the cache. However, the most common reasons are related to Post/Page edits or deletions, Category/Tag edits or deletions, and Theme changes. %1$s handles most scenarios all by itself. However, many site owners like to clear the cache manually; for a variety of reasons (just to force a refresh).', 'zencache'), esc_html(NAME)).'</p>'."\n";
            echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_admin_bar_enable]" data-target=".-cache-clear-admin-bar-options, .-cache-clear-admin-bar-roles-caps" style="width:auto;">'."\n";
            echo '          <option value="1"'.selected($this->plugin->options['cache_clear_admin_bar_enable'], '1', false).'>'.__('Yes, enable &quot;Clear Cache&quot; button in admin bar', 'zencache').'</option>'."\n";
            echo '          <option value="0"'.selected($this->plugin->options['cache_clear_admin_bar_enable'], '0', false).'>'.__('No, I don\'t intend to clear the cache manually.', 'zencache').'</option>'."\n";
            echo '         </select>'."\n";
            echo '         <span class="plugin-menu-page-panel-if-enabled -cache-clear-admin-bar-options"><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_admin_bar_options_enable]" class="-no-if-enabled" style="width:auto;">'."\n";
            echo '             <option value="1"'.selected($this->plugin->options['cache_clear_admin_bar_options_enable'], '1', false).'>'.__('w/ dropdown options.', 'zencache').'</option>'."\n";
            echo '             <option value="2"'.selected($this->plugin->options['cache_clear_admin_bar_options_enable'], '2', false).'>'.__('w/ dropdown options in split menu.', 'zencache').'</option>'."\n";
            echo '             <option value="0"'.selected($this->plugin->options['cache_clear_admin_bar_options_enable'], '0', false).'>'.__('w/o dropdown options.', 'zencache').'</option>'."\n";
            echo '         </select></span></p>'."\n";

            if (is_multisite()) {
                echo '  <div class="plugin-menu-page-panel-if-enabled -cache-clear-admin-bar-roles-caps">'."\n";
                echo '      <h4 style="margin-bottom:0;">'.__('Also allow Child Sites in a Network to clear the cache from their Admin Bar?', 'zencache').'</h4>'."\n";
                echo '      <p style="margin-top:2px;">'.sprintf(__('In a Multisite Network, each child site can clear its own cache. If you want child sites to see the "Clear Cache" button in their WordPress Admin Bar, you can specify a comma-delimited list of <a href="http://zencache.com/r/wp-roles-caps/" target="_blank">Roles and/or Capabilities</a> that are allowed. For example, if I want Administrators to be capable of clearing the cache from their Admin Bar, I could enter <code>administrator</code> here. If I also want to allow Editors, I can use a comma-delimited list: <code>administrator,editor</code>. Or, I could use a single Capability of: <code>edit_others_posts</code>; which covers both Administrators &amp; Editors at the same time.', 'zencache'), esc_html(NAME)).'</p>'."\n";
                echo '      <p style="margin-bottom:0;"><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_admin_bar_roles_caps]" value="'.esc_attr($this->plugin->options['cache_clear_admin_bar_roles_caps']).'" /></p>'."\n";
                echo '      <p style="margin-top:0;">'.sprintf(__('<strong>Note:</strong> As a security measure, in addition to the Role(s) and/or Capabilities that you list here, each child site owner must also have the ability to <code>%1$s</code>.', 'zencache'), esc_html(IS_PRO ? $this->plugin->clear_min_cap : 'edit_posts')).'</p>'."\n";
                echo '      </select></p>'."\n";
                echo '  </div>'."\n";
            } else {
                echo '  <div class="plugin-menu-page-panel-if-enabled -cache-clear-admin-bar-roles-caps">'."\n";
                echo '      <h4 style="margin-bottom:0;">'.__('Also allow others to clear the cache from their Admin Bar?', 'zencache').'</h4>'."\n";
                echo '      <p style="margin-top:2px;">'.sprintf(__('If you want others to see the "Clear Cache" button in their WordPress Admin Bar, you can specify a comma-delimited list of <a href="http://zencache.com/r/wp-roles-caps/" target="_blank">Roles and/or Capabilities</a> that are allowed. For example, if I want Editors to be capable of clearing the cache from their Admin Bar, I could enter <code>editor</code> here. If I also want to allow Authors, I can use a comma-delimited list: <code>editor,author</code>. Or, I could use a single Capability of: <code>publish_posts</code>; which covers both Editors &amp; Authors at the same time.', 'zencache'), esc_html(NAME)).'</p>'."\n";
                echo '      <p style="margin-bottom:0;"><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_admin_bar_roles_caps]" value="'.esc_attr($this->plugin->options['cache_clear_admin_bar_roles_caps']).'" /></p>'."\n";
                echo '      <p style="margin-top:0;">'.sprintf(__('<strong>Note:</strong> As a security measure, in addition to the Role(s) and/or Capabilities that you list here, each user must also have the ability to <code>%1$s</code>.', 'zencache'), esc_html(IS_PRO ? $this->plugin->clear_min_cap : 'edit_posts')).'</p>'."\n";
                echo '      </select></p>'."\n";
                echo '  </div>'."\n";
            }
            if ($this->plugin->functionIsPossible('opcache_reset')) {
                echo '  <hr />'."\n";
                echo '  <h3>'.__('Clear the <a href="http://zencache.com/r/php-opcache/" target="_blank">PHP OPcache</a> Too?', 'zencache').'</h3>'."\n";
                echo '  <p>'.sprintf(__('If you clear the cache manually, do you want %1$s to clear the PHP OPcache too? This is not necessary, but if you want a truly clean start, this will clear all PHP files in the server\'s opcode cache also. Note: If you don\'t already know what the PHP OPcache is, it is suggested that you leave this disabled. It really is not necessary. This is just an added feature for advanced users.', 'zencache'), esc_html(NAME)).'</p>'."\n";
                echo '  <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_opcache_enable]" class="-no-if-enabled">'."\n";
                echo '      <option value="0"'.selected($this->plugin->options['cache_clear_opcache_enable'], '0', false).'>'.__('No, I don\'t use the PHP OPcache extension; or, I don\'t want the opcode cache cleared.', 'zencache').'</option>'."\n";
                echo '      <option value="1"'.selected($this->plugin->options['cache_clear_opcache_enable'], '1', false).'>'.__('Yes, if the PHP OPcache extension is enabled, also clear the entire PHP opcode cache.', 'zencache').'</option>'."\n";
                echo '  </select></p>'."\n";
            }
            if ($this->plugin->functionIsPossible('s2clean')) {
                echo '  <hr />'."\n";
                echo '  <h3>'.__('Clear the <a href="http://websharks-inc.com/product/s2clean/" target="_blank">s2Clean</a> Cache Too?', 'zencache').'</h3>'."\n";
                echo '  <p>'.sprintf(__('If the s2Clean theme is installed, and you clear the cache manually, %1$s can clear the s2Clean Markdown cache too (if you\'ve enabled Markdown processing with s2Clean).', 'zencache'), esc_html(NAME)).'</p>'."\n";
                echo '  <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_s2clean_enable]" class="-no-if-enabled">'."\n";
                echo '      <option value="1"'.selected($this->plugin->options['cache_clear_s2clean_enable'], '1', false).'>'.__('Yes, if the s2Clean theme is installed, also clear s2Clean-related caches.', 'zencache').'</option>'."\n";
                echo '      <option value="0"'.selected($this->plugin->options['cache_clear_s2clean_enable'], '0', false).'>'.__('No, I don\'t use s2Clean; or, I don\'t want s2Clean-related caches cleared.', 'zencache').'</option>'."\n";
                echo '  </select></p>'."\n";
            }
            echo '      <hr />'."\n";
            echo '      <h3>'.__('Evaluate Custom PHP Code when Clearing the Cache?', 'zencache').'</h3>'."\n";
            echo '      <p>'.sprintf(__('If you have any custom routines you\'d like to process when the cache is cleared manually, please enter PHP code here. If your PHP code outputs a message, it will be displayed along with any other notes from %1$s itself. This feature is intended for developers, and it may come in handy if you need to clear any system caches not already covered by %1$s configuration options.', 'zencache'), esc_html(NAME)).'</p>'."\n";
            echo '      <p style="margin-bottom:0;"><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_eval_code]" rows="5" spellcheck="false" class="monospace">'.format_to_edit($this->plugin->options['cache_clear_eval_code']).'</textarea></p>'."\n";
            echo '      <p class="info" style="margin-top:0;">'.__('<strong>Example:</strong> <code>&lt;?php apc_clear_cache(); echo \'&lt;p&gt;Also cleared APC cache.&lt;/p&gt;\'; ?&gt;</code>', 'zencache').'</p>'."\n";

            echo '      <hr />'."\n";
            echo '      <h3>'.__('Clear the CDN Cache Too?', 'zencache').'</h3>'."\n";
            echo '      <p>'.sprintf(__('If you clear the cache manually, do you want %1$s to automatically bump the CDN invalidation counter too? i.e., automatically increment the <code>?%2$s=[counter]</code> in all static CDN URLs?', 'zencache'), esc_html(NAME), esc_html($this->plugin->options['cdn_invalidation_var'])).'</p>'."\n";
            echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_cdn_enable]" class="-no-if-enabled">'."\n";
            echo '            <option value="0"'.selected($this->plugin->options['cache_clear_cdn_enable'], '0', false).'>'.__('No, I don\'t use Static CDN Filters; or, I don\'t want the CDN cache cleared.', 'zencache').'</option>'."\n";
            echo '            <option value="1"'.selected($this->plugin->options['cache_clear_cdn_enable'], '1', false).'>'.__('Yes, if Static CDN Filters are enabled, also clear the CDN cache.', 'zencache').'</option>'."\n";
            echo '      </select></p>'."\n";
            echo '   </div>'."\n";

            echo '</div>'."\n";
        }
        /* --------------------------------------------------------------------------------------------------------------------------------------------------------------------------- */

        echo '<div class="plugin-menu-page-panel'.(!IS_PRO && $this->plugin->isProPreview() ? ' pro-preview' : '').'">'."\n";

        echo '   <a href="#" class="plugin-menu-page-panel-heading'.((!IS_PRO && $this->plugin->isProPreview()) ? ' pro-preview-additional-features' : '').'">'."\n";
        echo '      <i class="si si-server"></i> '.__('Automatic Cache Clearing', 'zencache')."\n";
        echo '   </a>'."\n";

        echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";

        echo '      <h3>'.__('Clearing the Cache Automatically', 'zencache').'</h3>'."\n";
        echo '      <img src="'.esc_attr($this->plugin->url('/src/client-s/images/auto-clear-ss.png')).'" class="screenshot" />'."\n";
        echo '      <p>'.sprintf(__('This is built into the %1$s plugin; i.e., this functionality is "always on". If you edit a Post/Page (or delete one), %1$s will automatically clear the cache file(s) associated with that content. This way a new updated version of the cache will be created automatically the next time this content is accessed. Simple updates like this occur each time you make changes in the Dashboard, and %1$s will notify you of these as they occur. %1$s monitors changes to Posts (of any kind, including Pages), Categories, Tags, Links, Themes (even Users), and more.', 'zencache'), esc_html(NAME)).'</p>'."\n";
        if (IS_PRO || $this->plugin->isProPreview()) {
            echo '  <div class="'.(!IS_PRO ? 'pro-preview-feature' : '').'">'."\n";
            echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][change_notifications_enable]" class="-no-if-enabled" style="width:auto;">'."\n";
            echo '          <option value="1"'.selected($this->plugin->options['change_notifications_enable'], '1', false).'>'.sprintf(__('Yes, enable %1$s notifications in the Dashboard when changes are detected &amp; one or more cache files are cleared automatically.', 'zencache'), esc_html(NAME)).'</option>'."\n";
            echo '          <option value="0"'.selected($this->plugin->options['change_notifications_enable'], '0', false).'>'.sprintf(__('No, I don\'t want to know (don\'t really care) what %1$s is doing behind-the-scene.', 'zencache'), esc_html(NAME)).'</option>'."\n";
            echo '      </select></p>'."\n";
            echo '  </div>'."\n";
        }
        echo '      <hr />'."\n";
        echo '      <h3>'.__('Primary Page Options', 'zencache').'</h3>'."\n";

        echo '      <h4 style="margin-bottom:0;">'.__('Auto-Clear Designated "Home Page" Too?', 'zencache').'</h4>'."\n";
        echo '      <p style="margin-top:2px;">'.sprintf(__('On many sites, the Home Page (aka: the Front Page) offers an archive view of all Posts (or even Pages). Therefore, if a single Post/Page is changed in some way; and %1$s clears/resets the cache for a single Post/Page, would you like %1$s to also clear any existing cache files for the "Home Page"?', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_home_page_enable]" class="-no-if-enabled">'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['cache_clear_home_page_enable'], '1', false).'>'.__('Yes, if any single Post/Page is cleared/reset; also clear the "Home Page".', 'zencache').'</option>'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['cache_clear_home_page_enable'], '0', false).'>'.__('No, my Home Page does not provide a list of Posts/Pages; e.g., this is not necessary.', 'zencache').'</option>'."\n";
        echo '         </select></p>'."\n";
        echo '      <h4 style="margin-bottom:0;">'.__('Auto-Clear Designated "Posts Page" Too?', 'zencache').'</h4>'."\n";
        echo '      <p style="margin-top:2px;">'.sprintf(__('On many sites, the Posts Page (aka: the Blog Page) offers an archive view of all Posts (or even Pages). Therefore, if a single Post/Page is changed in some way; and %1$s clears/resets the cache for a single Post/Page, would you like %1$s to also clear any existing cache files for the "Posts Page"?', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_posts_page_enable]" class="-no-if-enabled">'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['cache_clear_posts_page_enable'], '1', false).'>'.__('Yes, if any single Post/Page is cleared/reset; also clear the "Posts Page".', 'zencache').'</option>'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['cache_clear_posts_page_enable'], '0', false).'>'.__('No, I don\'t use a separate Posts Page; e.g., my Home Page IS my Posts Page.', 'zencache').'</option>'."\n";
        echo '         </select></p>'."\n";

        echo '      <hr />'."\n";
        echo '      <h3>'.__('Author, Archive, and Tag/Term Options', 'zencache').'</h3>'."\n";

        echo '      <h4 style="margin-bottom:0;">'.__('Auto-Clear "Author Page" Too?', 'zencache').'</h4>'."\n";
        echo '      <p style="margin-top:2px;">'.sprintf(__('On many sites, each author has a related "Author Page" that offers an archive view of all posts associated with that author. Therefore, if a single Post/Page is changed in some way; and %1$s clears/resets the cache for a single Post/Page, would you like %1$s to also clear any existing cache files for the related "Author Page"?', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_author_page_enable]" class="-no-if-enabled">'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['cache_clear_author_page_enable'], '1', false).'>'.__('Yes, if any single Post/Page is cleared/reset; also clear the "Author Page".', 'zencache').'</option>'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['cache_clear_author_page_enable'], '0', false).'>'.__('No, my site doesn\'t use multiple authors and/or I don\'t have any "Author Page" archive views.', 'zencache').'</option>'."\n";
        echo '         </select></p>'."\n";

        echo '      <h4 style="margin-bottom:0;">'.__('Auto-Clear "Category Archives" Too?', 'zencache').'</h4>'."\n";
        echo '      <p style="margin-top:2px;">'.sprintf(__('On many sites, each post is associated with at least one Category. Each category then has an archive view that contains all the posts within that category. Therefore, if a single Post/Page is changed in some way; and %1$s clears/resets the cache for a single Post/Page, would you like %1$s to also clear any existing cache files for the associated Category archive views?', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_term_category_enable]" class="-no-if-enabled">'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['cache_clear_term_category_enable'], '1', false).'>'.__('Yes, if any single Post/Page is cleared/reset; also clear the associated Category archive views.', 'zencache').'</option>'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['cache_clear_term_category_enable'], '0', false).'>'.__('No, my site doesn\'t use Categories and/or I don\'t have any Category archive views.', 'zencache').'</option>'."\n";
        echo '         </select></p>'."\n";

        echo '      <h4 style="margin-bottom:0;">'.__('Auto-Clear "Tag Archives" Too?', 'zencache').'</h4>'."\n";
        echo '      <p style="margin-top:2px;">'.sprintf(__('On many sites, each post may be associated with at least one Tag. Each tag then has an archive view that contains all the posts assigned that tag. Therefore, if a single Post/Page is changed in some way; and %1$s clears/resets the cache for a single Post/Page, would you like %1$s to also clear any existing cache files for the associated Tag archive views?', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_term_post_tag_enable]" class="-no-if-enabled">'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['cache_clear_term_post_tag_enable'], '1', false).'>'.__('Yes, if any single Post/Page is cleared/reset; also clear the associated Tag archive views.', 'zencache').'</option>'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['cache_clear_term_post_tag_enable'], '0', false).'>'.__('No, my site doesn\'t use Tags and/or I don\'t have any Tag archive views.', 'zencache').'</option>'."\n";
        echo '         </select></p>'."\n";

        echo '      <h4 style="margin-bottom:0;">'.__('Auto-Clear "Custom Term Archives" Too?', 'zencache').'</h4>'."\n";
        echo '      <p style="margin-top:2px;">'.sprintf(__('Most sites do not use any custom Terms so it should be safe to leave this disabled. However, if your site uses custom Terms and they have their own Term archive views, you may want to clear those when the associated post is cleared. Therefore, if a single Post/Page is changed in some way; and %1$s clears/resets the cache for a single Post/Page, would you like %1$s to also clear any existing cache files for the associated Tag archive views?', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_term_other_enable]" class="-no-if-enabled">'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['cache_clear_term_other_enable'], '1', false).'>'.__('Yes, if any single Post/Page is cleared/reset; also clear any associated custom Term archive views.', 'zencache').'</option>'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['cache_clear_term_other_enable'], '0', false).'>'.__('No, my site doesn\'t use any custom Terms and/or I don\'t have any custom Term archive views.', 'zencache').'</option>'."\n";
        echo '         </select></p>'."\n";

        echo '      <h4 style="margin-bottom:0;">'.__('Auto-Clear "Custom Post Type Archives" Too?', 'zencache').'</h4>'."\n";
        echo '      <p style="margin-top:2px;">'.sprintf(__('Most sites do not use any Custom Post Types so it should be safe to disable this option. However, if your site uses Custom Post Types and they have their own Custom Post Type archive views, you may want to clear those when any associated post is cleared. Therefore, if a single Post with a Custom Post Type is changed in some way; and %1$s clears/resets the cache for that post, would you like %1$s to also clear any existing cache files for the associated Custom Post Type archive views?', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_custom_post_type_enable]" class="-no-if-enabled">'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['cache_clear_custom_post_type_enable'], '1', false).'>'.__('Yes, if any single Post with a Custom Post Type is cleared/reset; also clear any associated Custom Post Type archive views.', 'zencache').'</option>'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['cache_clear_custom_post_type_enable'], '0', false).'>'.__('No, my site doesn\'t use any Custom Post Types and/or I don\'t have any Custom Post Type archive views.', 'zencache').'</option>'."\n";
        echo '         </select></p>'."\n";

        echo '      <hr />'."\n";
        echo '      <h3>'.__('Feed-Related Options', 'zencache').'</h3>'."\n";

        echo '      <h4 style="margin-bottom:0;">'.__('Auto-Clear "RSS/RDF/ATOM Feeds" Too?', 'zencache').'</h4>'."\n";
        echo '      <p style="margin-top:2px;">'.sprintf(__('If you enable Feed Caching (below), this can be quite handy. If enabled, when you update a Post/Page, approve a Comment, or make other changes where %1$s can detect that certain types of Feeds should be cleared to keep your site up-to-date, then %1$s will do this for you automatically. For instance, the blog\'s master feed, the blog\'s master comments feed, feeds associated with comments on a Post/Page, term-related feeds (including mixed term-related feeds), author-related feeds, etc. Under various circumstances (i.e., as you work in the Dashboard) these can be cleared automatically to keep your site up-to-date.', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_xml_feeds_enable]" class="-no-if-enabled">'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['cache_clear_xml_feeds_enable'], '1', false).'>'.__('Yes, automatically clear RSS/RDF/ATOM Feeds from the cache when certain changes occur.', 'zencache').'</option>'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['cache_clear_xml_feeds_enable'], '0', false).'>'.__('No, I don\'t have Feed Caching enabled, or I prefer not to automatically clear Feeds.', 'zencache').'</option>'."\n";
        echo '         </select></p>'."\n";

        echo '      <hr />'."\n";
        echo '      <h3>'.__('Sitemap-Related Options', 'zencache').'</h3>'."\n";

        echo '      <h4 style="margin-bottom:0;">'.__('Auto-Clear "XML Sitemaps" Too?', 'zencache').'</h4>'."\n";
        echo '      <p style="margin-top:2px;">'.sprintf(__('If you\'re generating XML Sitemaps with a plugin like <a href="http://wordpress.org/plugins/google-sitemap-generator/" target="_blank">Google XML Sitemaps</a>, you can tell %1$s to automatically clear the cache of any XML Sitemaps whenever it clears a Post/Page. Note: This does NOT clear the XML Sitemap itself of course, only the cache. The point being, to clear the cache and allow changes to a Post/Page to be reflected by a fresh copy of your XML Sitemap; sooner rather than later.', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_xml_sitemaps_enable]" data-target=".-cache-clear-xml-sitemap-patterns">'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['cache_clear_xml_sitemaps_enable'], '1', false).'>'.__('Yes, if any single Post/Page is cleared/reset; also clear the cache for any XML Sitemaps.', 'zencache').'</option>'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['cache_clear_xml_sitemaps_enable'], '0', false).'>'.__('No, my site doesn\'t use any XML Sitemaps and/or I prefer NOT to clear the cache for XML Sitemaps.', 'zencache').'</option>'."\n";
        echo '         </select></p>'."\n";
        echo '      <div class="plugin-menu-page-panel-if-enabled -cache-clear-xml-sitemap-patterns">'."\n";
        echo '          <p>'.__('<strong style="font-size:110%;">XML Sitemap Patterns...</strong> A default value of <code>/sitemap**.xml</code> covers all XML Sitemaps for most installations. However, you may customize this further if you deem necessary. <strong>Please list one pattern per line.</strong> These XML Sitemap Pattern searches are performed against the <a href="https://gist.github.com/jaswsinc/338b6eb03a36c048c26f" target="_blank">REQUEST_URI</a>. A wildcard <code>**</code> character can also be used when necessary; e.g., <code>/sitemap**.xml</code> (where <code>**</code> = 0 or more characters of any kind, including <code>/</code> slashes). Other special characters include: <code>*</code> = 0 or more characters that are NOT a slash <code>/</code>; <code>^</code> = beginning of the string; <code>$</code> = end of the string. To learn more about this syntax, please see <a href="http://zencache.com/r/watered-down-regex-syntax/" target="_blank">this KB article</a>.', 'zencache').'</p>'."\n";
        echo '          <p><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_xml_sitemap_patterns]" rows="5" spellcheck="false" class="monospace">'.format_to_edit($this->plugin->options['cache_clear_xml_sitemap_patterns']).'</textarea></p>'."\n";
        if (is_multisite()) {
            echo '      <p class="info" style="display:block; margin-top:-15px;">'.__('In a Multisite Network, each child blog (whether it be a sub-domain, a sub-directory, or a mapped domain); will automatically change the leading <code>http://[sub.]domain/[sub-directory]</code> used in pattern matching. In short, there is no need to add sub-domains or sub-directories for each child blog in these patterns. Please include only the <a href="https://gist.github.com/jaswsinc/338b6eb03a36c048c26f" target="_blank">REQUEST_URI</a> (i.e., the path) which leads to the XML Sitemap on all child blogs in the network.', 'zencache').'</p>'."\n";
        }
        echo '      </div>'."\n";

        if (IS_PRO || $this->plugin->isProPreview()) {
            echo '      <hr />'."\n";
            echo '      <h3 class="'.(!IS_PRO ? 'pro-preview-feature' : '').'">'.__('Misc. Auto-Clear Options', 'zencache').'</h3>'."\n";
            echo '<h4 style="margin-bottom:0;">'.__('Auto-Clear a List of Custom URLs Too?', 'zencache').'</h4>'."\n";
            echo '<p style="margin-top:2px;">'.sprintf(__('When you update a Post/Page, approve a Comment, or make other changes where %1$s can detect that a Post/Page cache should be cleared to keep your site up-to-date; then %1$s will also clear a list of custom URLs that you list here. <strong>Please list one URL per line.</strong> A wildcard <code>*</code> character can also be used when necessary; e.g., <code>/category/abc-followed-by-*</code> (where <code>*</code> = 0 or more characters that are NOT a slash <code>/</code>). Other special characters include: <code>**</code> = 0 or more characters of any kind, including <code>/</code> slashes; <code>^</code> = beginning of the string; <code>$</code> = end of the string. To learn more about this syntax, please see <a href ="http://zencache.com/r/watered-down-regex-syntax/" target="_blank">this KB article</a>.', 'zencache'), esc_html(NAME)).'</p>'."\n";
            echo '<p><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_urls]" spellcheck="false" wrap="off" rows="5">'.format_to_edit($this->plugin->options['cache_clear_urls']).'</textarea></p>'."\n";
        }
        echo '   </div>'."\n";

        echo '</div>'."\n";

        /* ----------------------------------------------------------------------------------------- */

        if (IS_PRO || $this->plugin->isProPreview()) {
            echo '<div class="plugin-menu-page-panel">'."\n";

            echo '   <a href="#" class="plugin-menu-page-panel-heading'.(!IS_PRO ? ' pro-preview-feature' : '').'">'."\n";
            echo '      <i class="si si-pie-chart"></i> '.__('Cache-Related Statistics', 'zencache')."\n";
            echo '   </a>'."\n";

            echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
            echo '      <i class="si si-pie-chart si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
            echo '      <h3>'.__('Enable Cache-Related Stats &amp; Charts?', 'zencache').'</h3>'."\n";
            echo '      <p>'.sprintf(__('%1$s can collect and display cache-related statistics (including charts). Stats are displayed in the WordPress Admin Bar, and also in your Dashboard under: <strong>%1$s → Stats/Charts</strong>. Cache-related stats provide you with a quick look at what\'s happening behind-the-scenes. Your site grows faster and faster as the cache grows larger in size.', 'zencache'), esc_html(NAME)).'</p>'."\n";
            echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][stats_enable]" data-target=".-stats-admin-bar-enable">'."\n";
            echo '            <option value="1"'.selected($this->plugin->options['stats_enable'], '1', false).'>'.__('Yes, enable stats collection &amp; the menu page in WordPress for viewing stats.', 'zencache').'</option>'."\n";
            echo '            <option value="0"'.selected($this->plugin->options['stats_enable'], '0', false).'>'.__('No, I have a VERY large site and I want to avoid any unnecessary directory scans.', 'zencache').'</option>'."\n";
            echo '         </select></p>'."\n";
            echo '      <p class="info">'.sprintf(__('<strong>Note:</strong> %1$s does a great job of collecting stats, in ways that don\'t cause a performance issue. In addition, as your cache grows larger than several hundred files in total size, statistics are collected less often and at longer intervals. All of that being said, if you run a VERY large site (e.g., more than 20K posts), you might want to disable stats collection in favor of blazing fast speeds not impeded by any directory scans needed to collect stats.', 'zencache'), esc_html(NAME)).'</p>'."\n";
            echo '      <hr />'."\n";

            echo '      <div class="plugin-menu-page-panel-if-enabled -stats-admin-bar-enable">'."\n";
            echo '          <h3>'.__('Show Stats in the WordPress Admin Bar?', 'zencache').'</h3>'."\n";
            echo '          <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][stats_admin_bar_enable]" data-target=".-stats-admin-bar-roles-caps">'."\n";
            echo '              <option value="1"'.selected($this->plugin->options['stats_admin_bar_enable'], '1', false).'>'.__('Yes, enable stats in the WordPress admin bar.', 'zencache').'</option>'."\n";
            echo '              <option value="0"'.selected($this->plugin->options['stats_admin_bar_enable'], '0', false).'>'.__('No, I\'ll review stats from the menu page in WordPress if I need to.', 'zencache').'</option>'."\n";
            echo '          </select></p>'."\n";
            if (is_multisite()) {
                echo '      <div class="plugin-menu-page-panel-if-enabled -stats-admin-bar-roles-caps">'."\n";
                echo '          <h4 style="margin-bottom:0;">'.__('Allow Child Sites in a Network to See Stats in Admin Bar?', 'zencache').'</h4>'."\n";
                echo '          <p style="margin-top:2px;">'.sprintf(__('In a Multisite Network, each child site has stats of its own. If you want child sites to see cache-related stats in their WordPress Admin Bar, you can specify a comma-delimited list of <a href="http://zencache.com/r/wp-roles-caps/" target="_blank">Roles and/or Capabilities</a> that are allowed to see stats. For example, if I want the Administrator to see stats in their Admin Bar, I could enter <code>administrator</code> here. If I also want to show stats to Editors, I can use a comma-delimited list: <code>administrator,editor</code>. Or, I could use a single Capability of: <code>edit_others_posts</code>; which covers both Administrators &amp; Editors at the same time.', 'zencache'), esc_html(NAME)).'</p>'."\n";
                echo '          <p style="margin-bottom:0;"><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][stats_admin_bar_roles_caps]" value="'.esc_attr($this->plugin->options['stats_admin_bar_roles_caps']).'" /></p>'."\n";
                echo '          <p style="margin-top:0;">'.sprintf(__('<strong>Note:</strong> As a security measure, in addition to the Role(s) and/or Capabilities that you list here, each child site owner must also have the ability to <code>%1$s</code>.', 'zencache'), esc_html(IS_PRO ? $this->plugin->stats_min_cap : 'edit_posts')).'</p>'."\n";
                echo '          </select></p>'."\n";
                echo '      </div>'."\n";
            } else {
                echo '      <div class="plugin-menu-page-panel-if-enabled -stats-admin-bar-roles-caps">'."\n";
                echo '          <h4 style="margin-bottom:0;">'.__('Allow Others to See Stats in Admin Bar?', 'zencache').'</h4>'."\n";
                echo '          <p style="margin-top:2px;">'.sprintf(__('If you want others to see cache-related stats in their WordPress Admin Bar, you can specify a comma-delimited list of <a href="http://zencache.com/r/wp-roles-caps/" target="_blank">Roles and/or Capabilities</a> that are allowed to see stats. For example, if I want Editors to see stats in their Admin Bar, I could enter <code>editor</code> here. If I also want to show stats to Authors, I can use a comma-delimited list: <code>editor,author</code>. Or, I could use a single Capability of: <code>publish_posts</code>; which covers both Editors &amp; Authors at the same time.', 'zencache'), esc_html(NAME)).'</p>'."\n";
                echo '          <p style="margin-bottom:0;"><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][stats_admin_bar_roles_caps]" value="'.esc_attr($this->plugin->options['stats_admin_bar_roles_caps']).'" /></p>'."\n";
                echo '          <p style="margin-top:0;">'.sprintf(__('<strong>Note:</strong> As a security measure, in addition to the Role(s) and/or Capabilities that you list here, each user must also have the ability to <code>%1$s</code>.', 'zencache'), esc_html(IS_PRO ? $this->plugin->stats_min_cap : 'edit_posts')).'</p>'."\n";
                echo '          </select></p>'."\n";
                echo '      </div>'."\n";
            }
            echo '      </div>'."\n";
            echo '   </div>'."\n";

            echo '</div>'."\n";
        }
        /* ----------------------------------------------------------------------------------------- */

        echo '<div class="plugin-menu-page-panel">'."\n";

        echo '   <a href="#" class="plugin-menu-page-panel-heading">'."\n";
        echo '      <i class="si si-folder-open"></i> '.__('Cache Directory', 'zencache')."\n";
        echo '   </a>'."\n";

        echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
        echo '      <h3>'.__('Base Cache Directory (Must be Writable; i.e., <a href="http://zencache.com/r/wp-file-permissions/" target="_blank">Permissions</a> <code>755</code> or Higher)', 'zencache').'</h3>'."\n";
        echo '      <p>'.sprintf(__('This is where %1$s will store the cached version of your site. If you\'re not sure how to deal with directory permissions, don\'t worry too much about this. If there is a problem, %1$s will let you know about it. By default, this directory is created by %1$s and the permissions are setup automatically. In most cases there is nothing more you need to do.', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '      <table style="width:100%;"><tr><td style="width:1px; font-weight:bold; white-space:nowrap;">'.esc_html(WP_CONTENT_DIR).'/</td><td><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][base_dir]" value="'.esc_attr($this->plugin->options['base_dir']).'" /></td><td style="width:1px; font-weight:bold; white-space:nowrap;">/</td></tr></table>'."\n";
        echo '   </div>'."\n";

        echo '</div>'."\n";

        /* ----------------------------------------------------------------------------------------- */

        echo '<div class="plugin-menu-page-panel'.(!IS_PRO && $this->plugin->isProPreview() ? ' pro-preview' : '').'">'."\n";

        echo '   <a href="#" class="plugin-menu-page-panel-heading'.((!IS_PRO && $this->plugin->isProPreview()) ? ' pro-preview-additional-features' : '').'">'."\n";
        echo '      <i class="si si-clock-o"></i> '.__('Cache Expiration Time', 'zencache')."\n";
        echo '   </a>'."\n";

        echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
        echo '      <i class="si si-clock-o si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
        echo '      <h3>'.__('Automatic Expiration Time (Max Age)', 'zencache').'</h3>'."\n";
        echo '      <p>'.__('If you don\'t update your site much, you could set this to <code>6 months</code> and optimize everything even further. The longer the Cache Expiration Time is, the greater your performance gain. Alternatively, the shorter the Expiration Time, the fresher everything will remain on your site. A default value of <code>7 days</code> (recommended); is a good conservative middle-ground.', 'zencache').'</p>'."\n";
        echo '      <p>'.sprintf(__('Keep in mind that your Expiration Time is only one part of the big picture. %1$s will also clear the cache automatically as changes are made to the site (i.e., you edit a post, someone comments on a post, you change your theme, you add a new navigation menu item, etc., etc.). Thus, your Expiration Time is really just a fallback; e.g., the maximum amount of time that a cache file could ever possibly live.', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p>'.sprintf(__('All of that being said, you could set this to just <code>60 seconds</code> and you would still see huge differences in speed and performance. If you\'re just starting out with %1$s (perhaps a bit nervous about old cache files being served to your visitors); you could set this to something like <code>30 minutes</code> and experiment with it while you build confidence in %1$s. It\'s not necessary to do so, but many site owners have reported this makes them feel like they\'re more-in-control when the cache has a short expiration time. All-in-all, it\'s a matter of preference <i class="si si-smile-o"></i>.', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_max_age]" value="'.esc_attr($this->plugin->options['cache_max_age']).'" /></p>'."\n";
        echo '      <p class="info">'.__('<strong>Tip:</strong> the value that you specify here MUST be compatible with PHP\'s <a href="http://php.net/manual/en/function.strtotime.php" target="_blank" style="text-decoration:none;"><code>strtotime()</code></a> function. Examples: <code>30 seconds</code>, <code>2 hours</code>, <code>7 days</code>, <code>6 months</code>, <code>1 year</code>.', 'zencache').'</p>'."\n";
        echo '      <p class="info">'.sprintf(__('<strong>Note:</strong> %1$s will never serve a cache file that is older than what you specify here (even if one exists in your cache directory; stale cache files are never used). In addition, a WP Cron job will automatically cleanup your cache directory (once per hour); purging expired cache files periodically. This prevents a HUGE cache from building up over time, creating a potential storage issue.', 'zencache'), esc_html(NAME)).'</p>'."\n";

        echo '      <hr />'."\n";

        echo '      <h3>'.__('Cache Cleanup Schedule', 'zencache').'</h3>'."\n";
        echo '      <p>'.sprintf(__('If you have an extremely large site and you lower the default Cache Expiration Time of <code>7 days</code>, expired cache files can build up more quickly. By default, %1$s cleans up expired cache files via <a href="http://zencache.com/r/wp_cron-functions/" target="_blank">WP Cron</a> at an <code>hourly</code> interval, but you can tell %1$s to use a custom Cache Cleanup Schedule below to run the cleanup process more or less frequently, depending on your specific needs.', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_cleanup_schedule]">'."\n";
        foreach (wp_get_schedules() as $_wp_cron_schedule_key => $_wp_cron_schedule) {
            echo '       <option value="'.esc_attr($_wp_cron_schedule_key).'"'.selected($this->plugin->options['cache_cleanup_schedule'], $_wp_cron_schedule_key, false).'>'.esc_html($_wp_cron_schedule['display']).'</option>'."\n";
        } // This builds the list of options using WP_Cron schedules configured for this WP installation.
        unset($_wp_cron_schedule_key, $_wp_cron_schedule);
        echo '      </select></p>'."\n";

        if (IS_PRO || $this->plugin->isProPreview()) {
            $_sys_getloadavg_unavailable = ($this->plugin->isProPreview() ? false : !$this->plugin->sysLoadAverages());
            echo '  <div>'."\n";
            echo '      <hr />'."\n";
            echo '      <h3 class="'.(!IS_PRO ? 'pro-preview-feature' : '').'" style="'.($_sys_getloadavg_unavailable ? 'opacity: 0.5;' : '').'">'.__('Disable Cache Expiration If Server Load Average is High?', 'zencache').'</h3>'."\n";
            echo '      <p style="'.($_sys_getloadavg_unavailable ? 'opacity: 0.5;' : '').'">'.sprintf(__('If you have high traffic at certain times of the day, %1$s can be told to check the current load average via <a href="http://zencache.com/r/system-load-average-via-php/" target="_blank"><code>sys_getloadavg()</code></a>. If your server\'s load average has been high in the last 15 minutes or so, cache expiration is disabled automatically to help reduce stress on the server; i.e., to avoid generating a new version of the cache while the server is very busy.', 'zencache'), esc_html(NAME)).'</p>'."\n";
            echo '      <p style="'.($_sys_getloadavg_unavailable ? 'opacity: 0.5;' : '').'">'.sprintf(__('To enable this functionality you should first determine what a high load average is for your server. If you log into your machine via SSH you can run the <code>top</code> command to get a feel for what a high load average looks like. Once you know the number, you can enter it in the field below; e.g., <code>1.05</code> might be a high load average for a server with one CPU. See also: <a href="http://zencache.com/r/understanding-load-average/" target="_blank">Understanding Load Average</a>', 'zencache'), esc_html(NAME)).'</p>'."\n";
            echo '      <p><input '.($_sys_getloadavg_unavailable ? 'disabled' : '').' type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_max_age_disable_if_load_average_is_gte]" value="'.esc_attr($this->plugin->options['cache_max_age_disable_if_load_average_is_gte']).'" /></p>'."\n";
            if ($_sys_getloadavg_unavailable && stripos(PHP_OS, 'win') === 0) { // See: <http://jas.xyz/1HZsZ9v>
                echo '  <p class="warning">'.__('<strong>Note:</strong> It appears that your server is running Windows. The <code>sys_getloadavg()</code> function has not been implemented in PHP for Windows servers yet.', 'zencache').'</p>'."\n";
            } elseif ($_sys_getloadavg_unavailable && stripos(PHP_OS, 'win') !== 0) {
                echo '  <p class="warning">'.__('<strong>Note:</strong> <code>sys_getloadavg()</code> has been disabled by your web hosting company or is not available on your server.', 'zencache').'</p>'."\n";
            }
            echo '   </div>'."\n";
        }
        echo '   </div>'."\n";

        echo '</div>'."\n";

        /* ----------------------------------------------------------------------------------------- */

        echo '<div class="plugin-menu-page-panel">'."\n";

        echo '   <a href="#" class="plugin-menu-page-panel-heading">'."\n";
        echo '      <i class="si si-octi-tach"></i> '.__('Client-Side Cache', 'zencache')."\n";
        echo '   </a>'."\n";

        echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
        echo '      <i class="si si-desktop si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
        echo '      <h3>'.__('Allow Double-Caching In The Client-Side Browser?', 'zencache').'</h3>'."\n";
        echo '      <p>'.__('Recommended setting: <code>No</code> (for membership sites, very important). Otherwise, <code>Yes</code> would be better (if users do NOT log in/out of your site).', 'zencache').'</p>'."\n";
        echo '      <p>'.sprintf(__('%1$s handles content delivery through its ability to communicate with a browser using PHP. If you allow a browser to (cache) the caching system itself, you are momentarily losing some control; and this can have a negative impact on users that see more than one version of your site; e.g., one version while logged-in, and another while NOT logged-in. For instance, a user may log out of your site, but upon logging out they report seeing pages on the site which indicate they are STILL logged in (even though they\'re not — that\'s bad). This can happen if you allow a client-side cache, because their browser may cache web pages they visited while logged into your site which persist even after logging out. Sending no-cache headers will work to prevent this issue.', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p>'.__('All of that being said, if all you care about is blazing fast speed and users don\'t log in/out of your site (only you do); you can safely set this to <code>Yes</code> (recommended in this case). Allowing a client-side browser cache will improve speed and reduce outgoing bandwidth when this option is feasible.', 'zencache').'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][allow_browser_cache]">'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['allow_browser_cache'], '0', false).'>'.__('No, prevent a client-side browser cache (safest option).', 'zencache').'</option>'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['allow_browser_cache'], '1', false).'>'.__('Yes, I will allow a client-side browser cache of pages on the site.', 'zencache').'</option>'."\n";
        echo '         </select></p>'."\n";
        echo '      <p class="info">'.__('<strong>Tip:</strong> Setting this to <code>No</code> is highly recommended when running a membership plugin like <a href="http://wordpress.org/plugins/s2member/" target="_blank">s2Member</a> (as one example). In fact, many plugins like s2Member will send <a href="http://codex.wordpress.org/Function_Reference/nocache_headers" target="_blank">nocache_headers()</a> on their own, so your configuration here will likely be overwritten when you run such plugins (which is better anyway). In short, if you run a membership plugin, you should NOT allow a client-side browser cache.', 'zencache').'</p>'."\n";
        echo '      <p class="info">'.__('<strong>Tip:</strong> Setting this to <code>No</code> will NOT impact static content; e.g., CSS, JS, images, or other media. This setting pertains only to dynamic PHP scripts which produce content generated by WordPress.', 'zencache').'</p>'."\n";
        echo '      <p class="info">'.sprintf(__('<strong>Advanced Tip:</strong> if you have this set to <code>No</code>, but you DO want to allow a few special URLs to be cached by the browser; you can add this parameter to your URL <code>?%2$sABC=1</code>. This tells %1$s that it\'s OK for the browser to cache that particular URL. In other words, the <code>%2$sABC=1</code> parameter tells %1$s NOT to send no-cache headers to the browser.', 'zencache'), esc_html(NAME), esc_html(strtolower(SHORT_NAME))).'</p>'."\n";
        echo '      <h3>'.__('Exclusion Patterns for Client-Side Caching', 'zencache').'</h3>'."\n";
        echo '      <p>'.__('When you enable Client-Side Caching above, you may want to prevent certain pages on your site from being cached by a client-side browser. This is where you will enter those if you need to (one per line). Searches are performed against the <a href="https://gist.github.com/jaswsinc/338b6eb03a36c048c26f" target="_blank" style="text-decoration:none;"><code>REQUEST_URI</code></a>; i.e., <code>/path/?query</code> (caSe insensitive). So, don\'t put in full URLs here, just word fragments found in the file path (or query string) is all you need, excluding the http:// and domain name. A wildcard <code>*</code> character can also be used when necessary; e.g., <code>/category/abc-followed-by-*</code> (where <code>*</code> = 0 or more characters that are NOT a slash <code>/</code>). Other special characters include: <code>**</code> = 0 or more characters of any kind, including <code>/</code> slashes; <code>^</code> = beginning of the string; <code>$</code> = end of the string. To learn more about this syntax, please see <a href ="http://zencache.com/r/watered-down-regex-syntax/" target="_blank">this KB article</a>.', 'zencache').'</p>'."\n";
        echo '      <p><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][exclude_client_side_uris]" rows="5" spellcheck="false" class="monospace">'.format_to_edit($this->plugin->options['exclude_client_side_uris']).'</textarea></p>'."\n";
        echo '      <p class="info">'.__('<strong>Tip:</strong> let\'s use this example URL: <code>http://www.example.com/post/example-post-123</code>. To exclude this URL, you would put this line into the field above: <code>/post/example-post-123</code>. Or, you could also just put in a small fragment, like: <code>example</code> or <code>example-*-123</code> and that would exclude any URI containing that word fragment.', 'zencache').'</p>'."\n";
        echo '      <p class="info">'.__('<strong>Note:</strong> please remember that your entries here should be formatted as a line-delimited list; e.g., one exclusion pattern per line.', 'zencache').'</p>'."\n";
        echo '   </div>'."\n";

        echo '</div>'."\n";

        /* ----------------------------------------------------------------------------------------- */

        if (IS_PRO || $this->plugin->isProPreview()) {
            echo '<div class="plugin-menu-page-panel'.(!IS_PRO ? ' pro-preview' : '').'">'."\n";

            echo '   <a href="#" class="plugin-menu-page-panel-heading'.(!IS_PRO ? ' pro-preview-feature' : '').'">'."\n";
            echo '      <i class="si si-octi-organization"></i> '.__('Logged-In Users', 'zencache')."\n";
            echo '   </a>'."\n";

            echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
            echo '      <i class="si si-group si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
            echo '      <h3>'.__('Caching Enabled for Logged-In Users &amp; Comment Authors?', 'zencache').'</h3>'."\n";
            echo '      <p>'.__('This should almost ALWAYS be set to <code>No</code>. Most sites will NOT want to cache content generated while a user is logged-in. Doing so could result in a cache of dynamic content generated specifically for a particular user, where the content being cached may contain details that pertain only to the user that was logged-in when the cache was generated. Imagine visiting a website that says you\'re logged-in as Billy Bob (but you\'re not Billy Bob; NOT good). In short, do NOT turn this on unless you know what you\'re doing.', 'zencache').'</p>'."\n";
            echo '      <i class="si si-sitemap si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
            echo '      <p>'.sprintf(__('<strong>Exception (Membership Sites):</strong> If you run a site with many users and the majority of your traffic comes from users who ARE logged-in, please choose: <code>Yes (maintain separate cache)</code>. %1$s will operate normally; but when a user is logged-in, the cache is user-specific. %1$s will intelligently refresh the cache when/if a user submits a form on your site with the GET or POST method. Or, if you make changes to their account (or another plugin makes changes to their account); including user <a href="http://codex.wordpress.org/Function_Reference/update_user_option" target="_blank">option</a>|<a href="http://codex.wordpress.org/Function_Reference/update_user_meta" target="_blank">meta</a> additions, updates &amp; deletions too. However, please note that enabling this feature (e.g., user-specific cache entries); will eat up MUCH more disk space. That being said, the benefits of this feature for most sites will outweigh the disk overhead (e.g., it\'s NOT an issue in most cases). Unless you are short on disk space (or you have MANY thousands of users), the disk overhead is neglible.', 'zencache'), esc_html(NAME)).'</p>'."\n";
            echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][when_logged_in]">'."\n";
            echo '            <option value="0"'.selected($this->plugin->options['when_logged_in'], '0', false).'>'.__('No, do NOT cache; or serve a cache file when a user is logged-in (safest option).', 'zencache').'</option>'."\n";
            echo '            <option value="postload"'.selected($this->plugin->options['when_logged_in'], 'postload', false).'>'.__('Yes, and maintain a separate cache for each user (recommended for membership sites).', 'zencache').'</option>'."\n";
            if ($this->plugin->options['when_logged_in'] === '1' || get_site_option(GLOBAL_NS.'_when_logged_in_was_1')) {
                update_site_option(GLOBAL_NS.'_when_logged_in_was_1', '1');
                echo '            <option value="1"'.selected($this->plugin->options['when_logged_in'], '1', false).'>'.__('Yes, but DON\'T maintain a separate cache for each user (I know what I\'m doing).', 'zencache').'</option>'."\n";
            }
            echo '         </select></p>'."\n";
            if ($this->plugin->options['when_logged_in'] === '1' && $this->plugin->applyWpFilters(GLOBAL_NS.'_when_logged_in_no_admin_bar', true)) {
                echo '<p class="warning">'.sprintf(__('<strong>Warning:</strong> Whenever you enable caching for logged-in users (without a separate cache for each user), the WordPress Admin Bar <em>must</em> be disabled to prevent one user from seeing another user\'s details in the Admin Bar. <strong>Given your current configuration, %1$s will automatically hide the WordPress Admin Bar on the front-end of your site.</strong>', 'zencache'), esc_html(NAME)).'</p>'."\n";
            }
            echo '      <p class="info">'.__('<strong>Note:</strong> For most sites, the majority of their traffic (if not all of their traffic) comes from visitors who are not logged in, so disabling the cache for logged-in users is NOT ordinarily a performance issue. When a user IS logged-in, disabling the cache is considered ideal, because a logged-in user has a session open with your site; and the content they view should remain very dynamic in this scenario.', 'zencache').'</p>'."\n";
            echo '      <p class="info">'.sprintf(__('<strong>Note:</strong> This setting includes some users who AREN\'T actually logged into the system, but who HAVE authored comments recently. %1$s includes comment authors as part of it\'s logged-in user check. This way comment authors will be able to see updates to the comment thread immediately; and, so that any dynamically-generated messages displayed by your theme will work as intended. In short, %1$s thinks of a comment author as a logged-in user, even though technically they are not. ~ Users who gain access to password-protected Posts/Pages are also included.', 'zencache'), esc_html(NAME)).'</p>'."\n";
            echo '      <hr />'."\n";
            echo '      <h3>'.__('Static CDN Filters Enabled for Logged-In Users &amp; Comment Authors?', 'zencache').'</h3>'."\n";
            echo '      <p>'.__('While this defaults to a value of <code>No</code>, it should almost always be set to <code>Yes</code>. This value defaults to <code>No</code> only because Logged-In User caching (see above) defaults to <code>No</code> and setting this value to <code>Yes</code> by default can cause confusion for some users. Once you understand that Static CDN Filters can be applied safely for all visitors (logged-in or not logged-in), please choose <code>Yes</code> in the dropdown below. If you are not using Static CDN Filters, the value below is ignored.', 'zencache').'</p>'."\n";
            echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cdn_when_logged_in]">'."\n";
            echo '            <option value="0"'.selected($this->plugin->options['cdn_when_logged_in'], '0', false).'>'.__('No, disable Static CDN Filters when a user is logged-in.', 'zencache').'</option>'."\n";
            echo '            <option value="postload"'.selected($this->plugin->options['cdn_when_logged_in'], 'postload', false).'>'.__('Yes, enable Static CDN Filters for logged-in users (recommended) .', 'zencache').'</option>'."\n";
            echo '         </select></p>'."\n";
            echo '      <p class="info">'.__('<strong>Note:</strong> Static CDN Filters serve <em>static</em> resources. Static resources, are, simply put, static. Thus, it is not a problem to cache these resources for any visitor (logged-in or not logged-in). To avoid confusion, this defaults to a value of <code>No</code>, and we ask that you set it to <code>Yes</code> on your own so that you\'ll know to expect this behavior; i.e., that static resources will always be served from the CDN (logged-in or not logged-in) even though Logged-In User caching may be disabled above.', 'zencache').'</p>'."\n";
            echo '   </div>'."\n";

            echo '</div>'."\n";
        }
        /* ----------------------------------------------------------------------------------------- */

        echo '<div class="plugin-menu-page-panel">'."\n";

        echo '   <a href="#" class="plugin-menu-page-panel-heading">'."\n";
        echo '      <i class="si si-question-circle"></i> '.__('GET Requests', 'zencache')."\n";
        echo '   </a>'."\n";

        echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
        echo '      <i class="si si-question-circle si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
        echo '      <h3>'.__('Caching Enabled for GET (Query String) Requests?', 'zencache').'</h3>'."\n";
        echo '      <p>'.__('This should almost ALWAYS be set to <code>No</code>. UNLESS, you\'re using unfriendly Permalinks. In other words, if all of your URLs contain a query string (e.g., <code>/?key=value</code>); you\'re using unfriendly Permalinks. Ideally, you would refrain from doing this; and instead, update your Permalink options immediately; which also optimizes your site for search engines. That being said, if you really want to use unfriendly Permalinks, and ONLY if you\'re using unfriendly Permalinks, you should set this to <code>Yes</code>; and don\'t worry too much, the sky won\'t fall on your head :-)', 'zencache').'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][get_requests]">'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['get_requests'], '0', false).'>'.__('No, do NOT cache (or serve a cache file) when a query string is present.', 'zencache').'</option>'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['get_requests'], '1', false).'>'.__('Yes, I would like to cache URLs that contain a query string.', 'zencache').'</option>'."\n";
        echo '         </select></p>'."\n";
        echo '      <p class="info">'.__('<strong>Note:</strong> POST requests (i.e., forms with <code>method=&quot;post&quot;</code>) are always excluded from the cache, which is the way it should be. Any <a href="http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html" target="_blank">POST/PUT/DELETE</a> request should NEVER (ever) be cached. CLI (and self-serve) requests are also excluded from the cache (always). A CLI request is one that comes from the command line; commonly used by CRON jobs and other automated routines. A self-serve request is an HTTP connection established from your site -› to your site. For instance, a WP Cron job, or any other HTTP request that is spawned not by a user, but by the server itself.', 'zencache').'</p>'."\n";
        echo '      <p class="info">'.sprintf(__('<strong>Advanced Tip:</strong> If you are NOT caching GET requests (recommended), but you DO want to allow some special URLs that include query string parameters to be cached; you can add this special parameter to any URL <code>?%2$sAC=1</code>. This tells %1$s that it\'s OK to cache that particular URL, even though it contains query string arguments. If you ARE caching GET requests and you want to force %1$s to NOT cache a specific request, you can add this special parameter to any URL <code>?%2$sAC=0</code>.', 'zencache'), esc_html(NAME), esc_html(strtolower(SHORT_NAME))).'</p>'."\n";
        echo '   </div>'."\n";

        echo '</div>'."\n";

        /* ----------------------------------------------------------------------------------------- */

        echo '<div class="plugin-menu-page-panel">'."\n";

        echo '   <a href="#" class="plugin-menu-page-panel-heading">'."\n";
        echo '      <i class="si si-chain-broken"></i> '.__('404 Requests', 'zencache')."\n";
        echo '   </a>'."\n";

        echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
        echo '      <i class="si si-question-circle si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
        echo '      <h3>'.__('Caching Enabled for 404 Requests?', 'zencache').'</h3>'."\n";
        echo '      <p>'.sprintf(__('When this is set to <code>No</code>, %1$s will ignore all 404 requests and no cache file will be served. While this is fine for most site owners, caching the 404 page on a high-traffic site may further reduce server load. When this is set to <code>Yes</code>, %1$s will cache the 404 page (see <a href="https://codex.wordpress.org/Creating_an_Error_404_Page" target="_blank">Creating an Error 404 Page</a>) and then serve that single cache file to all future 404 requests.', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_404_requests]">'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['cache_404_requests'], '0', false).'>'.__('No, do NOT cache (or serve a cache file) for 404 requests.', 'zencache').'</option>'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['cache_404_requests'], '1', false).'>'.__('Yes, I would like to cache the 404 page and serve the cached file for 404 requests.', 'zencache').'</option>'."\n";
        echo '         </select></p>'."\n";
        echo '      <p class="info">'.sprintf(__('<strong>How does %1$s cache 404 requests?</strong> %1$s will create a special cache file (<code>----404----.html</code>, see Advanced Tip below) for the first 404 request and then <a href="http://www.php.net/manual/en/function.symlink.php" target="_blank">symlink</a> future 404 requests to this special cache file. That way you don\'t end up with lots of 404 cache files that all contain the same thing (the contents of the 404 page). Instead, you\'ll have one 404 cache file and then several symlinks (i.e., references) to that 404 cache file.', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p class="info">'.__('<strong>Advanced Tip:</strong> The default 404 cache filename (<code>----404----.html</code>) is designed to minimize the chance of a collision with a cache file for a real page with the same name. However, if you want to override this default and define your own 404 cache filename, you can do so by adding <code>define(\'ZENCACHE_404_CACHE_FILENAME\', \'your-404-cache-filename\');</code> to your <code>wp-config.php</code> file (note that the <code>.html</code> extension should be excluded when defining a new filename).', 'zencache').'</p>'."\n";
        echo '   </div>'."\n";

        echo '</div>'."\n";

        /* ----------------------------------------------------------------------------------------- */

        echo '<div class="plugin-menu-page-panel">'."\n";

        echo '   <a href="#" class="plugin-menu-page-panel-heading">'."\n";
        echo '      <i class="si si-feed"></i> '.__('RSS, RDF, and Atom Feeds', 'zencache')."\n";
        echo '   </a>'."\n";

        echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
        echo '      <i class="si si-question-circle si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
        echo '      <h3>'.__('Caching Enabled for RSS, RDF, Atom Feeds?', 'zencache').'</h3>'."\n";
        echo '      <p>'.__('This should almost ALWAYS be set to <code>No</code>. UNLESS, you\'re sure that you want to cache your feeds. If you use a web feed management provider like Google® Feedburner and you set this option to <code>Yes</code>, you may experience delays in the detection of new posts. <strong>NOTE:</strong> If you do enable this, it is highly recommended that you also enable automatic Feed Clearing too. Please see the section above: "Automatic Cache Clearing". Find the sub-section titled: "Auto-Clear RSS/RDF/ATOM Feeds".', 'zencache').'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][feeds_enable]" class="-no-if-enabled">'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['feeds_enable'], '0', false).'>'.__('No, do NOT cache (or serve a cache file) when displaying a feed.', 'zencache').'</option>'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['feeds_enable'], '1', false).'>'.__('Yes, I would like to cache feed URLs.', 'zencache').'</option>'."\n";
        echo '         </select></p>'."\n";
        echo '      <p class="info">'.__('<strong>Note:</strong> This option affects all feeds served by WordPress, including the site feed, the site comment feed, post-specific comment feeds, author feeds, search feeds, and category and tag feeds. See also: <a href="http://codex.wordpress.org/WordPress_Feeds" target="_blank">WordPress Feeds</a>.', 'zencache').'</p>'."\n";
        echo '   </div>'."\n";

        echo '</div>'."\n";

        /* ----------------------------------------------------------------------------------------- */

        echo '<div class="plugin-menu-page-panel">'."\n";

        echo '   <a href="#" class="plugin-menu-page-panel-heading">'."\n";
        echo '      <i class="si si-ban"></i> '.__('URI Exclusion Patterns', 'zencache')."\n";
        echo '   </a>'."\n";

        echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
        echo '      <h3>'.__('Don\'t Cache These Special URI Exclusion Patterns?', 'zencache').'</h3>'."\n";
        echo '      <p>'.__('Sometimes there are certain cases where a particular file, or a particular group of files, should never be cached. This is where you will enter those if you need to (one per line). Searches are performed against the <a href="https://gist.github.com/jaswsinc/338b6eb03a36c048c26f" target="_blank" style="text-decoration:none;"><code>REQUEST_URI</code></a>; i.e., <code>/path/?query</code> (caSe insensitive). So, don\'t put in full URLs here, just word fragments found in the file path (or query string) is all you need, excluding the http:// and domain name. A wildcard <code>*</code> character can also be used when necessary; e.g., <code>/category/abc-followed-by-*</code> (where <code>*</code> = 0 or more characters that are NOT a slash <code>/</code>). Other special characters include: <code>**</code> = 0 or more characters of any kind, including <code>/</code> slashes; <code>^</code> = beginning of the string; <code>$</code> = end of the string. To learn more about this syntax, please see <a href ="http://zencache.com/r/watered-down-regex-syntax/" target="_blank">this KB article</a>.', 'zencache').'</p>'."\n";
        echo '      <p><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][exclude_uris]" rows="5" spellcheck="false" class="monospace">'.format_to_edit($this->plugin->options['exclude_uris']).'</textarea></p>'."\n";

        echo '      <p class="info">'.__('<strong>Tip:</strong> let\'s use this example URL: <code>http://www.example.com/post/example-post-123</code>. To exclude this URL, you would put this line into the field above: <code>/post/example-post-123</code>. Or, you could also just put in a small fragment, like: <code>example</code> or <code>example-*-123</code> and that would exclude any URI containing that word fragment.', 'zencache').'</p>'."\n";
        echo '      <p class="info">'.__('<strong>Note:</strong> please remember that your entries here should be formatted as a line-delimited list; e.g., one exclusion pattern per line.', 'zencache').'</p>'."\n";
        echo '   </div>'."\n";

        echo '</div>'."\n";

        /* ----------------------------------------------------------------------------------------- */

        echo '<div class="plugin-menu-page-panel">'."\n";

        echo '   <a href="#" class="plugin-menu-page-panel-heading">'."\n";
        echo '      <i class="si si-ban"></i> '.__('HTTP Referrer Exclusion Patterns', 'zencache')."\n";
        echo '   </a>'."\n";

        echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
        echo '      <h3>'.__('Don\'t Cache These Special HTTP Referrer Exclusion Patterns?', 'zencache').'</h3>'."\n";
        echo '      <p>'.__('Sometimes there are special cases where a particular referring URL (or referring domain) that sends you traffic; or even a particular group of referring URLs or domains that send you traffic; should result in a page being loaded on your site that is NOT from the cache (and that resulting page should never be cached). This is where you will enter those if you need to (one per line). Searches are performed against the <a href="http://www.php.net//manual/en/reserved.variables.server.php" target="_blank" style="text-decoration:none;"><code>HTTP_REFERER</code></a> (caSe insensitive). A wildcard <code>*</code> character can also be used when necessary; e.g., <code>*.domain.com</code> (where <code>*</code> = 0 or more characters that are NOT a slash <code>/</code>). Other special characters include: <code>**</code> = 0 or more characters of any kind, including <code>/</code> slashes; <code>^</code> = beginning of the string; <code>$</code> = end of the string. To learn more about this syntax, please see <a href ="http://zencache.com/r/watered-down-regex-syntax/" target="_blank">this KB article</a>.', 'zencache').'</p>'."\n";
        echo '      <p><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][exclude_refs]" rows="5" spellcheck="false" class="monospace">'.format_to_edit($this->plugin->options['exclude_refs']).'</textarea></p>'."\n";
        echo '      <p class="info">'.__('<strong>Tip:</strong> let\'s use this example URL: <code>http://www.referring-domain.com/search/?q=search+terms</code>. To exclude this referring URL, you could put this line into the field above: <code>www.referring-domain.com</code>. Or, you could also just put in a small fragment, like: <code>/search/</code> or <code>q=*</code>; and that would exclude any referrer containing that word fragment.', 'zencache').'</p>'."\n";
        echo '      <p class="info">'.__('<strong>Note:</strong> please remember that your entries here should be formatted as a line-delimited list; e.g., one exclusion pattern per line.', 'zencache').'</p>'."\n";
        echo '   </div>'."\n";

        echo '</div>'."\n";

        /* ----------------------------------------------------------------------------------------- */

        echo '<div class="plugin-menu-page-panel">'."\n";

        echo '   <a href="#" class="plugin-menu-page-panel-heading">'."\n";
        echo '      <i class="si si-ban"></i> '.__('User-Agent Exclusion Patterns', 'zencache')."\n";
        echo '   </a>'."\n";

        echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
        echo '      <h3>'.__('Don\'t Cache These Special User-Agent Exclusion Patterns?', 'zencache').'</h3>'."\n";
        echo '      <p>'.__('Sometimes there are special cases when a particular user-agent (e.g., a specific browser or a specific type of device); should be shown a page on your site that is NOT from the cache (and that resulting page should never be cached). This is where you will enter those if you need to (one per line). Searches are performed against the <a href="http://www.php.net//manual/en/reserved.variables.server.php" target="_blank" style="text-decoration:none;"><code>HTTP_USER_AGENT</code></a> (caSe insensitive). A wildcard <code>*</code> character can also be used when necessary; e.g., <code>Android *; Chrome/* Mobile</code> (where <code>*</code> = 0 or more characters that are NOT a slash <code>/</code>). Other special characters include: <code>**</code> = 0 or more characters of any kind, including <code>/</code> slashes; <code>^</code> = beginning of the string; <code>$</code> = end of the string. To learn more about this syntax, please see <a href ="http://zencache.com/r/watered-down-regex-syntax/" target="_blank">this KB article</a>.', 'zencache').'</p>'."\n";
        echo '      <p><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][exclude_agents]" rows="5" spellcheck="false" class="monospace">'.format_to_edit($this->plugin->options['exclude_agents']).'</textarea></p>'."\n";
        echo '      <p class="info">'.sprintf(__('<strong>Tip:</strong> if you wanted to exclude iPhones put this line into the field above: <code>iPhone;*AppleWebKit</code>. Or, you could also just put in a small fragment, like: <code>iphone</code>; and that would exclude any user-agent containing that word fragment. Note, this is just an example. With a default installation of %1$s, there is no compelling reason to exclude iOS devices (or any mobile device for that matter).', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p class="info">'.__('<strong>Note:</strong> please remember that your entries here should be formatted as a line-delimited list; e.g., one exclusion pattern per line.', 'zencache').'</p>'."\n";
        echo '   </div>'."\n";

        echo '</div>'."\n";

        /* ----------------------------------------------------------------------------------------- */

        if (IS_PRO || $this->plugin->isProPreview()) {
            echo '<div class="plugin-menu-page-panel'.(!IS_PRO ? ' pro-preview' : '').'">'."\n";

            echo '   <a href="#" class="plugin-menu-page-panel-heading'.(!IS_PRO ? ' pro-preview-feature' : '').'">'."\n";
            echo '      <i class="si si-sitemap"></i> '.__('Auto-Cache Engine', 'zencache')."\n";
            echo '   </a>'."\n";

            echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
            echo '      <i class="si si-question-circle si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
            echo '      <h3>'.__('Enable the Auto-Cache Engine?', 'zencache').'</h3>'."\n";
            echo '      <p>'.sprintf(__('After using %1$s for awhile (or any other page caching plugin, for that matter); it becomes obvious that at some point (based on your configured Expiration Time) %1$s has to refresh itself. It does this by ditching its cached version of a page, reloading the database-driven content, and then recreating the cache with the latest data. This is a never ending regeneration cycle that is based entirely on your configured Expiration Time.', 'zencache'), esc_html(NAME)).'</p>'."\n";
            echo '      <p>'.__('Understanding this, you can see that 99% of your visitors are going to receive a lightning fast response from your server. However, there will always be around 1% of your visitors that land on a page for the very first time (before it\'s been cached), or land on a page that needs to have its cache regenerated, because the existing cache has become outdated. We refer to this as a <em>First-Come Slow-Load Issue</em>. Not a huge problem, but if you\'re optimizing your site for every ounce of speed possible, the Auto-Cache Engine can help with this. The Auto-Cache Engine has been designed to combat this issue by taking on the responsibility of being that first visitor to a page that has not yet been cached, or has an expired cache. The Auto-Cache Engine is powered, in part, by <a href="http://codex.wordpress.org/Category:WP-Cron_Functions" target="_blank">WP-Cron</a> (already built into WordPress). The Auto-Cache Engine runs at 15-minute intervals via WP-Cron. It also uses the <a href="http://core.trac.wordpress.org/browser/trunk/wp-includes/http.php" target="_blank">WP_Http</a> class, which is also built into WordPress already.', 'zencache').'</p>'."\n";
            echo '      <p>'.__('The Auto-Cache Engine obtains its list of URLs to auto-cache, from two different sources. It can read an <a href="http://wordpress.org/extend/plugins/google-sitemap-generator/" target="_blank">XML Sitemap</a> and/or a list of specific URLs that you supply. If you supply both sources, it will use both sources collectively. The Auto-Cache Engine takes ALL of your other configuration options into consideration too, including your Expiration Time, as well as any cache exclusion rules.', 'zencache').'</p>'."\n";
            echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][auto_cache_enable]" data-target=".-auto-cache-options">'."\n";
            echo '            <option value="0"'.(!IS_PRO ? '' : selected($this->plugin->options['auto_cache_enable'], '0', false)).'>'.__('No, leave the Auto-Cache Engine disabled please.', 'zencache').'</option>'."\n";
            echo '            <option value="1"'.(!IS_PRO ? ' selected' : selected($this->plugin->options['auto_cache_enable'], '1', false)).'>'.__('Yes, I want the Auto-Cache Engine to keep pages cached automatically.', 'zencache').'</option>'."\n";
            echo '         </select></p>'."\n";

            echo '      <hr />'."\n";

            echo '      <div class="plugin-menu-page-panel-if-enabled -auto-cache-options">'."\n";
            echo '         <h3>'.__('XML Sitemap URL (or an XML Sitemap Index)', 'zencache').'</h3>'."\n";
            echo '         <table style="width:100%;"><tr><td style="width:1px; font-weight:bold; white-space:nowrap;">'.esc_html(home_url('/')).'</td><td><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][auto_cache_sitemap_url]" value="'.esc_attr($this->plugin->options['auto_cache_sitemap_url']).'" /></td></tr></table>'."\n";
            if (is_multisite()) {
                echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][auto_cache_ms_children_too]">'."\n";
                echo '            <option value="0"'.selected($this->plugin->options['auto_cache_ms_children_too'], '0', false).'>'.__('All URLs in this network are in the sitemap for the main site.', 'zencache').'</option>'."\n";
                echo '            <option value="1"'.selected($this->plugin->options['auto_cache_ms_children_too'], '1', false).'>'.__('Using the path I\'ve given, look for blog-specific sitemaps in each child blog also.', 'zencache').'</option>'."\n";
                echo '         </select></p>'."\n";
                echo '      <p class="info" style="display:block; margin-top:0;">'.sprintf(__('<strong>↑</strong> If enabled here, each child blog can be auto-cached too. %1$s will dynamically change the leading <code>%2$s</code> as necessary; for each child blog in the network. %1$s supports both sub-directory &amp; sub-domain networks, including domain mapping plugins. For more information about how the Auto-Cache Engine caches child blogs, see <a href="http://zencache.com/r/kb-article-how-does-the-auto-cache-engine-cache-child-blogs-in-a-multisite-network/" target="_blank">this article</a>.', 'zencache'), esc_html(NAME), esc_html(home_url('/'))).'</p>'."\n";
            }
            echo '         <hr />'."\n";

            echo '         <h3>'.__('And/Or; a List of URLs to Auto-Cache (One Per Line)', 'zencache').'</h3>'."\n";
            echo '         <p><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][auto_cache_other_urls]" rows="5" spellcheck="false" class="monospace">'.format_to_edit($this->plugin->options['auto_cache_other_urls']).'</textarea></p>'."\n";
            echo '         <p class="info" style="display:block; margin-top:-5px;">'.__('<strong>Note:</strong> Wildcards are NOT supported here. If you are going to supply a list of URLs above, each line must contain one full URL for the Auto-Cache Engine to auto-cache. If you have many URLs, we recommend using an <a href="https://en.wikipedia.org/wiki/Sitemaps" target="_blank">XML Sitemap</a>.', 'zencache').'</p>'."\n";

            echo '         <hr />'."\n";

            echo '         <h3>'.__('Auto-Cache Delay Timer (in Milliseconds)', 'zencache').'</h3>'."\n";
            echo '         <p>'.__('As the Auto-Cache Engine runs through each URL, you can tell it to wait X number of milliseconds between each connection that it makes. It is strongly suggested that you DO have some small delay here. Otherwise, you run the risk of hammering your own web server with multiple repeated connections whenever the Auto-Cache Engine is running. This is especially true on very large sites; where there is the potential for hundreds of repeated connections as the Auto-Cache Engine goes through a long list of URLs. Adding a delay between each connection will prevent the Auto-Cache Engine from placing a heavy load on the processor that powers your web server. A value of <code>500</code> milliseconds is suggested here (half a second). If you experience problems, you can bump this up a little at a time, in increments of <code>500</code> milliseconds; until you find a happy place for your server. <em>Please note that <code>1000</code> milliseconds = <code>1</code> full second.</em>', 'zencache').'</p>'."\n";
            echo '         <p><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][auto_cache_delay]" value="'.esc_attr($this->plugin->options['auto_cache_delay']).'" /></p>'."\n";

            echo '         <hr />'."\n";

            echo '         <h3>'.__('Auto-Cache User-Agent String', 'zencache').'</h3>'."\n";
            echo '         <table style="width:100%;"><tr><td><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][auto_cache_user_agent]" value="'.esc_attr($this->plugin->options['auto_cache_user_agent']).'" /></td><td style="width:1px; font-weight:bold; white-space:nowrap;">; '.esc_html(SLUG_TD.' '.VERSION).'</td></tr></table>'."\n";
            echo '         <p class="info" style="display:block;">'.__('This is how the Auto-Cache Engine identifies itself when connecting to URLs. See <a href="http://en.wikipedia.org/wiki/User_agent" target="_blank">User Agent</a> in the Wikipedia.', 'zencache').'</p>'."\n";
            echo '      </div>'."\n";
            echo '   </div>'."\n";

            echo '</div>'."\n";
        }
        /* ----------------------------------------------------------------------------------------- */

        if (IS_PRO || $this->plugin->isProPreview()) {
            echo '<div class="plugin-menu-page-panel'.(!IS_PRO ? ' pro-preview' : '').'">'."\n";

            echo '   <a href="#" class="plugin-menu-page-panel-heading'.(!IS_PRO ? ' pro-preview-feature' : '').'">'."\n";
            echo '      <i class="si si-html5"></i> '.__('HTML Compression', 'zencache')."\n";
            echo '   </a>'."\n";

            echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
            echo '      <i class="si si-question-circle si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
            echo '      <h3>'.__('Enable WebSharks™ HTML Compression?', 'zencache').'</h3>'."\n";
            echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_enable]" data-target=".-htmlc-options">'."\n";
            echo '            <option value="0"'.(!IS_PRO ? '' : selected($this->plugin->options['htmlc_enable'], '0', false)).'>'.__('No, do NOT compress HTML/CSS/JS code at runtime.', 'zencache').'</option>'."\n";
            echo '            <option value="1"'.(!IS_PRO ? ' selected' : selected($this->plugin->options['htmlc_enable'], '1', false)).'>'.__('Yes, I want to compress HTML/CSS/JS for blazing fast speeds.', 'zencache').'</option>'."\n";
            echo '         </select></p>'."\n";
            echo '      <p class="info" style="display:block;">'.__('<strong>Note:</strong> This is experimental. Please <a href="https://github.com/websharks/zencache/issues" target="_blank">report issues here</a>.', 'zencache').'</p>'."\n";
            echo '      <hr />'."\n";
            echo '      <div class="plugin-menu-page-panel-if-enabled -htmlc-options">'."\n";
            echo '         <h3>'.__('HTML Compression Options', 'zencache').'</h3>'."\n";
            echo '         <p>'.__('You can <a href="https://github.com/websharks/html-compressor" target="_blank">learn more about all of these options here</a>.', 'zencache').'</p>'."\n";
            echo '         <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_compress_combine_head_body_css]" autocomplete="off">'."\n";
            echo '               <option value="1"'.selected($this->plugin->options['htmlc_compress_combine_head_body_css'], '1', false).'>'.__('Yes, combine CSS from &lt;head&gt; and &lt;body&gt; into fewer files.', 'zencache').'</option>'."\n";
            echo '               <option value="0"'.selected($this->plugin->options['htmlc_compress_combine_head_body_css'], '0', false).'>'.__('No, do not combine CSS from &lt;head&gt; and &lt;body&gt; into fewer files.', 'zencache').'</option>'."\n";
            echo '            </select></p>'."\n";
            echo '         <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_compress_css_code]" autocomplete="off">'."\n";
            echo '               <option value="1"'.selected($this->plugin->options['htmlc_compress_css_code'], '1', false).'>'.__('Yes, compress the code in any unified CSS files.', 'zencache').'</option>'."\n";
            echo '               <option value="0"'.selected($this->plugin->options['htmlc_compress_css_code'], '0', false).'>'.__('No, do not compress the code in any unified CSS files.', 'zencache').'</option>'."\n";
            echo '            </select></p>'."\n";
            echo '         <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_compress_combine_head_js]" autocomplete="off">'."\n";
            echo '               <option value="1"'.selected($this->plugin->options['htmlc_compress_combine_head_js'], '1', false).'>'.__('Yes, combine JS from &lt;head&gt; into fewer files.', 'zencache').'</option>'."\n";
            echo '               <option value="0"'.selected($this->plugin->options['htmlc_compress_combine_head_js'], '0', false).'>'.__('No, do not combine JS from &lt;head&gt; into fewer files.', 'zencache').'</option>'."\n";
            echo '            </select></p>'."\n";
            echo '         <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_compress_combine_footer_js]" autocomplete="off">'."\n";
            echo '               <option value="1"'.selected($this->plugin->options['htmlc_compress_combine_footer_js'], '1', false).'>'.__('Yes, combine JS footer scripts into fewer files.', 'zencache').'</option>'."\n";
            echo '               <option value="0"'.selected($this->plugin->options['htmlc_compress_combine_footer_js'], '0', false).'>'.__('No, do not combine JS footer scripts into fewer files.', 'zencache').'</option>'."\n";
            echo '            </select></p>'."\n";
            echo '         <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_compress_combine_remote_css_js]" autocomplete="off">'."\n";
            echo '               <option value="1"'.selected($this->plugin->options['htmlc_compress_combine_remote_css_js'], '1', false).'>'.__('Yes, combine CSS/JS from remote resources too.', 'zencache').'</option>'."\n";
            echo '               <option value="0"'.selected($this->plugin->options['htmlc_compress_combine_remote_css_js'], '0', false).'>'.__('No, do not combine CSS/JS from remote resources.', 'zencache').'</option>'."\n";
            echo '            </select></p>'."\n";
            echo '         <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_compress_js_code]" autocomplete="off">'."\n";
            echo '               <option value="1"'.selected($this->plugin->options['htmlc_compress_js_code'], '1', false).'>'.__('Yes, compress the code in any unified JS files.', 'zencache').'</option>'."\n";
            echo '               <option value="0"'.selected($this->plugin->options['htmlc_compress_js_code'], '0', false).'>'.__('No, do not compress the code in any unified JS files.', 'zencache').'</option>'."\n";
            echo '            </select></p>'."\n";
            echo '         <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_compress_inline_js_code]" autocomplete="off">'."\n";
            echo '               <option value="1"'.selected($this->plugin->options['htmlc_compress_inline_js_code'], '1', false).'>'.__('Yes, compress inline JavaScript snippets.', 'zencache').'</option>'."\n";
            echo '               <option value="0"'.selected($this->plugin->options['htmlc_compress_inline_js_code'], '0', false).'>'.__('No, do not compress inline JavaScript snippets.', 'zencache').'</option>'."\n";
            echo '            </select></p>'."\n";
            echo '         <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_compress_html_code]" autocomplete="off">'."\n";
            echo '               <option value="1"'.selected($this->plugin->options['htmlc_compress_html_code'], '1', false).'>'.__('Yes, compress (remove extra whitespace) in the final HTML code too.', 'zencache').'</option>'."\n";
            echo '               <option value="0"'.selected($this->plugin->options['htmlc_compress_html_code'], '0', false).'>'.__('No, do not compress the final HTML code.', 'zencache').'</option>'."\n";
            echo '            </select></p>'."\n";
            echo '         <hr />'."\n";
            echo '         <h3>'.__('CSS Exclusion Patterns?', 'zencache').'</h3>'."\n";
            echo '         <p>'.__('Sometimes there are special cases when a particular CSS file should NOT be consolidated or compressed in any way. This is where you will enter those if you need to (one per line). Searches are performed against the <code>&lt;link href=&quot;&quot;&gt;</code> value, and also against the contents of any inline <code>&lt;style&gt;</code> tags (caSe insensitive). A wildcard <code>*</code> character can also be used when necessary; e.g., <code>xy*-framework</code> (where <code>*</code> = 0 or more characters that are NOT a slash <code>/</code>). Other special characters include: <code>**</code> = 0 or more characters of any kind, including <code>/</code> slashes; <code>^</code> = beginning of the string; <code>$</code> = end of the string. To learn more about this syntax, please see <a href ="http://zencache.com/r/watered-down-regex-syntax/" target="_blank">this KB article</a>.', 'zencache').'</p>'."\n";
            echo '         <p><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_css_exclusions]" rows="5" spellcheck="false" class="monospace">'.format_to_edit($this->plugin->options['htmlc_css_exclusions']).'</textarea></p>'."\n";
            echo '         <p class="info" style="display:block;">'.__('<strong>Note:</strong> please remember that your entries here should be formatted as a line-delimited list; e.g., one exclusion pattern per line.', 'zencache').'</p>'."\n";
            echo '         <h3>'.__('JavaScript Exclusion Patterns?', 'zencache').'</h3>'."\n";
            echo '         <p>'.__('Sometimes there are special cases when a particular JS file should NOT be consolidated or compressed in any way. This is where you will enter those if you need to (one per line). Searches are performed against the <code>&lt;script src=&quot;&quot;&gt;</code> value, and also against the contents of any inline <code>&lt;script&gt;</code> tags (caSe insensitive). A wildcard <code>*</code> character can also be used when necessary; e.g., <code>xy*-framework</code> (where <code>*</code> = 0 or more characters that are NOT a slash <code>/</code>). Other special characters include: <code>**</code> = 0 or more characters of any kind, including <code>/</code> slashes; <code>^</code> = beginning of the string; <code>$</code> = end of the string. To learn more about this syntax, please see <a href ="http://zencache.com/r/watered-down-regex-syntax/" target="_blank">this KB article</a>.', 'zencache').'</p>'."\n";
            echo '         <p><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_js_exclusions]" rows="5" spellcheck="false" class="monospace">'.format_to_edit($this->plugin->options['htmlc_js_exclusions']).'</textarea></p>'."\n";
            echo '         <p class="info" style="display:block;">'.__('<strong>Note:</strong> please remember that your entries here should be formatted as a line-delimited list; e.g., one exclusion pattern per line.', 'zencache').'</p>'."\n";
            echo '         <h3>'.__('URI Exclusions for HTML Compressor?', 'zencache').'</h3>'."\n";
            echo '         <p>'.__('When you enable HTML Compression above, you may want to prevent certain pages on your site from being cached by the HTML Compressor. This is where you will enter those if you need to (one per line). Searches are performed against the <a href="https://gist.github.com/jaswsinc/338b6eb03a36c048c26f" target="_blank" style="text-decoration:none;"><code>REQUEST_URI</code></a>; i.e., <code>/path/?query</code> (caSe insensitive). So, don\'t put in full URLs here, just word fragments found in the file path (or query string) is all you need, excluding the http:// and domain name. A wildcard <code>*</code> character can also be used when necessary; e.g., <code>/category/abc-followed-by-*</code> (where <code>*</code> = 0 or more characters that are NOT a slash <code>/</code>). Other special characters include: <code>**</code> = 0 or more characters of any kind, including <code>/</code> slashes; <code>^</code> = beginning of the string; <code>$</code> = end of the string. To learn more about this syntax, please see <a href ="http://zencache.com/r/watered-down-regex-syntax/" target="_blank">this KB article</a>.', 'zencache').'</p>'."\n";
            echo '         <p><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_uri_exclusions]" rows="5" spellcheck="false" class="monospace">'.format_to_edit($this->plugin->options['htmlc_uri_exclusions']).'</textarea></p>'."\n";
            echo '         <p class="info">'.__('<strong>Tip:</strong> let\'s use this example URL: <code>http://www.example.com/post/example-post-123</code>. To exclude this URL, you would put this line into the field above: <code>/post/example-post-123</code>. Or, you could also just put in a small fragment, like: <code>example</code> or <code>example-*-123</code> and that would exclude any URI containing that word fragment.', 'zencache').'</p>'."\n";
            echo '         <p class="info">'.__('<strong>Note:</strong> please remember that your entries here should be formatted as a line-delimited list; e.g., one exclusion pattern per line.', 'zencache').'</p>'."\n";
            echo '         <hr />'."\n";
            echo '         <h3>'.__('HTML Compression Cache Expiration', 'zencache').'</h3>'."\n";
            echo '         <p><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_cache_expiration_time]" value="'.esc_attr($this->plugin->options['htmlc_cache_expiration_time']).'" /></p>'."\n";
            echo '         <p class="info" style="display:block;">'.__('<strong>Tip:</strong> the value that you specify here MUST be compatible with PHP\'s <a href="http://php.net/manual/en/function.strtotime.php" target="_blank" style="text-decoration:none;"><code>strtotime()</code></a> function. Examples: <code>2 hours</code>, <code>7 days</code>, <code>6 months</code>, <code>1 year</code>.', 'zencache').'</p>'."\n";
            echo '         <p>'.sprintf(__('<strong>Note:</strong> This does NOT impact the overall cache expiration time that you configure with %1$s. It only impacts the sub-routines provided by the HTML Compressor. In fact, this expiration time is mostly irrelevant. The HTML Compressor uses an internal checksum, and it also checks <code>filemtime()</code> before using an existing cache file. The HTML Compressor class also handles the automatic cleanup of your cache directories to keep it from growing too large over time. Therefore, unless you have VERY little disk space there is no reason to set this to a lower value (even if your site changes dynamically quite often). If anything, you might like to increase this value which could help to further reduce server load. You can <a href="https://github.com/websharks/HTML-Compressor" target="_blank">learn more here</a>. We recommend setting this value to at least double that of your overall %1$s expiration time.', 'zencache'), esc_html(NAME)).'</p>'."\n";
            echo '      </div>'."\n";
            echo '   </div>'."\n";

            echo '</div>'."\n";
        }
        /* ----------------------------------------------------------------------------------------- */

        echo '<div class="plugin-menu-page-panel">'."\n";

        echo '   <a href="#" class="plugin-menu-page-panel-heading">'."\n";
        echo '      <i class="si si-file-archive-o"></i> '.__('GZIP Compression', 'zencache')."\n";
        echo '   </a>'."\n";

        echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
        echo '      <img src="'.esc_attr($this->plugin->url('/src/client-s/images/gzip.png')).'" class="screenshot" />'."\n";
        echo '      <h3>'.__('<a href="https://developers.google.com/speed/articles/gzip" target="_blank">GZIP Compression</a> (Optional; Highly Recommended)', 'zencache').'</h3>'."\n";
        echo '      <p>'.__('You don\'t have to use an <code>.htaccess</code> file to enjoy the performance enhancements provided by this plugin; caching is handled automatically by WordPress/PHP alone. That being said, if you want to take advantage of the additional speed enhancements associated w/ GZIP compression (and we do recommend this), then you WILL need an <code>.htaccess</code> file to accomplish that part.', 'zencache').'</p>'."\n";
        echo '      <p>'.sprintf(__('%1$s fully supports GZIP compression on its output. However, it does not handle GZIP compression directly. We purposely left GZIP compression out of this plugin, because GZIP compression is something that should really be enabled at the Apache level or inside your <code>php.ini</code> file. GZIP compression can be used for things like JavaScript and CSS files as well, so why bother turning it on for only WordPress-generated pages when you can enable GZIP at the server level and cover all the bases!', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p>'.__('If you want to enable GZIP, create an <code>.htaccess</code> file in your WordPress® installation directory, and put the following few lines in it. Alternatively, if you already have an <code>.htaccess</code> file, just add these lines to it, and that is all there is to it. GZIP is now enabled in the recommended way! See also: <a href="https://developers.google.com/speed/articles/gzip" target="_blank"><i class="si si-youtube-play"></i> video about GZIP Compression</a>.', 'zencache').'</p>'."\n";
        echo '      <pre class="code"><code>'.esc_html(file_get_contents(dirname(dirname(__FILE__)).'/templates/gzip-htaccess.txt')).'</code></pre>'."\n";
        echo '      <hr />'."\n";
        echo '      <p class="info" style="display:block;"><strong>Or</strong>, if your server is missing <code>mod_deflate</code>/<code>mod_filter</code>; open your <strong>php.ini</strong> file and add this line: <a href="http://php.net/manual/en/zlib.configuration.php" target="_blank" style="text-decoration:none;"><code>zlib.output_compression = on</code></a></p>'."\n";
        echo '   </div>'."\n";

        echo '</div>'."\n";

        /* ----------------------------------------------------------------------------------------- */

        if (IS_PRO || $this->plugin->isProPreview()) {
            echo '<div class="plugin-menu-page-panel'.(!IS_PRO ? ' pro-preview' : '').'">'."\n";

            echo '   <a href="#" class="plugin-menu-page-panel-heading'.(!IS_PRO ? ' pro-preview-feature' : '').'">'."\n";
            echo '      <i class="si si-cloud"></i> '.__('Static CDN Filters', 'zencache')."\n";
            echo '   </a>'."\n";

            echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
            echo '   <button type="button" class="plugin-menu-page-clear-cdn-cache" style="float:right; margin:0 0 1em 1em;" title="'.esc_attr(__('Clear CDN Cache (Bump CDN Invalidation Counter)', 'zencache')).'">'.__('Clear CDN Cache', 'zencache').' <img src="'.esc_attr($this->plugin->url('/src/client-s/images/clear.png')).'" style="width:16px; height:16px;" /></button>'."\n";
            echo '      <h3>'.__('Enable Static CDN Filters (e.g., MaxCDN/CloudFront)?', 'zencache').'</h3>'."\n";
            echo '      <p>'.sprintf(__('This feature allows you to serve some and/or ALL static files on your site from a CDN of your choosing. This is made possible through content/URL filters exposed by WordPress and implemented by %1$s. All it requires is that you setup a CDN host name sourced by your WordPress installation domain. You enter that CDN host name below and %1$s will do the rest! Super easy, and it doesn\'t require any DNS changes either. :-) Please <a href="http://zencache.com/r/static-cdn-filters-general-instructions/" target="_blank">click here</a> for a general set of instructions.', 'zencache'), esc_html(NAME)).'</p>'."\n";
            echo '      <p>'.__('<strong>What\'s a CDN?</strong> It\'s a Content Delivery Network (i.e., a network of optimized servers) designed to cache static resources served from your site (e.g., JS/CSS/images and other static files) onto it\'s own servers, which are located strategically in various geographic areas around the world. Integrating a CDN for static files can dramatically improve the speed and performance of your site, lower the burden on your own server, and reduce latency associated with visitors attempting to access your site from geographic areas of the world that might be very far away from the primary location of your own web servers.', 'zencache').'</p>'."\n";
            if ($is_nginx && $this->plugin->applyWpFilters(GLOBAL_NS.'_wp_htaccess_nginx_notice', true) && (!isset($_SERVER['WP_NGINX_CONFIG']) || $_SERVER['WP_NGINX_CONFIG'] !== 'done')) {
                echo '<div class="plugin-menu-page-notice error">'."\n";
                echo '   <i class="si si-thumbs-down"></i> '.__('It appears that your server is running NGINX and does not support <code>.htaccess</code> rules. Please <a href="http://zencache.com/r/kb-article-recommended-nginx-server-configuration/" target="_new">update your server configuration manually</a>. Note that updating your NGINX server configuration <em>before</em> enabling Static CDN Filters is recommended to prevent any <a href="http://zencache.com/r/kb-article-what-are-cross-origin-request-blocked-cors-errors/" target="_new">CORS errors</a> with your CDN. If you\'ve already updated your NGINX configuration, you can safely <a href="http://zencache.com/r/kb-article-how-do-i-disable-the-nginx-htaccess-notice/" target="_new">ignore this message</a>.', 'zencache')."\n";
                echo '</div>'."\n";
            }
            echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cdn_enable]" data-target=".-static-cdn-filter-options">'."\n";
            echo '            <option value="0"'.(!IS_PRO ? '' : selected($this->plugin->options['cdn_enable'], '0', false)).'>'.__('No, I do NOT want CDN filters applied at runtime.', 'zencache').'</option>'."\n";
            echo '            <option value="1"'.(!IS_PRO ? ' selected' : selected($this->plugin->options['cdn_enable'], '1', false)).'>'.__('Yes, I want CDN filters applied w/ my configuration below.', 'zencache').'</option>'."\n";
            echo '         </select></p>'."\n";

            echo '      <hr />'."\n";

            echo '      <div class="plugin-menu-page-panel-if-enabled -static-cdn-filter-options">'."\n";

            echo '         <h3>'.__('CDN Host Name (Required)', 'zencache').'</h3>'."\n";

            echo '         <p class="info" style="display:block;">'.// This note includes three graphics. One for MaxCDN; another for CloudFront, and another for KeyCDN.
             '              <a href="http://zencache.com/r/keycdn/" target="_blank"><img src="'.esc_attr($this->plugin->url('/src/client-s/images/keycdn-logo.png')).'" style="width:90px; float:right; margin: 18px 10px 0 18px;" /></a>'.
             '              <a href="http://zencache.com/r/amazon-cloudfront/" target="_blank"><img src="'.esc_attr($this->plugin->url('/src/client-s/images/cloudfront-logo.png')).'" style="width:75px; float:right; margin: 8px 10px 0 25px;" /></a>'.
             '              <a href="http://zencache.com/r/maxcdn/" target="_blank"><img src="'.esc_attr($this->plugin->url('/src/client-s/images/maxcdn-logo.png')).'" style="width:125px; float:right; margin: 20px 0 0 25px;" /></a>'.
             '            '.__('This field is really all that\'s necessary to get Static CDN Filters working! However, it does requires a little bit of work on your part. You need to setup and configure a CDN before you can fill in this field. Once you configure a CDN, you\'ll receive a host name (provided by your CDN), which you\'ll enter here; e.g., <code>js9dgjsl4llqpp.cloudfront.net</code>. We recommend <a href="http://zencache.com/r/maxcdn/" target="_blank">MaxCDN</a>, <a href="http://zencache.com/r/amazon-cloudfront/" target="_blank">Amazon CloudFront</a>, <a href="http://zencache.com/r/keycdn/" target="_blank">KeyCDN</a>, and/or <a href="http://zencache.com/r/cdn77/" target="_blank">CDN77</a> but this should work with many of the most popular CDNs. Please read <a href="http://zencache.com/r/static-cdn-filters-general-instructions/" target="_blank">this article</a> for a general set of instructions. We also have a <a href="http://zencache.com/r/static-cdn-filters-maxcdn/" target="_blank">MaxCDN tutorial</a>, <a href="http://zencache.com/r/static-cdn-filters-cloudfront/" target="_blank">CloudFront tutorial</a>, <a href="http://zencache.com/r/static-cdn-filters-keycdn/" target="_blank">KeyCDN tutorial</a>, and a <a href="http://zencache.com/r/static-cdn-filters-cdn77/" target="_blank">CDN77 tutorial</a> to walk you through the process.', 'zencache').'</p>'."\n";
            echo '         <p><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][cdn_host]" value="'.esc_attr($this->plugin->options['cdn_hosts'] ? '' : $this->plugin->options['cdn_host']).'"'.($this->plugin->options['cdn_hosts'] ? ' disabled="disabled"' : '').' /></p>'."\n";

            echo '         <hr />'."\n";

            echo '         <h3>'.__('Multiple CDN Host Names for Domain Sharding and Multisite Networks (Optional)', 'zencache').'</h3>'."\n";
            echo '         <p>'.sprintf(__('%1$s also supports multiple CDN Host Names for any given domain. Using multiple CDN Host Names (instead of just one, as seen above) is referred to as <strong><a href="http://zencache.com/r/domain-sharding/" target="_blank">Domain Sharding</a></strong> (<a href="http://zencache.com/r/domain-sharding/" target="_blank">click here to learn more</a>). If you configure multiple CDN Host Names (i.e., if you implement Domain Sharding), %1$s will use the first one that you list for static resources loaded in the HTML <code>&lt;head&gt;</code> section, the last one for static resources loaded in the footer, and it will choose one at random for all other static resource locations. Configuring multiple CDN Host Names can improve speed! This is a way for advanced site owners to work around concurrency limits in popular browsers; i.e., making it possible for browsers to download many more resources simultaneously, resulting in a faster overall completion time. In short, this tells the browser that your website will not be overloaded by concurrent requests, because static resources are in fact being served by a content-delivery network (i.e., multiple CDN host names). If you use this functionality for Domain Sharding, we suggest that you setup one CDN Distribution (aka: Pull Zone), and then create multiple CNAME records pointing to that distribution. You can enter each of your CNAMES in the field below, as instructed.', 'zencache'), esc_html(NAME)).'</p>'."\n";
            echo '         <p class="info" style="display:block;">'.sprintf(__('<strong>On WordPress Multisite Network installations</strong>, this field also allows you to configure different CDN Host Names for each domain (or sub-domain) that you run from a single installation of WordPress. For more information about configuring Static CDN Filters on a WordPress Multisite Network, see this tutorial: <a href="http://zencache.com/r/static-cdn-filters-for-wordpress-multisite-networks/" target="_blank">Static CDN Filters for WordPress Multisite Networks</a>.', 'zencache'), esc_html(NAME)).'</p>'."\n";
            echo '         <p style="margin-bottom:0;"><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][cdn_hosts]" rows="5" spellcheck="false" autocomplete="off" placeholder="'.esc_attr('e.g., '.$this->plugin->hostToken(false, true).' = cdn1.'.$this->plugin->hostToken(false, true).', cdn2.'.$this->plugin->hostToken(false, true).', cdn3.'.$this->plugin->hostToken(false, true)).'" wrap="off" style="white-space:nowrap;">'.esc_textarea($this->plugin->options['cdn_hosts']).'</textarea></p>'."\n";
            echo '         <p style="margin-top:0;">'.sprintf(__('<strong>↑ Syntax:</strong> This is a line-delimited list of domain mappings. Each line should start with your WordPress domain name (e.g., <code>%1$s</code>), followed by an <code>=</code> sign, followed by a comma-delimited list of CDN Host Names associated with the domain in that line. If you\'re running a Multisite Network installation of WordPress, you might have multiple configuration lines. Otherwise, you should only need one line to configure multiple CDN Host Names for a standard WordPress installation.', 'zencache'), esc_html($this->plugin->hostToken(false, true))).'</p>'."\n";

            echo '         <hr />'."\n";

            echo '         <h3>'.__('CDN Supports HTTPS Connections?', 'zencache').'</h3>'."\n";
            echo '         <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cdn_over_ssl]" autocomplete="off">'."\n";
            echo '                  <option value="0"'.selected($this->plugin->options['cdn_over_ssl'], '0', false).'>'.__('No, I don\'t serve content over https://; or I haven\'t configured my CDN w/ an SSL certificate.', 'zencache').'</option>'."\n";
            echo '                  <option value="1"'.selected($this->plugin->options['cdn_over_ssl'], '1', false).'>'.__('Yes, I\'ve configured my CDN w/ an SSL certificate; I need https:// enabled.', 'zencache').'</option>'."\n";
            echo '            </select></p>'."\n";

            echo '         <hr />'."\n";

            echo '         <h3 style="margin-bottom:0;">'.
                                '<a href="#" class="dotted" data-toggle-target=".'.esc_attr(GLOBAL_NS.'-static-cdn-filters--more-options').'">'.
                                    '<i class="si si-eye"></i> '.__('Additional Options (For Advanced Users)', 'zencache').' <i class="si si-eye"></i>'.
                                '</a>'.
                           '</h3>'."\n";

            echo '         <div class="'.esc_attr(GLOBAL_NS.'-static-cdn-filters--more-options').'" style="'.(!IS_PRO ? '' : 'display:none; ').'margin-top:1em;">'."\n";
            echo '              <p class="info" style="display:block;">'.__('Everything else below is 100% completely optional; i.e., not required to enjoy the benefits of Static CDN Filters.', 'zencache').'</p>'."\n";

            echo '              <hr />'."\n";

            echo '              <h3>'.__('Whitelisted File Extensions (Optional; Comma-Delimited)', 'zencache').'</h3>'."\n";
            echo '              <p><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][cdn_whitelisted_extensions]" value="'.esc_attr($this->plugin->options['cdn_whitelisted_extensions']).'" /></p>'."\n";
            echo '              <p>'.__('If you leave this empty a default set of extensions are taken from WordPress itself. The default set of whitelisted file extensions includes everything supported by the WordPress media library.', 'zencache').(IS_PRO ? ' '.__('This includes the following: <code style="white-space:normal; word-wrap:break-word;">'.esc_html(implode(',', CdnFilters::defaultWhitelistedExtensions())).'</code>', 'zencache') : '').'</p>'."\n";

            echo '              <h3>'.__('Blacklisted File Extensions (Optional; Comma-Delimited)', 'zencache').'</h3>'."\n";
            echo '              <p><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][cdn_blacklisted_extensions]" value="'.esc_attr($this->plugin->options['cdn_blacklisted_extensions']).'" /></p>'."\n";
            echo '              <p>'.__('With or without a whitelist, you can force exclusions by explicitly blacklisting certain file extensions of your choosing. Please note, the <code>php</code> extension will never be considered a static resource; i.e., it is automatically blacklisted at all times.', 'zencache').'</p>'."\n";

            echo '              <hr />'."\n";

            echo '              <h3>'.__('Whitelisted URI Inclusion Patterns (Optional; One Per Line)', 'zencache').'</h3>'."\n";
            echo '              <p><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][cdn_whitelisted_uri_patterns]" rows="5" spellcheck="false" class="monospace">'.format_to_edit($this->plugin->options['cdn_whitelisted_uri_patterns']).'</textarea></p>'."\n";
            echo '              <p class="info" style="display:block;">'.__('<strong>Note:</strong> please remember that your entries here should be formatted as a line-delimited list; e.g., one inclusion pattern per line.', 'zencache').'</p>'."\n";
            echo '              <p>'.__('If provided, only local URIs matching one of the patterns you list here will be served from your CDN Host Name. URI patterns are caSe-insensitive. A wildcard <code>*</code> will match zero or more characters in any of your patterns. A caret <code>^</code> symbol will match zero or more characters that are NOT the <code>/</code> character. For instance, <code>*/wp-content/*</code> here would indicate that you only want to filter URLs that lead to files located inside the <code>wp-content</code> directory. Adding an additional line with <code>*/wp-includes/*</code> would filter URLs in the <code>wp-includes</code> directory also. <strong>If you leave this empty</strong>, ALL files matching a static file extension will be served from your CDN; i.e., the default behavior.', 'zencache').'</p>'."\n";
            echo '              <p>'.__('Please note that URI patterns are tested against a file\'s path (i.e., a file\'s URI, and NOT its full URL). A URI always starts with a leading <code>/</code>. To clarify, a URI is the portion of the URL which comes after the host name. For instance, given the following URL: <code>http://example.com/path/to/style.css?ver=3</code>, the URI you are matching against would be: <code>/path/to/style.css?ver=3</code>. To whitelist this URI, you could use a line that contains something like this: <code>/path/to/*.css*</code>', 'zencache').'</p>'."\n";

            echo '              <h3>'.__('Blacklisted URI Exclusion Patterns (Optional; One Per Line)', 'zencache').'</h3>'."\n";
            echo '              <p><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][cdn_blacklisted_uri_patterns]" rows="5" spellcheck="false" class="monospace">'.format_to_edit($this->plugin->options['cdn_blacklisted_uri_patterns']).'</textarea></p>'."\n";
            echo '              <p>'.__('With or without a whitelist, you can force exclusions by explicitly blacklisting certain URI patterns. URI patterns are caSe-insensitive. A wildcard <code>*</code> will match zero or more characters in any of your patterns. A caret <code>^</code> symbol will match zero or more characters that are NOT the <code>/</code> character. For instance, <code>*/wp-content/*/dynamic.pdf*</code> would exclude a file with the name <code>dynamic.pdf</code> located anywhere inside a sub-directory of <code>wp-content</code>.', 'zencache').'</p>'."\n";
            echo '              <p class="info" style="display:block;">'.__('<strong>Note:</strong> please remember that your entries here should be formatted as a line-delimited list; e.g., one exclusion pattern per line.', 'zencache').'</p>'."\n";

            echo '              <hr />'."\n";

            echo '              <h3>'.__('Query String Invalidation Variable Name', 'zencache').'</h3>'."\n";
            echo '              <p><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][cdn_invalidation_var]" value="'.esc_attr($this->plugin->options['cdn_invalidation_var']).'" /></p>'."\n";
            echo '              <p>'.sprintf(__('Each filtered URL (which then leads to your CDN) will include this query string variable as an easy way to invalidate the CDN cache at any time. Invalidating the CDN cache is simply a matter of changing the global invalidation counter (i.e., the value assigned to this query string variable). %1$s manages invalidations automatically; i.e., %1$s will automatically bump an internal counter each time you upgrade a WordPress component (e.g., a plugin, theme, or WP itself). Or, if you ask %1$s to invalidate the CDN cache (e.g., a manual clearing of the CDN cache); the internal counter is bumped then too. In short, %1$s handles cache invalidations for you reliably. This option simply allows you to customize the query string variable name which makes cache invalidations possible. <strong>Please note, the default value is adequate for most sites. You can change this if you like, but it\'s not necessary.</strong>', 'zencache'), esc_html(NAME)).'</p>'."\n";
            echo '              <p class="info" style="display:block;">'.sprintf(__('<strong>Tip:</strong> You can also tell %1$s to automatically bump the CDN Invalidation Counter whenever you clear the cache manually. See: <strong>%1$s → Manual Cache Clearing → Clear the CDN Cache Too?</strong>', 'zencache'), esc_html(NAME)).'</p>'."\n";
            echo '              <p class="info" style="display:block;">'.sprintf(__('<strong>Note:</strong> If you empty this field, it will effectively disable the %1$s invalidation system for Static CDN Filters; i.e., the query string variable will NOT be included if you do not supply a variable name.', 'zencache'), esc_html(NAME)).'</p>'."\n";
            echo '         </div>'."\n";
            echo '      </div>'."\n";
            echo '   </div>'."\n";

            echo '</div>'."\n";
        }
        /* ----------------------------------------------------------------------------------------- */

        if (IS_PRO || $this->plugin->isProPreview()) {
            echo '<div class="plugin-menu-page-panel'.(!IS_PRO ? ' pro-preview' : '').'">'."\n";

            echo '   <a href="#" class="plugin-menu-page-panel-heading'.(!IS_PRO ? ' pro-preview-feature' : '').'">'."\n";
            echo '      <i class="si si-octi-versions"></i> '.__('Dynamic Version Salt', 'zencache')."\n";
            echo '   </a>'."\n";

            echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
            echo '      <img src="'.esc_attr($this->plugin->url('/src/client-s/images/salt.png')).'" class="screenshot" />'."\n";
            echo '      <h3>'.__('<i class="si si-flask"></i> <span style="display:inline-block; padding:5px; border-radius:3px; background:#FFFFFF; color:#354913;"><span style="font-weight:bold; font-size:80%;">GEEK ALERT</span></span> This is for VERY advanced users only...', 'zencache').'</h3>'."\n";
            echo '      <p>'.sprintf(__('<em>Note: Understanding the %1$s <a href="http://zencache.com/r/kb-branched-cache-structure/" target="_blank">Branched Cache Structure</a> is a prerequisite to understanding how Dynamic Version Salts are added to the mix.</em>', 'zencache'), esc_html(NAME)).'</p>'."\n";
            echo '      <p>'.__('A Version Salt gives you the ability to dynamically create multiple variations of the cache, and those dynamic variations will be served on subsequent visits; e.g., if a visitor has a specific cookie (of a certain value) they will see pages which were cached with that version (i.e., w/ that Version Salt: the value of the cookie). A Version Salt can really be anything.', 'zencache').'</p>'."\n";
            echo '      <p>'.__('A Version Salt can be a single variable like <code>$_COOKIE[\'my_cookie\']</code>, or it can be a combination of multiple variables, like <code>$_COOKIE[\'my_cookie\'].$_COOKIE[\'my_other_cookie\']</code>. (When using multiple variables, please separate them with a dot, as shown in the example.)', 'zencache').'</p>'."\n";
            echo '      <p>'.__('Experts could even use PHP ternary expressions that evaluate into something. For example: <code>((preg_match(\'/iPhone/i\', $_SERVER[\'HTTP_USER_AGENT\'])) ? \'iPhones\' : \'\')</code>. This would force a separate version of the cache to be created for iPhones (e.g., <code>/cache/PROTOCOL/HOST/REQUEST-URI.v/iPhones.html</code>).', 'zencache').'</p>'."\n";
            echo '      <p>'.__('For more documentation, please see <a href="http://zencache.com/r/kb-dynamic-version-salts/" target="_blank">Dynamic Version Salts</a>.', 'zencache').'</p>'."\n";
            echo '      <hr />'."\n";
            echo '      <h3>'.sprintf(__('Create a Dynamic Version Salt For %1$s? &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-size:90%%; opacity:0.5;">150%% OPTIONAL</span>', 'zencache'), esc_html(NAME)).'</h3>'."\n";
            echo '      <table style="width:100%;"><tr><td style="width:1px; font-weight:bold; white-space:nowrap;">/cache/PROTOCOL/HOST/REQUEST_URI.</td><td><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][version_salt]" value="'.esc_attr($this->plugin->options['version_salt']).'" class="monospace" placeholder="$_COOKIE[\'my_cookie\']" /></td><td style="width:1px; font-weight:bold; white-space:nowrap;"></td></tr></table>'."\n";
            echo '      <p class="info" style="display:block;">'.__('<a href="http://php.net/manual/en/language.variables.superglobals.php" target="_blank">Super Globals</a> work here; <a href="http://codex.wordpress.org/Editing_wp-config.php#table_prefix" target="_blank"><code>$GLOBALS[\'table_prefix\']</code></a> is a popular one.<br />Or, perhaps a PHP Constant defined in <code>/wp-config.php</code>; such as <code>WPLANG</code> or <code>DB_HOST</code>.', 'zencache').'</p>'."\n";
            echo '      <p class="notice" style="display:block;">'.__('<strong>Important:</strong> your Version Salt is scanned for PHP syntax errors via <a href="http://phpcodechecker.com/" target="_blank"><code>phpCodeChecker.com</code></a>. If errors are found, you\'ll receive a notice in the Dashboard.', 'zencache').'</p>'."\n";
            echo '      <p class="info" style="display:block;">'.__('If you\'ve enabled a separate cache for each user (optional) that\'s perfectly OK. A Version Salt works with user caching too.', 'zencache').'</p>'."\n";
            echo '   </div>'."\n";

            echo '</div>'."\n";
        }
        /* ----------------------------------------------------------------------------------------- */

        echo '<div class="plugin-menu-page-panel">'."\n";

        echo '   <a href="#" class="plugin-menu-page-panel-heading">'."\n";
        echo '      <i class="si si-octi-plug"></i> '.__('Theme/Plugin Developers', 'zencache')."\n";
        echo '   </a>'."\n";

        echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
        echo '      <i class="si si-puzzle-piece si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
        echo '      <h3>'.__('Developing a Theme or Plugin for WordPress?', 'zencache').'</h3>'."\n";
        echo '      <p>'.sprintf(__('<strong>Tip:</strong> %1$s can be disabled temporarily. If you\'re a theme/plugin developer, you can set a flag within your PHP code to disable the cache engine at runtime. Perhaps on a specific page, or in a specific scenario. In your PHP script, set: <code>$_SERVER[\'ZENCACHE_ALLOWED\'] = FALSE;</code> or <code>define(\'ZENCACHE_ALLOWED\', FALSE)</code>. %1$s is also compatible with: <code>define(\'DONOTCACHEPAGE\', TRUE)</code>. It does\'t matter where or when you define one of these, because %1$s is the last thing to run before script execution ends.', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '      <hr />'."\n";
        echo '      <h3>'.sprintf(__('Writing "Advanced Cache" Plugins Specifically for %1$s', 'zencache'), esc_html(NAME)).'</h3>'."\n";
        echo '      <p>'.sprintf(__('Theme/plugin developers can take advantage of the %1$s plugin architecture by creating PHP files inside this special directory: <code>/wp-content/ac-plugins/</code>. There is an <a href="http://zencache.com/r/ac-plugin-example/" target="_blank">example plugin file @ GitHub</a> (please review it carefully and ask questions). If you develop a plugin for %1$s, please share it with the community by publishing it in the plugins respository at WordPress.org.', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p class="info">'.sprintf(__('<strong>Why does %1$s have it\'s own plugin architecture?</strong> WordPress loads the <code>advanced-cache.php</code> drop-in file (for caching purposes) very early-on; before any other plugins or a theme. For this reason, %1$s implements it\'s own watered-down version of functions like <code>add_action()</code>, <code>do_action()</code>, <code>add_filter()</code>, <code>apply_filters()</code>.', 'zencache'), esc_html(NAME)).'</p>'."\n";
        echo '   </div>'."\n";

        echo '</div>'."\n";

        /* ----------------------------------------------------------------------------------------- */

        if (IS_PRO || $this->plugin->isProPreview()) {
            echo '<div class="plugin-menu-page-panel'.(!IS_PRO ? ' pro-preview' : '').'">'."\n";

            echo '   <a href="#" class="plugin-menu-page-panel-heading'.(!IS_PRO ? ' pro-preview-feature' : '').'">'."\n";
            echo '      <i class="si si-arrow-circle-o-up"></i> '.__('Import/Export Options', 'zencache')."\n";
            echo '   </a>'."\n";

            echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
            echo '      <i class="si si-arrow-circle-o-up si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
            echo '      <h3>'.sprintf(__('Import Options from Another %1$s Installation?', 'zencache'), esc_html(NAME)).'</h3>'."\n";
            echo '      <p>'.sprintf(__('Upload your <code>%1$s-options.json</code> file and click "Save All Changes" below. The options provided by your import file will override any that exist currently.', 'zencache'), GLOBAL_NS).'</p>'."\n";
            echo '      <p><input type="file" name="'.esc_attr(GLOBAL_NS).'[import_options]" /></p>'."\n";
            echo '      <hr />'."\n";
            echo '      <h3>'.sprintf(__('Export Existing Options from this %1$s Installation?', 'zencache'), esc_html(NAME)).'</h3>'."\n";
            echo '      <button type="button" class="plugin-menu-page-export-options" style="float:right; margin: 0 0 0 25px;"'.// Exports existing options from this installation.
             '         data-action="'.esc_attr(add_query_arg(urlencode_deep(array('page' => GLOBAL_NS, '_wpnonce' => wp_create_nonce(), GLOBAL_NS => array('exportOptions' => '1'))), self_admin_url('/admin.php'))).'">'.
             '         '.sprintf(__('%1$s-options.json', 'zencache'), GLOBAL_NS).' <i class="si si-arrow-circle-o-down"></i></button>'."\n";
            echo '      <p>'.sprintf(__('Download your existing options and import them all into another %1$s installation; saves time on future installs.', 'zencache'), esc_html(NAME)).'</p>'."\n";
            echo '   </div>'."\n";

            echo '</div>'."\n";
        }
        /* ----------------------------------------------------------------------------------------- */

        echo '<div class="plugin-menu-page-save">'."\n";
        echo '   <button type="submit">'.__('Save All Changes', 'zencache').' <i class="si si-save"></i></button>'."\n";
        echo '</div>'."\n";

        /* ----------------------------------------------------------------------------------------- */

        echo '</div>'."\n";
        echo '</form>';
    }
}
