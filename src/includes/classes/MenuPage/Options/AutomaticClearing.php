<?php
namespace WebSharks\CometCache\Classes\MenuPage\Options;

use WebSharks\CometCache\Classes;

/**
 * Options section.
 *
 * @since 17xxxx Refactor menu pages.
 */
class AutomaticClearing extends Classes\AbsBase
{
    /**
     * Constructor.
     *
     * @since 17xxxx Refactor menu pages.
     */
    public function __construct()
    {
        parent::__construct(); // Parent constructor.

        echo '<div class="plugin-menu-page-panel'.(!IS_PRO && $this->plugin->isProPreview() ? ' pro-preview' : '').'">'."\n";

        echo '   <a href="#" class="plugin-menu-page-panel-heading" data-additional-pro-features="'.(!IS_PRO && $this->plugin->isProPreview() ? __('additional pro features', 'comet-cache') : '').'">'."\n";
        echo '      <i class="si si-server"></i> '.__('Automatic Cache Clearing', 'comet-cache')."\n";
        echo '   </a>'."\n";

        echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";

        echo '      <h3>'.__('Clearing the Cache Automatically', 'comet-cache').'</h3>'."\n";
        echo '      <img src="'.esc_attr($this->plugin->url('/src/client-s/images/auto-clear-ss.png')).'" class="screenshot" />'."\n";
        echo '      <p>'.sprintf(__('This is built into the %1$s plugin; i.e., this functionality is "always on". If you edit a Post/Page (or delete one), %1$s will automatically clear the cache file(s) associated with that content. This way a new updated version of the cache will be created automatically the next time this content is accessed. Simple updates like this occur each time you make changes in the Dashboard, and %1$s will notify you of these as they occur. %1$s monitors changes to Posts (of any kind, including Pages), Categories, Tags, Links, Themes (even Users), and more.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
        if (IS_PRO || $this->plugin->isProPreview()) {
            echo '  <div data-pro-version-only="'.(!IS_PRO ? __('pro version only', 'comet-cache') : '').'">'."\n";
            echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][change_notifications_enable]" class="-no-if-enabled" style="width:auto;">'."\n";
            echo '          <option value="1"'.selected($this->plugin->options['change_notifications_enable'], '1', false).'>'.sprintf(__('Yes, enable %1$s notifications in the Dashboard when cache files are cleared automatically.', 'comet-cache'), esc_html(NAME)).'</option>'."\n";
            echo '          <option value="0"'.selected($this->plugin->options['change_notifications_enable'], '0', false).'>'.sprintf(__('No, I don\'t want to know (don\'t really care) what %1$s is doing behind-the-scene.', 'comet-cache'), esc_html(NAME)).'</option>'."\n";
            echo '      </select></p>'."\n";
            echo '  </div>'."\n";
        }
        echo '      <hr />'."\n";
        echo '      <h3>'.__('Primary Page Options', 'comet-cache').'</h3>'."\n";

        echo '      <h4 style="margin-bottom:0;">'.__('Auto-Clear Designated "Home Page" Too?', 'comet-cache').'</h4>'."\n";
        echo '      <p style="margin-top:2px;">'.sprintf(__('On many sites, the Home Page (aka: the Front Page) offers an archive view of all Posts (or even Pages). Therefore, if a single Post/Page is changed in some way; and %1$s clears/resets the cache for a single Post/Page, would you like %1$s to also clear any existing cache files for the "Home Page"?', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_home_page_enable]" class="-no-if-enabled">'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['cache_clear_home_page_enable'], '1', false).'>'.__('Yes, if any single Post/Page is cleared/reset; also clear the "Home Page".', 'comet-cache').'</option>'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['cache_clear_home_page_enable'], '0', false).'>'.__('No, my Home Page does not provide a list of Posts/Pages; e.g., this is not necessary.', 'comet-cache').'</option>'."\n";
        echo '         </select></p>'."\n";
        echo '      <h4 style="margin-bottom:0;">'.__('Auto-Clear Designated "Posts Page" Too?', 'comet-cache').'</h4>'."\n";
        echo '      <p style="margin-top:2px;">'.sprintf(__('On many sites, the Posts Page (aka: the Blog Page) offers an archive view of all Posts (or even Pages). Therefore, if a single Post/Page is changed in some way; and %1$s clears/resets the cache for a single Post/Page, would you like %1$s to also clear any existing cache files for the "Posts Page"?', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_posts_page_enable]" class="-no-if-enabled">'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['cache_clear_posts_page_enable'], '1', false).'>'.__('Yes, if any single Post/Page is cleared/reset; also clear the "Posts Page".', 'comet-cache').'</option>'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['cache_clear_posts_page_enable'], '0', false).'>'.__('No, I don\'t use a separate Posts Page; e.g., my Home Page IS my Posts Page.', 'comet-cache').'</option>'."\n";
        echo '         </select></p>'."\n";

        echo '      <hr />'."\n";
        echo '      <h3>'.__('Author, Archive, and Tag/Term Options', 'comet-cache').'</h3>'."\n";

        echo '      <h4 style="margin-bottom:0;">'.__('Auto-Clear "Author Page" Too?', 'comet-cache').'</h4>'."\n";
        echo '      <p style="margin-top:2px;">'.sprintf(__('On many sites, each author has a related "Author Page" that offers an archive view of all posts associated with that author. Therefore, if a single Post/Page is changed in some way; and %1$s clears/resets the cache for a single Post/Page, would you like %1$s to also clear any existing cache files for the related "Author Page"?', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_author_page_enable]" class="-no-if-enabled">'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['cache_clear_author_page_enable'], '1', false).'>'.__('Yes, if any single Post/Page is cleared/reset; also clear the "Author Page".', 'comet-cache').'</option>'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['cache_clear_author_page_enable'], '0', false).'>'.__('No, my site doesn\'t use multiple authors and/or I don\'t have any "Author Page" archive views.', 'comet-cache').'</option>'."\n";
        echo '         </select></p>'."\n";

        echo '      <h4 style="margin-bottom:0;">'.__('Auto-Clear "Category Archives" Too?', 'comet-cache').'</h4>'."\n";
        echo '      <p style="margin-top:2px;">'.sprintf(__('On many sites, each post is associated with at least one Category. Each category then has an archive view that contains all the posts within that category. Therefore, if a single Post/Page is changed in some way; and %1$s clears/resets the cache for a single Post/Page, would you like %1$s to also clear any existing cache files for the associated Category archive views?', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_term_category_enable]" class="-no-if-enabled">'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['cache_clear_term_category_enable'], '1', false).'>'.__('Yes, if any single Post/Page is cleared/reset; also clear the associated Category archive views.', 'comet-cache').'</option>'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['cache_clear_term_category_enable'], '0', false).'>'.__('No, my site doesn\'t use Categories and/or I don\'t have any Category archive views.', 'comet-cache').'</option>'."\n";
        echo '         </select></p>'."\n";

        echo '      <h4 style="margin-bottom:0;">'.__('Auto-Clear "Tag Archives" Too?', 'comet-cache').'</h4>'."\n";
        echo '      <p style="margin-top:2px;">'.sprintf(__('On many sites, each post may be associated with at least one Tag. Each tag then has an archive view that contains all the posts assigned that tag. Therefore, if a single Post/Page is changed in some way; and %1$s clears/resets the cache for a single Post/Page, would you like %1$s to also clear any existing cache files for the associated Tag archive views?', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_term_post_tag_enable]" class="-no-if-enabled">'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['cache_clear_term_post_tag_enable'], '1', false).'>'.__('Yes, if any single Post/Page is cleared/reset; also clear the associated Tag archive views.', 'comet-cache').'</option>'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['cache_clear_term_post_tag_enable'], '0', false).'>'.__('No, my site doesn\'t use Tags and/or I don\'t have any Tag archive views.', 'comet-cache').'</option>'."\n";
        echo '         </select></p>'."\n";

        echo '      <h4 style="margin-bottom:0;">'.__('Auto-Clear "Date-Based Archives" Too?', 'comet-cache').'</h4>'."\n";
        echo '      <p style="margin-top:2px;">'.sprintf(__('Date-Based Archives allow visitors to browse Posts by the year, month, or day they were originally published. If a single Post (of any type) is changed in some way; and %1$s clears/resets the cache for that Post, would you like %1$s to also clear any existing cache files for Dated-Based Archives that match the publication time?', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_date_archives_enable]" class="-no-if-enabled">'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['cache_clear_date_archives_enable'], '1', false).'>'.__('Yes, if any single Post is cleared/reset, also clear the associated Date archive views.', 'comet-cache').'</option>'."\n";
        echo '            <option value="2"'.selected($this->plugin->options['cache_clear_date_archives_enable'], '2', false).'>'.__('Yes, but only clear the associated Day and Month archive views.', 'comet-cache').'</option>'."\n";
        echo '            <option value="3"'.selected($this->plugin->options['cache_clear_date_archives_enable'], '3', false).'>'.__('Yes, but only clear the associated Day archive view.', 'comet-cache').'</option>'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['cache_clear_date_archives_enable'], '0', false).'>'.__('No, don\'t clear any associated Date archive views.', 'comet-cache').'</option>'."\n";
        echo '         </select></p>'."\n";

        echo '      <h4 style="margin-bottom:0;">'.__('Auto-Clear "Custom Term Archives" Too?', 'comet-cache').'</h4>'."\n";
        echo '      <p style="margin-top:2px;">'.sprintf(__('Most sites do not use any custom Terms so it should be safe to leave this disabled. However, if your site uses custom Terms and they have their own Term archive views, you may want to clear those when the associated post is cleared. Therefore, if a single Post/Page is changed in some way; and %1$s clears/resets the cache for a single Post/Page, would you like %1$s to also clear any existing cache files for the associated Tag archive views?', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_term_other_enable]" class="-no-if-enabled">'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['cache_clear_term_other_enable'], '1', false).'>'.__('Yes, if any single Post/Page is cleared/reset; also clear any associated custom Term archive views.', 'comet-cache').'</option>'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['cache_clear_term_other_enable'], '0', false).'>'.__('No, my site doesn\'t use any custom Terms and/or I don\'t have any custom Term archive views.', 'comet-cache').'</option>'."\n";
        echo '         </select></p>'."\n";

        echo '      <h4 style="margin-bottom:0;">'.__('Auto-Clear "Custom Post Type Archives" Too?', 'comet-cache').'</h4>'."\n";
        echo '      <p style="margin-top:2px;">'.sprintf(__('Most sites do not use any Custom Post Types so it should be safe to disable this option. However, if your site uses Custom Post Types and they have their own Custom Post Type archive views, you may want to clear those when any associated post is cleared. Therefore, if a single Post with a Custom Post Type is changed in some way; and %1$s clears/resets the cache for that post, would you like %1$s to also clear any existing cache files for the associated Custom Post Type archive views?', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_custom_post_type_enable]" class="-no-if-enabled">'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['cache_clear_custom_post_type_enable'], '1', false).'>'.__('Yes, if any single Post with a Custom Post Type is cleared/reset; also clear any associated Custom Post Type archive views.', 'comet-cache').'</option>'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['cache_clear_custom_post_type_enable'], '0', false).'>'.__('No, my site doesn\'t use any Custom Post Types and/or I don\'t have any Custom Post Type archive views.', 'comet-cache').'</option>'."\n";
        echo '         </select></p>'."\n";

        echo '      <hr />'."\n";
        echo '      <h3>'.__('Feed-Related Options', 'comet-cache').'</h3>'."\n";

        echo '      <h4 style="margin-bottom:0;">'.__('Auto-Clear "RSS/RDF/ATOM Feeds" Too?', 'comet-cache').'</h4>'."\n";
        echo '      <p style="margin-top:2px;">'.sprintf(__('If you enable Feed Caching (below), this can be quite handy. If enabled, when you update a Post/Page, approve a Comment, or make other changes where %1$s can detect that certain types of Feeds should be cleared to keep your site up-to-date, then %1$s will do this for you automatically. For instance, the blog\'s master feed, the blog\'s master comments feed, feeds associated with comments on a Post/Page, term-related feeds (including mixed term-related feeds), author-related feeds, etc. Under various circumstances (i.e., as you work in the Dashboard) these can be cleared automatically to keep your site up-to-date.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_xml_feeds_enable]" class="-no-if-enabled">'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['cache_clear_xml_feeds_enable'], '1', false).'>'.__('Yes, automatically clear RSS/RDF/ATOM Feeds from the cache when certain changes occur.', 'comet-cache').'</option>'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['cache_clear_xml_feeds_enable'], '0', false).'>'.__('No, I don\'t have Feed Caching enabled, or I prefer not to automatically clear Feeds.', 'comet-cache').'</option>'."\n";
        echo '         </select></p>'."\n";

        echo '      <hr />'."\n";
        echo '      <h3>'.__('Sitemap-Related Options', 'comet-cache').'</h3>'."\n";

        echo '      <h4 style="margin-bottom:0;">'.__('Auto-Clear "XML Sitemaps" Too?', 'comet-cache').'</h4>'."\n";
        echo '      <p style="margin-top:2px;">'.sprintf(__('If you\'re generating XML Sitemaps with a plugin like <a href="http://wordpress.org/plugins/google-sitemap-generator/" target="_blank">Google XML Sitemaps</a>, you can tell %1$s to automatically clear the cache of any XML Sitemaps whenever it clears a Post/Page. Note: This does NOT clear the XML Sitemap itself of course, only the cache. The point being, to clear the cache and allow changes to a Post/Page to be reflected by a fresh copy of your XML Sitemap; sooner rather than later.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_xml_sitemaps_enable]" data-target=".-cache-clear-xml-sitemap-patterns">'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['cache_clear_xml_sitemaps_enable'], '1', false).'>'.__('Yes, if any single Post/Page is cleared/reset; also clear the cache for any XML Sitemaps.', 'comet-cache').'</option>'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['cache_clear_xml_sitemaps_enable'], '0', false).'>'.__('No, my site doesn\'t use any XML Sitemaps and/or I prefer NOT to clear the cache for XML Sitemaps.', 'comet-cache').'</option>'."\n";
        echo '         </select></p>'."\n";
        echo '      <div class="plugin-menu-page-panel-if-enabled -cache-clear-xml-sitemap-patterns">'."\n";
        echo '          <p>'.__('<strong style="font-size:110%;">XML Sitemap Patterns (one per line):</strong> A default value of <code>/sitemap**.xml</code> covers all XML Sitemaps for most installations. However, you may customize this further if you deem necessary. Please list one pattern per line. XML Sitemap Pattern searches are performed against the <a href="https://gist.github.com/jaswsinc/338b6eb03a36c048c26f" target="_blank">REQUEST_URI</a>. A wildcard <code>**</code> (double asterisk) can be used when necessary; e.g., <code>/sitemap**.xml</code>. Note that <code>**</code> = 0 or more characters of any kind, including <code>/</code> slashes. <code>*</code> (a single asterisk) means 0 or more characters that are NOT a slash <code>/</code>. Your patterns must match from beginning to end; i.e., the special chars: <code>^</code> (beginning of string) and <code>$</code> (end of the string) are always on for these patterns (i.e., applied internally). For that reason, if you want to match part of a URI, use <code>**</code> to match anything before and/or after the fragment you\'re searching for. For example, <code>**/sitemap**.xml</code> will match any URI containing <code>/sitemap</code> (anywhere), so long as the URI also ends with <code>.xml</code>. On the other hand, <code>/sitemap*.xml</code> will only match URIs that begin with <code>/sitemap</code>, and it will only match URIs ending in <code>.xml</code> in that immediate directory — bypassing any inside nested sub-directories. To learn more about this syntax, please see <a href="http://cometcache.com/r/watered-down-regex-syntax/" target="_blank">this KB article</a>.', 'comet-cache').'</p>'."\n";
        echo '          <p><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_xml_sitemap_patterns]" rows="5" spellcheck="false" class="monospace">'.format_to_edit($this->plugin->options['cache_clear_xml_sitemap_patterns']).'</textarea></p>'."\n";
        if (is_multisite()) {
            echo '      <p class="info" style="display:block; margin-top:-15px;">'.__('In a Multisite Network, each child blog (whether it be a sub-domain, a sub-directory, or a mapped domain); will automatically change the leading <code>http://[sub.]domain/[sub-directory]</code> used in pattern matching. In short, there is no need to add sub-domains or sub-directories for each child blog in these patterns. Please include only the <a href="https://gist.github.com/jaswsinc/338b6eb03a36c048c26f" target="_blank">REQUEST_URI</a> (i.e., the path) which leads to the XML Sitemap on all child blogs in the network.', 'comet-cache').'</p>'."\n";
        }
        echo '      </div>'."\n";

        if (IS_PRO || $this->plugin->isProPreview()) {
            echo '      <hr />'."\n";
            echo '      <h3 data-pro-version-only="'.(!IS_PRO ? __('pro version only', 'comet-cache') : '').'">'.__('Misc. Auto-Clear Options', 'comet-cache').'</h3>'."\n";
            echo '      <h4 style="margin-bottom:0;">'.__('Auto-Clear Custom URL Patterns Too?', 'comet-cache').'</h4>'."\n";
            echo '      <p style="margin-top:2px;">'.sprintf(__('<strong>Auto-Clear Custom URL Patterns (one per line):</strong> When you update a Post/Page, approve a Comment, etc., %1$s will detect that a Post/Page cache should be cleared to keep your site up-to-date. When this occurs, %1$s can also clear a list of custom URLs that you enter here. Please list one URL per line. A wildcard <code>*</code> character can be used when necessary; e.g., <code>https://example.com/category/abc/**</code>. Note that <code>**</code> (double asterisk) means 0 or more characters of any kind, including <code>/</code> slashes. <code>*</code> (a single asterisk) means 0 or more characters that are NOT a slash <code>/</code>. Your patterns must match from beginning to end; i.e., the special chars: <code>^</code> (beginning of string) and <code>$</code> (end of the string) are always on for these patterns (i.e., applied internally). For that reason, if you want to match part of a URL, use <code>**</code> to match anything before and/or after the fragment you\'re searching for. For example, <code>https://**/category/abc/**</code> will find all URLs containing <code>/category/abc/</code> (anywhere); whereas <code>https://*/category/abc/*</code> will match URLs on any domain, but the path must then begin with <code>/category/abc/</code> and the pattern will only match paths in that immediate directory — bypassing any additional paths in sub-directories. To learn more about this syntax, please see <a href="http://cometcache.com/r/watered-down-regex-syntax/" target="_blank">this KB article</a>.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            echo '      <p><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_urls]" spellcheck="false" wrap="off" rows="5">'.format_to_edit($this->plugin->options['cache_clear_urls']).'</textarea></p>'."\n";
            echo '      <p class="info" style="display:block;">'.__('<strong>Note:</strong> Relative URLs (e.g., <code>/name-of-post</code>) should NOT be used. Each entry above should start with <code>http://</code> or <code>https://</code> and include a fully qualified domain name (or wildcard characters in your pattern that will match the domain).', 'comet-cache').'</p>'."\n";
        }
        echo '   </div>'."\n";

        echo '</div>'."\n";
    }
}
