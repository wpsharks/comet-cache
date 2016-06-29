<?php
namespace WebSharks\CometCache\Classes;

/**
 * Conflicts.
 *
 * @since 150422 Rewrite.
 */
class Conflicts
{
    /**
     * Check.
     *
     * @since 150422 Rewrite.
     */
    public static function check()
    {
        if (static::doCheck()) {
            static::maybeEnqueueNotice();
        }

        return $GLOBALS[GLOBAL_NS.'_conflicting_plugin'];
    }

    /**
     * Perform check.
     *
     * @since 150422 Rewrite.
     */
    protected static function doCheck()
    {
        if (!empty($GLOBALS[GLOBAL_NS.'_conflicting_plugin'])) {
            return $GLOBALS[GLOBAL_NS.'_conflicting_plugin'];
        }
        $conflicting_plugin_slugs = [
            'zencache', 'zencache-pro', 'quick-cache', 'quick-cache-pro',
            str_replace('_', '-', GLOBAL_NS).(IS_PRO ? '' : '-pro'),
            'wp-super-cache', 'w3-total-cache', 'hyper-cache', 'wp-rocket',
        ];
        $active_plugins          = (array) get_option('active_plugins', []);
        $active_sitewide_plugins = is_multisite() ? array_keys((array) get_site_option('active_sitewide_plugins', [])) : [];
        $active_plugins          = array_unique(array_merge($active_plugins, $active_sitewide_plugins));

        foreach ($active_plugins as $_active_plugin_basename) {
            if (!($_active_plugin_slug = strstr($_active_plugin_basename, '/', true))) {
                continue; // Nothing to check in this case.
            }
            if (in_array($_active_plugin_slug, $conflicting_plugin_slugs, true)) {
                if (empty($GLOBALS[GLOBAL_NS.'_uninstalling']) && is_admin() && in_array($_active_plugin_slug, ['comet-cache', 'comet-cache-pro', 'zencache', 'zencache-pro', 'quick-cache', 'quick-cache-pro'], true)) {
                    add_action('admin_init', function () use ($_active_plugin_basename) {
                        deactivate_plugins($_active_plugin_basename, true);
                    }, -1000);
                } else {
                    return $GLOBALS[GLOBAL_NS.'_conflicting_plugin'] = $_active_plugin_slug;
                }
            }
        }

        return $GLOBALS[GLOBAL_NS.'_conflicting_plugin'] = ''; // i.e. No conflicting plugins.
    }

    /**
     * Maybe enqueue dashboard notice.
     *
     * @since 150422 Rewrite.
     */
    protected function maybeEnqueueNotice()
    {
        if (!empty($GLOBALS[GLOBAL_NS.'_uninstalling'])) {
            return; // Not when uninstalling.
        }
        if (empty($GLOBALS[GLOBAL_NS.'_conflicting_plugin'])) {
            return; // Not conflicts.
        }
        add_action('all_admin_notices', function () {
            if (!empty($GLOBALS[GLOBAL_NS.'_conflicting_plugin_lite_pro'])) {
                return; // Already did this in one plugin or the other.
            }
            $construct_name = function ($slug_or_ns) {
                $name = trim(mb_strtolower((string) $slug_or_ns));
                $name = preg_replace('/[_\-]+(?:lite|pro)$/u', '', $name);
                $name = preg_replace('/[^a-z0-9]/u', ' ', $name);
                $name = str_replace('cache', 'Cache', ucwords($name));

                return $name; // e.g., `x-cache` becomes `X Cache`.
            };
            $this_plugin_name = NAME; // See `src/includes/stub.php` for details.
            $conflicting_plugin_name = $construct_name($GLOBALS[GLOBAL_NS.'_conflicting_plugin']);

            if (strcasecmp($this_plugin_name, $conflicting_plugin_name) === 0) {
                $this_plugin_name = $this_plugin_name.' '.__('Pro', 'comet-cache');
                $conflicting_plugin_name = $conflicting_plugin_name.' '.__('Lite', 'comet-cache');
                $GLOBALS[GLOBAL_NS.'_conflicting_plugin_lite_pro'] = true;
            }
            echo '<div class="error">'.// Error notice.
                 '   <p>'.// Running one or more conflicting plugins at the same time.
                 '      '.sprintf(__('<strong>%1$s</strong> is NOT running. A conflicting plugin, <strong>%2$s</strong>, is currently active. Please deactivate the %2$s plugin to clear this message.', 'comet-cache'), esc_html($this_plugin_name), esc_html($conflicting_plugin_name)).
                 '   </p>'.
                 '</div>';
        });
    }
}
