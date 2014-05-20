<?php
if(!function_exists('wp_php53'))
{
	/**
	 * @return boolean TRUE if PHP v5.3+.
	 */
	function wp_php53()
	{
		return version_compare(PHP_VERSION, '5.3', '>=');
	}
}
if(!function_exists('wp_php53_notice'))
{
	/**
	 * @param string $software_name Optional. Name of the calling theme/plugin. Defaults to `this software`.
	 * @param string $software_text_domain Optional i18n text domain. Defaults to slugified `$plugin_name`.
	 * @param string $notice_cap Optional. Capability to view notice. Defaults to `activate_plugins`.
	 * @param string $notice_hook Optional. Action hook. Defaults to `all_admin_notices`.
	 * @param string $notice Optional. Custom notice HTML; instead of default markup.
	 */
	function wp_php53_notice($software_name = '', $software_text_domain = '', $notice_cap = '', $notice_hook = '', $notice = '')
	{
		if(!$software_name) $software_name = 'this software';
		if(!$software_text_domain) // We can build this dynamically.
			$software_text_domain = strtolower(trim(preg_replace('/[^a-z0-9\-]/i', '-', $software_name), '-'));
		if(!$notice_cap) $notice_cap = 'activate_plugins'; // WordPress capability.
		if(!$notice_hook) $notice_hook = 'all_admin_notices'; // Action hook.

		if(!$notice) // Only if there is NOT a custom `$notice` defined already.
		{
			$notice = sprintf(__('<strong>%1$s is NOT active; %1$s requires PHP v5.3 (or higher).</strong>', $software_text_domain), $software_name);
			$notice .= ' '.sprintf(__('You\'re currently running <code>PHP v%1$s</code>.', $software_text_domain), PHP_VERSION);
			$notice .= ' '.__('A simple update is necessary. Please ask your web hosting company to do this for you.', $software_text_domain);
			$notice .= ' '.sprintf(__('To remove this message, please deactivate %1$s.', $software_text_domain), $software_name);
		}
		$notice_handler = create_function('', 'if(current_user_can(\''.str_replace("'", "\\'", $notice_cap).'\'))'.
		                                      '  echo \'<div class="error"><p>'.str_replace("'", "\\'", $notice).'</p></div>\';');
		add_action($notice_hook, $notice_handler);
	}
}
if(!function_exists('wp_php53_custom_notice'))
{
	/**
	 * @param string $notice Optional. Custom notice HTML; instead of default markup.
	 * @param string $notice_cap Optional. Capability to view notice. Defaults to `activate_plugins`.
	 * @param string $notice_hook Optional. Action hook. Defaults to `all_admin_notices`.
	 */
	function wp_php53_custom_notice($notice = '', $notice_cap = '', $notice_hook = '')
	{
		return wp_php53_notice('', '', $notice_cap, $notice_hook, $notice);
	}
}
/*
 * Return on `include/require`.
 */
return wp_php53(); // TRUE if PHP v5.3+; FALSE otherwise.