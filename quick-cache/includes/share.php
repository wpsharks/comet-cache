<?php
namespace quick_cache // Root namespace.
{
	if(!defined('WPINC')) // MUST have WordPress.
		exit('Do NOT access this file directly: '.basename(__FILE__));

	if(!class_exists('\\'.__NAMESPACE__.'\\share'))
	{
		/**
		 * Quick Cache (Shared Methods)
		 *
		 * @package quick_cache\share
		 * @since 14xxxx Reorganizing class members.
		 */
		class share // Shared between {@link advanced_cache} and {@link plugin}.
		{
			public function __construct()
			{
				// @TODO Bring shareable methods into this class.
			}
		}

		$GLOBALS[__NAMESPACE__.'__share'] = new share();
	}
}