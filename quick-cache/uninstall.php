<?php
/**
 * Quick Cache Uninstaller
 *
 * @package quick_cache\uninstall
 * @since 14xxxx Adding plugin uninstaller.
 * @copyright WebSharks, Inc. <http://www.websharks-inc.com>
 * @license GNU General Public License, version 2
 */
namespace quick_cache
{
	if(!defined('WPINC')) // MUST have WordPress.
		exit('Do NOT access this file directly: '.basename(__FILE__));

	require_once dirname(__FILE__).'/quick-cache-pro.inc.php';

	if(!class_exists('\\'.__NAMESPACE__.'\\uninstall'))
	{
		class uninstall // Uninstall handler.
		{
			/**
			 * @since 14xxxx Adding uninstaller.
			 *
			 * @var plugin Primary plugin class instance.
			 */
			protected $plugin; // Set by constructor.

			/**
			 * Uninstall constructor.
			 *
			 * @since 14xxxx Adding uninstall handler.
			 */
			public function __construct()
			{
				$this->plugin = plugin( /* Without hooks. */);
				$this->plugin->setup( /* Without hooks. */);
				$this->plugin->uninstall();
			}
		}

		new uninstall(); // Run the uninstaller.
	}
}