<?php
namespace WebSharks\CometCache\Traits\Plugin;

use WebSharks\CometCache\Classes;

trait DbUtils
{
    /**
     * WordPress database instance.
     *
     * @since 150422 Rewrite.
     *
     * @return \wpdb Reference for IDEs.
     */
    public function wpdb()
    {
        return $GLOBALS['wpdb'];
    }
}
