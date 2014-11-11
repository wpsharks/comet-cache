<?php
/**
 * Feed Utilities
 *
 * @package quick_cache\utils_feed
 * @since 141110 Refactoring cache clear/purge routines.
 * @copyright WebSharks, Inc. <http://www.websharks-inc.com>
 * @license GNU General Public License, version 2
 */
namespace quick_cache // Root namespace.
{
	if(!defined('WPINC')) // MUST have WordPress.
		exit('Do NOT access this file directly: '.basename(__FILE__));

	/**
	 * Feed Utilities
	 */
	class utils_feed
	{
		/**
		 * @var plugin Quick Cache instance.
		 *
		 * @since 141110 Refactoring cache clear/purge routines.
		 */
		protected $plugin;

		/**
		 * @var string WordPress `home_url('/')`.
		 *
		 * @since 141110 Refactoring cache clear/purge routines.
		 */
		protected $home_url;

		/**
		 * @var string Default feed type; e.g. `rss2`.
		 *
		 * @since 141110 Refactoring cache clear/purge routines.
		 */
		protected $default_feed;

		/**
		 * @var boolean Using SEO-friendly permalinks?
		 *
		 * @since 141110 Refactoring cache clear/purge routines.
		 */
		protected $seo_friendly_permalinks;

		/**
		 * @var array All unique feed types.
		 *
		 * @since 141110 Refactoring cache clear/purge routines.
		 */
		protected $feed_types;

		/**
		 * Class constructor.
		 *
		 * @since 141110 Refactoring cache clear/purge routines.
		 */
		public function __construct()
		{
			$this->plugin = plugin();

			$this->home_url                = home_url('/');
			$this->default_feed            = get_default_feed();
			$this->seo_friendly_permalinks = (boolean)get_option('permalink_structure');
			$this->feed_types              = array_unique(array($this->default_feed, 'rdf', 'rss', 'rss2', 'atom'));
		}

		/**
		 * Feed link variations.
		 *
		 * @since 141110 Refactoring cache clear/purge routines.
		 *
		 * @param string $type_prefix A feed type prefix; optional.
		 *
		 * @return array An array of all feed link variations.
		 */
		public function feed_link_variations($type_prefix = '')
		{
			$variations = array(); // Initialize.

			foreach($this->feed_types as $_feed_type)
				$variations[] = get_feed_link((string)$type_prefix.$_feed_type);
			unset($_feed_type); // Housekeeping.

			return $variations;
		}

		/**
		 * Post comments; feed link variations.
		 *
		 * @since 141110 Refactoring cache clear/purge routines.
		 *
		 * @param \WP_Post A WordPress post class instance.
		 *
		 * @return array An array of all feed link variations.
		 */
		public function post_comments_feed_link_variations(\WP_Post $post)
		{
			$variations = array(); // Initialize.

			foreach($this->feed_types as $_feed_type)
				$variations[] = get_post_comments_feed_link($post->ID, $_feed_type);
			unset($_feed_type); // Housekeeping.

			return $variations;
		}

		/**
		 * Post author; feed link variations.
		 *
		 * @since 141110 Refactoring cache clear/purge routines.
		 *
		 * @param \WP_Post A WordPress post class instance.
		 *
		 * @return array An array of all feed link variations.
		 */
		public function post_author_feed_link_variations(\WP_Post $post)
		{
			$variations = array(); // Initialize.

			foreach($this->feed_types as $_feed_type)
				$variations[] = get_author_feed_link($post->post_author, $_feed_type);

			if($this->seo_friendly_permalinks && ($post_author = get_userdata($post->post_author)))
				foreach($this->feed_types as $_feed_type)
				{
					$variations[] = add_query_arg(urlencode_deep(array('author' => $post->post_author)), $this->home_url.'feed/'.urlencode($_feed_type).'/');
					$variations[] = add_query_arg(urlencode_deep(array('author' => $post_author->user_nicename)), $this->home_url.'feed/'.urlencode($_feed_type).'/');
				}
			unset($_feed_type); // Housekeeping.

			return $variations;
		}

		/**
		 * Post type archive; feed link variations.
		 *
		 * @since 141110 Refactoring cache clear/purge routines.
		 *
		 * @param \WP_Post A WordPress post class instance.
		 *
		 * @return array An array of all feed link variations.
		 */
		public function post_type_archive_link_variations(\WP_Post $post)
		{
			$variations = array(); // Initialize.

			foreach($this->feed_types as $_feed_type)
				$variations[] = get_post_type_archive_feed_link($post->post_type, $_feed_type);
			unset($_feed_type); // Housekeeping.

			return $variations;
		}

		/**
		 * Post terms; feed link variations.
		 *
		 * @since 141110 Refactoring cache clear/purge routines.
		 *
		 * @param \WP_Post A WordPress post class instance.
		 *
		 * @param boolean  $include_regex_wildcard_keys Defaults to a `FALSE` value.
		 *    If `TRUE`, some associative array keys will be returned also.
		 *
		 * @return array An array of all feed link variations.
		 *
		 * @note If `$include_regex_wildcard_keys` is `TRUE`...
		 *    This particular method may return some associative keys also.
		 *    For string/associative keys, each key is `[feed type]::[regex]`, and the feed link/URL
		 *    will contain a single `*` wildcard character where the `[regex]` pattern should go.
		 */
		public function post_term_feed_link_variations(\WP_Post $post, $include_regex_wildcard_keys = FALSE)
		{
			$variations = array(); // Initialize.
			$post_terms = array(); // Initialize.

			if(!is_array($post_taxonomies = get_object_taxonomies($post, 'objects')) || !$post_taxonomies)
				return $variations; // Nothing to do here; post has no terms.

			foreach($post_taxonomies as $_post_taxonomy) // Collect terms for each taxonomy.
				if(is_array($_post_taxonomy_terms = wp_get_post_terms($post->ID, $_post_taxonomy->name)) && $_post_taxonomy_terms)
					$post_terms = array_merge($post_terms, $_post_taxonomy_terms);
			unset($_post_taxonomy, $_post_taxonomy_terms); // Housekeeping.

			foreach($post_terms as $_post_term) // Iterate all post terms.
			{
				foreach($this->feed_types as $_feed_type)
				{
					$_term_feed_link = get_term_feed_link($_post_term->term_id, $_post_term->taxonomy, $_feed_type);
					$variations[]    = $_term_feed_link; // Add this variation; always.

					if($include_regex_wildcard_keys && $_term_feed_link && strpos($_term_feed_link, '?') === FALSE)
					{
						// Quick example: `(?:123|slug)`; to consider both.
						$_term_id_or_slug = '(?:'.preg_quote($_post_term->term_id, '/').
						                    '|'.preg_quote(preg_replace('/[^a-z0-9\/.]/i', '-', $_post_term->slug), '/').')';

						// Quick example: `http://www.example.com/tax/term/feed`;
						//    with a wildcard this becomes: `http://www.example.com/tax/*/feed`.
						$_term_feed_link_with_wildcard = preg_replace('/\/[^\/]+\/feed([\/?#]|$)/', '/*/feed'.'${1}', $_term_feed_link);

						// Quick example: `http://www.example.com/tax/*/feed`;
						//   becomes: `\/http\/www\.example\.com\/tax\/.*?(?=[\/\-]?(?:123|slug)[\/\-]).*?\/feed`
						//    ... this covers variations that use: `/tax/term,term/feed/`.
						//    ... also covers variations that use: `/tax/term/tax/term/feed/`.
						$variations[$_feed_type.'::.*?(?=[\/\-]?'.$_term_id_or_slug.'[\/\-]).*?'] = $_term_feed_link_with_wildcard;
						// NOTE: This may also pick up false-positives. Not much we can do about this.
						//    For instance, if another feed has the same word/slug in what is actually a longer/different term.
						//    Or, if another feed has the same word/slug in what is actually the name of a taxonomy.
					}
				}
				unset($_feed_type, $_term_feed_link, $_term_id_or_slug, $_term_feed_link_with_wildcard); // Housekeeping.

				if($this->seo_friendly_permalinks && is_object($_taxonomy = get_taxonomy($_post_term->taxonomy)))
				{
					if($_taxonomy->name === 'category')
						$_taxonomy_query_var = 'cat'; // Special case.
					else $_taxonomy_query_var = $_taxonomy->query_var;

					foreach($this->feed_types as $_feed_type)
						$variations[] = add_query_arg(urlencode_deep(array($_taxonomy_query_var => $_post_term->term_id)), $this->home_url.'feed/'.urlencode($_feed_type).'/');
					unset($_feed_type); // Housekeeping.

					foreach($this->feed_types as $_feed_type)
						$variations[] = add_query_arg(urlencode_deep(array($_taxonomy_query_var => $_post_term->slug)), $this->home_url.'feed/'.urlencode($_feed_type).'/');
					unset($_feed_type); // Housekeeping.
				}
				unset($_taxonomy, $_taxonomy_query_var); // Housekeeping.
			}
			unset($_post_term); // Housekeeping.

			return $variations;
		}

		/**
		 * Convert variations into regex fragments; relative to the current host|blog directory.
		 *
		 * @since 141110 Refactoring cache clear/purge routines.
		 *
		 * @param array $variations An array of variations built by other class members.
		 *
		 * @return array An array of all feed link variations; converted to regex fragments.
		 *    Regex fragments are relative to the current host|blog directory.
		 *
		 * @note This automatically forces the following {@link build_cache_path()} flags.
		 *
		 *       - {@link CACHE_PATH_NO_SCHEME}
		 *       - {@link CACHE_PATH_NO_HOST}
		 *       - {@link CACHE_PATH_NO_USER}
		 *       - {@link CACHE_PATH_NO_VSALT}
		 *       - {@link CACHE_PATH_NO_EXT}
		 *       - {@link CACHE_PATH_ALLOW_WILDCARDS}; when applicable.
		 */
		public function convert_variations_to_host_cache_path_regex_frags(array $variations)
		{
			$plugin      = $this->plugin; // For proper syntax.
			$regex_frags = array(); // Initialize regex variation frags.

			$flags = $plugin::CACHE_PATH_NO_SCHEME | $plugin::CACHE_PATH_NO_HOST
			         | $plugin::CACHE_PATH_NO_USER | $plugin::CACHE_PATH_NO_VSALT
			         | $plugin::CACHE_PATH_NO_EXT;

			$host                  = $_SERVER['HTTP_HOST'];
			$host_base_dir_tokens  = $this->plugin->host_base_dir_tokens();
			$host_url              = rtrim('http://'.$host.$host_base_dir_tokens, '/');
			$host_cache_path_flags = $flags | $plugin::CACHE_PATH_NO_QUV; // Add one more flag here.
			$host_cache_path       = $this->plugin->build_cache_path($host_url, '', '', $host_cache_path_flags);

			foreach($variations as $_key => $_variation)
			{
				if(!$_variation || !is_string($_variation))
					continue; // Invalid variation.

				if(is_string($_key) && strpos($_key, '::') !== FALSE && strpos($_variation, '*') !== FALSE)
				{
					$_flags = $flags | $plugin::CACHE_PATH_ALLOW_WILDCARDS;
					list($_feed_type, $_wildcard_regex) = explode('::', $_key, 2);

					$_cache_path                = $this->plugin->build_cache_path($_variation, '', '', $_flags);
					$_relative_cache_path       = preg_replace('/^'.preg_quote($host_cache_path, '/').'(?:\/|$)/i', '', $_cache_path);
					$_relative_cache_path_regex = preg_replace('/\\\\\*/', $_wildcard_regex, preg_quote($_relative_cache_path, '/'));

					$regex_frags[] = $_relative_cache_path_regex; // Add variation now.

					unset($_flags, $_feed_type, $_wildcard_regex);// Housekeeping.
					unset($_cache_path, $_relative_cache_path, $_relative_cache_path_regex);
				}
				else // This is just a regular variation; i.e. a URL without any regex/wildcard to parse.
				{
					$_cache_path                = $this->plugin->build_cache_path($_variation, '', '', $flags);
					$_relative_cache_path       = preg_replace('/^'.preg_quote($host_cache_path, '/').'(?:\/|$)/i', '', $_cache_path);
					$_relative_cache_path_regex = preg_quote($_relative_cache_path, '/');

					$regex_frags[] = $_relative_cache_path_regex; // Add variation now.

					unset($_cache_path, $_relative_cache_path, $_relative_cache_path_regex);
				}
			}
			unset($_key, $_variation); // Housekeeping.

			return $regex_frags;
		}
	}
}