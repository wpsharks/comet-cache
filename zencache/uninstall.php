<?php
/**
 * ZenCache Uninstaller
 *
 * @package zencache\uninstall
 * @since 140829 Adding plugin uninstaller.
 * @copyright WebSharks, Inc. <http://www.websharks-inc.com>
 * @license GNU General Public License, version 2
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));

$GLOBALS['wp_php_rv'] = '5.3'; // Require PHP v5.3+.
if(require(dirname(__FILE__).'/submodules/wp-php-rv/wp-php-rv.php'))
	require_once dirname(__FILE__).'/uninstall.inc.php';