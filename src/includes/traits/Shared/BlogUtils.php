<?php
namespace WebSharks\CometCache\Traits\Shared;

use WebSharks\CometCache\Classes;

trait BlogUtils
{
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
        if (($blog_id = (integer) $blog_id) < 0) {
            $blog_id = (integer) get_current_site()->blog_id;
        }
        if (!$blog_id) {
            $blog_id = (integer) get_current_blog_id();
        }
        if (!$blog_id || $blog_id < 0) {
            return null; // Not possible.
        }
        $details = get_blog_details($blog_id);

        return is_object($details) ? $details : null;
    }
}
