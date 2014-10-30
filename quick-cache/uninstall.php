<?php
/**
 * Quick Cache Uninstaller
 *
 * @package quick_cache\uninstall
 * @since 140829 Adding plugin uninstaller.
 * @copyright WebSharks, Inc. <http://www.websharks-inc.com>
 * @license GNU General Public License, version 2
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));

if(require(dirname(__FILE__).'/includes/wp-php53.php')) // TRUE if running PHP v5.3+.
	require_once dirname(__FILE__).'/uninstall.inc.php';