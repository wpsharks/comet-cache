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

    wp_enqueue_style(GLOBAL_NS, $self->url('/src/client-s/css/menu-pages.css'), $deps, VERSION, 'all');
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
    $deps = array('jquery'); // Plugin dependencies.

    wp_enqueue_script(GLOBAL_NS, $self->url('/src/client-s/js/menu-pages.js'), $deps, VERSION, true);
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
    add_submenu_page(GLOBAL_NS, __('Plugin Options', SLUG_TD), __('Plugin Options', SLUG_TD), $self->network_cap, GLOBAL_NS, array($self, 'menuPageOptions'));

    
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
    add_submenu_page(GLOBAL_NS, __('Plugin Options', SLUG_TD), __('Plugin Options', SLUG_TD), $self->cap, GLOBAL_NS, array($self, 'menuPageOptions'));

    
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
    $links[] = '<a href="'.esc_attr(add_query_arg(urlencode_deep(array('page' => GLOBAL_NS)), self_admin_url('/admin.php'))).'">'.__('Settings', SLUG_TD).'</a>';
    if (!IS_PRO) {
        $links[] = '<br/><a href="'.esc_attr(add_query_arg(urlencode_deep(array('page' => GLOBAL_NS, GLOBAL_NS.'_pro_preview' => '1')), self_admin_url('/admin.php'))).'">'.__('Preview Pro Features', SLUG_TD).'</a>';
        $links[] = '<a href="'.esc_attr('http://zencache.com/prices/').'" target="_blank">'.__('Upgrade', SLUG_TD).'</a>';
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
