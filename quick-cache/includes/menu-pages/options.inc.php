<?php
/*
Copyright: © 2009 WebSharks, Inc. ( coded in the USA )
<mailto:support@websharks-inc.com> <http://www.websharks-inc.com/>

Released under the terms of the GNU General Public License.
You should have received a copy of the GNU General Public License,
along with this software. In the main directory, see: /licensing/
If not, see: <http://www.gnu.org/licenses/>.
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
/*
Options page.
*/
echo '<div class="wrap ws-menu-page">' . "\n";
/**/
echo '<div id="icon-plugins" class="icon32"><br /></div>' . "\n";
echo '<h2>Quick Cache Options</h2>' . "\n";
/**/
echo '<table class="ws-menu-page-table">' . "\n";
echo '<tbody class="ws-menu-page-table-tbody">' . "\n";
echo '<tr class="ws-menu-page-table-tr">' . "\n";
echo '<td class="ws-menu-page-table-l">' . "\n";
/**/
echo '<form method="post" name="ws_plugin__qcache_options_form" id="ws-plugin--qcache-options-form">' . "\n";
echo '<input type="hidden" name="ws_plugin__qcache_options_save" id="ws-plugin--qcache-options-save" value="' . esc_attr (wp_create_nonce ("ws-plugin--qcache-options-save")) . '" />' . "\n";
echo '<input type="hidden" name="ws_plugin__qcache_configured" id="ws-plugin--qcache-configured" value="1" />' . "\n";
/**/
do_action ("ws_plugin__qcache_during_options_page_before_left_sections", get_defined_vars ());
/**/
if (apply_filters ("ws_plugin__qcache_during_options_page_during_left_sections_display_activation", true, get_defined_vars ()))
	{
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_before_activation", get_defined_vars ());
		/**/
		echo '<div class="ws-menu-page-group" title="Quick Cache ( On/Off )"' . ((!$GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["enabled"]) ? ' default-state="open"' : '') . '>' . "\n";
		/**/
		echo '<div class="ws-menu-page-section ws-plugin--qcache-activation-section">' . "\n";
		echo '<h3>Quick Caching Enabled?</h3>' . "\n";
		echo '<p>You can turn caching On/Off at any time you like. It is recommended that you turn it <code>On</code>. <strong>This really is the only option that you need to enable</strong>. All of the other options below, are for web developers only, and are NOT required; because the defaults will work just fine. In other words, just turn Quick Cache on here, and then skip all the way down to the very bottom and click Save :-)</p>' . "\n";
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_during_activation", get_defined_vars ());
		/**/
		echo '<table class="form-table">' . "\n";
		echo '<tbody>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<th>' . "\n";
		echo '<label for="ws-plugin--qcache-enabled">' . "\n";
		echo 'Caching Enabled?' . "\n";
		echo '</label>' . "\n";
		echo '</th>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<td>' . "\n";
		echo '<select name="ws_plugin__qcache_enabled" id="ws-plugin--qcache-enabled"' . ((!$GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["enabled"]) ? ' class="ws-menu-page-error-hilite"' : '') . '>' . "\n";
		echo '<option value="0"' . ((!$GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["enabled"]) ? ' selected="selected"' : '') . '>Off ( Disabled )</option>' . "\n";
		echo '<option value="1"' . (($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["enabled"]) ? ' selected="selected"' : '') . '>On ( Enabled )</option>' . "\n";
		echo '</select><br />' . "\n";
		echo 'Quick Cache improves speed &amp; performance!' . "\n";
		echo '</td>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '</tbody>' . "\n";
		echo '</table>' . "\n";
		echo '</div>' . "\n";
		/**/
		echo '</div>' . "\n";
		/**/
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_after_activation", get_defined_vars ());
	}
/**/
if (apply_filters ("ws_plugin__qcache_during_options_page_during_left_sections_display_debugging", true, get_defined_vars ()))
	{
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_before_debugging", get_defined_vars ());
		/**/
		echo '<div class="ws-menu-page-group" title="Internal Debugging">' . "\n";
		/**/
		echo '<div class="ws-menu-page-section ws-plugin--qcache-debugging-section">' . "\n";
		echo '<h3>Enable Internal Debugging For Quick Cache?</h3>' . "\n";
		echo '<p>This option is reserved for future implementation. There is already a built-in debugging system for Quick Cache that stays on at all times. Every file it caches and/or serves up will include a comment line or two at the very bottom of the file. Once Quick Cache is enabled you can simply (right-click -> View Source) on your site and look for these to appear. Quick Cache will also report major problems through this method. In the future, additional debugging routines will be added and this option will be used at that time for additional fine-tuning.</p>' . "\n";
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_during_debugging", get_defined_vars ());
		/**/
		echo '<table class="form-table">' . "\n";
		echo '<tbody>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<th>' . "\n";
		echo '<label for="ws-plugin--qcache-enable-debugging">' . "\n";
		echo 'Cache Debugging:' . "\n";
		echo '</label>' . "\n";
		echo '</th>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<td>' . "\n";
		echo '<select name="ws_plugin__qcache_enable_debugging" id="ws-plugin--qcache-enable-debugging">' . "\n";
		echo '<option value="0"' . ((!$GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["enable_debugging"]) ? ' selected="selected"' : '') . '>False ( Disable )</option>' . "\n";
		echo '<option value="1"' . (($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["enable_debugging"]) ? ' selected="selected"' : '') . '>True ( Enable )</option>' . "\n";
		echo '</select><br />' . "\n";
		echo 'Recommended setting ( False ).' . "\n";
		echo '</td>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '</tbody>' . "\n";
		echo '</table>' . "\n";
		echo '</div>' . "\n";
		/**/
		echo '</div>' . "\n";
		/**/
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_after_debugging", get_defined_vars ());
	}
/**/
if (apply_filters ("ws_plugin__qcache_during_options_page_during_left_sections_display_logged_in", true, get_defined_vars ()))
	{
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_before_logged_in", get_defined_vars ());
		/**/
		echo '<div class="ws-menu-page-group" title="Logged In Users">' . "\n";
		/**/
		echo '<div class="ws-menu-page-section ws-plugin--qcache-logged-in-section">' . "\n";
		echo '<h3>Don\'t Cache Pages For Logged In Users?</h3>' . "\n";
		echo '<p>It is best to leave this set to True at all times. Most visitors are NOT logged in, so this does not hurt performance at all :-) Also, this setting includes some users who AREN\'T actually logged into the system, but who HAVE authored comments recently. This way comment authors will be able to see updates to the spool immediately. In other words, Quick Cache thinks of a comment author as a logged in user, even though technically they are not.</p>' . "\n";
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_during_logged_in", get_defined_vars ());
		/**/
		echo '<table class="form-table">' . "\n";
		echo '<tbody>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<th>' . "\n";
		echo '<label for="ws-plugin--qcache-dont-cache-when-logged-in">' . "\n";
		echo 'Login Sessions:' . "\n";
		echo '</label>' . "\n";
		echo '</th>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<td>' . "\n";
		echo '<select name="ws_plugin__qcache_dont_cache_when_logged_in" id="ws-plugin--qcache-dont-cache-when-logged-in">' . "\n";
		echo '<option value="1"' . (($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["dont_cache_when_logged_in"]) ? ' selected="selected"' : '') . '>True ( Don\'t Cache )</option>' . "\n";
		echo '<option value="0"' . ((!$GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["dont_cache_when_logged_in"]) ? ' selected="selected"' : '') . '>False ( Always Cache )</option>' . "\n";
		echo '</select><br />' . "\n";
		echo 'Recommended setting ( True ).' . "\n";
		echo '</td>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '</tbody>' . "\n";
		echo '</table>' . "\n";
		echo '</div>' . "\n";
		/**/
		echo '</div>' . "\n";
		/**/
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_after_logged_in", get_defined_vars ());
	}
/**/
if (apply_filters ("ws_plugin__qcache_during_options_page_during_left_sections_display_get_requests", true, get_defined_vars ()))
	{
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_before_get_requests", get_defined_vars ());
		/**/
		echo '<div class="ws-menu-page-group" title="GET Requests">' . "\n";
		/**/
		echo '<div class="ws-menu-page-section ws-plugin--qcache-get-requests-section">' . "\n";
		echo '<h3>Don\'t Cache Query String GET Requests?</h3>' . "\n";
		echo '<p>This should almost always be set to True, <strong>unless</strong> you\'re using unfriendly Permalinks on your site. In other words, if all of your URLs contain a query string <code>( /?something=something )</code>, you ARE using unfriendly Permalinks, and you should update your Permalink options in WordPress® immediately, because that also optimizes your site for search engines. That being said, if you really want to use unfriendly Permalinks, and only if you\'re using unfriendly Permalinks, you should set this to False; and don\'t worry too much, the sky won\'t fall on your head :-) It should also be noted that POST requests ( forms with method="POST" ) are always excluded from the cache, which is the way it should be. POST requests should never be cached. CLI requests are also excluded from the cache. A CLI request is one that comes from the command line; commonly used by cron jobs and other automated routines.</p>' . "\n";
		echo '<p><em>* <b>Advanced Tip:</b> If you are NOT caching GET requests ( recommended ), but you do want to allow some special URLs that include query string parameters to be cached; you can add this special parameter to your URL <code>&amp;qcAC=1</code> to tell Quick Cache that it is OK to cache that particular URL, even though it contains query string arguments.</em></p>' . "\n";
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_during_get_requests", get_defined_vars ());
		/**/
		echo '<table class="form-table">' . "\n";
		echo '<tbody>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<th>' . "\n";
		echo '<label for="ws-plugin--qcache-dont-cache-query-string-requests">' . "\n";
		echo 'Query Strings:' . "\n";
		echo '</label>' . "\n";
		echo '</th>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<td>' . "\n";
		echo '<select name="ws_plugin__qcache_dont_cache_query_string_requests" id="ws-plugin--qcache-dont-cache-query-string-requests">' . "\n";
		echo '<option value="1"' . (($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["dont_cache_query_string_requests"]) ? ' selected="selected"' : '') . '>True ( Don\'t Cache )</option>' . "\n";
		echo '<option value="0"' . ((!$GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["dont_cache_query_string_requests"]) ? ' selected="selected"' : '') . '>False ( Always Cache )</option>' . "\n";
		echo '</select><br />' . "\n";
		echo 'Recommended setting ( True ).' . "\n";
		echo '</td>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '</tbody>' . "\n";
		echo '</table>' . "\n";
		echo '</div>' . "\n";
		/**/
		echo '</div>' . "\n";
		/**/
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_after_get_requests", get_defined_vars ());
	}
/**/
if (apply_filters ("ws_plugin__qcache_during_options_page_during_left_sections_display_double_cache", true, get_defined_vars ()))
	{
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_before_double_cache", get_defined_vars ());
		/**/
		echo '<div class="ws-menu-page-group" title="Client-Side Cache">' . "\n";
		/**/
		echo '<div class="ws-menu-page-section ws-plugin--qcache-double-cache-section">' . "\n";
		echo '<h3>Allow Double-Caching In The Client-Side Browser?</h3>' . "\n";
		echo '<p>It is best to leave this set to False, particularly if you have users logging in and out a lot. Quick Cache optimizes everything through its ability to communicate with a browser using PHP. If you allow the browser to (cache) the caching system itself, then you are momentarily losing control over whether a cached version will be served or not. We say momentary, because the cached version will eventually expire on its own anyway. This is one major difference between Quick Cache and the original Super Cache plugin. Super Cache allows sort of a double-cache, which really is not very practical, and becomes quite confusing to site owners that spend hours testing &amp; tweaking. All of that being said, if all you care about is blazing fast speed, and you don\'t update your site that often, you can safely set this to True, and see how you like it.</p>' . "\n";
		echo '<p><em>* <b>Advanced Tip:</b> If you have Double-Caching turned OFF ( recommended ), but you do want to allow some special URLs to be cached by the browser; you can add this special parameter to your URL <code>&amp;qcABC=1</code>. That tells Quick Cache that it\'s OK for the browser to cache that particular URL, even though you have it disabled for all others. In other words, the <code>qcABC=1</code> parameter will prevent Quick Cache from sending no-cache headers to the browser.</em></p>' . "\n";
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_during_double_cache", get_defined_vars ());
		/**/
		echo '<table class="form-table">' . "\n";
		echo '<tbody>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<th>' . "\n";
		echo '<label for="ws-plugin--qcache-allow-browser-cache">' . "\n";
		echo 'Browser Cache:' . "\n";
		echo '</label>' . "\n";
		echo '</th>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<td>' . "\n";
		echo '<select name="ws_plugin__qcache_allow_browser_cache" id="ws-plugin--qcache-allow-browser-cache">' . "\n";
		echo '<option value="0"' . ((!$GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["allow_browser_cache"]) ? ' selected="selected"' : '') . '>False ( Disallow )</option>' . "\n";
		echo '<option value="1"' . (($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["allow_browser_cache"]) ? ' selected="selected"' : '') . '>True ( Allow )</option>' . "\n";
		echo '</select><br />' . "\n";
		echo 'Recommended setting ( False ).' . "\n";
		echo '</td>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '</tbody>' . "\n";
		echo '</table>' . "\n";
		echo '</div>' . "\n";
		/**/
		echo '</div>' . "\n";
		/**/
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_after_double_cache", get_defined_vars ());
	}
/**/
if (apply_filters ("ws_plugin__qcache_during_options_page_during_left_sections_display_expiration", true, get_defined_vars ()))
	{
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_before_expiration", get_defined_vars ());
		/**/
		echo '<div class="ws-menu-page-group" title="Cache Expiration Time">' . "\n";
		/**/
		echo '<div class="ws-menu-page-section ws-plugin--qcache-expiration-section">' . "\n";
		echo '<h3>Set The Expiration Time On Quick Cache Files?</h3>' . "\n";
		echo '<p>If you don\'t update your site much, you could set this to 1 week ( 604800 seconds ) and optimize everything even further. The longer the Cache Expiration Time is, the greater your performance gain. Alternatively, the shorter the Expiration Time, the fresher everything will remain on your site. 3600 ( which is 1 hour ) is the recommended Expiration Time; it\'s a good middle ground. That being said, you could set this to just 60 seconds, and you would still see huge differences in speed and performance.</p>' . "\n";
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_during_expiration", get_defined_vars ());
		/**/
		echo '<table class="form-table">' . "\n";
		echo '<tbody>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<th>' . "\n";
		echo '<label for="ws-plugin--qcache-expiration">' . "\n";
		echo 'Cache Expiration Time ( in seconds ):' . "\n";
		echo '</label>' . "\n";
		echo '</th>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<td>' . "\n";
		echo '<input type="text" name="ws_plugin__qcache_expiration" id="ws-plugin--qcache-expiration" value="' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["expiration"]) . '" /><br />' . "\n";
		echo 'Recommended setting ( 3600 ).' . "\n";
		echo '</td>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '</tbody>' . "\n";
		echo '</table>' . "\n";
		echo '</div>' . "\n";
		/**/
		echo '</div>' . "\n";
		/**/
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_after_expiration", get_defined_vars ());
	}
/**/
if (apply_filters ("ws_plugin__qcache_during_options_page_during_left_sections_display_pruning", true, get_defined_vars ()))
	{
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_before_pruning", get_defined_vars ());
		/**/
		echo '<div class="ws-menu-page-group" title="Dynamic Cache Pruning">' . "\n";
		/**/
		echo '<div class="ws-menu-page-section ws-plugin--qcache-pruning-section">' . "\n";
		echo '<h3>Enable Dynamic Cache Pruning Routines?</h3>' . "\n";
		echo '<p>So let\'s summarize things here and review your configuration thus far. There is an automatic expiration system ( the Garbage Collector ), which runs through WordPress® behind-the-scenes, according to your Expiration setting. Then, there is also a built-in Expiration Time on existing files, which is checked before any cache file is served up; this also uses your Expiration setting. So... what happens if you\'re working on your site, and you update a Post or a Page? Do visitors have to wait an hour before they see these changes? Or should they see changes like this automatically?</p>' . "\n";
		echo '<p>That is where this configuration option comes in. Whenever you update a Post or a Page, Quick Cache can automatically Prune that particular file from the cache, so it instantly becomes fresh again. Otherwise, your visitors would need to wait for the previous cached version to expire. If you\'d like Quick Cache to handle this for you, set this option to <em>Single</em>. If you want Quick Cache to completely reset ( purge all cache files ) when this happens; and be triggered on other actions too — like if you rename a category or add links, set this to <em>All</em>. If you don\'t want any of this, and you just want blazing fast speed at all times, set this to <em>None</em>.</p>' . "\n";
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_during_pruning", get_defined_vars ());
		/**/
		echo '<table class="form-table">' . "\n";
		echo '<tbody>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<th>' . "\n";
		echo '<label for="ws-plugin--qcache-clear-on-update">' . "\n";
		echo 'Dynamic Cache Pruning Option:' . "\n";
		echo '</label>' . "\n";
		echo '</th>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<td>' . "\n";
		echo '<select name="ws_plugin__qcache_clear_on_update" id="ws-plugin--qcache-clear-on-update">' . "\n";
		echo '<option value="single"' . (($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["clear_on_update"] === "single") ? ' selected="selected"' : '') . '>Single ( Purge Only The Specific Post/Page )</option>' . "\n";
		echo '<option value="single-fp"' . (($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["clear_on_update"] === "single-fp") ? ' selected="selected"' : '') . '>Single + Front Page ( Purge The Specific Post/Page + My Front Page )</option>' . "\n";
		echo '<option value="all"' . (($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["clear_on_update"] === "all") ? ' selected="selected"' : '') . '>All ( Purge All Cached Files In The System * slower )</option>' . "\n";
		echo '<option value="no"' . (($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["clear_on_update"] === "none") ? ' selected="selected"' : '') . '>None ( Wait For Garbage Collector To Handle It )</option>' . "\n";
		echo '</select><br />' . "\n";
		echo 'Recommended setting ( Single, or Single + Front Page ).' . "\n";
		echo '</td>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '</tbody>' . "\n";
		echo '</table>' . "\n";
		echo '</div>' . "\n";
		/**/
		echo '</div>' . "\n";
		/**/
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_after_pruning", get_defined_vars ());
	}
/**/
if (apply_filters ("ws_plugin__qcache_during_options_page_during_left_sections_display_no_cache_uris", true, get_defined_vars ()))
	{
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_before_no_cache_uris", get_defined_vars ());
		/**/
		echo '<div class="ws-menu-page-group" title="No-Cache URI Patterns">' . "\n";
		/**/
		echo '<div class="ws-menu-page-section ws-plugin--qcache-no-cache-uris-section">' . "\n";
		echo '<h3>Don\'t Cache These Special URI Patterns?</h3>' . "\n";
		echo '<p>Sometimes there are special cases where a particular file, or a particular group of files, should never be cached. This is where you will enter those if you need to. Searches are performed against the REQUEST_URI ( case sensitive ). So don\'t put in full URLs here, just word fragments found in the file path is all you need, excluding the http:// and the domain name. Wildcards and other regex patterns are not supported here; so you don\'t need to escape special characters or anything. Please see the examples below for more information.</p>' . "\n";
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_during_no_cache_uris", get_defined_vars ());
		/**/
		echo '<table class="form-table">' . "\n";
		echo '<tbody>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<th>' . "\n";
		echo '<label for="ws-plugin--qcache-dont-cache-these-uris">' . "\n";
		echo 'Don\'t Cache These URI Patterns:' . "\n";
		echo '</label>' . "\n";
		echo '</th>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<td>' . "\n";
		echo 'One per line please... ( these ARE case sensitive )<br />' . "\n";
		echo '<textarea name="ws_plugin__qcache_dont_cache_these_uris" id="ws-plugin--qcache-dont-cache-these-uris" rows="3" wrap="off" spellcheck="false">' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["dont_cache_these_uris"]) . '</textarea><br />' . "\n";
		echo 'Do NOT include a leading http:// or your domain name. Let\'s use this example URL: <code>http://www.example.com/post/example-post</code>. To exclude this URL, you would put this line into the field above: <code>/post/example-post</code>. Or you could also just put in a small fragment, like: <code>example-</code> and that would exclude any URI containing that word fragment.' . "\n";
		echo '</td>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '</tbody>' . "\n";
		echo '</table>' . "\n";
		echo '</div>' . "\n";
		/**/
		echo '</div>' . "\n";
		/**/
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_after_no_cache_uris", get_defined_vars ());
	}
/**/
if (apply_filters ("ws_plugin__qcache_during_options_page_during_left_sections_display_no_cache_refs", true, get_defined_vars ()))
	{
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_before_no_cache_refs", get_defined_vars ());
		/**/
		echo '<div class="ws-menu-page-group" title="No-Cache Referrer Patterns">' . "\n";
		/**/
		echo '<div class="ws-menu-page-section ws-plugin--qcache-no-cache-refs-section">' . "\n";
		echo '<h3>Don\'t Cache These Special Referrer Patterns?</h3>' . "\n";
		echo '<p>Sometimes there are special cases where a particular referring URL ( or referring domain ) that sends you traffic; or even a particular group of referring URLs or domains that send you traffic; should result in a page being loaded on your site that is NOT a cached version. This is where you will enter those if you need to. Searches are performed against the HTTP_REFERER ( case sensitive ). Wildcards and other regex patterns are not supported here; so you don\'t need to escape special characters or anything. Please see the examples below for more information.</p>' . "\n";
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_during_no_cache_refs", get_defined_vars ());
		/**/
		echo '<table class="form-table">' . "\n";
		echo '<tbody>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<th>' . "\n";
		echo '<label for="ws-plugin--qcache-dont-cache-these-refs">' . "\n";
		echo 'Don\'t Cache These Referrer Patterns:' . "\n";
		echo '</label>' . "\n";
		echo '</th>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<td>' . "\n";
		echo 'One per line please... ( these ARE case sensitive )<br />' . "\n";
		echo '<textarea name="ws_plugin__qcache_dont_cache_these_refs" id="ws-plugin--qcache-dont-cache-these-refs" rows="3" wrap="off" spellcheck="false">' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["dont_cache_these_refs"]) . '</textarea><br />' . "\n";
		echo 'Let\'s use this example URL: <code>http://www.referring-domain.com/?q=search+terms</code>. To exclude this referring URL, you could put this line into the field above: <code>www.referring-domain.com</code>. Or you could also just put in a small fragment, like: <code>q=</code> and that would exclude any referrer containing that word fragment.' . "\n";
		echo '</td>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '</tbody>' . "\n";
		echo '</table>' . "\n";
		echo '</div>' . "\n";
		/**/
		echo '</div>' . "\n";
		/**/
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_after_no_cache_refs", get_defined_vars ());
	}
/**/
if (apply_filters ("ws_plugin__qcache_during_options_page_during_left_sections_display_no_cache_uagents", true, get_defined_vars ()))
	{
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_before_no_cache_uagents", get_defined_vars ());
		/**/
		echo '<div class="ws-menu-page-group" title="No-Cache User-Agent Patterns">' . "\n";
		/**/
		echo '<div class="ws-menu-page-section ws-plugin--qcache-no-cache-uagents-section">' . "\n";
		echo '<h3>Don\'t Cache These User-Agent Patterns?</h3>' . "\n";
		echo '<p>If your site has been designed to support mobile devices through special detection scripting, you might want to disable caching for those devices here. Searches are performed against the HTTP_USER_AGENT string ( case insensitive ). Just put in word fragments that you want to look for in the User-Agent string. If a word fragment is found in the User-Agent string, no caching will occur, and only database-driven content will be served up. Wildcards and other regex patterns are not supported in this field; so you don\'t need to escape special characters or anything.</p>' . "\n";
		echo '<p>Another way to deal with this problem, is to use a custom Salt ( that option is down below ). You could use a custom Salt that includes $_SERVER["HTTP_USER_AGENT"]. This would create different cached versions for every different browser, thereby eliminating the need for this option all together. If your site is really large, you might want to think this through. Having a different set of cache files for every different browser could take up lots of disk space, and there are lots of different browsers out there.</p>' . "\n";
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_during_no_cache_uagents", get_defined_vars ());
		/**/
		echo '<table class="form-table">' . "\n";
		echo '<tbody>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<th>' . "\n";
		echo '<label for="ws-plugin--qcache-dont-cache-these-agents">' . "\n";
		echo 'Don\'t Cache These User-Agent Patterns:' . "\n";
		echo '</label>' . "\n";
		echo '</th>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<td>' . "\n";
		echo 'One per line please... ( these are NOT case sensitive )<br />' . "\n";
		echo '<textarea name="ws_plugin__qcache_dont_cache_these_agents" id="ws-plugin--qcache-dont-cache-these-agents" rows="3" wrap="off" spellcheck="false">' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["dont_cache_these_agents"]) . '</textarea><br />' . "\n";
		echo 'If you wanted to prevent caching on a BlackBerry, iPhones, and Playstation systems:<br />' . "\n";
		echo '<code>BlackBerry</code><br /><code>Playstation</code><br /><code>iPhone</code>' . "\n";
		echo '</td>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '</tbody>' . "\n";
		echo '</table>' . "\n";
		echo '</div>' . "\n";
		/**/
		echo '</div>' . "\n";
		/**/
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_after_no_cache_uagents", get_defined_vars ());
	}
/**/
if (apply_filters ("ws_plugin__qcache_during_options_page_during_left_sections_display_mutex", true, get_defined_vars ()))
	{
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_before_mutex", get_defined_vars ());
		/**/
		echo '<div class="ws-menu-page-group" title="Mutex File Locking">' . "\n";
		/**/
		echo '<div class="ws-menu-page-section ws-plugin--qcache-mutex-section">' . "\n";
		echo '<h3>Maintain Mutex Using Flock() Or A Semaphore?</h3>' . "\n";
		echo '<p>On high traffic sites with dedicated servers, a Semaphore (<em>sem_get</em>) offers better performance. Unless your hosting provider has suggested otherwise, it is best to leave this set to the more reliable <em>sem_get</em> method. If your system does not support <em>sem_get</em>, Quick Cache will detect that automatically &amp; fall back on the <em>flock</em> method for you. The <em>flock</em> method can be used on any system, so if you have any trouble using Quick Cache, set this to <em>flock</em> for maximum compatibility.</p>' . "\n";
		echo '<p><strong>Cloud Computing?</strong> If your site is hosted on a Cloud Computing model, such as the Rackspace® Cloud, or (mt) Media Temple; you should set this to <em>flock</em> unless they tell you otherwise.</p>' . "\n";
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_during_mutex", get_defined_vars ());
		/**/
		echo '<table class="form-table">' . "\n";
		echo '<tbody>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<th>' . "\n";
		echo '<label for="ws-plugin--qcache-use-flock-or-sem">' . "\n";
		echo 'Mutex Method:' . "\n";
		echo '</label>' . "\n";
		echo '</th>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<td>' . "\n";
		echo '<select name="ws_plugin__qcache_use_flock_or_sem" id="ws-plugin--qcache-use-flock-or-sem">' . "\n";
		echo '<option value="sem"' . (($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["use_flock_or_sem"] === "sem") ? ' selected="selected"' : '') . '>Mutex ( Semaphore )</option>' . "\n";
		echo '<option value="flock"' . (($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["use_flock_or_sem"] === "flock") ? ' selected="selected"' : '') . '>Mutex ( Flock )</option>' . "\n";
		echo '</select><br />' . "\n";
		echo 'Recommended setting ( Semaphore ).' . "\n";
		echo '</td>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '</tbody>' . "\n";
		echo '</table>' . "\n";
		echo '</div>' . "\n";
		/**/
		echo '</div>' . "\n";
		/**/
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_after_mutex", get_defined_vars ());
	}
/**/
if (apply_filters ("ws_plugin__qcache_during_options_page_during_left_sections_display_md5_salt", true, get_defined_vars ()))
	{
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_before_md5_salt", get_defined_vars ());
		/**/
		echo '<div class="ws-menu-page-group" title="MD5 Version Salt">' . "\n";
		/**/
		echo '<div class="ws-menu-page-section ws-plugin--qcache-md5-salt-section">' . "\n";
		echo '<h3>Create An MD5 Version Salt For Quick Cache?</h3>' . "\n";
		echo '<p>This is for advanced users only. Alright, here goes... Quick Cache stores its cache files using an <code>md5()</code> hash of the HOST/URI that it\'s caching. If you want to build these hash strings out of something other than just the HOST/URI, you can add a Salt to the mix. So instead of just <code>md5($_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"])</code>, you might have <code>md5($_COOKIE["myCookie"].$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"])</code>. This would create multiple versions of each page depending on the value of <code>$_COOKIE["myCookie"]</code>. If <code>$_COOKIE["myCookie"]</code> is empty, then just <code>$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]</code> are used. So you see, this gives you the ability to dynamically create multiple variations of the cache, and those dynamic variations will be served on subsequent visits.</p>' . "\n";
		echo '<p>A Salt can be a single variable like <code>$_COOKIE["myCookie"]</code>, or it can be a combination of multiple variables, like <code>$_COOKIE["myCookie"].$_COOKIE["myOtherCookie"]</code>. When using multiple variables, please separate them with a dot, as shown in the example. Experts can use PHP ternary expressions that evaluate into something. For example: <code>((preg_match("/IPHONE/i", $_SERVER["HTTP_USER_AGENT"])) ? "IPHONES" : "")</code>. This would force a separate version of the cache to be created for iPhone browsers. With this method your possibilities are limitless.</p>' . "\n";
		echo '<p>Quick Cache can also be disabled temporarily. If you\'re a plugin developer, you can define a special constant within your plugin to disable the cache engine at runtime, on a specific page, or in a specific scenario. In your PHP script, do this: <code>define("QUICK_CACHE_ALLOWED", false)</code>. Quick Cache is also compatible with: <code>$_SERVER["QUICK_CACHE_ALLOWED"] = false</code>, as well as <code>define("DONOTCACHEPAGE", true)</code>, which is backward compatible with the WP Super Cache plugin.</p>' . "\n";
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_during_md5_salt", get_defined_vars ());
		/**/
		echo '<table class="form-table">' . "\n";
		echo '<tbody>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<th>' . "\n";
		echo '<label for="ws-plugin--qcache-version-salt">' . "\n";
		echo 'MD5 Version Salt:' . "\n";
		echo '</label>' . "\n";
		echo '</th>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<td>' . "\n";
		echo 'md5(<input type="text" name="ws_plugin__qcache_version_salt" id="ws-plugin--qcache-version-salt" value="' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["version_salt"]) . '" style="width:300px;" />.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"])<br />' . "\n";
		echo 'You can use Super Globals: $_SERVER, $_GET, $_REQUEST, $_COOKIE, etc. Or Constants defined in wp-config.php. Example: <code>DB_NAME.DB_HOST.$_SERVER["REMOTE_PORT"]</code> ( separate multiple variables with a dot ). Your Salt will be checked for PHP syntax errors. If syntax errors are found, you\'ll receive a JavaScript alert, after you click Save.<br />' . "\n";
		echo '</td>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '</tbody>' . "\n";
		echo '</table>' . "\n";
		echo '</div>' . "\n";
		/**/
		echo '</div>' . "\n";
		/**/
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_after_md5_salt", get_defined_vars ());
	}
/**/
if (apply_filters ("ws_plugin__qcache_during_options_page_during_left_sections_display_auto_caching", true, get_defined_vars ()))
	{
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_before_auto_caching", get_defined_vars ());
		/**/
		echo '<div class="ws-menu-page-group" title="Sitemap Auto-Caching">' . "\n";
		/**/
		echo '<div class="ws-menu-page-section ws-plugin--auto-caching-section">' . "\n";
		echo '<h3>XML Sitemap Auto-Caching + Additional URLs</h3>' . "\n";
		echo '<p><em>On sites with PHP scripts that are <strong>extremely processor intensive</strong>, and in some other rare cases; Auto-Caching <strong>might</strong> be desirable.</em></p>' . "\n";
		echo '<p>After using Quick Cache for awhile ( or any other cache plugin, for that matter ); it becomes obvious, that at some point ( based on your Expiration setting ), Quick Cache has to refresh itself. It does this by ditching its cached version of a page, re-loading the database-driven content for that page, and re-creating the cache with the latest snapshot. This is a never ending Re-generation Cycle, that is directly affected by your Cache Expiration setting.</p>' . "\n";
		echo '<p>This Re-generation Cycle, is how Quick Cache keeps everything up-to-date. Understanding this, you can see that 99% of your visitors are going to receive a lightning fast response from your server. However, there will always be around 1% of your visitors, that land on a page for the very first time ( before it\'s been cached ), or land on a page that needs to have its cache re-generated, because the existing cache has become outdated. We refer to this as ( <em>the first-come / slow-load issue</em> ).</p>' . "\n";
		echo '<p>The Auto-Cache Engine, is designed to combat this issue, by taking on the responsibility of being that first visitor to a page that has not yet been cached, or... has an expired cache. The Auto-Cache Engine is powered, in part, by <a href="http://codex.wordpress.org/Category:WP-Cron_Functions" target="_blank" rel="external">WP-Cron</a> ( already built into the WP® core framework ). It also uses the <a href="http://core.trac.wordpress.org/browser/trunk/wp-includes/http.php" target="_blank" rel="external">WP_Http</a> class, which is also built into WordPress® by default.</p>' . "\n";
		echo '<p>The Auto-Cache Engine, obtains its list of URLs, to "Auto Cache", from two different sources. It can read your <a href="http://wordpress.org/extend/plugins/google-sitemap-generator/" target="_blank" rel="external">XML Sitemap</a>, and/or a list of specific URLs that you supply. If you supply both sources, it will use both sources collectively, and intuitively. The Auto-Cache engine takes ALL of your other configuration options into consideration, including your Expiration setting, as well as any No-Cache rules.</p>' . "\n";
		echo '<p><em>The Auto-Cache Engine is highly optimized, so that unnecessary visits to each URL are avoided if at all possible. You can further optimize the Auto-Cache Engine, by setting your Cache Expiration to at least <code>3600</code>. Generally speaking, we suggest an Expiration setting of <code>86400</code>. As one of its built-in safeguards, the Auto-Cache Engine will NOT run if your Expiration setting is lower than <code>3600</code>.</em></p>' . "\n";
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_during_auto_caching", get_defined_vars ());
		/**/
		echo '<table class="form-table">' . "\n";
		echo '<tbody>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<th>' . "\n";
		echo '<label for="ws-plugin--qcache-auto-cache-enabled">' . "\n";
		echo 'Enable The Auto-Cache Engine?' . "\n";
		echo '</label>' . "\n";
		echo '</th>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<td>' . "\n";
		echo '<select name="ws_plugin__qcache_auto_cache_enabled" id="ws-plugin--qcache-auto-cache-enabled">' . "\n";
		echo '<option value="0"' . ((!$GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["auto_cache_enabled"]) ? ' selected="selected"' : '') . '>No ( not necessary )</option>' . "\n";
		echo '<option value="1"' . (($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["auto_cache_enabled"]) ? ' selected="selected"' : '') . '>Yes ( keep my site auto-cached )</option>' . "\n";
		echo '</select><br />' . "\n";
		echo 'Recommended setting for most sites ( No ).' . "\n";
		echo '</td>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<th>' . "\n";
		echo '<label for="ws-plugin--qcache-auto-cache-agent">' . "\n";
		echo 'Auto Cache User-Agent String:' . "\n";
		echo '</label>' . "\n";
		echo '</th>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<td>' . "\n";
		echo '<input type="text" name="ws_plugin__qcache_auto_cache_agent" id="ws-plugin--qcache-auto-cache-agent" value="' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["auto_cache_agent"]) . '" /><br />' . "\n";
		echo 'Quick Cache will pretend it is this User-Agent, and auto-append: <code>+ Quick Cache ( Auto-Cache Engine )</code>' . "\n";
		echo '</td>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<th>' . "\n";
		echo '<label for="ws-plugin--qcache-auto-cache-sitemap-url">' . "\n";
		echo 'The Full URL To Your XML Sitemap:' . "\n";
		echo '</label>' . "\n";
		echo '</th>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<td>' . "\n";
		echo '<input type="text" name="ws_plugin__qcache_auto_cache_sitemap_url" id="ws-plugin--qcache-auto-cache-sitemap-url" value="' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["auto_cache_sitemap_url"]) . '" /><br />' . "\n";
		echo '<strong>Tip:</strong> the <a href="http://wordpress.org/extend/plugins/google-sitemap-generator/" target="_blank" rel="external">Google® XML Sitemaps</a> plugin for WordPress®, will keep this up-to-date automatically :-)' . "\n";
		echo '</td>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<th>' . "\n";
		echo '<label for="ws-plugin--qcache-auto-cache-additional-urls">' . "\n";
		echo 'A List Of Additional URLs, to "Auto-Cache" ( one per line ):' . "\n";
		echo '</label>' . "\n";
		echo '</th>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<td>' . "\n";
		echo '<textarea name="ws_plugin__qcache_auto_cache_additional_urls" id="ws-plugin--qcache-auto-cache-additional-urls" rows="5" wrap="off" spellcheck="false">' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["auto_cache_additional_urls"]) . '</textarea>' . "\n";
		echo '</td>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<th>' . "\n";
		echo '<label for="ws-plugin--qcache-auto-cache-max-processes">' . "\n";
		echo 'Maximum Processes Allowed ( prevents flooding ):' . "\n";
		echo '</label>' . "\n";
		echo '</th>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<td>' . "\n";
		echo '<select name="ws_plugin__qcache_auto_cache_max_processes" id="ws-plugin--qcache-auto-cache-max-processes">' . "\n";
		echo '<option value="1"' . (($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["auto_cache_max_processes"] == 1) ? ' selected="selected"' : '') . '>Up to 1 page ( every 5 minutes )</option>' . "\n";
		echo '<option value="2"' . (($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["auto_cache_max_processes"] == 2) ? ' selected="selected"' : '') . '>Up to 2 pages ( every 5 minutes )</option>' . "\n";
		echo '<option value="3"' . (($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["auto_cache_max_processes"] == 3) ? ' selected="selected"' : '') . '>Up to 3 pages ( every 5 minutes )</option>' . "\n";
		echo '<option value="4"' . (($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["auto_cache_max_processes"] == 4) ? ' selected="selected"' : '') . '>Up to 4 pages ( every 5 minutes )</option>' . "\n";
		echo '<option value="5"' . (($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["auto_cache_max_processes"] == 5) ? ' selected="selected"' : '') . '>Up to 5 pages ( every 5 minutes )</option>' . "\n";
		echo '<option value="10"' . (($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["auto_cache_max_processes"] == 10) ? ' selected="selected"' : '') . '>Up to 10 pages ( every 5 minutes )</option>' . "\n";
		echo '<option value="15"' . (($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["auto_cache_max_processes"] == 15) ? ' selected="selected"' : '') . '>Up to 15 pages ( every 5 minutes )</option>' . "\n";
		echo '<option value="20"' . (($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["auto_cache_max_processes"] == 20) ? ' selected="selected"' : '') . '>Up to 20 pages ( every 5 minutes )</option>' . "\n";
		echo '<option value="25"' . (($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["auto_cache_max_processes"] == 25) ? ' selected="selected"' : '') . '>Up to 25 pages ( every 5 minutes )</option>' . "\n";
		echo '</select><br />' . "\n";
		echo 'The Auto-Cache Engine is highly optimized. Unnecessary visits to each URL are avoided if at all possible.<br />' . "\n";
		echo 'A log will be maintained at: <code>/wp-content/cache/qc-l-auto-cache.log</code><br />' . "\n";
		echo 'Recommended setting for most sites ( 5 ).' . "\n";
		echo '</td>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '</tbody>' . "\n";
		echo '</table>' . "\n";
		echo '</div>' . "\n";
		/**/
		echo '</div>' . "\n";
		/**/
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_after_auto_caching", get_defined_vars ());
	}
/**/
if (apply_filters ("ws_plugin__qcache_during_options_page_during_left_sections_display_deactivation", true, get_defined_vars ()))
	{
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_before_deactivation", get_defined_vars ());
		/**/
		echo '<div class="ws-menu-page-group" title="Deactivation Safeguards">' . "\n";
		/**/
		echo '<div class="ws-menu-page-section ws-plugin--qcache-deactivation-section">' . "\n";
		echo '<h3>Deactivation Safeguards ( optional, recommended )</h3>' . "\n";
		echo '<p>By default, Quick Cache will cleanup ( erase ) all of it\'s Configuration Options when/if you deactivate it from the Plugins Menu in WordPress®. If you would like to Safeguard all of this information, in case Quick Cache is deactivated inadvertently, please choose Yes ( safeguard all Quick Cache data/options ).</p>' . "\n";
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_during_deactivation", get_defined_vars ());
		/**/
		echo '<table class="form-table">' . "\n";
		echo '<tbody>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<th>' . "\n";
		echo '<label for="ws-plugin--qcache-run-deactivation-routines">' . "\n";
		echo 'Safeguard Quick Cache Data/Options?' . "\n";
		echo '</label>' . "\n";
		echo '</th>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '<tr>' . "\n";
		/**/
		echo '<td>' . "\n";
		echo '<select name="ws_plugin__qcache_run_deactivation_routines" id="ws-plugin--qcache-run-deactivation-routines">' . "\n";
		echo '<option value="1"' . (($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["run_deactivation_routines"]) ? ' selected="selected"' : '') . '></option>' . "\n";
		echo '<option value="0"' . ((!$GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["run_deactivation_routines"]) ? ' selected="selected"' : '') . '>Yes ( safeguard all data/options )</option>' . "\n";
		echo '</select><br />' . "\n";
		echo 'Recommended setting: ( <code>Yes, safeguard all data/options</code> )' . "\n";
		echo '</td>' . "\n";
		/**/
		echo '</tr>' . "\n";
		echo '</tbody>' . "\n";
		echo '</table>' . "\n";
		echo '</div>' . "\n";
		/**/
		echo '</div>' . "\n";
		/**/
		do_action ("ws_plugin__qcache_during_options_page_during_left_sections_after_deactivation", get_defined_vars ());
	}
/**/
do_action ("ws_plugin__qcache_during_options_page_after_left_sections", get_defined_vars ());
/**/
echo '<div class="ws-menu-page-hr"></div>' . "\n";
/**/
echo '<p class="submit"><input type="submit" class="button-primary" value="Save All Changes" /></p>' . "\n";
/**/
echo '</form>' . "\n";
/**/
echo '</td>' . "\n";
/**/
echo '<td class="ws-menu-page-table-r">' . "\n";
/**/
do_action ("ws_plugin__qcache_during_options_page_before_right_sections", get_defined_vars ());
do_action ("ws_plugin__qcache_during_menu_pages_before_right_sections", get_defined_vars ());
/**/
echo ($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["menu_pages"]["installation"]) ? '<div class="ws-menu-page-installation"><a href="' . esc_attr (c_ws_plugin__qcache_readmes::parse_readme_value ("Professional Installation URI")) . '" target="_blank"><img src="' . esc_attr ($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["dir_url"]) . '/images/brand-installation.png" alt="." /></a></div>' . "\n" : '';
echo ($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["menu_pages"]["tools"]) ? '<div class="ws-menu-page-tools"><img src="' . esc_attr ($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["dir_url"]) . '/images/brand-tools.png" alt="." /></div>' . "\n" : '';
echo ($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["menu_pages"]["videos"]) ? '<div class="ws-menu-page-videos"><a href="' . esc_attr (c_ws_plugin__qcache_readmes::parse_readme_value ("Video Tutorials")) . '" target="_blank"><img src="' . esc_attr ($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["dir_url"]) . '/images/brand-videos.png" alt="." /></a></div>' . "\n" : '';
echo ($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["menu_pages"]["support"]) ? '<div class="ws-menu-page-support"><a href="' . esc_attr (c_ws_plugin__qcache_readmes::parse_readme_value ("Forum URI")) . '" target="_blank"><img src="' . esc_attr ($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["dir_url"]) . '/images/brand-support.png" alt="." /></a></div>' . "\n" : '';
echo ($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["menu_pages"]["donations"]) ? '<div class="ws-menu-page-donations"><a href="' . esc_attr (c_ws_plugin__qcache_readmes::parse_readme_value ("Donate link")) . '" target="_blank"><img src="' . esc_attr ($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["dir_url"]) . '/images/brand-donations.png" alt="." /></a></div>' . "\n" : '';
/**/
do_action ("ws_plugin__qcache_during_menu_pages_after_right_sections", get_defined_vars ());
do_action ("ws_plugin__qcache_during_options_page_after_right_sections", get_defined_vars ());
/**/
echo '</td>' . "\n";
/**/
echo '</tr>' . "\n";
echo '</tbody>' . "\n";
echo '</table>' . "\n";
/**/
echo '</div>' . "\n";
?>