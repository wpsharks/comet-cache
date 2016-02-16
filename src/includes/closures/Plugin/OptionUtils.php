<?php
namespace WebSharks\ZenCache;

/*
 * Get plugin options.
 *
 * @since 151002 Improving multisite compat.
 *
 * @return array Plugin options.
 */
$self->getOptions = function () use ($self) {
    if (!($options = $self->options)) { // Not defined yet?
        if (!is_array($options = get_site_option(GLOBAL_NS.'_options'))) {
            $options = array(); // Force array.
        }
        if (!$options && is_array($quick_cache_options = get_site_option('quick_cache_options'))) {
            $options                = $quick_cache_options; // Old Quick Cache options.
            $options['crons_setup'] = $this->default_options['crons_setup'];
        }
    }
    $self->options = array_merge($self->default_options, $options);
    $self->options = $self->applyWpFilters(GLOBAL_NS.'_options', $self->options);
    $self->options = array_intersect_key($self->options, $self->default_options);

    foreach ($self->options as $_key => &$_value) {
        $_value = trim((string) $_value); // Force strings.
    } unset($_key, $_value); // Housekeeping.

    $self->options['base_dir'] = trim($self->options['base_dir'], '\\/'." \t\n\r\0\x0B");
    if (!$self->options['base_dir'] || strpos(basename($self->options['base_dir']), 'wp-') === 0) {
        $self->options['base_dir'] = $self->default_options['base_dir'];
    }
    return $self->options; // Plugin options.
};

/*
 * Update plugin options.
 *
 * @since 151002 Improving multisite compat.
 *
 * @param array $options One or more new options.
 *
 * @return array Plugin options after update.
 */
$self->updateOptions = function (array $options) use ($self) {
    if (!IS_PRO) { // Do not save Pro option keys.
        $options = array_diff_key($options, $self->pro_only_option_keys);
    }
    if (!empty($options['base_dir']) && $options['base_dir'] !== $self->options['base_dir']) {
        $self->tryErasingAllFilesDirsIn($self->wpContentBaseDirTo(''));
    }
    $self->options = array_merge($self->default_options, $self->options, $options);
    $self->options = array_intersect_key($self->options, $self->default_options);
    update_site_option(GLOBAL_NS.'_options', $self->options);

    return $self->getOptions();
};

/*
 * Restore default plugin options.
 *
 * @since 151002 Improving multisite compat.
 *
 * @return array Plugin options after update.
 */
$self->restoreDefaultOptions = function () use ($self) {
    delete_site_option(GLOBAL_NS.'_options'); // Force restore.
    $self->options = $self->default_options; // In real-time.
    return $self->getOptions();
};
