<?php
namespace WebSharks\ZenCache;

/*
 * Adds CSS for administrative menu pages.
 *
 * @since 150422 Rewrite.
 *
 * @attaches-to `admin_enqueue_scripts` hook.
 */
$self->enqueueAdminStyles = function () use ($self) {
    if (empty($_GET['page']) || strpos($_GET['page'], GLOBAL_NS) !== 0) {
        return; // NOT a plugin page in the administrative area.
    }
    $deps = array(); // Plugin dependencies.

    wp_enqueue_style(GLOBAL_NS, $self->url('/src/client-s/css/menu-pages.min.css'), $deps, VERSION, 'all');
};

/*
 * Adds JS for administrative menu pages.
 *
 * @since 150422 Rewrite.
 *
 * @attaches-to `admin_enqueue_scripts` hook.
 */
$self->enqueueAdminScripts = function () use ($self) {
    if (empty($_GET['page']) || strpos($_GET['page'], GLOBAL_NS) !== 0) {
        return; // NOT a plugin page in the administrative area.
    }
    $deps = array('jquery', 'chartjs'); // Plugin dependencies.

    wp_enqueue_script('chartjs', set_url_scheme('//cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js'), array(), null, true);
    wp_enqueue_script(GLOBAL_NS, $self->url('/src/client-s/js/menu-pages.min.js'), $deps, VERSION, true);
    wp_localize_script(GLOBAL_NS, GLOBAL_NS.'_menu_page_vars', array(
        '_wpnonce'                 => wp_create_nonce(),
        'isMultisite'              => is_multisite(), // Network?
        'currentUserHasCap'        => current_user_can($self->cap),
        'currentUserHasNetworkCap' => current_user_can($self->network_cap),
        'htmlCompressorEnabled'    => (boolean) $self->options['htmlc_enable'],
        'ajaxURL'                  => site_url('/wp-load.php', is_ssl() ? 'https' : 'http'),
        'emptyStatsCountsImageUrl' => $self->url('/src/client-s/images/stats-fc-empty.png'),
        'emptyStatsFilesImageUrl' => $self->url('/src/client-s/images/stats-fs-empty.png'),
        'i18n'                     => array(
            'name'           => NAME,
            'perSymbol'      => __('%', 'zencache'),
            'file'           => __('file', 'zencache'),
            'files'          => __('files', 'zencache'),
            'pageCache'      => __('Page Cache', 'zencache'),
            'htmlCompressor' => __('HTML Compressor', 'zencache'),
            'currentTotal'   => __('Current Total', 'zencache'),
            'currentSite'    => __('Current Site', 'zencache'),
            'xDayHigh'       => __('%s Day High', 'zencache'),
        ),
    ));
};

/*
 * Creates network admin menu pages.
 *
 * @since 150422 Rewrite.
 *
 * @attaches-to `network_admin_menu` hook.
 */
$self->addNetworkMenuPages = function () use ($self) {
    if (!is_multisite()) {
        return; // Not applicable.
    }
    $icon = file_get_contents(dirname(dirname(dirname(dirname(__FILE__)))).'/client-s/images/inline-icon.svg');
    $icon = 'data:image/svg+xml;base64,'.base64_encode($self->colorSvgMenuIcon($icon));

    add_menu_page(NAME, NAME, $self->network_cap, GLOBAL_NS, array($self, 'menuPageOptions'), $icon);
    add_submenu_page(GLOBAL_NS, __('Plugin Options', 'zencache'), __('Plugin Options', 'zencache'), $self->network_cap, GLOBAL_NS, array($self, 'menuPageOptions'));

    

    
};

/*
 * Creates admin menu pages.
 *
 * @since 150422 Rewrite.
 *
 * @attaches-to `admin_menu` hook.
 */
$self->addMenuPages = function () use ($self) {
    if (is_multisite()) {
        return; // Multisite networks MUST use network admin area.
    }
    $icon = file_get_contents(dirname(dirname(dirname(dirname(__FILE__)))).'/client-s/images/inline-icon.svg');
    $icon = 'data:image/svg+xml;base64,'.base64_encode($self->colorSvgMenuIcon($icon));

    add_menu_page(NAME, NAME, $self->cap, GLOBAL_NS, array($self, 'menuPageOptions'), $icon);
    add_submenu_page(GLOBAL_NS, __('Plugin Options', 'zencache'), __('Plugin Options', 'zencache'), $self->cap, GLOBAL_NS, array($self, 'menuPageOptions'));

    

    
};

/*
 * Adds link(s) to ZenCache row on the WP plugins page.
 *
 * @since 150422 Rewrite.
 *
 * @attaches-to `plugin_action_links_'.plugin_basename(PLUGIN_FILE)` filter.
 *
 * @param array $links An array of the existing links provided by WordPress.
 *
 * @return array Revised array of links.
 */
$self->addSettingsLink = function ($links) use ($self) {
    if (is_multisite() && !is_network_admin()) {
        return $links;
    }

    $links[] = '<a href="'.esc_attr(add_query_arg(urlencode_deep(array('page' => GLOBAL_NS)), self_admin_url('/admin.php'))).'">'.__('Settings', 'zencache').'</a>';
    if (!IS_PRO) {
        $links[] = '<br/><a href="'.esc_attr(add_query_arg(urlencode_deep(array('page' => GLOBAL_NS, GLOBAL_NS.'_pro_preview' => '1')), self_admin_url('/admin.php'))).'">'.__('Preview Pro Features', 'zencache').'</a>';
        $links[] = '<a href="'.esc_attr('http://zencache.com/prices/').'" target="_blank">'.__('Upgrade', 'zencache').'</a>';
    }
    return $links;
};

/*
 * Fills menu page inline SVG icon color.
 *
 * @since 150422 Rewrite.
 *
 * @param string $svg Inline SVG icon markup.
 *
 * @return string Inline SVG icon markup.
 */
$self->colorSvgMenuIcon = function ($svg) use ($self) {
    if (!($color = get_user_option('admin_color'))) {
        $color = 'fresh'; // Default color scheme.
    }
    if (empty($self->wp_admin_icon_colors[$color])) {
        return $svg; // Not possible.
    }
    $icon_colors         = $self->wp_admin_icon_colors[$color];
    $use_icon_fill_color = $icon_colors['base']; // Default base.

    $current_pagenow = !empty($GLOBALS['pagenow']) ? $GLOBALS['pagenow'] : '';
    $current_page    = !empty($_REQUEST['page']) ? $_REQUEST['page'] : '';

    if (strpos($current_pagenow, GLOBAL_NS) === 0 || strpos($current_page, GLOBAL_NS) === 0) {
        $use_icon_fill_color = $icon_colors['current'];
    }
    return str_replace(' fill="currentColor"', ' fill="'.esc_attr($use_icon_fill_color).'"', $svg);
};

/*
 * Loads the admin menu page options.
 *
 * @since 150422 Rewrite.
 */
$self->menuPageOptions = function () use ($self) {
    new MenuPage('options');
};





/*
 * WordPress admin icon color schemes.
 *
 * @since 150422 Rewrite.
 *
 * @type array WP admin icon colors.
 *
 * @note These must be hard-coded, because they don't become available
 *    in core until `admin_init`; i.e., too late for `admin_menu`.
 */
$self->wp_admin_icon_colors = array(
    'fresh'     => array('base' => '#999999', 'focus' => '#2EA2CC', 'current' => '#FFFFFF'),
    'light'     => array('base' => '#999999', 'focus' => '#CCCCCC', 'current' => '#CCCCCC'),
    'blue'      => array('base' => '#E5F8FF', 'focus' => '#FFFFFF', 'current' => '#FFFFFF'),
    'midnight'  => array('base' => '#F1F2F3', 'focus' => '#FFFFFF', 'current' => '#FFFFFF'),
    'sunrise'   => array('base' => '#F3F1F1', 'focus' => '#FFFFFF', 'current' => '#FFFFFF'),
    'ectoplasm' => array('base' => '#ECE6F6', 'focus' => '#FFFFFF', 'current' => '#FFFFFF'),
    'ocean'     => array('base' => '#F2FCFF', 'focus' => '#FFFFFF', 'current' => '#FFFFFF'),
    'coffee'    => array('base' => '#F3F2F1', 'focus' => '#FFFFFF', 'current' => '#FFFFFF'),
);

/*
 * On a specific menu page?
 *
 * @since 151002 Improving multisite compat.
 *
 * @param string $which Which page to check; may contain wildcards.
 *
 * @return boolean True if is the menu page.
 */
$self->isMenuPage = function ($which) use ($self) {
    if (!($which = trim((string) $which))) {
        return false; // Empty.
    }
    if (!is_admin()) {
        return false;
    }
    $page = $pagenow = ''; // Initialize.

    if (!empty($_REQUEST['page'])) {
        $page = (string) $_REQUEST['page'];
    }
    if (!empty($GLOBALS['pagenow'])) {
        $pagenow = (string) $GLOBALS['pagenow'];
    }
    if ($page && fnmatch($which, $page, FNM_CASEFOLD)) {
        return true; // Wildcard match.
    }
    if ($pagenow && fnmatch($which, $pagenow, FNM_CASEFOLD)) {
        return true; // Wildcard match.
    }
    return false; // Nope.
};
