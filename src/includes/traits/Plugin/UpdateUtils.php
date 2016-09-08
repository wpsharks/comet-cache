<?php
namespace WebSharks\CometCache\Traits\Plugin;

use WebSharks\CometCache\Classes;

trait UpdateUtils
{
    /**
     * Checks for a new lite release.
     *
     * @since 151220 Show version number in plugin options.
     * @since $v Don't check current user.
     *
     * @attaches-to `admin_init` hook.
     */
    public function maybeCheckLatestLiteVersion()
    {
        if (IS_PRO) {
            return; // Not applicable.
        } elseif (!$this->options['lite_update_check']) {
            return; // Nothing to do.
        } elseif ($this->options['last_lite_update_check'] >= strtotime('-1 hour')) {
            if (empty($_REQUEST['force-check'])) {
                return; // Nothing to do.
            }
        }
        $this->updateOptions(['last_lite_update_check' => time()]);

        $product_api_url        = 'https://'.urlencode(DOMAIN).'/';
        $product_api_input_vars = ['product_api' => ['action' => 'latest_lite_version']];

        $product_api_response = wp_remote_post($product_api_url, ['body' => $product_api_input_vars]);
        $product_api_response = json_decode(wp_remote_retrieve_body($product_api_response));

        if (is_object($product_api_response) && !empty($product_api_response->lite_version)) {
            $this->updateOptions(['latest_lite_version' => $product_api_response->lite_version]);
        }
    }

    
}
