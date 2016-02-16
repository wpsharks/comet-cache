<?php
namespace WebSharks\ZenCache;

/*
 * Checks for a new lite release.
 *
 * @since 151220 Show version number in plugin options.
 *
 * @attaches-to `admin_init` hook.
 */
$self->maybeCheckLatestLiteVersion = function () use ($self) {
    if (IS_PRO) {
        return; // Not applicable.
    }
    if (!$self->options['lite_update_check']) {
        return; // Nothing to do.
    }
    if (!current_user_can($self->update_cap)) {
        return; // Nothing to do.
    }
    if (is_multisite() && !current_user_can($self->network_cap)) {
        return; // Nothing to do.
    }
    if ($self->options['last_lite_update_check'] >= strtotime('-1 hour')) {
        return; // No reason to keep checking on this.
    }
    $self->updateOptions(array('last_lite_update_check' => time()));

    $product_api_url        = 'https://'.urlencode(DOMAIN).'/';
    $product_api_input_vars = array('product_api' => array('action' => 'latest_lite_version'));

    $product_api_response = wp_remote_post($product_api_url, array('body' => $product_api_input_vars));
    $product_api_response = json_decode(wp_remote_retrieve_body($product_api_response));

    if (is_object($product_api_response) && !empty($product_api_response->lite_version)) {
        $self->updateOptions(array('latest_lite_version' => $product_api_response->lite_version));
    }
    // Disabling the notice for now. We only run this check to collect the latest version number.
    #if ($self->options['latest_lite_version'] && version_compare(VERSION, $self->options['latest_lite_version'], '<')) {
    #    $self->dismissMainNotice('new-lite-version-available'); // Dismiss any existing notices like this.
    #    $lite_updater_page = network_admin_url('/plugins.php'); // In a network this points to the master plugins list.
    #    $self->enqueueMainNotice(sprintf(__('<strong>%1$s:</strong> a new version is now available. Please <a href="%2$s">upgrade to v%3$s</a>.', 'zencache'), esc_html(NAME), esc_attr($lite_updater_page), esc_html($self->options['latest_lite_version'])), array('persistent_key' => 'new-lite-version-available'));
    #}
};


