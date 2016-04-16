<?php
namespace WebSharks\CometCache\Traits\Plugin;

use WebSharks\CometCache\Classes;

trait CondUtils
{
    /**
     * Is pro preview?
     *
     * @since 150511 Rewrite.
     *
     * @return bool `TRUE` if it's a pro preview.
     */
    public function isProPreview()
    {
        return !empty($_REQUEST[GLOBAL_NS.'_pro_preview']);
    }
}
