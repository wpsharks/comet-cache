<?php
namespace WebSharks\CometCache\Traits\Plugin;

use WebSharks\CometCache\Classes;

trait WcpWooCommerceUtils
{
    /**
     * Automatically clears cache file for a WooCommerce Product when its stock is changed.
     *
     * @since 151220 Improving WooCommerce Compatibility.
     *
     * @attaches-to `woocommerce_product_set_stock` hook.
     *
     * @param \WC_Product $product A WooCommerce WC_Product object
     */
    public function autoClearPostCacheOnWooCommerceSetStock($product)
    {
        $counter = 0; // Initialize.

        if (!is_null($done = &$this->cacheKey('autoClearPostCacheOnWooCommerceSetStock'))) {
            return $counter; // Already did this.
        }
        $done = true; // Flag as having been done.

        if (class_exists('\\WooCommerce')) {
            $counter += $this->autoClearPostCache($product->id);
        }
    }

    /**
     * Automatically clears cache file for a WooCommerce Product when its stock status is changed.
     *
     * @since 161119 Improving WooCommerce Compatibility.
     *
     * @attaches-to `woocommerce_product_set_stock_status` hook.
     *
     * @param string|int $product_id A WooCommerce product ID.
     */
    public function autoClearPostCacheOnWooCommerceSetStockStatus($product_id)
    {
        $counter = 0; // Initialize.

        if (!is_null($done = &$this->cacheKey('autoClearPostCacheOnWooCommerceSetStockStatus'))) {
            return $counter; // Already did this.
        }
        $done = true; // Flag as having been done.

        if (class_exists('\\WooCommerce')) {
            $counter += $this->autoClearPostCache($product_id);
        }
    }
}
