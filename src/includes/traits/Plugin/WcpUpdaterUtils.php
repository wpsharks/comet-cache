<?php
namespace WebSharks\CometCache\Traits\Plugin;

use WebSharks\CometCache\Classes;

trait WcpUpdaterUtils
{
    /**
     * Automatically clears all cache files for current blog when WordPress core, or an active component, is upgraded.
     *
     * @since 150422 Rewrite.
     *
     * @attaches-to `upgrader_process_complete` hook.
     *
     * @param \WP_Upgrader $upgrader_instance An instance of \WP_Upgrader.
     *                                        Or, any class that extends \WP_Upgrader.
     * @param array        $data              Array of bulk item update data.
     *
     *    This array may include one or more of the following keys:
     *
     *       - `string` `$action` Type of action. Default 'update'.
     *       - `string` `$type` Type of update process; e.g. 'plugin', 'theme', 'core'.
     *       - `boolean` `$bulk` Whether the update process is a bulk update. Default true.
     *       - `array` `$packages` Array of plugin, theme, or core packages to update.
     */
    public function autoClearOnUpgraderProcessComplete(\WP_Upgrader $upgrader_instance, array $data)
    {
        $counter = 0; // Initialize.

        switch (!empty($data['type']) ? $data['type'] : '') {
            case 'plugin': // Plugin upgrade.

                $multi_plugin_update     = $single_plugin_update     = false;
                $upgrading_active_plugin = false; // Initialize.

                if (!empty($data['bulk']) && !empty($data['plugins']) && is_array($data['plugins'])) {
                    $multi_plugin_update = true;
                } elseif (!empty($data['plugin']) && is_string($data['plugin'])) {
                    $single_plugin_update = true;
                }
                if ($multi_plugin_update) {
                    foreach ($data['plugins'] as $_plugin) {
                        if ($_plugin && is_string($_plugin) && is_plugin_active($_plugin)) {
                            $upgrading_active_plugin = true;
                            break; // Got what we need here.
                        }
                    }
                    unset($_plugin); // Housekeeping.
                } elseif ($single_plugin_update && (is_plugin_active($data['plugin'])
                        || !empty($upgrader_instance->skin->upgrader->skin->plugin_active)
                        || !empty($upgrader_instance->skin->upgrader->skin->plugin_network_active))) {
                    $upgrading_active_plugin = true;
                }
                if ($upgrading_active_plugin) {
                    $counter += $this->autoClearCache();
                    add_action('shutdown', [$this, 'wipeOpcacheByForce'], PHP_INT_MAX);
                }
                break; // Break switch.

            case 'theme': // Theme upgrade.

                $current_active_theme          = wp_get_theme();
                $current_active_theme_parent   = $current_active_theme->parent();
                $multi_theme_update            = $single_theme_update            = false;
                $upgrading_active_parent_theme = $upgrading_active_theme = false;

                if (!empty($data['bulk']) && !empty($data['themes']) && is_array($data['themes'])) {
                    $multi_theme_update = true;
                } elseif (!empty($data['theme']) && is_string($data['theme'])) {
                    $single_theme_update = true;
                }
                if ($multi_theme_update) {
                    foreach ($data['themes'] as $_theme) {
                        if (!$_theme || !is_string($_theme) || !($_theme_obj = wp_get_theme($_theme))) {
                            continue; // Unable to acquire theme object instance.
                        }
                        if ($current_active_theme_parent && $current_active_theme_parent->get_stylesheet() === $_theme_obj->get_stylesheet()) {
                            $upgrading_active_parent_theme = true;
                            break; // Got what we needed here.
                        } elseif ($current_active_theme->get_stylesheet() === $_theme_obj->get_stylesheet()) {
                            $upgrading_active_theme = true;
                            break; // Got what we needed here.
                        }
                    }
                    unset($_theme, $_theme_obj); // Housekeeping.
                } elseif ($single_theme_update && ($_theme_obj = wp_get_theme($data['theme']))) {
                    if ($current_active_theme_parent && $current_active_theme_parent->get_stylesheet() === $_theme_obj->get_stylesheet()) {
                        $upgrading_active_parent_theme = true;
                    } elseif ($current_active_theme->get_stylesheet() === $_theme_obj->get_stylesheet()) {
                        $upgrading_active_theme = true;
                    }
                }
                unset($_theme_obj); // Housekeeping.

                if ($upgrading_active_theme || $upgrading_active_parent_theme) {
                    $counter += $this->autoClearCache();
                    add_action('shutdown', [$this, 'wipeOpcacheByForce'], PHP_INT_MAX);
                }
                break; // Break switch.

            case 'core': // Core upgrade.
            default: // Or any other sort of upgrade.
                $counter += $this->autoClearCache();
                add_action('shutdown', [$this, 'wipeOpcacheByForce'], PHP_INT_MAX);
                break; // Break switch.
        }
    }
}
