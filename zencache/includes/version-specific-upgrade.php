<?php
namespace zencache // Root namespace.
{
	if(!defined('WPINC')) // MUST have WordPress.
		exit('Do NOT access this file directly: '.basename(__FILE__));

	if(!class_exists('\\'.__NAMESPACE__.'\\version_specific_upgrade'))
	{
		/**
		 * ZenCache (Upgrade Handlers)
		 *
		 * @since 140725 Reorganizing class members.
		 * @package zencache\version_specific_upgrade
		 */
		class version_specific_upgrade // Version-specific upgrade handlers.
		{
			/**
			 * @var plugin Plugin reference.
			 */
			protected $plugin; // Set by constructor.

			/**
			 * @var string Version they are upgrading from.
			 */
			protected $prev_version = ''; // Set by constructor.

			/**
			 * Class constructor.
			 *
			 * @since 140725 Reorganizing class members.
			 *
			 * @param string $prev_version Version they are upgrading from.
			 */
			public function __construct($prev_version)
			{
				$this->plugin       = plugin();
				$this->prev_version = (string)$prev_version;
				$this->run_handlers(); // Run upgrade(s).
			}

			/**
			 * Runs upgrade handlers in the proper order.
			 */
			public function run_handlers()
			{
				$this->from_lt_v110523();
				$this->from_lt_v140104();
				$this->from_lt_v140605();
				$this->from_lt_v140612();
				$this->from_lt_v141009();
				$this->from_quick_cache();
			}

			/*
			 * Upgrading from a version prior to our rewrite.
			 */
			public function from_lt_v110523()
			{
				if(version_compare($this->prev_version, '110523', '<'))
				{
					delete_option('ws_plugin__qcache_options'); // Ditch these.
					delete_option('ws_plugin__qcache_notices'); // Ditch these.
					delete_option('ws_plugin__qcache_configured'); // Ditch this too.

					wp_clear_scheduled_hook('ws_plugin__qcache_garbage_collector__schedule'); // Ditch old CRON job.
					wp_clear_scheduled_hook('ws_plugin__qcache_auto_cache_engine__schedule'); // Ditch old CRON job.

					$this->plugin->enqueue_notice(sprintf(__('<strong>%1$s:</strong> this version is a <strong>complete rewrite</strong> of Quick Cache :-) Please review your %1$s options carefully!', 'zencache'), esc_html($this->plugin->name)));
				}
			}

			/*
			 * Upgrading from a version prior to v140104 where we introduced feed caching.
			 */
			public function from_lt_v140104()
			{
				if(version_compare($this->prev_version, '140104', '<')) // When this sort of update occurs, we issue a notice about this new feature.
					$this->plugin->enqueue_notice(sprintf(__('<strong>%1$s Feature Notice:</strong> This version of %1$s adds new options for Feed caching. Feed caching is now disabled by default. If you wish to enable feed caching, please visit the %1$s options panel.', 'zencache'), esc_html($this->plugin->name)));
			}

			/*
			 * Upgrading from a version prior to v140605, where we introduced a branched cache structure.
			 * See <https://github.com/websharks/zencache/issues/147#issuecomment-42659131>
			 *    We also also moved to a base directory layout.
			 */
			public function from_lt_v140605()
			{
				if(version_compare($this->prev_version, '140605', '<'))
				{
					if((is_multisite() && is_array($existing_options = get_site_option(__NAMESPACE__.'_options')))
					   || is_array($existing_options = get_option(__NAMESPACE__.'_options'))

					   || (is_multisite() && is_array($existing_options = get_site_option('quick_cache_options')))
					   || is_array($existing_options = get_option('quick_cache_options'))

					) // Upgrading from a version before we introduced a branched cache structure?
					{
						if(!empty($existing_options['cache_dir']))
						{
							$wp_content_dir_relative = // We considered custom locations.
								trim(str_replace(ABSPATH, '', WP_CONTENT_DIR), '\\/'." \t\n\r\0\x0B");

							$this->plugin->options['base_dir'] = $existing_options['cache_dir'] = trim($existing_options['cache_dir'], '\\/'." \t\n\r\0\x0B");

							if(!$this->plugin->options['base_dir'] || $this->plugin->options['base_dir'] === $wp_content_dir_relative.'/cache')
								$this->plugin->options['base_dir'] = $this->plugin->default_options['base_dir'];

							if($existing_options['cache_dir']) // Wipe old files?
								$this->plugin->wipe_cache(FALSE, ABSPATH.$existing_options['cache_dir']);
							unset($this->plugin->options['cache_dir']); // Just to be sure.

							update_option(__NAMESPACE__.'_options', $this->plugin->options);
							if(is_multisite()) update_site_option(__NAMESPACE__.'_options', $this->plugin->options);

							$this->plugin->activate(); // Reactivate plugin w/ new options.
						}
						$this->plugin->enqueue_notice(sprintf(__('<strong>%1$s Feature Notice:</strong> This version of %1$s introduces a new <a href="http://zencache.com/r/kb-branched-cache-structure/" target="_blank">Branched Cache Structure</a> and several other <a href="http://www.websharks-inc.com/post/quick-cache-v140605-now-available/" target="_blank">new features</a>.', 'zencache'), esc_html($this->plugin->name)));
					}
				}
			}

			/**
			 * Upgrading from a version before we changed base directory from `ABSPATH` to `WP_CONTENT_DIR`.
			 *    If so, we need to reset the cache location on sites
			 *    that have `wp-content` in their base directory.
			 */
			public function from_lt_v140612()
			{
				if(version_compare($this->prev_version, '140612', '<'))
				{
					if((is_multisite() && is_array($existing_options = get_site_option(__NAMESPACE__.'_options')))
					   || is_array($existing_options = get_option(__NAMESPACE__.'_options'))

					   || (is_multisite() && is_array($existing_options = get_site_option('quick_cache_options')))
					   || is_array($existing_options = get_option('quick_cache_options'))

					) // Upgrading from a version before we changed base directory from `ABSPATH` to `WP_CONTENT_DIR`?
					{
						if(!empty($existing_options['base_dir']) && stripos($existing_options['base_dir'], basename(WP_CONTENT_DIR)) !== FALSE)
						{
							$this->plugin->wipe_cache(FALSE, ABSPATH.$existing_options['base_dir']);
							$this->plugin->options['base_dir'] = $this->plugin->default_options['base_dir'];

							update_option(__NAMESPACE__.'_options', $this->plugin->options);
							if(is_multisite()) update_site_option(__NAMESPACE__.'_options', $this->plugin->options);

							$this->plugin->activate(); // Reactivate plugin w/ new options.

							$this->plugin->enqueue_notice( // Give site owners a quick heads up about this.
								'<p>'.sprintf(__('<strong>%1$s Notice:</strong> This version of %1$s changes the default base directory that it uses, from <code>ABSPATH</code> to <code>WP_CONTENT_DIR</code>. This is for improved compatibility with installations that choose to use a custom <code>WP_CONTENT_DIR</code> location.', 'zencache'), esc_html($this->plugin->name)).
								' '.sprintf(__('%1$s has detected that your previously configured cache directory may have been in conflict with this change. As a result, your %1$s configuration has been updated to the new default value; just to keep things running smoothly for you :-). If you would like to review this change, please see: <code>Dashboard ⥱ %1$s ⥱ Directory &amp; Expiration Time</code>; where you may customize it further if necessary.', 'zencache'), esc_html($this->plugin->name)).'</p>'
							);
						}
					}
				}
			}

			/**
			 * Upgrading from a version before we changed several `cache_purge_*` options to `cache_clear_*`.
			 *    If so, we need to use the existing options to fill the new keys.
			 *    And, of course, then we save the updated options.
			 */
			public function from_lt_v141009()
			{
				if(version_compare($this->prev_version, '141009', '<'))
				{
					if((is_multisite() && is_array($existing_options = get_site_option(__NAMESPACE__.'_options')))
					   || is_array($existing_options = get_option(__NAMESPACE__.'_options'))

					   || (is_multisite() && is_array($existing_options = get_site_option('quick_cache_options')))
					   || is_array($existing_options = get_option('quick_cache_options'))

					) // Update old purge keys; which now use `clear` instead of `purge`.
					{
						foreach(array('cache_purge_home_page_enable',
						              'cache_purge_posts_page_enable',
						              'cache_purge_author_page_enable',
						              'cache_purge_term_category_enable',
						              'cache_purge_term_post_tag_enable',
						              'cache_purge_term_other_enable',
						        ) as $_old_purge_option)
							if(isset($existing_options[$_old_purge_option][0]))
							{
								$found_old_purge_options                                                  = TRUE;
								$this->plugin->options[str_replace('purge', 'clear', $_old_purge_option)] = $existing_options[$_old_purge_option][0];
							}
						unset($_old_purge_option); // Housekeeping.

						if(!empty($found_old_purge_options))
						{
							update_option(__NAMESPACE__.'_options', $this->plugin->options);
							if(is_multisite()) update_site_option(__NAMESPACE__.'_options', $this->plugin->options);
						}
					}
				}
			}

			/**
			 * Upgrading from a version before we changed the name to ZenCache.
			 */
			public function from_quick_cache()
			{
				if((is_multisite() && is_array($quick_cache_options = get_site_option('quick_cache_options')))
				   || is_array($quick_cache_options = get_option('quick_cache_options'))

				) // Automatically uninstall Quick Cache; for the most part anyway.
				{
					delete_option('quick_cache_options');
					if(is_multisite()) // Delete network options too.
						delete_site_option('quick_cache_options');

					delete_option('quick_cache_notices');
					delete_option('quick_cache_errors');

					wp_clear_scheduled_hook('_cron_quick_cache_auto_cache');
					wp_clear_scheduled_hook('_cron_quick_cache_cleanup');

					deactivate_plugins(array('quick-cache/quick-cache.php', 'quick-cache-pro/quick-cache-pro.php'), TRUE);

					// Use the new base dir for ZenCache; remove the old base dir for Quick Cache.

					if(!empty($quick_cache_options['base_dir']))
						$this->plugin->delete_all_files_dirs_in(WP_CONTENT_DIR.'/'.trim($quick_cache_options['base_dir'], '/'), TRUE);
					$this->plugin->remove_base_dir(); // Let's be extra sure that the old base directory is gone.

					$this->plugin->options['base_dir'] = $this->plugin->default_options['base_dir'];

					// Reset CRONs. We need CRONs to be set up again for ZenCache.

					$this->plugin->options['crons_setup'] = $this->plugin->default_options['crons_setup'];

					// Save revised options; reactive the plugin with the new options.

					update_option(__NAMESPACE__.'_options', $this->plugin->options);
					if(is_multisite()) update_site_option(__NAMESPACE__.'_options', $this->plugin->options);

					$this->plugin->activate(); // Reactivate plugin w/ new options.

					$this->plugin->enqueue_notice( // Give site owners a quick heads up about this.
						'<p>'.sprintf(__('<strong>Woohoo! %1$s activated.</strong> :-)', 'zencache'), esc_html($this->plugin->name)).'</p>'.
						'<p>'.sprintf(__('NOTE: Your Quick Cache options were preserved by %1$s (for more details, visit the <a href="http://zencache.com/r/quick-cache-lite-migration-faq/" target="_blank">Migration FAQ</a>).'.'', 'zencache'), esc_html($this->plugin->name)).'</p>'.
						'<p>'.sprintf(__('To review your configuration, please see: <a href="%2$s">%1$s ⥱ Plugin Options</a>.'.'', 'zencache'), esc_html($this->plugin->name), esc_attr(add_query_arg(urlencode_deep(array('page' => __NAMESPACE__)), self_admin_url('/admin.php')))).'</p>'
					);
				}
			}
		}
	}
}
