<?php
namespace WebSharks\CometCache\Traits\Plugin;

use WebSharks\CometCache\Classes;

trait OptionUtils
{
    /**
     * Get plugin options.
     *
     * @since 151002 Improving multisite compat.
     *
     * @param bool  $intersect Discard options not present in $this->default_options
     * @param bool  $refresh   Force-pull options directly from get_site_option()
     *
     * @return array Plugin options.
     *
     * @note $intersect should be `false` when this method is called via a VS upgrade routine or during inital startup on when upgrading. See https://git.io/viGIK
     */
    public function getOptions($intersect = true, $refresh = false)
    {
        if (!($options = $this->options) || $refresh) { // If not defined yet, or if we're forcing a refresh via get_site_option()
            if (!is_array($options = get_site_option(GLOBAL_NS.'_options'))) {
                $options = []; // Force array.
            }
            if (!$options && is_array($zencache_options = get_site_option('zencache_options'))) {
                $options                = $zencache_options; // Old ZenCache options.
                $options['crons_setup'] = $this->default_options['crons_setup'];
                $options['latest_lite_version'] = $this->default_options['latest_lite_version'];
                $options['latest_pro_version'] = $this->default_options['latest_pro_version'];
            }
        }
        $this->options = array_merge($this->default_options, $options);
        $this->options = $this->applyWpFilters(GLOBAL_NS.'_options', $this->options);
        $this->options = $intersect ? array_intersect_key($this->options, $this->default_options) : $this->options;

        foreach ($this->options as $_key => &$_value) {
            $_value = trim((string) $_value); // Force strings.
        }
        unset($_key, $_value); // Housekeeping.

        $this->options['base_dir'] = trim($this->options['base_dir'], '\\/'." \t\n\r\0\x0B");
        if (!$this->options['base_dir'] || mb_strpos(basename($this->options['base_dir']), 'wp-') === 0) {
            $this->options['base_dir'] = $this->default_options['base_dir'];
        }
        return $this->options; // Plugin options.
    }

    /**
     * Update plugin options.
     *
     * @since 151002 Improving multisite compat.
     *
     * @param array $options One or more new options.
     * @param bool  $intersect Discard options not present in $this->default_options
     *
     * @return array Plugin options after update.
     *
     * @note $intersect should be `false` when this method is called via a VS upgrade routine. See https://git.io/viGIK
     */
    public function updateOptions(array $options, $intersect = true)
    {
        if (!IS_PRO) { // Do not save Pro option keys.
            $options = array_diff_key($options, $this->pro_only_option_keys);
        }
        if (!empty($options['base_dir']) && $options['base_dir'] !== $this->options['base_dir']) {
            $this->tryErasingAllFilesDirsIn($this->wpContentBaseDirTo(''));
        }
        $this->options = array_merge($this->default_options, $this->options, $options);
        $this->options = $intersect ? array_intersect_key($this->options, $this->default_options) : $this->options;
        update_site_option(GLOBAL_NS.'_options', $this->options);

        return $this->getOptions($intersect);
    }

    /**
     * Restore default plugin options.
     *
     * @since 151002 Improving multisite compat.
     *
     * @return array Plugin options after update.
     */
    public function restoreDefaultOptions()
    {
        delete_site_option(GLOBAL_NS.'_options'); // Force restore.
        $this->options = $this->default_options; // In real-time.
        return $this->getOptions();
    }
}
