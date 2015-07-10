<?php
/**
 * ZenCache Conflicts.
 *
 * @package zencache\conflicts
 * @since 140420 API class addition.
 * @copyright WebSharks, Inc. <http://www.websharks-inc.com>
 * @license GNU General Public License, version 2
 */
namespace zencache
{
	if(!defined('WPINC')) // MUST have WordPress.
		exit('Do NOT access this file directly: '.basename(__FILE__));

	${__FILE__}['check'] = function ()
	{
		$plugin_slug      = str_replace('_', '-', __NAMESPACE__);
		$plugin_lite_slug = $plugin_slug; // i.e. the same.
		$plugin_pro_slug  = $plugin_slug.'-pro';

		if(class_exists('\\'.__NAMESPACE__.'\\plugin'))
			return $plugin_slug; // Lite/Pro conflict exists.

		$conflicting_plugin_slugs = array(
			$plugin_pro_slug, // Exclude lite version; i.e. self.
			'quick-cache', 'quick-cache-pro', // Old plugin slugs.
			'wp-super-cache', 'w3-total-cache', 'hyper-cache', 'wp-rocket',
		);
		$active_plugins           = (array)get_option('active_plugins', array());
		$active_sitewide_plugins  = is_multisite() ? array_keys((array)get_site_option('active_sitewide_plugins', array())) : array();
		$active_plugins           = array_unique(array_merge($active_plugins, $active_sitewide_plugins));

		foreach($active_plugins as $_active_plugin_basename)
		{
			if(!($_active_plugin_slug = strstr($_active_plugin_basename, '/', TRUE)))
				continue; // Nothing to check in this case.

			if(in_array($_active_plugin_slug, $conflicting_plugin_slugs, TRUE))
				if(in_array($_active_plugin_slug, array('quick-cache', 'quick-cache-pro'), TRUE))
					add_action('admin_init', function () use ($_active_plugin_basename)
					{
						if(function_exists('deactivate_plugins'))
							deactivate_plugins($_active_plugin_basename, TRUE);
					}, -1000);
				else return $_active_plugin_slug;
		}
		return ''; // i.e. No conflicting plugin found above.
	};
	return ($GLOBALS[__NAMESPACE__.'_conflicting_plugin'] = ${__FILE__}['check']());
}