<?php
/**
 * Quick Cache Uninstaller
 *
 * @package quick_cache\uninstall
 * @since 140829 Adding plugin uninstaller.
 * @copyright WebSharks, Inc. <http://www.websharks-inc.com>
 * @license GNU General Public License, version 2
 */
namespace quick_cache
{
	if(!defined('WPINC')) // MUST have WordPress.
		exit('Do NOT access this file directly: '.basename(__FILE__));

	$GLOBALS[__NAMESPACE__.'_uninstalling']    = TRUE;
	$GLOBALS[__NAMESPACE__.'_autoload_plugin'] = FALSE;

	require_once dirname(__FILE__).'/quick-cache.inc.php';

	if(!class_exists('\\'.__NAMESPACE__.'\\uninstall'))
	{
		class uninstall // Uninstall handler.
		{
			/**
			 * @since 141001 Adding uninstaller.
			 *
			 * @var plugin Primary plugin class instance.
			 */
			protected $plugin; // Set by constructor.

			/**
			 * Uninstall constructor.
			 *
			 * @since 141001 Adding uninstall handler.
			 */
			public function __construct()
			{
				$GLOBALS[__NAMESPACE__] // Without hooks.
					= $this->plugin = new plugin(FALSE);

				$this->plugin->uninstall();
			}
		}
	}
	new uninstall(); // Run the uninstaller.
}