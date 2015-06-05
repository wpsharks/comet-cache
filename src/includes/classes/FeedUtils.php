<?php
namespace WebSharks\ZenCache;

/**
 * Feed Utils.
 *
 * @since 150422 Rewrite.
 */
class FeedUtils extends AbsBase
{
    /**
     * @type string WordPress `home_url('/')`.
     *
     * @since 150422 Rewrite.
     */
    protected $home_url;

    /**
     * @type string Default feed type; e.g. `rss2`.
     *
     * @since 150422 Rewrite.
     */
    protected $default_feed;

    /**
     * @type bool Using SEO-friendly permalinks?
     *
     * @since 150422 Rewrite.
     */
    protected $seo_friendly_permalinks;

    /**
     * @type array All unique feed types.
     *
     * @since 150422 Rewrite.
     */
    protected $feed_types;

    /**
     * Class constructor.
     *
     * @since 150422 Rewrite.
     */
    public function __construct()
    {
        parent::__construct();

        $this->home_url                = home_url('/');
        $this->default_feed            = get_default_feed();
        $this->seo_friendly_permalinks = (boolean) get_option('permalink_structure');
        $this->feed_types              = array_unique(array($this->default_feed, 'rdf', 'rss', 'rss2', 'atom'));
    }

    /**
     * Feed link variations.
     *
     * @since 150422 Rewrite.
     *
     * @param string $type_prefix A feed type prefix; optional.
     *
     * @return array An array of all feed link variations.
     */
    public function feedLinkVariations($type_prefix = '')
    {
        $variations = array(); // Initialize.

        foreach ($this->feed_types as $_feed_type) {
            $variations[] = get_feed_link((string) $type_prefix.$_feed_type);
        }
        unset($_feed_type); // Housekeeping.

        return $variations;
    }

    /**
     * Post comments; feed link variations.
     *
     * @since 150422 Rewrite.
     *
     * @param \WP_Post A WordPress post class instance.
     *
     * @return array An array of all feed link variations.
     */
    public function postCommentsFeedLinkVariations(\WP_Post $post)
    {
        $variations = array(); // Initialize.

        foreach ($this->feed_types as $_feed_type) {
            $variations[] = get_post_comments_feed_link($post->ID, $_feed_type);
        }
        unset($_feed_type); // Housekeeping.

        return $variations;
    }

    /**
     * Post author; feed link variations.
     *
     * @since 150422 Rewrite.
     *
     * @param \WP_Post A WordPress post class instance.
     *
     * @return array An array of all feed link variations.
     */
    public function postAuthorFeedLinkVariations(\WP_Post $post)
    {
        $variations = array(); // Initialize.

        foreach ($this->feed_types as $_feed_type) {
            $variations[] = get_author_feed_link($post->post_author, $_feed_type);
        }
        if ($this->seo_friendly_permalinks && ($post_author = get_userdata($post->post_author))) {
            foreach ($this->feed_types as $_feed_type) {
                $variations[] = add_query_arg(urlencode_deep(array('author' => $post->post_author)), $this->home_url.'feed/'.urlencode($_feed_type).'/');
                $variations[] = add_query_arg(urlencode_deep(array('author' => $post_author->user_nicename)), $this->home_url.'feed/'.urlencode($_feed_type).'/');
            }
        }
        unset($_feed_type); // Housekeeping.

        return $variations;
    }

    /**
     * Post type archive; feed link variations.
     *
     * @since 150422 Rewrite.
     *
     * @param \WP_Post A WordPress post class instance.
     *
     * @return array An array of all feed link variations.
     */
    public function postTypeArchiveFeedLinkVariations(\WP_Post $post)
    {
        $variations = array(); // Initialize.

        foreach ($this->feed_types as $_feed_type) {
            $variations[] = get_post_type_archive_feed_link($post->post_type, $_feed_type);
        }
        unset($_feed_type); // Housekeeping.

        return $variations;
    }

    /**
     * Post terms; feed link variations.
     *
     * @since 150422 Rewrite.
     *
     * @param \WP_Post A WordPress post class instance.
     * @param bool $include_regex_wildcard_keys Defaults to a `FALSE` value.
     *
     * @return array An array of all feed link variations.
     *
     * @note If `$include_regex_wildcard_keys` is `TRUE`:
     *    This method may return some associative keys also.
     *    For string/associative keys, each key is `[feed type]::[regex]`, and the feed link/URL
     *    will contain a single `*` wildcard character where the `[regex]` pattern should go.
     */
    public function postTermFeedLinkVariations(\WP_Post $post, $include_regex_wildcard_keys = false)
    {
        $variations = $post_terms = array(); // Initialize.

        if (!is_array($post_taxonomies = get_object_taxonomies($post, 'objects')) || !$post_taxonomies) {
            return $variations; // Nothing to do here; post has no terms.
        }
        foreach ($post_taxonomies as $_post_taxonomy) {
            if (is_array($_post_taxonomy_terms = wp_get_post_terms($post->ID, $_post_taxonomy->name)) && $_post_taxonomy_terms) {
                $post_terms = array_merge($post_terms, $_post_taxonomy_terms);
            }
            unset($_post_taxonomy, $_post_taxonomy_terms);
        }
        foreach ($post_terms as $_post_term) {
            foreach ($this->feed_types as $_feed_type) {
                $_term_feed_link = get_term_feed_link($_post_term->term_id, $_post_term->taxonomy, $_feed_type);
                $variations[]    = $_term_feed_link; // Add this variation; always.

                if ($include_regex_wildcard_keys && $_term_feed_link && strpos($_term_feed_link, '?') === false) {
                    // Quick example: `(?:123|slug)`; to consider both of these variations.
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

            if ($this->seo_friendly_permalinks && is_object($_taxonomy = get_taxonomy($_post_term->taxonomy))) {
                if ($_taxonomy->name === 'category') {
                    $_taxonomy_query_var = 'cat';
                } else {
                    $_taxonomy_query_var = $_taxonomy->query_var;
                }
                foreach ($this->feed_types as $_feed_type) {
                    $variations[] = add_query_arg(urlencode_deep(array($_taxonomy_query_var => $_post_term->term_id)), $this->home_url.'feed/'.urlencode($_feed_type).'/');
                }
                foreach ($this->feed_types as $_feed_type) {
                    $variations[] = add_query_arg(urlencode_deep(array($_taxonomy_query_var => $_post_term->slug)), $this->home_url.'feed/'.urlencode($_feed_type).'/');
                }
            }
            unset($_taxonomy, $_taxonomy_query_var, $_feed_type); // Housekeeping.
        }
        unset($_post_term); // Housekeeping.

        return $variations;
    }

    /**
     * Convert variations into regex fragments; relative to the current host|blog directory.
     *
     * @since 150422 Rewrite.
     *
     * @param array $variations An array of variations built by other class members.
     *
     * @return array An array of all feed link variations; converted to regex fragments.
     *               Regex fragments are relative to the current host|blog directory.
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
    public function convertVariationsToHostCachePathRegexFrags(array $variations)
    {
        $regex_frags = array(); // Initialize.

        $flags = CACHE_PATH_NO_SCHEME | CACHE_PATH_NO_HOST
                 | CACHE_PATH_NO_USER | CACHE_PATH_NO_VSALT
                 | CACHE_PATH_NO_EXT;

        $host                  = !empty($_SERVER['HTTP_HOST'])
            ? (string) $_SERVER['HTTP_HOST'] : '';
        $host_base_dir_tokens  = $this->plugin->hostBaseDirTokens();
        $host_url              = rtrim('http://'.$host.$host_base_dir_tokens, '/');
        $host_cache_path_flags = $flags | CACHE_PATH_NO_QUV; // Add one more flag here.
        $host_cache_path       = $this->plugin->buildCachePath($host_url, '', '', $host_cache_path_flags);

        foreach ($variations as $_key => $_variation) {
            if (!$_variation || !is_string($_variation)) {
                continue; // Invalid variation.
            }
            if (is_string($_key) && strpos($_key, '::') !== false && strpos($_variation, '*') !== false) {
                $_flags                             = $flags | CACHE_PATH_ALLOW_WILDCARDS;
                list($_feed_type, $_wildcard_regex) = explode('::', $_key, 2);

                $_cache_path                = $this->plugin->buildCachePath($_variation, '', '', $_flags);
                $_relative_cache_path       = preg_replace('/^'.preg_quote($host_cache_path, '/').'(?:\/|$)/i', '', $_cache_path);
                $_relative_cache_path_regex = preg_replace('/\\\\\*/', $_wildcard_regex, preg_quote($_relative_cache_path, '/'));

                $regex_frags[] = $_relative_cache_path_regex;

                unset($_flags, $_feed_type, $_wildcard_regex);// Housekeeping.
                unset($_cache_path, $_relative_cache_path, $_relative_cache_path_regex);
            } else {
                // This is just a regular variation; i.e. a URL without any regex/wildcard to parse.
                $_cache_path                = $this->plugin->buildCachePath($_variation, '', '', $flags);
                $_relative_cache_path       = preg_replace('/^'.preg_quote($host_cache_path, '/').'(?:\/|$)/i', '', $_cache_path);
                $_relative_cache_path_regex = preg_quote($_relative_cache_path, '/');

                $regex_frags[] = $_relative_cache_path_regex;

                unset($_cache_path, $_relative_cache_path, $_relative_cache_path_regex);
            }
        }
        unset($_key, $_variation); // Housekeeping.

        return $regex_frags;
    }
}
