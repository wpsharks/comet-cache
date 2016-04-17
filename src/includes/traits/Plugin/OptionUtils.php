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
     * @return array Plugin options.
     */
    public function getOptions()
    {
        if (!($options = $this->options)) { // Not defined yet?
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
        $this->options = array_intersect_key($this->options, $this->default_options);

        foreach ($this->options as $_key => &$_value) {
            $_value = trim((string) $_value); // Force strings.
        }
        unset($_key, $_value); // Housekeeping.

        $this->options['base_dir'] = trim($this->options['base_dir'], '\\/'." \t\n\r\0\x0B");
        if (!$this->options['base_dir'] || strpos(basename($this->options['base_dir']), 'wp-') === 0) {
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
     *
     * @return array Plugin options after update.
     */
    public function updateOptions(array $options)
    {
        if (!IS_PRO) { // Do not save Pro option keys.
            $options = array_diff_key($options, $this->pro_only_option_keys);
        }
        if (!empty($options['base_dir']) && $options['base_dir'] !== $this->options['base_dir']) {
            $this->tryErasingAllFilesDirsIn($this->wpContentBaseDirTo(''));
        }
        $this->options = array_merge($this->default_options, $this->options, $options);
        $this->options = array_intersect_key($this->options, $this->default_options);
        update_site_option(GLOBAL_NS.'_options', $this->options);

        return $this->getOptions();
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
