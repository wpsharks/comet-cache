<?php
namespace WebSharks\CometCache\Traits\Plugin;

use WebSharks\CometCache\Classes;

trait AdminBarUtils
{
    /**
     * Showing admin bar.
     *
     * @since 151002 Improving admin bar.
     *
     * @param bool $feature Check something specific?
     *
     * @return bool True if showing.
     */
    public function adminBarShowing($feature = '')
    {
        $feature = trim(mb_strtolower((string) $feature));
        if (!is_null($showing = &$this->cacheKey('adminBarShowing', $feature))) {
            return $showing; // Already cached this.
        }
        $is_multisite = is_multisite(); // Call this once only.
        if (($showing = $this->options['enable'] && is_admin_bar_showing())) {
            switch ($feature) {
                case 'cache_wipe':
                    $showing = $this->options['cache_clear_admin_bar_enable'] && $is_multisite;
                    break;
                case 'cache_clear':
                

                default: // Default case handler.
                    $showing = ($this->options['cache_clear_admin_bar_enable'] && $is_multisite)
                               
                               || ($this->options['cache_clear_admin_bar_enable'] && (!$is_multisite || !is_network_admin() || $this->isMenuPage(GLOBAL_NS.'*')));
                    break;
            }
        }
        if ($showing) {
            $current_user_can_wipe_cache  = $is_multisite && current_user_can($this->network_cap);
            $current_user_can_clear_cache = $this->currentUserCanClearCache();
            
            switch ($feature) {
                case 'cache_wipe':
                    $showing = $current_user_can_wipe_cache;
                    break;
                case 'cache_clear':
                
                default: // Default case handler.
                    $showing = $current_user_can_wipe_cache
                               
                               || $current_user_can_clear_cache;

                    break;
            }
        }
        return $showing;
    }
    /**
     * Filter WordPress admin bar.
     *
     * @since 150422 Rewrite.
     *
     * @attaches-to `admin_bar_menu` hook.
     *
     * @param $wp_admin_bar \WP_Admin_Bar
     */
    public function adminBarMenu(\WP_Admin_Bar &$wp_admin_bar)
    {
        if (!$this->adminBarShowing()) {
            return; // Nothing to do.
        }
        if ($this->adminBarShowing('cache_wipe')) {
            $wp_admin_bar->add_menu(
                [
                    'parent' => 'top-secondary',
                    'id'     => GLOBAL_NS.'-wipe',
                    'title'  => __('Wipe', 'comet-cache'),
                    'href'   => '#',
                    'meta'   => [
                        'title'    => __('Wipe Cache (Start Fresh). Clears the cache for all sites in this network at once!', 'comet-cache'),
                        'class'    => '-wipe',
                        'tabindex' => -1,
                    ],
                ]
            );
        }
        if ($this->adminBarShowing('cache_clear')) {
            
            $wp_admin_bar->add_menu(
                [
                    'parent' => 'top-secondary',
                    'id'     => GLOBAL_NS.'-clear',
                    'title'  => __('Clear Cache', 'comet-cache'),
                    'href'   => '#',
                    'meta'   => [
                        'title' => is_multisite() && current_user_can($this->network_cap)
                            ? __('Clear Cache (Start Fresh). Affects the current site only.', 'comet-cache')
                            : '',
                        'class'    => '-clear',
                        'tabindex' => -1,
                    ],
                ]
            );
            
        }
        
    }
    /**
     * Injects `<meta>` tag w/ JSON-encoded data.
     *
     * @since 150422 Rewrite.
     *
     * @attaches-to `admin_head` hook.
     */
    public function adminBarMetaTags()
    {
        if (!$this->adminBarShowing()) {
            return; // Nothing to do.
        }
        $vars = [
            '_wpnonce'                 => wp_create_nonce(),
            'isMultisite'              => is_multisite(),
            'currentUserHasCap'        => current_user_can($this->cap),
            'currentUserHasNetworkCap' => current_user_can($this->network_cap),
            'htmlCompressorEnabled'    => (bool) $this->options['htmlc_enable'],
            'ajaxURL'                  => site_url('/wp-load.php', is_ssl() ? 'https' : 'http'),
            'i18n'                     => [
                'name'             => NAME,
                'perSymbol'        => __('%', 'comet-cache'),
                'file'             => __('file', 'comet-cache'),
                'files'            => __('files', 'comet-cache'),
                'pageCache'        => __('Page Cache', 'comet-cache'),
                'htmlCompressor'   => __('HTML Compressor', 'comet-cache'),
                'currentTotal'     => __('Current Total', 'comet-cache'),
                'currentSite'      => __('Current Site', 'comet-cache'),
                'xDayHigh'         => __('%s Day High', 'comet-cache'),
                'enterSpecificUrl' => __('Enter a specific URL to clear the cache for that page:', 'comet-cache'),
            ],
        ];
        echo '<meta property="'.esc_attr(GLOBAL_NS).':admin-bar-vars" content="data-json"'.
             ' data-json="'.esc_attr(json_encode($vars)).'" id="'.esc_attr(GLOBAL_NS).'-admin-bar-vars" />'."\n";
    }
    /**
     * Adds CSS for WordPress admin bar.
     *
     * @since 150422 Rewrite.
     *
     * @attaches-to `wp_enqueue_scripts` hook.
     * @attaches-to `admin_enqueue_scripts` hook.
     */
    public function adminBarStyles()
    {
        if (!$this->adminBarShowing()) {
            return; // Nothing to do.
        }
        $deps = []; // Plugin dependencies.
        wp_enqueue_style(GLOBAL_NS.'-admin-bar', $this->url('/src/client-s/css/admin-bar.min.css'), $deps, VERSION, 'all');
    }
    /**
     * Adds JS for WordPress admin bar.
     *
     * @since 150422 Rewrite.
     *
     * @attaches-to `wp_enqueue_scripts` hook.
     * @attaches-to `admin_enqueue_scripts` hook.
     */
    public function adminBarScripts()
    {
        if (!$this->adminBarShowing()) {
            return; // Nothing to do.
        }
        $deps = ['jquery', 'admin-bar']; // Plugin dependencies.

        if (IS_PRO && $this->adminBarShowing('stats')) {
            $deps[] = 'chartjs'; // Add ChartJS dependency.
            wp_enqueue_script('chartjs', set_url_scheme('//cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js'), [], null, true);
        }
        wp_enqueue_script(GLOBAL_NS.'-admin-bar', $this->url('/src/client-s/js/admin-bar.min.js'), $deps, VERSION, true);
    }
}
