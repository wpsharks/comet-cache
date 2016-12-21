<?php
namespace WebSharks\CometCache\Traits\Shared;

use WebSharks\CometCache\Classes;

trait BlogUtils
{
    /**
     * Get child blogs.
     *
     * @since 161221 Replacing `wp_get_sites()`.
     *
     * @return array An array of child blogs (max 100).
     *
     * @note The return value of this function is NOT cached in support of `$GLOBALS['wpdb']->siteid`.
     */
    public function getBlogs()
    {
        if (!is_multisite()) {
            return []; // Not possible.
        }
        $sites = []; // Initialize.

        foreach (get_sites([
            'number' => 100, 'count' => false,
            'network_id' => $GLOBALS['wpdb']->siteid,
        ]) as $_site) {
            if (($_site = get_site($_site))) {
                $sites[] = $_site->to_array();
            } // For compatibiliey with old `wp_get_sites()`.
        } // unset($_site);

        return $sites;
    }

    /**
     * Get blog details.
     *
     * @since 150821 Improving multisite compat.
     *
     * @param int $blog_id For which blog ID?
     *
     * @return \stdClass|null Blog details if possible.
     *
     * @note The return value of this function is NOT cached in support of `switch_to_blog()`.
     */
    public function blogDetails($blog_id = 0)
    {
        if (!is_multisite() || $this->isAdvancedCache()) {
            return null; // Not possible.
        }
        if (($blog_id = (int) $blog_id) < 0) {
            $blog_id = (int) get_current_site()->blog_id;
        }
        if (!$blog_id) {
            $blog_id = (int) get_current_blog_id();
        }
        if (!$blog_id || $blog_id < 0) {
            return null; // Not possible.
        }
        $details = get_blog_details($blog_id);

        return is_object($details) ? $details : null;
    }
}
