<?php
// @TODO Add docBlocks to this class.

namespace quick_cache // Root namespace.
{
	if(!defined('WPINC')) // MUST have WordPress.
		exit('Do NOT access this file directly: '.basename(__FILE__));

	class actions // Action handlers.
	{
		protected $plugin; // Set by constructor.

		public function __construct()
		{
			$this->plugin = plugin();

			if(empty($_REQUEST[__NAMESPACE__])) return;
			foreach((array)$_REQUEST[__NAMESPACE__] as $action => $args)
				if(method_exists($this, $action)) $this->{$action}($args);
		}

		public function wipe_cache($args)
		{
			if(!current_user_can($this->plugin->network_cap))
				return; // Nothing to do.

			if(empty($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce']))
				return; // Unauthenticated POST data.

			$counter = $this->plugin->wipe_cache(TRUE); // Counter.

			$redirect_to = self_admin_url('/admin.php'); // Redirect preparations.
			$query_args  = array('page' => __NAMESPACE__, __NAMESPACE__.'__cache_wiped' => '1');
			$redirect_to = add_query_arg(urlencode_deep($query_args), $redirect_to);

			wp_redirect($redirect_to).exit(); // All done :-)
		}

		public function clear_cache($args)
		{
			if(!current_user_can($this->plugin->cap))
				return; // Nothing to do.

			if(empty($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce']))
				return; // Unauthenticated POST data.

			$counter = $this->plugin->clear_cache(TRUE); // Counter.

			$redirect_to = self_admin_url('/admin.php'); // Redirect preparations.
			$query_args  = array('page' => __NAMESPACE__, __NAMESPACE__.'__cache_cleared' => '1');
			$redirect_to = add_query_arg(urlencode_deep($query_args), $redirect_to);

			wp_redirect($redirect_to).exit(); // All done :-)
		}

		public function save_options($args)
		{
			if(!current_user_can($this->plugin->cap))
				return; // Nothing to do.

			if(empty($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce']))
				return; // Unauthenticated POST data.

			$args = array_map('trim', stripslashes_deep((array)$args));
			if(isset($args['base_dir'])) // No leading/trailing slashes please.
				$args['base_dir'] = trim($args['base_dir'], '\\/'." \t\n\r\0\x0B");

			$this->plugin->options = array_merge($this->plugin->default_options, $this->plugin->options, $args);
			$this->plugin->options = array_intersect_key($this->plugin->options, $this->plugin->default_options);

			if(!trim($this->plugin->options['base_dir'], '\\/'." \t\n\r\0\x0B") // Empty?
			   || strpos(basename($this->plugin->options['base_dir']), 'wp-') === 0 // Reserved?
			) $this->plugin->options['base_dir'] = $this->plugin->default_options['base_dir'];

			update_option(__NAMESPACE__.'_options', $this->plugin->options); // Blog-specific.
			if(is_multisite()) update_site_option(__NAMESPACE__.'_options', $this->plugin->options);

			$redirect_to = self_admin_url('/admin.php'); // Redirect preparations.
			$query_args  = array('page' => __NAMESPACE__, __NAMESPACE__.'__updated' => '1');

			$this->plugin->auto_wipe_cache(); // May produce a notice.

			if($this->plugin->options['enable']) // Enable.
			{
				if(!($add_wp_cache_to_wp_config = $this->plugin->add_wp_cache_to_wp_config()))
					$query_args[__NAMESPACE__.'__wp_config_wp_cache_add_failure'] = '1';

				if(!($add_advanced_cache = $this->plugin->add_advanced_cache()))
					$query_args[__NAMESPACE__.'__advanced_cache_add_failure']
						= ($add_advanced_cache === NULL)
						? 'qc-advanced-cache' : '1';

				$this->plugin->update_blog_paths();
			}
			else // We need to disable Quick Cache in this case.
			{
				if(!($remove_wp_cache_from_wp_config = $this->plugin->remove_wp_cache_from_wp_config()))
					$query_args[__NAMESPACE__.'__wp_config_wp_cache_remove_failure'] = '1';

				if(!($remove_advanced_cache = $this->plugin->remove_advanced_cache()))
					$query_args[__NAMESPACE__.'__advanced_cache_remove_failure'] = '1';
			}
			$redirect_to = add_query_arg(urlencode_deep($query_args), $redirect_to);

			wp_redirect($redirect_to).exit(); // All done :-)
		}

		public function restore_default_options($args)
		{
			if(!current_user_can($this->plugin->cap))
				return; // Nothing to do.

			if(empty($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce']))
				return; // Unauthenticated POST data.

			delete_option(__NAMESPACE__.'_options'); // Blog-specific.
			if(is_multisite()) delete_site_option(__NAMESPACE__.'_options');
			$this->plugin->options = $this->plugin->default_options;

			$redirect_to = self_admin_url('/admin.php'); // Redirect preparations.
			$query_args  = array('page' => __NAMESPACE__, __NAMESPACE__.'__restored' => '1');

			$this->plugin->auto_wipe_cache(); // May produce a notice.

			if($this->plugin->options['enable']) // Enable.
			{
				if(!($add_wp_cache_to_wp_config = $this->plugin->add_wp_cache_to_wp_config()))
					$query_args[__NAMESPACE__.'__wp_config_wp_cache_add_failure'] = '1';

				if(!($add_advanced_cache = $this->plugin->add_advanced_cache()))
					$query_args[__NAMESPACE__.'__advanced_cache_add_failure']
						= ($add_advanced_cache === NULL)
						? 'qc-advanced-cache' : '1';

				$this->plugin->update_blog_paths();
			}
			else // We need to disable Quick Cache in this case.
			{
				if(!($remove_wp_cache_from_wp_config = $this->plugin->remove_wp_cache_from_wp_config()))
					$query_args[__NAMESPACE__.'__wp_config_wp_cache_remove_failure'] = '1';

				if(!($remove_advanced_cache = $this->plugin->remove_advanced_cache()))
					$query_args[__NAMESPACE__.'__advanced_cache_remove_failure'] = '1';
			}
			$redirect_to = add_query_arg(urlencode_deep($query_args), $redirect_to);

			wp_redirect($redirect_to).exit(); // All done :-)
		}

		public function dismiss_notice($args)
		{
			if(!current_user_can($this->plugin->cap))
				return; // Nothing to do.

			if(empty($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce']))
				return; // Unauthenticated POST data.

			$args = array_map('trim', stripslashes_deep((array)$args));
			if(empty($args['key'])) return; // Nothing to dismiss.

			$notices = (is_array($notices = get_option(__NAMESPACE__.'_notices'))) ? $notices : array();
			unset($notices[$args['key']]); // Dismiss this notice.
			update_option(__NAMESPACE__.'_notices', $notices);

			wp_redirect(remove_query_arg(__NAMESPACE__)).exit();
		}

		public function dismiss_error($args)
		{
			if(!current_user_can($this->plugin->cap))
				return; // Nothing to do.

			if(empty($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce']))
				return; // Unauthenticated POST data.

			$args = array_map('trim', stripslashes_deep((array)$args));
			if(empty($args['key'])) return; // Nothing to dismiss.

			$errors = (is_array($errors = get_option(__NAMESPACE__.'_errors'))) ? $errors : array();
			unset($errors[$args['key']]); // Dismiss this error.
			update_option(__NAMESPACE__.'_errors', $errors);

			wp_redirect(remove_query_arg(__NAMESPACE__)).exit();
		}
	}

	new actions(); // Initialize/handle actions.
}