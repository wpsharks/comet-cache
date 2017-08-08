<?php
namespace WebSharks\CometCache\Classes\MenuPage\Options;

use WebSharks\CometCache\Classes;

/**
 * Options section.
 *
 * @since 17xxxx Refactor menu pages.
 */
class Notices extends Classes\AbsBase
{
    /**
     * Constructor.
     *
     * @since 17xxxx Refactor menu pages.
     */
    public function __construct()
    {
        parent::__construct(); // Parent constructor.

        if (!empty($_REQUEST[GLOBAL_NS.'_updated'])) {
            echo '<div class="plugin-menu-page-notice notice">'."\n";
            echo '   <i class="si si-thumbs-up"></i> '.__('Options updated successfully.', 'comet-cache')."\n";
            echo '</div>'."\n";
        }
        if (!empty($_REQUEST[GLOBAL_NS.'_restored'])) {
            echo '<div class="plugin-menu-page-notice notice">'."\n";
            echo '   <i class="si si-thumbs-up"></i> '.__('Default options successfully restored.', 'comet-cache')."\n";
            echo '</div>'."\n";
        }
        if (!empty($_REQUEST[GLOBAL_NS.'_cache_wiped'])) {
            echo '<div class="plugin-menu-page-notice notice">'."\n";
            echo '   <img src="'.esc_attr($this->plugin->url('/src/client-s/images/wipe.png')).'" /> '.__('Cache wiped across all sites; re-creation will occur automatically over time.', 'comet-cache')."\n";
            echo '</div>'."\n";
        }
        if (!empty($_REQUEST[GLOBAL_NS.'_cache_cleared'])) {
            echo '<div class="plugin-menu-page-notice notice">'."\n";
            if (is_multisite() && is_main_site()) {
                echo '<img src="'.esc_attr($this->plugin->url('/src/client-s/images/clear.png')).'" /> '.__('Cache cleared for main site; re-creation will occur automatically over time.', 'comet-cache')."\n";
            } else {
                echo '<img src="'.esc_attr($this->plugin->url('/src/client-s/images/clear.png')).'" /> '.__('Cache cleared for this site; re-creation will occur automatically over time.', 'comet-cache')."\n";
            }
            echo '</div>'."\n";
        }
        if (!empty($_REQUEST[GLOBAL_NS.'_wp_htaccess_add_failure'])) {
            echo '<div class="plugin-menu-page-notice error">'."\n";
            echo '   <i class="si si-thumbs-down"></i> '.sprintf(__('Failed to update your <code>/.htaccess</code> file automatically. Most likely a permissions error. Please make sure it has permissions <code>644</code> or higher (perhaps <code>666</code>). Once you\'ve done this, please try saving the %1$s options again.', 'comet-cache'), esc_html(NAME))."\n";
            echo '</div>'."\n";
        }
        if (!empty($_REQUEST[GLOBAL_NS.'_wp_htaccess_remove_failure'])) {
            echo '<div class="plugin-menu-page-notice error">'."\n";
            echo '   <i class="si si-thumbs-down"></i> '.sprintf(__('Failed to update your <code>/.htaccess</code> file automatically. Most likely a permissions error. Please make sure it has permissions <code>644</code> or higher (perhaps <code>666</code>). Once you\'ve done this, please try saving the %1$s options again.', 'comet-cache'), esc_html(NAME))."\n";
            echo '</div>'."\n";
        }
        if (!empty($_REQUEST[GLOBAL_NS.'_wp_htaccess_nginx_notice'])) {
            echo '<div class="plugin-menu-page-notice error">'."\n";
            echo '   <i class="si si-thumbs-down"></i> '.__('It appears that your server is running NGINX and does not support <code>.htaccess</code> rules. Please <a href="http://cometcache.com/r/kb-article-recommended-nginx-server-configuration/" target="_new">update your server configuration manually</a>. If you\'ve already updated your NGINX configuration, you can safely <a href="http://cometcache.com/r/kb-article-how-do-i-disable-the-nginx-htaccess-notice/" target="_new">ignore this message</a>.', 'comet-cache')."\n";
            echo '</div>'."\n";
        }
        if (!empty($_REQUEST[GLOBAL_NS.'_wp_config_wp_cache_add_failure'])) {
            echo '<div class="plugin-menu-page-notice error">'."\n";
            echo '   <i class="si si-thumbs-down"></i> '.__('Failed to update your <code>/wp-config.php</code> file automatically. Please add the following line to your <code>/wp-config.php</code> file (right after the opening <code>&lt;?php</code> tag; on it\'s own line). <pre class="code"><code>define( \'WP_CACHE\', true );</code></pre>', 'comet-cache')."\n";
            echo '</div>'."\n";
        }
        if (!empty($_REQUEST[GLOBAL_NS.'_wp_config_wp_cache_remove_failure'])) {
            echo '<div class="plugin-menu-page-notice error">'."\n";
            echo '   <i class="si si-thumbs-down"></i> '.__('Failed to update your <code>/wp-config.php</code> file automatically. Please remove the following line from your <code>/wp-config.php</code> file, or set <code>WP_CACHE</code> to a <code>FALSE</code> value. <pre class="code"><code>define( \'WP_CACHE\', true );</code></pre>', 'comet-cache')."\n";
            echo '</div>'."\n";
        }
        if (!empty($_REQUEST[GLOBAL_NS.'_advanced_cache_add_failure'])) {
            echo '<div class="plugin-menu-page-notice error">'."\n";
            if ($_REQUEST[GLOBAL_NS.'_advanced_cache_add_failure'] === 'advanced-cache') {
                echo '<i class="si si-thumbs-down"></i> '.sprintf(__('Failed to update your <code>/wp-content/advanced-cache.php</code> file. Cannot write file: <code>%1$s/%2$s-advanced-cache</code>. Please be sure this directory exists (and that it\'s writable): <code>%1$s</code>. Please use directory permissions <code>755</code> or higher (perhaps <code>777</code>). Once you\'ve done this, please try again.', 'comet-cache'), esc_html($this->plugin->cacheDir()), esc_html(mb_strtolower(SHORT_NAME)))."\n";
            } else {
                echo '<i class="si si-thumbs-down"></i> '.__('Failed to update your <code>/wp-content/advanced-cache.php</code> file. Most likely a permissions error. Please create an empty file here: <code>/wp-content/advanced-cache.php</code> (just an empty PHP file, with nothing in it); give it permissions <code>644</code> or higher (perhaps <code>666</code>). Once you\'ve done this, please try again.', 'comet-cache')."\n";
            }
            echo '</div>'."\n";
        }
        if (!empty($_REQUEST[GLOBAL_NS.'_advanced_cache_remove_failure'])) {
            echo '<div class="plugin-menu-page-notice error">'."\n";
            echo '   <i class="si si-thumbs-down"></i> '.__('Failed to remove your <code>/wp-content/advanced-cache.php</code> file. Most likely a permissions error. Please delete (or empty the contents of) this file: <code>/wp-content/advanced-cache.php</code>.', 'comet-cache')."\n";
            echo '</div>'."\n";
        }
        if (!empty($_REQUEST[GLOBAL_NS.'_ua_info_dir_population_failure'])) {
            echo '<div class="plugin-menu-page-notice error">'."\n";
            echo '   <i class="si si-thumbs-down"></i> '.sprintf(__('Failed to populate User-Agent detection files for Mobile-Adaptive Mode. User-Agent detection files are pulled from a remote location so you\'ll always have the most up-to-date information needed for accurate detection. However, it appears the remote source of this information is currently unvailable. Please wait 15 minutes, then try saving your %1$s options again.', 'comet-cache'), esc_html(NAME))."\n";
            echo '</div>'."\n";
        }
        if (!IS_PRO && $this->plugin->isProPreview()) {
            echo '<div class="plugin-menu-page-notice info">'."\n";
            echo '<a href="'.add_query_arg(urlencode_deep(['page' => GLOBAL_NS]), self_admin_url('/admin.php')).'" class="pull-right" style="margin:0 0 15px 25px; float:right; font-variant:small-caps; text-decoration:none;">'.__('close', 'comet-cache').' <i class="si si-eye-slash"></i></a>'."\n";
            echo '   <i class="si si-eye"></i> '.sprintf(__('<strong>Pro Features (Preview)</strong> ~ New option panels below. Please explore before <a href="http://cometcache.com/prices/" target="_blank">upgrading <i class="si si-heart-o"></i></a>.<br /><small>NOTE: the free version of %1$s (this lite version) is more-than-adequate for most sites. Please upgrade only if you desire advanced features or would like to support the developer.</small>', 'comet-cache'), esc_html(NAME))."\n";
            echo '</div>'."\n";
        }
        if (!$this->plugin->options['enable']) {
            echo '<div class="plugin-menu-page-notice warning">'."\n";
            echo '   <i class="si si-warning"></i> '.sprintf(__('%1$s is currently disabled; please review options below.', 'comet-cache'), esc_html(NAME))."\n";
            echo '</div>'."\n";
        }
    }
}
