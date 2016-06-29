<?php
namespace WebSharks\CometCache\Classes;

/**
 * Feed Utils.
 *
 * @since 150422 Rewrite.
 */
class FeedUtils extends AbsBase
{
    /**
     * @type string WordPress `home_url()`.
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

        $this->home_url                = rtrim(home_url(), '/');
        $this->default_feed            = get_default_feed(); // Default feed type.
        $this->seo_friendly_permalinks = (boolean) get_option('permalink_structure');
        $this->feed_types              = array_unique([$this->default_feed, 'rdf', 'rss', 'rss2', 'atom']);
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
        $variations = []; // Initialize.

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
        $variations = []; // Initialize.

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
        $variations = []; // Initialize.

        foreach ($this->feed_types as $_feed_type) {
            $variations[] = get_author_feed_link($post->post_author, $_feed_type);
        }
        if ($this->seo_friendly_permalinks && ($post_author = get_userdata($post->post_author))) {
            foreach ($this->feed_types as $_feed_type) {
                $variations[] = add_query_arg(urlencode_deep(['author' => $post->post_author]), $this->home_url.'/feed/'.urlencode($_feed_type).'/');
                $variations[] = add_query_arg(urlencode_deep(['author' => $post_author->user_nicename]), $this->home_url.'/feed/'.urlencode($_feed_type).'/');
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
        $variations = []; // Initialize.

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
        $variations = $post_terms = []; // Initialize.

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

                if ($include_regex_wildcard_keys && $_term_feed_link && mb_strpos($_term_feed_link, '?') === false) {
                    // Quick example: `(?:123|slug)`; to consider both of these variations.
                    $_term_id_or_slug = '(?:'.preg_quote($_post_term->term_id, '/').
                        '|'.preg_quote(preg_replace('/[^a-z0-9\/.]/ui', '-', $_post_term->slug), '/').')';

                    // Quick example: `http://www.example.com/tax/term/feed`;
                    //    with a wildcard this becomes: `http://www.example.com/tax/*/feed`.
                    $_term_feed_link_with_wildcard = preg_replace('/\/[^\/]+\/feed([\/?#]|$)/u', '/*/feed'.'${1}', $_term_feed_link);

                    // Quick example: `http://www.example.com/tax/*/feed`;
                    //   becomes: `\/http\/www\.example\.com\/tax\/.*?(?=[\/\-]?(?:123|slug)[\/\-]).*?\/feed`
                    //    ... this covers variations that use: `/tax/term,term/feed/`.
                    //    ... also covers variations that use: `/tax/term/tax/term/feed/`.
                    $variations[$_feed_type.'::.*?(?=[\/\-]?'.$_term_id_or_slug.'[\/\-]).*?'] = $_term_feed_link_with_wildcard;
                    // This may also pick up false-positives. Not much we can do about this.
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
                    $variations[] = add_query_arg(urlencode_deep([$_taxonomy_query_var => $_post_term->term_id]), $this->home_url.'/feed/'.urlencode($_feed_type).'/');
                }
                foreach ($this->feed_types as $_feed_type) {
                    $variations[] = add_query_arg(urlencode_deep([$_taxonomy_query_var => $_post_term->slug]), $this->home_url.'/feed/'.urlencode($_feed_type).'/');
                }
            }
            unset($_taxonomy, $_taxonomy_query_var, $_feed_type); // Housekeeping.
        }
        unset($_post_term); // Housekeeping.

        return $variations;
    }

    /**
     * Convert variations into regex fragments for a call to `deleteFilesFromHostCacheDir()`.
     *
     * @since 150422 Rewrite. Updated 151002 w/ multisite compat. improvements.
     *
     * @param array $variations An array of variations (URLs) built by other class members.
     *
     * @return array An array of regex fragments for a call to `deleteFilesFromHostCacheDir()`.
     */
    public function convertVariationsToHostCachePathRegexFrags(array $variations)
    {
        $regex_frags                 = [];
        $is_multisite                = is_multisite();
        $can_consider_domain_mapping = $is_multisite && $this->plugin->canConsiderDomainMapping();
        $flags                       = $this::CACHE_PATH_NO_SCHEME | $this::CACHE_PATH_NO_HOST // Default flags.
                                       | $this::CACHE_PATH_NO_USER | $this::CACHE_PATH_NO_VSALT | $this::CACHE_PATH_NO_EXT;
        // Flags: note that we DO allow for query string data in these regex fragments.

        foreach ($variations as $_key => $_url) {
            $_url = trim((string) $_url); // Force string value.

            if ($_url && $is_multisite && $can_consider_domain_mapping) {
                // Shortest possible URI; i.e., consider domain mapping.
                $_url                  = $this->plugin->domainMappingUrlFilter($_url);
                $_is_url_domain_mapped = $_url && $this->plugin->domainMappingBlogId($_url);
            } else {
                $_is_url_domain_mapped = false; // No, obviously.
            }
            if (!$_url || !($_url_parts = $this->plugin->parseUrl($_url)) || empty($_url_parts['host'])) {
                continue; // Invalid variation.
            }
            $_host_base_dir_tokens = $this->plugin->hostBaseDirTokens(false, $_is_url_domain_mapped, !empty($_url_parts['path']) ? $_url_parts['path'] : '/');
            $_host_url             = rtrim('http://'.$_url_parts['host'].$_host_base_dir_tokens, '/');
            $_host_cache_path      = $this->plugin->buildCachePath($_host_url, '', '', $flags);

            if (is_string($_key) && mb_strpos($_key, '::') !== false && mb_strpos($_url, '*') !== false) {
                list($_feed_type, $_wildcard_regex) = explode('::', $_key, 2); // This regex replaces wildcards.
                $_cache_path                        = $this->plugin->buildCachePath($_url, '', '', $flags | $this::CACHE_PATH_ALLOW_WILDCARDS);
                $_relative_cache_path               = preg_replace('/^'.preg_quote($_host_cache_path, '/').'(?:\/|$)/ui', '', $_cache_path);
                $_relative_cache_path_regex         = preg_replace('/\\\\\*/u', $_wildcard_regex, preg_quote($_relative_cache_path, '/'));
            } else {
                $_cache_path                = $this->plugin->buildCachePath($_url, '', '', $flags); // Default flags.
                $_relative_cache_path       = preg_replace('/^'.preg_quote($_host_cache_path, '/').'(?:\/|$)/ui', '', $_cache_path);
                $_relative_cache_path_regex = preg_quote($_relative_cache_path, '/');
            }
            if ($_relative_cache_path_regex) {
                $regex_frags[] = $_relative_cache_path_regex; // No leading slash.
            }
        }
        unset($_key, $_url); // Housekeeping; for all temporary vars used above.
        unset($_is_url_domain_mapped, $_url_parts, $_host_base_dir_tokens, $_host_url, $_host_cache_path);
        unset($_feed_type, $_wildcard_regex, $_cache_path, $_relative_cache_path, $_relative_cache_path_regex);

        return $regex_frags ? array_unique($regex_frags) : $regex_frags;
    }
}
