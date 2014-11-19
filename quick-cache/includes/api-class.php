<?php
/**
 * Quick Cache API Class.
 *
 * @package quick_cache\api
 * @since 140420 API class addition.
 * @copyright WebSharks, Inc. <http://www.websharks-inc.com>
 * @license GNU General Public License, version 2
 */
namespace // Global namespace.
{
	if(!defined('WPINC')) // MUST have WordPress.
		exit('Do NOT access this file directly: '.basename(__FILE__));

	if(!class_exists('\\quick_cache'))
	{
		/**
		 * Quick Cache API Class.
		 */
		class quick_cache
		{
			/**
			 * Current QC plugin instance.
			 *
			 * @return \quick_cache\plugin instance.
			 */
			public static function plugin()
			{
				return $GLOBALS[__CLASS__];
			}

			/**
			 * Gives you the current version string.
			 *
			 * @return string Current version string.
			 */
			public static function version()
			{
				return static::plugin()->version;
			}

			/**
			 * Gives you the current array of configured options.
			 *
			 * @return array Current array of options.
			 */
			public static function options()
			{
				return static::plugin()->options;
			}

			/**
			 * Purges expired cache files, leaving all others intact.
			 *
			 * @note This occurs automatically over time via WP Cron;
			 *    but this will force an immediate purge if you so desire.
			 *
			 * @return integer Total files purged (if any).
			 */
			public static function purge()
			{
				return static::plugin()->purge_cache();
			}

			/**
			 * This erases the entire cache for the current blog.
			 *
			 * @note In a multisite network this impacts only the current blog,
			 *    it does not clear the cache for other child blogs.
			 *
			 * @return integer Total files cleared (if any).
			 */
			public static function clear()
			{
				return static::plugin()->clear_cache();
			}

			/**
			 * This wipes out the entire cache.
			 *
			 * @note On a standard WP installation this is the same as quick_cache::clear();
			 *    but on a multisite installation it impacts the entire network
			 *    (i.e. wipes the cache for all blogs in the network).
			 *
			 * @return integer Total files wiped (if any).
			 */
			public static function wipe()
			{
				return static::plugin()->wipe_cache();
			}
		}
	}
}