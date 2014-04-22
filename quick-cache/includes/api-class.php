<?php
/*
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
 *    This wipes out the entire cache. On a standard WP installation this is the same as quick_cache::clear(); but on a multisite installation it impacts the entire network (i.e. wipes the cache for all blogs in the network).
 *
 */
namespace
	{
		class quick_cache
		{
			public static function version()
				{
					return $GLOBALS[__CLASS__]->version;
				}
			public static function options()
				{
					return $GLOBALS[__CLASS__]->options;
				}
			public static function purge()
				{
					return $GLOBALS[__CLASS__]->purge_cache();
				}
			public static function clear()
				{
					return $GLOBALS[__CLASS__]->clear_cache();
				}
			public static function wipe()
				{
					return $GLOBALS[__CLASS__]->wipe_cache();
				}
		}
	}