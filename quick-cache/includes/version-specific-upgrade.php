<?php
namespace quick_cache // Root namespace.
{
	if(!defined('WPINC')) // MUST have WordPress.
		exit('Do NOT access this file directly: '.basename(__FILE__));

	if(!class_exists('\\'.__NAMESPACE__.'\\version_specific_upgrade'))
	{
		/**
		 * Quick Cache (Upgrade Handlers)
		 *
		 * @since 140725 Reorganizing class members.
		 * @package quick_cache\version_specific_upgrade
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
				$this->from_lt_v141105();
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

					$this->plugin->enqueue_notice(__('<strong>Quick Cache:</strong> this version is a <strong>complete rewrite</strong> :-) Please review your Quick Cache options carefully!', $this->plugin->text_domain));
				}
			}

			/*
			 * Upgrading from a version prior to v140104 where we introduced feed caching.
			 */
			public function from_lt_v140104()
			{
				if(version_compare($this->prev_version, '140104', '<')) // When this sort of update occurs, we issue a notice about this new feature.
					$this->plugin->enqueue_notice(__('<strong>Quick Cache Feature Notice:</strong> This version of Quick Cache adds new options for Feed caching. Feed caching is now disabled by default. If you wish to enable feed caching, please visit the Quick Cache options panel.', $this->plugin->text_domain));
			}

			/*
			 * Upgrading from a version prior to v140605, where we introduced a branched cache structure.
			 * See <https://github.com/WebSharks/Quick-Cache/issues/147#issuecomment-42659131>
			 *    We also also moved to a base directory layout.
			 */
			public function from_lt_v140605()
			{
				if(version_compare($this->prev_version, '140605', '<'))
				{
					if(!empty($this->plugin->options['cache_dir']))
					{
						$wp_content_dir_relative = // We considered custom locations.
							trim(str_replace(ABSPATH, '', WP_CONTENT_DIR), '\\/'." \t\n\r\0\x0B");

						$this->plugin->options['base_dir'] = $this->plugin->options['cache_dir']
							= trim($this->plugin->options['cache_dir'], '\\/'." \t\n\r\0\x0B");

						if(!$this->plugin->options['base_dir'] || $this->plugin->options['base_dir'] === $wp_content_dir_relative.'/cache')
							$this->plugin->options['base_dir'] = $this->plugin->default_options['base_dir'];

						if($this->plugin->options['cache_dir']) // Wipe old files?
							$this->plugin->wipe_cache(FALSE, ABSPATH.$this->plugin->options['cache_dir']);
						unset($this->plugin->options['cache_dir']);

						update_option(__NAMESPACE__.'_options', $this->plugin->options);
						if(is_multisite()) update_site_option(__NAMESPACE__.'_options', $this->plugin->options);
					}
					$this->plugin->enqueue_notice(__('<strong>Quick Cache Feature Notice:</strong> This version of Quick Cache introduces a new <a href="http://www.websharks-inc.com/r/quick-cache-branched-cache-structure-wiki/" target="_blank">Branched Cache Structure</a> and several other <a href="http://www.websharks-inc.com/post/quick-cache-v140605-now-available/" target="_blank">new features</a>.', $this->plugin->text_domain));
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
					if(is_array($existing_options = get_option(__NAMESPACE__.'_options')))
					{
						if(!empty($existing_options['base_dir']) && stripos($existing_options['base_dir'], basename(WP_CONTENT_DIR)) !== FALSE)
						{
							$this->plugin->wipe_cache(FALSE, ABSPATH.$existing_options['base_dir']);
							$this->plugin->options['base_dir'] = $this->plugin->default_options['base_dir'];

							update_option(__NAMESPACE__.'_options', $this->plugin->options);
							if(is_multisite()) update_site_option(__NAMESPACE__.'_options', $this->plugin->options);

							$this->plugin->enqueue_notice( // Give site owners a quick heads up about this.
								'<p>'.__('<strong>Quick Cache Notice:</strong> This version of Quick Cache changes the default base directory that it uses, from <code>ABSPATH</code> to <code>WP_CONTENT_DIR</code>. This is for improved compatibility with installations that choose to use a custom <code>WP_CONTENT_DIR</code> location.', $this->plugin->text_domain).
								' '.__('Quick Cache has detected that your previously configured cache directory may have been in conflict with this change. As a result, your Quick Cache configuration has been updated to the new default value; just to keep things running smoothly for you :-). If you would like to review this change, please see: <code>Dashboard ⥱ Quick Cache ⥱ Directory &amp; Expiration Time</code>; where you may customize it further if necessary.', $this->plugin->text_domain).'</p>'
							);
						}
					}
				}
			}

			/**
			 * Upgrading from a version before we changed several `cache_purge_*` optinos to `cache_clear_*`.
			 *    If so, we need to use the existing options to fill the new keys.
			 *    And, of course, then we save the updated options.
			 */
			public function from_lt_v141009()
			{
				if(version_compare($this->prev_version, '141009', '<'))
				{
					if(is_array($existing_options = get_option(__NAMESPACE__.'_options')))
					{
						foreach(array('cache_purge_home_page_enable',
						              'cache_purge_posts_page_enable',
						              'cache_purge_author_page_enable',
						              'cache_purge_term_category_enable',
						              'cache_purge_term_post_tag_enable',
						              'cache_purge_term_other_enable'
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

			/*
			 * Upgrading from a version prior to the first announcement that Quick Cache is changing its name to ZenCache
			 */
			public function from_lt_v141105()
			{
				if(version_compare($this->prev_version, '141105', '<'))
					$this->plugin->enqueue_notice(__('<strong>Important Quick Cache Announcement:</strong> Quick Cache is changing its name to ZenCache! Read more about this change <a href="http://www.websharks-inc.com/post/quick-cache-is-changing-its-name/" target="_blank">here</a>.', $this->plugin->text_domain), 'persistent-quick-cache-to-zencache-notice1', TRUE);
			}
		}
	}
}