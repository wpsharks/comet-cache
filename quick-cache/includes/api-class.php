<?php
/**
 * Quick Cache API Class.
 *
 * @package quick_cache\api
 * @since 140420 API class addition.
 * @copyright WebSharks, Inc. <http://www.websharks-inc.com>
 * @license GNU General Public License, version 2
 */
/*
 * @raamdev This section could be removed later in favor of docBlocks I think.
 *
 * An API Class to expose various aspects of Quick Cache for use by theme/plugin developers.
 *
 * `quick_cache::version()`
 *    Gives you the current version string.
 *
 * `quick_cache::options()`
 *    Gives you the current array of configured options.
 *
 * `quick_cache::purge()`
 *    Purges expired cache files, leaving all others intact. This occurs automatically over time via WP Cron; but this will force an immediate purge if you so desire.
 *
 * `quick_cache::clear()`
 *    This erases the entire cache for the current blog. In a multisite network this impacts only the current blog, it does not clear the cache for other child blogs.
 * `quick_cache::wipe()`
 *
 *    This wipes out the entire cache. On a standard WP installation this is the same as quick_cache::clear(); but on a multisite installation it impacts the entire network (i.e. wipes the cache for all blogs in the network).
 */
namespace // Global namespace.
	{
		if(!defined('WPINC')) // MUST have WordPress.
			exit('Do NOT access this file directly: '.basename(__FILE__));

		/**
		 * Quick Cache API Class.
		 */
		class quick_cache
		{
			/**
			 * @return \quick_cache\plugin instance.
			 */
			public static function plugin()
				{
					return $GLOBALS[__CLASS__];
				}

			/**
			 * @return string Current version string.
			 */
			public static function version()
				{
					return static::plugin()->version;
				}

			/**
			 * @return array Current array of options.
			 */
			public static function options()
				{
					return static::plugin()->options;
				}

			/**
			 * @return integer Total files purged (if any).
			 */
			public static function purge()
				{
					return static::plugin()->purge_cache();
				}

			/**
			 * @return integer Total files cleared (if any).
			 */
			public static function clear()
				{
					return static::plugin()->clear_cache();
				}

			/**
			 * @return integer Total files wiped (if any).
			 */
			public static function wipe()
				{
					return static::plugin()->wipe_cache();
				}
		}
	}