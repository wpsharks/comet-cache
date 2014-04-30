<?php
namespace quick_cache // Root namespace.
	{
		if(!defined('WPINC')) // MUST have WordPress.
			exit('Do NOT access this file directly: '.basename(__FILE__));

		class menu_pages // Plugin options.
		{
			public function options()
				{
					echo '<form id="plugin-menu-page" class="plugin-menu-page" method="post" enctype="multipart/form-data"'.
					     ' action="'.esc_attr(add_query_arg(urlencode_deep(array('page' => __NAMESPACE__, '_wpnonce' => wp_create_nonce())), self_admin_url('/admin.php'))).'">'."\n";

					echo '<div class="plugin-menu-page-heading">'."\n";

					if(is_multisite()) // Wipes entire cache (e.g. this clears ALL sites in a network).
						echo '   <button type="button" class="plugin-menu-page-wipe-cache" style="float:right; margin-left:15px;" title="'.esc_attr(__('Wipe Cache (Start Fresh); clears the cache for all sites in this network at once!', plugin()->text_domain)).'"'.
						     '      data-action="'.esc_attr(add_query_arg(urlencode_deep(array('page' => __NAMESPACE__, '_wpnonce' => wp_create_nonce(), __NAMESPACE__ => array('wipe_cache' => '1'))), self_admin_url('/admin.php'))).'">'.
						     '      '.__('Wipe', plugin()->text_domain).' <img src="'.esc_attr(plugin()->url('/client-s/images/wipe.png')).'" style="width:16px; height:16px;" /></button>'."\n";

					echo '   <button type="button" class="plugin-menu-page-clear-cache" style="float:right;" title="'.esc_attr(__('Clear Cache (Start Fresh)', plugin()->text_domain).((is_multisite()) ? __('; affects the current site only.', plugin()->text_domain) : '')).'"'.
					     '      data-action="'.esc_attr(add_query_arg(urlencode_deep(array('page' => __NAMESPACE__, '_wpnonce' => wp_create_nonce(), __NAMESPACE__ => array('clear_cache' => '1'))), self_admin_url('/admin.php'))).'">'.
					     '      '.__('Clear', plugin()->text_domain).' <img src="'.esc_attr(plugin()->url('/client-s/images/clear.png')).'" style="width:16px; height:16px;" /></button>'."\n";

					echo '   <button type="button" class="plugin-menu-page-restore-defaults"'. // Restores default options.
					     '      data-confirmation="'.esc_attr(__('Restore default plugin options? You will lose all of your current settings! Are you absolutely sure about this?', plugin()->text_domain)).'"'.
					     '      data-action="'.esc_attr(add_query_arg(urlencode_deep(array('page' => __NAMESPACE__, '_wpnonce' => wp_create_nonce(), __NAMESPACE__ => array('restore_default_options' => '1'))), self_admin_url('/admin.php'))).'">'.
					     '      '.__('Restore', plugin()->text_domain).' <i class="fa fa-ambulance"></i></button>'."\n";

					echo '   <div class="plugin-menu-page-panel-togglers" title="'.esc_attr(__('All Panels', plugin()->text_domain)).'">'."\n";
					echo '      <button type="button" class="plugin-menu-page-panels-open"><i class="fa fa-chevron-down"></i></button>'."\n";
					echo '      <button type="button" class="plugin-menu-page-panels-close"><i class="fa fa-chevron-up"></i></button>'."\n";
					echo '   </div>'."\n";

					echo '   <div class="plugin-menu-page-upsells">'."\n";
					echo '      <a href="'.esc_attr(add_query_arg(urlencode_deep(array('page' => __NAMESPACE__, __NAMESPACE__.'_pro_preview' => '1')), self_admin_url('/admin.php'))).'"><i class="fa fa-eye"></i> Preview Pro Features</a>'."\n";
					echo '      <a href="'.esc_attr('http://www.websharks-inc.com/product/'.str_replace('_', '-', __NAMESPACE__).'/').'" target="_blank"><i class="fa fa-heart-o"></i> '.__('Pro Upgrade', plugin()->text_domain).'</a>'."\n";
					echo '      <a href="'.esc_attr('http://www.websharks-inc.com/r/'.str_replace('_', '-', __NAMESPACE__).'-subscribe/').'" target="_blank"><i class="fa fa-envelope"></i> '.__('Newsletter (Subscribe)', plugin()->text_domain).'</a>'."\n";
					echo '   </div>'."\n";

					echo '   <img src="'.plugin()->url('/client-s/images/options.png').'" alt="'.esc_attr(__('Plugin Options', plugin()->text_domain)).'" />'."\n";

					echo '</div>'."\n";

					if(!empty($_REQUEST[__NAMESPACE__.'__updated'])) // Options updated successfully?
						{
							echo '<div class="plugin-menu-page-notice notice">'."\n";
							echo '   <i class="fa fa-thumbs-up"></i> '.__('Options updated successfully.', plugin()->text_domain)."\n";
							echo '</div>'."\n";
						}
					if(!empty($_REQUEST[__NAMESPACE__.'__restored'])) // Restored default options?
						{
							echo '<div class="plugin-menu-page-notice notice">'."\n";
							echo '   <i class="fa fa-thumbs-up"></i> '.__('Default options successfully restored.', plugin()->text_domain)."\n";
							echo '</div>'."\n";
						}
					if(!empty($_REQUEST[__NAMESPACE__.'__cache_wiped']))
						{
							echo '<div class="plugin-menu-page-notice notice">'."\n";
							echo '   <img src="'.esc_attr(plugin()->url('/client-s/images/wipe.png')).'" /> '.__('Cache wiped across all sites; recreation will occur automatically over time.', plugin()->text_domain)."\n";
							echo '</div>'."\n";
						}
					if(!empty($_REQUEST[__NAMESPACE__.'__cache_cleared']))
						{
							echo '<div class="plugin-menu-page-notice notice">'."\n";
							echo '   <img src="'.esc_attr(plugin()->url('/client-s/images/clear.png')).'" /> '.__('Cache cleared for this site; recreation will occur automatically over time.', plugin()->text_domain)."\n";
							echo '</div>'."\n";
						}
					if(!empty($_REQUEST[__NAMESPACE__.'__wp_config_wp_cache_add_failure']))
						{
							echo '<div class="plugin-menu-page-notice error">'."\n";
							echo '   <i class="fa fa-thumbs-down"></i> '.__('Failed to update your <code>/wp-config.php</code> file automatically. Please add the following line to your <code>/wp-config.php</code> file (right after the opening <code>&lt;?php</code> tag; on it\'s own line). <pre class="code"><code>&lt;?php<br />define(\'WP_CACHE\', TRUE);</code></pre>', plugin()->text_domain)."\n";
							echo '</div>'."\n";
						}
					if(!empty($_REQUEST[__NAMESPACE__.'__wp_config_wp_cache_remove_failure']))
						{
							echo '<div class="plugin-menu-page-notice error">'."\n";
							echo '   <i class="fa fa-thumbs-down"></i> '.__('Failed to update your <code>/wp-config.php</code> file automatically. Please remove the following line from your <code>/wp-config.php</code> file, or set <code>WP_CACHE</code> to a <code>FALSE</code> value. <pre class="code"><code>define(\'WP_CACHE\', TRUE);</code></pre>', plugin()->text_domain)."\n";
							echo '</div>'."\n";
						}
					if(!empty($_REQUEST[__NAMESPACE__.'__advanced_cache_add_failure']))
						{
							echo '<div class="plugin-menu-page-notice error">'."\n";
							if($_REQUEST[__NAMESPACE__.'__advanced_cache_add_failure'] === 'qc-advanced-cache')
								echo '   <i class="fa fa-thumbs-down"></i> '.sprintf(__('Failed to update your <code>/wp-content/advanced-cache.php</code> file. Cannot write stat file: <code>%1$s/qc-advanced-cache</code>. Please be sure this directory exists (and that it\'s writable): <code>%1$s</code>. Please use directory permissions <code>755</code> or higher (perhaps <code>777</code>). Once you\'ve done this, please try again.', plugin()->text_domain), rtrim(plugin()->options['cache_dir'], '/'))."\n";
							else echo '   <i class="fa fa-thumbs-down"></i> '.__('Failed to update your <code>/wp-content/advanced-cache.php</code> file. Most likely a permissions error. Please create an empty file here: <code>/wp-content/advanced-cache.php</code> (just an empty PHP file, with nothing in it); give it permissions <code>644</code> or higher (perhaps <code>666</code>). Once you\'ve done this, please try again.', plugin()->text_domain)."\n";
							echo '</div>'."\n";
						}
					if(!empty($_REQUEST[__NAMESPACE__.'__advanced_cache_remove_failure']))
						{
							echo '<div class="plugin-menu-page-notice error">'."\n";
							echo '   <i class="fa fa-thumbs-down"></i> '.__('Failed to remove your <code>/wp-content/advanced-cache.php</code> file. Most likely a permissions error. Please delete (or empty the contents of) this file: <code>/wp-content/advanced-cache.php</code>.', plugin()->text_domain)."\n";
							echo '</div>'."\n";
						}
					if(!empty($_REQUEST[__NAMESPACE__.'_pro_preview']))
						{
							echo '<div class="plugin-menu-page-notice info">'."\n";
							echo '<a href="'.add_query_arg(urlencode_deep(array('page' => __NAMESPACE__)), self_admin_url('/admin.php')).'" class="pull-right" style="margin:0 0 15px 25px; font-variant:small-caps; text-decoration:none;">'.__('close', plugin()->text_domain).' <i class="fa fa-eye-slash"></i></a>'."\n";
							echo '   <i class="fa fa-eye"></i> '.__('<strong>Pro Features (Preview)</strong> ~ New option panels below. Please explore before <a href="http://www.websharks-inc.com/product/quick-cache/" target="_blank">upgrading <i class="fa fa-heart-o"></i></a>.<br /><small>NOTE: the free version of Quick Cache (this LITE version); is more-than-adequate for most sites. Please upgrade only if you desire advanced features or would like to support the developer.</small>', plugin()->text_domain)."\n";
							echo '</div>'."\n";
						}
					if(!plugin()->options['enable']) // Not enabled yet?
						{
							echo '<div class="plugin-menu-page-notice warning">'."\n";
							echo '   <i class="fa fa-warning"></i> '.__('Quick Cache is currently disabled; please review options below.', plugin()->text_domain)."\n";
							echo '</div>'."\n";
						}
					echo '<div class="plugin-menu-page-body">'."\n";

					echo '<div class="plugin-menu-page-panel">'."\n";

					echo '   <div class="plugin-menu-page-panel-heading'.((!plugin()->options['enable']) ? ' open' : '').'">'."\n";
					echo '      <i class="fa fa-flag"></i> '.__('Enable/Disable', plugin()->text_domain)."\n";
					echo '   </div>'."\n";

					echo '   <div class="plugin-menu-page-panel-body'.((!plugin()->options['enable']) ? ' open' : '').' clearfix">'."\n";
					echo '      <p style="float:right; margin:-5px 0 0 0; font-weight:bold;">Quick Cache = <i class="fa fa-tachometer fa-4x"></i> SPEED<em>!!</em></p>'."\n";
					echo '      <p style="margin-top:1em;"><label class="switch-primary"><input type="radio" name="'.esc_attr(__NAMESPACE__).'[save_options][enable]" value="1"'.checked(plugin()->options['enable'], '1', FALSE).' /> <i class="fa fa-magic fa-flip-horizontal"></i> '.__('Yes, enable Quick Cache!', plugin()->text_domain).'</label> &nbsp;&nbsp;&nbsp; <label><input type="radio" name="'.esc_attr(__NAMESPACE__).'[save_options][enable]" value="0"'.checked(plugin()->options['enable'], '0', FALSE).' /> '.__('No, disable.', plugin()->text_domain).'</label></p>'."\n";
					echo '      <hr />'."\n";
					echo '      <p class="info">'.__('<strong>HUGE Time-Saver:</strong> Approx. 95% of all WordPress sites running Quick Cache, simply enable it here; and that\'s it :-) <strong>No further configuration is necessary (really).</strong> All of the other options (down below) are already tuned for the BEST performance on a typical WordPress installation. Simply enable Quick Cache here and click "Save All Changes". If you get any warnings please follow the instructions given. Otherwise, you\'re good <i class="fa fa-smile-o"></i>. This plugin is designed to run just fine like it is. Take it for a spin right away; you can always fine-tune things later if you deem necessary.', plugin()->text_domain).'</p>'."\n";
					echo '      <hr />'."\n";
					echo '      <img src="'.esc_attr(plugin()->url('/client-s/images/db-screenshot.png')).'" class="screenshot" />'."\n";
					echo '      <h3>'.__('How Can I Tell Quick Cache is Working?', plugin()->text_domain).'</h3>'."\n";
					echo '      <p>'.__('First of all, please make sure that you\'ve enabled Quick Cache here; then scroll down to the bottom of this page and click "Save All Changes". All of the other options (below) are already pre-configured for typical usage. Feel free to skip them all for now. You can go back through all of these later and fine-tune things the way you like them.', plugin()->text_domain).'</p>'."\n";
					echo '      <p>'.__('Once Quick Cache has been enabled, <strong>you\'ll need to log out (and/or clear browser cookies)</strong>. By default, cache files are NOT served to visitors who are logged-in, and that includes you too ;-) Cache files are NOT served to recent comment authors either. If you\'ve commented (or replied to a comment lately); please clear your browser cookies before testing.', plugin()->text_domain).'</p>'."\n";
					echo '      <p>'.__('<strong>To verify that Quick Cache is working</strong>, navigate your site like a normal visitor would. Right-click on any page (choose View Source), then scroll to the very bottom of the document. At the bottom, you\'ll find comments that show Quick Cache stats and information. You should also notice that page-to-page navigation is <i class="fa fa-flash"></i> <strong>lightning fast</strong> now that Quick Cache is running; and it gets faster over time!', plugin()->text_domain).'</p>'."\n";
					echo '      <p><select name="'.esc_attr(__NAMESPACE__).'[save_options][debugging_enable]">'."\n";
					echo '            <option value="1"'.selected(plugin()->options['debugging_enable'], '1', FALSE).'>'.__('Yes, enable notes in the source code so I can see it\'s working (recommended).', plugin()->text_domain).'</option>'."\n";
					echo '            <option value="0"'.selected(plugin()->options['debugging_enable'], '0', FALSE).'>'.__('No, I don\'t want my source code to contain any of these notes.', plugin()->text_domain).'</option>'."\n";
					echo '         </select></p>'."\n";
					echo '   </div>'."\n";

					echo '</div>'."\n";

					echo '<div class="plugin-menu-page-panel">'."\n";

					echo '   <div class="plugin-menu-page-panel-heading">'."\n";
					echo '      <i class="fa fa-shield"></i> '.__('Deactivation Safeguards', plugin()->text_domain)."\n";
					echo '   </div>'."\n";

					echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
					echo '      <i class="fa fa-shield fa-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
					echo '      <h3>'.__('Uninstall on Deactivation; or Safeguard Options?', plugin()->text_domain).'</h3>'."\n";
					echo '      <p>'.__('<strong>Tip:</strong> By default, if you deactivate Quick Cache from the plugins menu in WordPress; nothing is lost. However, if you want to uninstall Quick Cache you should set this to <code>Yes</code> and <strong>THEN</strong> deactivate it from the plugins menu in WordPress. This way Quick Cache will erase your options for the plugin, clear the cache, remove the <code>advanced-cache.php</code> file, terminate CRON jobs, etc. It erases itself from existence completely.', plugin()->text_domain).'</p>'."\n";
					echo '      <p><select name="'.esc_attr(__NAMESPACE__).'[save_options][uninstall_on_deactivation]">'."\n";
					echo '            <option value="0"'.selected(plugin()->options['uninstall_on_deactivation'], '0', FALSE).'>'.__('If I deactivate Quick Cache please safeguard my options and the cache (recommended).', plugin()->text_domain).'</option>'."\n";
					echo '            <option value="1"'.selected(plugin()->options['uninstall_on_deactivation'], '1', FALSE).'>'.__('Yes, uninstall (completely erase) Quick Cache on deactivation.', plugin()->text_domain).'</option>'."\n";
					echo '         </select></p>'."\n";
					echo '   </div>'."\n";

					echo '</div>'."\n";

					if(plugin()->is_pro_preview())
						{
							echo '<div class="plugin-menu-page-panel pro-preview">'."\n";

							echo '   <div class="plugin-menu-page-panel-heading">'."\n";
							echo '      <i class="fa fa-info-circle"></i> '.__('Clearing the Cache', plugin()->text_domain)."\n";
							echo '   </div>'."\n";

							echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
							echo '      <h3>'.__('Clearing the Cache (Manually)', plugin()->text_domain).'</h3>'."\n";
							echo '      <img src="'.esc_attr(plugin()->url('/client-s/images/cc-screenshot.png')).'" class="screenshot" />'."\n";
							echo '      <p>'.__('Once Quick Cache is enabled, you will find this new option in your WordPress Admin Bar (see screenshot on right). Clicking this button will clear the cache and you can start fresh at anytime (e.g. you can do this manually; and as often as you wish).', plugin()->text_domain).'</p>'."\n";
							echo '      <p>'.__('Depending on the structure of your site, there could be many reasons to clear the cache. However, the most common reasons are related to Post/Page edits or deletions, Category/Tag edits or deletions, and Theme changes. Quick Cache handles most scenarios all by itself. However, many site owners like to clear the cache manually; for a variety of reasons (just to force a refresh).', plugin()->text_domain).'</p>'."\n";
							echo '      <p><select name="'.esc_attr(__NAMESPACE__).'[save_options][admin_bar_enable]">'."\n";
							echo '            <option value="1" selected="selected">'.__('Yes, enable the &quot;Clear Cache&quot; button in the WordPress admin bar.', plugin()->text_domain).'</option>'."\n";
							echo '            <option value="0">'.__('No, I don\'t intend to clear the cache manually; exclude from admin bar.', plugin()->text_domain).'</option>'."\n";
							echo '         </select></p>'."\n";
							echo '      <h3>'.__('Running the <a href="http://www.websharks-inc.com/product/s2clean/" target="_blank">s2Clean Theme</a> by WebSharks?', plugin()->text_domain).'</h3>'."\n";
							echo '      <p>'.__('If s2Clean is installed, Quick Cache can be configured to clear the Markdown cache too (if you\'ve enabled Markdown processing with s2Clean). The s2Clean Markdown cache is only cleared when you manually clear the cache (with Quick Cache); and only if you enable this option here. Note: s2Clean\'s Markdown cache is extremely dynamic. Just like the rest of your site, s2Clean caches do NOT need to be cleared away at all, as this happens automatically when your content changes. However, some developers find this feature useful while developing their site; just to force a refresh.', plugin()->text_domain).'</p>'."\n";
							echo '      <p><select name="'.esc_attr(__NAMESPACE__).'[save_options][cache_clear_s2clean_enable]">'."\n";
							echo '            <option value="1">'.__('Yes, if the s2Clean theme is installed; also clear s2Clean-related caches.', plugin()->text_domain).'</option>'."\n";
							echo '            <option value="0" selected="selected">'.__('No, I don\'t use s2Clean; or, I don\'t want s2Clean-related caches cleared.', plugin()->text_domain).'</option>'."\n";
							echo '         </select></p>'."\n";
							echo '      <h3>'.__('Process Other Custom PHP Code?', plugin()->text_domain).'</h3>'."\n";
							echo '      <p>'.__('If you have other custom routines you\'d like to process when the cache is cleared manually, please type your custom PHP code here. The PHP code that you provide is only evaluated when you manually clear the cache (with Quick Cache); and only if the field below contains PHP code. Note: if your PHP code outputs a message (e.g. if you have <code>echo \'&lt;p&gt;My message&lt;/p&gt;\';</code>); your message will be displayed along with any other notes from Quick Cache itself. This could be useful to developers that need to clear server caches too (such as <a href="http://www.php.net/manual/en/function.apc-clear-cache.php" target="_blank">APC</a> or <a href="http://www.php.net/manual/en/memcache.flush.php" target="_blank">memcache</a>).', plugin()->text_domain).'</p>'."\n";
							echo '      <p style="margin-bottom:0;"><textarea name="'.esc_attr(__NAMESPACE__).'[save_options][cache_clear_eval_code]" rows="5" spellcheck="false" class="monospace"></textarea></p>'."\n";
							echo '      <p class="info" style="margin-top:0;">'.__('<strong>Example:</strong> <code>&lt;?php apc_clear_cache(); echo \'&lt;p&gt;Also cleared APC cache.&lt;/p&gt;\'; ?&gt;</code>', plugin()->text_domain).'</p>'."\n";
							echo '      <hr />'."\n";
							echo '      <h3>'.__('Purging the Cache (Automatically)', plugin()->text_domain).'</h3>'."\n";
							echo '      <img src="'.esc_attr(plugin()->url('/client-s/images/ap-screenshot.png')).'" class="screenshot" />'."\n";
							echo '      <p>'.__('This is built into the Quick Cache plugin; e.g. this functionality is "always on". If you edit a Post/Page (or delete one), Quick Cache will automatically purge the cache file(s) associated with that content. This way a new updated version of the cache will be created automatically the next time this content is accessed. Simple updates like this occur each time you make changes in the Dashboard, and Quick Cache will notify you of these as they occur. Quick Cache monitors changes to Posts (of any kind, including Pages), Categories, Tags, Links, Themes (even Users); and more. Notifications in the Dashboard regarding these detections can be enabled/disabled below.', plugin()->text_domain).'</p>'."\n";
							echo '      <p><select name="'.esc_attr(__NAMESPACE__).'[save_options][change_notifications_enable]">'."\n";
							echo '            <option value="1" selected="selected">'.__('Yes, enable Quick Cache notifications in the Dashboard when changes are detected &amp; one or more cache files are purged automatically.', plugin()->text_domain).'</option>'."\n";
							echo '            <option value="0">'.__('No, I don\'t want to know (don\'t really care) what Quick Cache is doing behind-the-scene.', plugin()->text_domain).'</option>'."\n";
							echo '         </select></p>'."\n";
							echo '      <h3>'.__('Auto-Purge Designated "Home Page" Too?', plugin()->text_domain).'</h3>'."\n";
							echo '      <p>'.__('On many sites, the Home Page (aka: the Front Page) offers an archive view of all Posts (or even Pages). Therefore, if a single Post/Page is changed in some way; and Quick Cache purges/resets the cache for a single Post/Page, would you like Quick Cache to also purge any existing cache files for the "Home Page"?', plugin()->text_domain).'</p>'."\n";
							echo '      <p><select name="'.esc_attr(__NAMESPACE__).'[save_options][cache_purge_home_page_enable]">'."\n";
							echo '            <option value="1" selected="selected">'.__('Yes, if any single Post/Page is purged/reset; also purge the "Home Page".', plugin()->text_domain).'</option>'."\n";
							echo '            <option value="0">'.__('No, my Home Page does not provide a list of Posts/Pages; e.g. this is not necessary.', plugin()->text_domain).'</option>'."\n";
							echo '         </select></p>'."\n";
							echo '      <h3>'.__('Auto-Purge Designated "Posts Page" Too?', plugin()->text_domain).'</h3>'."\n";
							echo '      <p>'.__('On many sites, the Posts Page (aka: the Blog Page) offers an archive view of all Posts (or even Pages). Therefore, if a single Post/Page is changed in some way; and Quick Cache purges/resets the cache for a single Post/Page, would you like Quick Cache to also purge any existing cache files for the "Posts Page"?', plugin()->text_domain).'</p>'."\n";
							echo '      <p><select name="'.esc_attr(__NAMESPACE__).'[save_options][cache_purge_posts_page_enable]">'."\n";
							echo '            <option value="1" selected="selected">'.__('Yes, if any single Post/Page is purged/reset; also purge the "Posts Page".', plugin()->text_domain).'</option>'."\n";
							echo '            <option value="0">'.__('No, I don\'t use a separate Posts Page; e.g. my Home Page IS my Posts Page.', plugin()->text_domain).'</option>'."\n";
							echo '         </select></p>'."\n";
							echo '      <h3>'.__('Auto-Purge "Author Page" Too?', plugin()->text_domain).'</h3>'."\n";
							echo '      <p>'.__('On many sites, each author has a related "Author Page" that offers an archive view of all posts associated with that author. Therefore, if a single Post/Page is changed in some way; and Quick Cache purges/resets the cache for a single Post/Page, would you like Quick Cache to also purge any existing cache files for the related "Author Page"?', plugin()->text_domain).'</p>'."\n";
							echo '      <p><select name="'.esc_attr(__NAMESPACE__).'[save_options][cache_purge_author_page_enable]">'."\n";
							echo '            <option value="1" selected="selected">'.__('Yes, if any single Post/Page is purged/reset; also purge the "Author Page".', plugin()->text_domain).'</option>'."\n";
							echo '            <option value="0">'.__('No, my site doesn\'t use multiple authors and/or I don\'t have any "Author Page" archive views.', plugin()->text_domain).'</option>'."\n";
							echo '         </select></p>'."\n";
							echo '      <h3>'.__('Auto-Purge "Category Archives" Too?', plugin()->text_domain).'</h3>'."\n";
							echo '      <p>'.__('On many sites, each post is associated with at least one Category. Each category then has an archive view that contains all the posts within that category. Therefore, if a single Post/Page is changed in some way; and Quick Cache purges/resets the cache for a single Post/Page, would you like Quick Cache to also purge any existing cache files for the associated Category archive views?', plugin()->text_domain).'</p>'."\n";
							echo '      <p><select name="'.esc_attr(__NAMESPACE__).'[save_options][cache_purge_term_category_enable]">'."\n";
							echo '            <option value="1" selected="selected">'.__('Yes, if any single Post/Page is purged/reset; also purge the associated Category archive views.', plugin()->text_domain).'</option>'."\n";
							echo '            <option value="0">'.__('No, my site doesn\'t use Categories and/or I don\'t have any Category archive views.', plugin()->text_domain).'</option>'."\n";
							echo '         </select></p>'."\n";
							echo '      <h3>'.__('Auto-Purge "Tag Archives" Too?', plugin()->text_domain).'</h3>'."\n";
							echo '      <p>'.__('On many sites, each post may be associated with at least one Tag. Each tag then has an archive view that contains all the posts assigned that tag. Therefore, if a single Post/Page is changed in some way; and Quick Cache purges/resets the cache for a single Post/Page, would you like Quick Cache to also purge any existing cache files for the associated Tag archive views?', plugin()->text_domain).'</p>'."\n";
							echo '      <p><select name="'.esc_attr(__NAMESPACE__).'[save_options][cache_purge_term_tag_enable]">'."\n";
							echo '            <option value="1" selected="selected">'.__('Yes, if any single Post/Page is purged/reset; also purge the associated Tag archive views.', plugin()->text_domain).'</option>'."\n";
							echo '            <option value="0">'.__('No, my site doesn\'t use Tags and/or I don\'t have any Tag archive views.', plugin()->text_domain).'</option>'."\n";
							echo '         </select></p>'."\n";
							echo '      <h3>'.__('Auto-Purge "Custom Term Archives" Too?', plugin()->text_domain).'</h3>'."\n";
							echo '      <p>'.__('Most sites do not use any custom Terms so it should be safe to leave this disabled. However, if your site uses custom Terms and they have their own Term archive views, you may want to clear those when the associated post is cleared. Therefore, if a single Post/Page is changed in some way; and Quick Cache purges/resets the cache for a single Post/Page, would you like Quick Cache to also purge any existing cache files for the associated Tag archive views?', plugin()->text_domain).'</p>'."\n";
							echo '      <p><select name="'.esc_attr(__NAMESPACE__).'[save_options][cache_purge_term_other_enable]">'."\n";
							echo '            <option value="1" selected="selected">'.__('Yes, if any single Post/Page is purged/reset; also purge any associated custom Term archive views.', plugin()->text_domain).'</option>'."\n";
							echo '            <option value="0">'.__('No, my site doesn\'t use any custom Terms and/or I don\'t have any custom Term archive views.', plugin()->text_domain).'</option>'."\n";
							echo '         </select></p>'."\n";
							echo '   </div>'."\n";

							echo '</div>'."\n";
						}
					echo '<div class="plugin-menu-page-panel">'."\n";

					echo '   <div class="plugin-menu-page-panel-heading">'."\n";
					echo '      <i class="fa fa-gears"></i> '.__('Directory / Expiration Time', plugin()->text_domain)."\n";
					echo '   </div>'."\n";

					echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
					echo '      <h3>'.__('Cache Directory (Must be Writable; e.g. <a href="http://codex.wordpress.org/Changing_File_Permissions" target="_blank">Permissions</a> <code>755</code> or Higher)', plugin()->text_domain).'</h3>'."\n";
					echo '      <p>'.__('This is where Quick Cache will store the cached version of your site. If you\'re not sure how to deal with directory permissions, don\'t worry too much about this. If there is a problem, Quick Cache will let you know about it. By default, this directory is created by Quick Cache and the permissions are setup automatically. In most cases there is nothing more you need to do.', plugin()->text_domain).'</p>'."\n";
					echo '      <table style="width:100%;"><tr><td style="width:1px; font-weight:bold; white-space:nowrap;">'.esc_html(ABSPATH).'</td><td><input type="text" name="'.esc_attr(__NAMESPACE__).'[save_options][cache_dir]" value="'.esc_attr(plugin()->options['cache_dir']).'" /></td><td style="width:1px; font-weight:bold; white-space:nowrap;">/</td></tr></table>'."\n";
					echo '      <hr />'."\n";
					echo '      <i class="fa fa-clock-o fa-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
					echo '      <h3>'.__('Automatic Expiration Time (Max Age)', plugin()->text_domain).'</h3>'."\n";
					echo '      <p>'.__('If you don\'t update your site much, you could set this to <code>6 months</code> and optimize everything even further. The longer the Cache Expiration Time is, the greater your performance gain. Alternatively, the shorter the Expiration Time, the fresher everything will remain on your site. A default value of <code>7 days</code> (recommended); is a good conservative middle-ground.', plugin()->text_domain).'</p>'."\n";
					echo '      <p>'.__('Keep in mind that your Expiration Time is only one part of the big picture. Quick Cache will also purge the cache automatically as changes are made to the site (i.e. you edit a post, someone comments on a post, you change your theme, you add a new navigation menu item, etc., etc.). Thus, your Expiration Time is really just a fallback; e.g. the maximum amount of time that a cache file could ever possibly live.', plugin()->text_domain).'</p>'."\n";
					echo '      <p>'.__('All of that being said, you could set this to just <code>60 seconds</code> and you would still see huge differences in speed and performance. If you\'re just starting out with Quick Cache (perhaps a bit nervous about old cache files being served to your visitors); you could set this to something like <code>30 minutes</code>, and experiment with it while you build confidence in Quick Cache. It\'s not necessary to do so, but many site owners have reported this makes them feel like they\'re more-in-control when the cache has a short expiration time. All-in-all, it\'s a matter of preference <i class="fa fa-smile-o"></i>.', plugin()->text_domain).'</p>'."\n";
					echo '      <p><input type="text" name="'.esc_attr(__NAMESPACE__).'[save_options][cache_max_age]" value="'.esc_attr(plugin()->options['cache_max_age']).'" /></p>'."\n";
					echo '      <p class="info">'.__('<strong>Tip:</strong> the value that you specify here MUST be compatible with PHP\'s <a href="http://php.net/manual/en/function.strtotime.php" target="_blank" style="text-decoration:none;"><code>strtotime()</code></a> function. Examples: <code>30 seconds</code>, <code>2 hours</code>, <code>7 days</code>, <code>6 months</code>, <code>1 year</code>.', plugin()->text_domain).'</p>'."\n";
					echo '      <p class="info">'.__('<strong>Note:</strong> Quick Cache will never serve a cache file that is older than what you specify here (even if one exists in your cache directory; stale cache files are never used). In addition, a WP Cron job will automatically cleanup your cache directory (once daily); purging expired cache files periodically. This prevents a HUGE cache from building up over time, creating a potential storage issue.', plugin()->text_domain).'</p>'."\n";
					echo '   </div>'."\n";

					echo '</div>'."\n";

					echo '<div class="plugin-menu-page-panel">'."\n";

					echo '   <div class="plugin-menu-page-panel-heading">'."\n";
					echo '      <i class="fa fa-gears"></i> '.__('Client-Side Cache', plugin()->text_domain)."\n";
					echo '   </div>'."\n";

					echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
					echo '      <i class="fa fa-desktop fa-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
					echo '      <h3>'.__('Allow Double-Caching In The Client-Side Browser?', plugin()->text_domain).'</h3>'."\n";
					echo '      <p>'.__('Recommended setting: <code>No</code> (for membership sites, very important). Otherwise, <code>Yes</code> would be better (if users do NOT log in/out of your site).', plugin()->text_domain).'</p>'."\n";
					echo '      <p>'.__('Quick Cache handles content delivery through its ability to communicate with a browser using PHP. If you allow a browser to (cache) the caching system itself, you are momentarily losing some control; and this can have a negative impact on users that see more than one version of your site; e.g. one version while logged-in, and another while NOT logged-in. For instance, a user may log out of your site, but upon logging out they report seeing pages on the site which indicate they are STILL logged in (even though they\'re not — that\'s bad). This can happen if you allow a client-side cache, because their browser may cache web pages they visited while logged into your site which persist even after logging out. Sending no-cache headers will work to prevent this issue.', plugin()->text_domain).'</p>'."\n";
					echo '      <p>'.__('All of that being said, if all you care about is blazing fast speed and users don\'t log in/out of your site (only you do); you can safely set this to <code>Yes</code> (recommended in this case). Allowing a client-side browser cache will improve speed and reduce outgoing bandwidth when this option is feasible.', plugin()->text_domain).'</p>'."\n";
					echo '      <p><select name="'.esc_attr(__NAMESPACE__).'[save_options][allow_browser_cache]">'."\n";
					echo '            <option value="0"'.selected(plugin()->options['allow_browser_cache'], '0', FALSE).'>'.__('No, prevent a client-side browser cache (safest option).', plugin()->text_domain).'</option>'."\n";
					echo '            <option value="1"'.selected(plugin()->options['allow_browser_cache'], '1', FALSE).'>'.__('Yes, I will allow a client-side browser cache of pages on the site.', plugin()->text_domain).'</option>'."\n";
					echo '         </select></p>'."\n";
					echo '      <p class="info">'.__('<strong>Tip:</strong> Setting this to <code>No</code> is highly recommended when running a membership plugin like <a href="http://wordpress.org/plugins/s2member/" target="_blank">s2Member</a> (as one example). In fact, many plugins like s2Member will send <a href="http://codex.wordpress.org/Function_Reference/nocache_headers" target="_blank">nocache_headers()</a> on their own, so your configuration here will likely be overwritten when you run such plugins (which is better anyway). In short, if you run a membership plugin, you should NOT allow a client-side browser cache.', plugin()->text_domain).'</p>'."\n";
					echo '      <p class="info">'.__('<strong>Tip:</strong> Setting this to <code>No</code> will NOT impact static content; e.g. CSS, JS, images, or other media. This setting pertains only to dynamic PHP scripts which produce content generated by WordPress.', plugin()->text_domain).'</p>'."\n";
					echo '      <p class="info">'.__('<strong>Advanced Tip:</strong> if you have this set to <code>No</code>, but you DO want to allow a few special URLs to be cached by the browser; you can add this parameter to your URL <code>?qcABC=1</code>. This tells Quick Cache that it\'s OK for the browser to cache that particular URL. In other words, the <code>qcABC=1</code> parameter tells Quick Cache NOT to send no-cache headers to the browser.', plugin()->text_domain).'</p>'."\n";
					echo '   </div>'."\n";

					echo '</div>'."\n";

					if(plugin()->is_pro_preview())
						{
							echo '<div class="plugin-menu-page-panel pro-preview">'."\n";

							echo '   <div class="plugin-menu-page-panel-heading">'."\n";
							echo '      <i class="fa fa-gears"></i> '.__('Logged-In Users', plugin()->text_domain)."\n";
							echo '   </div>'."\n";

							echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
							echo '      <i class="fa fa-group fa-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
							echo '      <h3>'.__('Caching Enabled for Logged-In Users &amp; Comment Authors?', plugin()->text_domain).'</h3>'."\n";
							echo '      <p>'.__('This should almost ALWAYS be set to <code>No</code>. Most sites will NOT want to cache content generated while a user is logged-in. Doing so could result in a cache of dynamic content generated specifically for a particular user, where the content being cached may contain details that pertain only to the user that was logged-in when the cache was generated. Imagine visiting a website that says you\'re logged-in as Billy Bob (but you\'re not Billy Bob; NOT good). In short, do NOT turn this on unless you know what you\'re doing.', plugin()->text_domain).'</p>'."\n";
							echo '      <i class="fa fa-sitemap fa-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
							echo '      <p>'.__('<strong>Exception (Membership Sites):</strong> If you run a site with many users and the majority of your traffic comes from users who ARE logged-in, please choose: <code>Yes (maintain separate cache)</code>. Quick Cache will operate normally; but when a user is logged-in, the cache is user-specific. Quick Cache will intelligently refresh the cache when/if a user submits a form on your site with the GET or POST method. Or, if you make changes to their account (or another plugin makes changes to their account); including user <a href="http://codex.wordpress.org/Function_Reference/update_user_option" target="_blank">option</a>|<a href="http://codex.wordpress.org/Function_Reference/update_user_meta" target="_blank">meta</a> additions, updates &amp; deletions too. However, please note that enabling this feature (e.g. user-specific cache entries); will eat up MUCH more disk space. That being said, the benefits of this feature for most sites will outweigh the disk overhead (e.g. it\'s NOT an issue in most cases). Unless you are short on disk space (or you have MANY thousands of users), the disk overhead is neglible.', plugin()->text_domain).'</p>'."\n";
							echo '      <p><select name="'.esc_attr(__NAMESPACE__).'[save_options][when_logged_in]">'."\n";
							echo '            <option value="0" selected="selected">'.__('No, do NOT cache; or serve a cache file when a user is logged-in (safest option).', plugin()->text_domain).'</option>'."\n";
							echo '            <option value="postload">'.__('Yes, and maintain a separate cache for each user (recommended for membership sites).', plugin()->text_domain).'</option>'."\n";
							echo '            <option value="1">'.__('Yes, but DON\'T maintain a separate cache for each user (I know what I\'m doing).', plugin()->text_domain).'</option>'."\n";
							echo '         </select></p>'."\n";
							echo '      <p class="info">'.__('<strong>Note:</strong> For most sites, the majority of their traffic (if not all of their traffic) comes from visitors who are not logged in, so disabling the cache for logged-in users is NOT ordinarily a performance issue. When a user IS logged-in, disabling the cache is considered ideal, because a logged-in user has a session open with your site; and the content they view should remain very dynamic in this scenario.', plugin()->text_domain).'</p>'."\n";
							echo '      <p class="info">'.__('<strong>Note:</strong> This setting includes some users who AREN\'T actually logged into the system, but who HAVE authored comments recently. Quick Cache includes comment authors as part of it\'s logged-in user check. This way comment authors will be able to see updates to the comment thread immediately; and, so that any dynamically-generated messages displayed by your theme will work as intended. In short, Quick Cache thinks of a comment author as a logged-in user, even though technically they are not. ~ Users who gain access to password-protected Posts/Pages are also included.', plugin()->text_domain).'</p>'."\n";
							echo '   </div>'."\n";

							echo '</div>'."\n";
						}
					echo '<div class="plugin-menu-page-panel">'."\n";

					echo '   <div class="plugin-menu-page-panel-heading">'."\n";
					echo '      <i class="fa fa-gears"></i> '.__('GET Requests', plugin()->text_domain)."\n";
					echo '   </div>'."\n";

					echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
					echo '      <i class="fa fa-question-circle fa-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
					echo '      <h3>'.__('Caching Enabled for GET (Query String) Requests?', plugin()->text_domain).'</h3>'."\n";
					echo '      <p>'.__('This should almost ALWAYS be set to <code>No</code>. UNLESS, you\'re using unfriendly Permalinks. In other words, if all of your URLs contain a query string (e.g. <code>/?key=value</code>); you\'re using unfriendly Permalinks. Ideally, you would refrain from doing this; and instead, update your Permalink options immediately; which also optimizes your site for search engines. That being said, if you really want to use unfriendly Permalinks, and ONLY if you\'re using unfriendly Permalinks, you should set this to <code>Yes</code>; and don\'t worry too much, the sky won\'t fall on your head :-)', plugin()->text_domain).'</p>'."\n";
					echo '      <p><select name="'.esc_attr(__NAMESPACE__).'[save_options][get_requests]">'."\n";
					echo '            <option value="0"'.selected(plugin()->options['get_requests'], '0', FALSE).'>'.__('No, do NOT cache (or serve a cache file) when a query string is present.', plugin()->text_domain).'</option>'."\n";
					echo '            <option value="1"'.selected(plugin()->options['get_requests'], '1', FALSE).'>'.__('Yes, I would like to cache URLs that contain a query string.', plugin()->text_domain).'</option>'."\n";
					echo '         </select></p>'."\n";
					echo '      <p class="info">'.__('<strong>Note:</strong> POST requests (i.e. forms with <code>method=&quot;post&quot;</code>) are always excluded from the cache, which is the way it should be. Any <a href="http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html" target="_blank">POST/PUT/DELETE</a> request should NEVER (ever) be cached. CLI (and self-serve) requests are also excluded from the cache (always). A CLI request is one that comes from the command line; commonly used by CRON jobs and other automated routines. A self-serve request is an HTTP connection established from your site -› to your site. For instance, a WP Cron job, or any other HTTP request that is spawned not by a user, but by the server itself.', plugin()->text_domain).'</p>'."\n";
					echo '      <p class="info">'.__('<strong>Advanced Tip:</strong> If you are NOT caching GET requests (recommended), but you DO want to allow some special URLs that include query string parameters to be cached; you can add this special parameter to any URL <code>?qcAC=1</code>. This tells Quick Cache that it\'s OK to cache that particular URL, even though it contains query string arguments.', plugin()->text_domain).'</p>'."\n";
					echo '   </div>'."\n";

					echo '</div>'."\n";

					echo '<div class="plugin-menu-page-panel">'."\n";

					echo '   <div class="plugin-menu-page-panel-heading">'."\n";
					echo '      <i class="fa fa-gears"></i> '.__('404 Requests', plugin()->text_domain)."\n";
					echo '   </div>'."\n";

					echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
					echo '      <i class="fa fa-question-circle fa-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
					echo '      <h3>'.__('Caching Enabled for 404 Requests?', plugin()->text_domain).'</h3>'."\n";
					echo '      <p>'.__('When this is set to <code>No</code>, Quick Cache will ignore all 404 requests and no cache file will be served. While this is fine for most site owners, caching the 404 page on a high-traffic site may further reduce server load. When this is set to <code>Yes</code>, Quick Cache will cache the 404 page (see <a href="https://codex.wordpress.org/Creating_an_Error_404_Page" target="_blank">Creating an Error 404 Page</a>) and then serve that single cache file to all future 404 requests.', plugin()->text_domain).'</p>'."\n";
					echo '      <p><select name="'.esc_attr(__NAMESPACE__).'[save_options][cache_404_requests]">'."\n";
					echo '            <option value="0"'.selected(plugin()->options['cache_404_requests'], '0', FALSE).'>'.__('No, do NOT cache (or serve a cache file) for 404 requests.', plugin()->text_domain).'</option>'."\n";
					echo '            <option value="1"'.selected(plugin()->options['cache_404_requests'], '1', FALSE).'>'.__('Yes, I would like to cache the 404 page and serve the cached file for 404 requests.', plugin()->text_domain).'</option>'."\n";
					echo '         </select></p>'."\n";
					echo '      <p class="info">'.__('<strong>How does Quick Cache cache 404 requests?</strong> Quick Cache will create a special cache file (<code>----404----.html</code>, see Advanced Tip below) for the first 404 request and then <a href="http://www.php.net/manual/en/function.symlink.php" target="_blank">symlink</a> future 404 requests to this special cache file. That way you don\'t end up with lots of 404 cache files that all contain the same thing (the contents of the 404 page). Instead, you\'ll have one 404 cache file and then several symlinks (i.e., references) to that 404 cache file.', plugin()->text_domain).'</p>'."\n";
					echo '      <p class="info">'.__('<strong>Advanced Tip:</strong> The default 404 cache filename (<code>----404----.html</code>) is designed to minimize the chance of a collision with a cache file for a real page with the same name. However, if you want to override this default and define your own 404 cache filename, you can do so by adding <code>define(\'QUICK_CACHE_404_CACHE_FILENAME\', \'your-404-cache-filename\');</code> to your <code>wp-config.php</code> file (note that the <code>.html</code> extension should be excluded when defining a new filename).', plugin()->text_domain).'</p>'."\n";
					echo '   </div>'."\n";

					echo '</div>'."\n";

					echo '<div class="plugin-menu-page-panel">'."\n";

					echo '   <div class="plugin-menu-page-panel-heading">'."\n";
					echo '      <i class="fa fa-gears"></i> '.__('RSS, RDF, and Atom Feeds', plugin()->text_domain)."\n";
					echo '   </div>'."\n";

					echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
					echo '      <i class="fa fa-question-circle fa-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
					echo '      <h3>'.__('Caching Enabled for RSS, RDF, Atom Feeds?', plugin()->text_domain).'</h3>'."\n";
					echo '      <p>'.__('This should almost ALWAYS be set to <code>No</code>. UNLESS, you\'re sure that you want to cache your feeds. If you use a web feed management provider like Google® Feedburner and you set this option to <code>Yes</code>, you may experience delays in the detection of new posts.', plugin()->text_domain).'</p>'."\n";
					echo '      <p><select name="'.esc_attr(__NAMESPACE__).'[save_options][feeds_enable]">'."\n";
					echo '            <option value="0"'.selected(plugin()->options['feeds_enable'], '0', FALSE).'>'.__('No, do NOT cache (or serve a cache file) when displaying a feed.', plugin()->text_domain).'</option>'."\n";
					echo '            <option value="1"'.selected(plugin()->options['feeds_enable'], '1', FALSE).'>'.__('Yes, I would like to cache feed URLs.', plugin()->text_domain).'</option>'."\n";
					echo '         </select></p>'."\n";
					echo '      <p class="info">'.__('<strong>Note:</strong> This option affects all feeds served by WordPress, including the site feed, the site comment feed, post-specific comment feeds, author feeds, search feeds, and category and tag feeds. See also: <a href="http://codex.wordpress.org/WordPress_Feeds" target="_blank">WordPress Feeds</a>.', plugin()->text_domain).'</p>'."\n";
					echo '   </div>'."\n";

					echo '</div>'."\n";

					if(plugin()->is_pro_preview())
						{
							echo '<div class="plugin-menu-page-panel pro-preview">'."\n";

							echo '   <div class="plugin-menu-page-panel-heading">'."\n";
							echo '      <i class="fa fa-gears"></i> '.__('URI Exclusion Patterns', plugin()->text_domain)."\n";
							echo '   </div>'."\n";

							echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
							echo '      <h3>'.__('Don\'t Cache These Special URI Exclusion Patterns?', plugin()->text_domain).'</h3>'."\n";
							echo '      <p>'.__('Sometimes there are certain cases where a particular file, or a particular group of files, should never be cached. This is where you will enter those if you need to (one per line). Searches are performed against the <a href="http://www.php.net/manual/en/reserved.variables.server.php" target="_blank" style="text-decoration:none;"><code>REQUEST_URI</code></a>; i.e. <code>/path/?query</code> (case sensitive). So, don\'t put in full URLs here, just word fragments found in the file path (or query string) is all you need, excluding the http:// and domain name. A wildcard <code>*</code> character can also be used when necessary; e.g. <code>/category/abc-followed-by-*</code>; (where <code>*</code> = anything, 0 or more characters in length).', plugin()->text_domain).'</p>'."\n";
							echo '      <p><textarea name="'.esc_attr(__NAMESPACE__).'[save_options][exclude_uris]" rows="5" spellcheck="false" class="monospace"></textarea></p>'."\n";
							echo '      <p class="info">'.__('<strong>Tip:</strong> let\'s use this example URL: <code>http://www.example.com/post/example-post-123</code>. To exclude this URL, you would put this line into the field above: <code>/post/example-post-123</code>. Or, you could also just put in a small fragment, like: <code>example</code> or <code>example-*-123</code> and that would exclude any URI containing that word fragment.', plugin()->text_domain).'</p>'."\n";
							echo '      <p class="info">'.__('<strong>Note:</strong> please remember that your entries here should be formatted as a line-delimited list; e.g. one exclusion pattern per line.', plugin()->text_domain).'</p>'."\n";
							echo '   </div>'."\n";

							echo '</div>'."\n";
						}
					if(plugin()->is_pro_preview())
						{
							echo '<div class="plugin-menu-page-panel pro-preview">'."\n";

							echo '   <div class="plugin-menu-page-panel-heading">'."\n";
							echo '      <i class="fa fa-gears"></i> '.__('HTTP Referrer Exclusion Patterns', plugin()->text_domain)."\n";
							echo '   </div>'."\n";

							echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
							echo '      <h3>'.__('Don\'t Cache These Special HTTP Referrer Exclusion Patterns?', plugin()->text_domain).'</h3>'."\n";
							echo '      <p>'.__('Sometimes there are special cases where a particular referring URL (or referring domain) that sends you traffic; or even a particular group of referring URLs or domains that send you traffic; should result in a page being loaded on your site that is NOT from the cache (and that resulting page should never be cached). This is where you will enter those if you need to (one per line). Searches are performed against the <a href="http://www.php.net/manual/en/reserved.variables.server.php" target="_blank" style="text-decoration:none;"><code>HTTP_REFERER</code></a> (case sensitive). A wildcard <code>*</code> character can also be used when necessary; e.g. <code>*.domain.com</code>; (where <code>*</code> = anything, 0 or more characters in length).', plugin()->text_domain).'</p>'."\n";
							echo '      <p><textarea name="'.esc_attr(__NAMESPACE__).'[save_options][exclude_refs]" rows="5" spellcheck="false" class="monospace"></textarea></p>'."\n";
							echo '      <p class="info">'.__('<strong>Tip:</strong> let\'s use this example URL: <code>http://www.referring-domain.com/search/?q=search+terms</code>. To exclude this referring URL, you could put this line into the field above: <code>www.referring-domain.com</code>. Or, you could also just put in a small fragment, like: <code>/search/</code> or <code>q=*</code>; and that would exclude any referrer containing that word fragment.', plugin()->text_domain).'</p>'."\n";
							echo '      <p class="info">'.__('<strong>Note:</strong> please remember that your entries here should be formatted as a line-delimited list; e.g. one exclusion pattern per line.', plugin()->text_domain).'</p>'."\n";
							echo '   </div>'."\n";

							echo '</div>'."\n";
						}
					if(plugin()->is_pro_preview())
						{
							echo '<div class="plugin-menu-page-panel pro-preview">'."\n";

							echo '   <div class="plugin-menu-page-panel-heading">'."\n";
							echo '      <i class="fa fa-gears"></i> '.__('User-Agent Exclusion Patterns', plugin()->text_domain)."\n";
							echo '   </div>'."\n";

							echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
							echo '      <h3>'.__('Don\'t Cache These Special User-Agent Exclusion Patterns?', plugin()->text_domain).'</h3>'."\n";
							echo '      <p>'.__('Sometimes there are special cases when a particular user-agent (e.g. a specific browser or a specific type of device); should be shown a page on your site that is NOT from the cache (and that resulting page should never be cached). This is where you will enter those if you need to (one per line). Searches are performed against the <a href="http://www.php.net/manual/en/reserved.variables.server.php" target="_blank" style="text-decoration:none;"><code>HTTP_USER_AGENT</code></a> (case insensitive). A wildcard <code>*</code> character can also be used when necessary; e.g. <code>Android *; Chrome/* Mobile</code>; (where <code>*</code> = anything, 0 or more characters in length).', plugin()->text_domain).'</p>'."\n";
							echo '      <p><textarea name="'.esc_attr(__NAMESPACE__).'[save_options][exclude_agents]" rows="5" spellcheck="false" class="monospace"></textarea></p>'."\n";
							echo '      <p class="info">'.__('<strong>Tip:</strong> if you wanted to exclude iPhones put this line into the field above: <code>iPhone;*AppleWebKit</code>. Or, you could also just put in a small fragment, like: <code>iphone</code>; and that would exclude any user-agent containing that word fragment. Note, this is just an example. With a default installation of Quick Cache, there is no compelling reason to exclude iOS devices (or any mobile device for that matter).', plugin()->text_domain).'</p>'."\n";
							echo '      <p class="info">'.__('<strong>Note:</strong> please remember that your entries here should be formatted as a line-delimited list; e.g. one exclusion pattern per line.', plugin()->text_domain).'</p>'."\n";
							echo '   </div>'."\n";

							echo '</div>'."\n";
						}
					if(plugin()->is_pro_preview())
						{
							echo '<div class="plugin-menu-page-panel pro-preview">'."\n";

							echo '   <div class="plugin-menu-page-panel-heading">'."\n";
							echo '      <i class="fa fa-gears"></i> '.__('Auto-Cache Engine', plugin()->text_domain)."\n";
							echo '   </div>'."\n";

							echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
							echo '      <i class="fa fa-question-circle fa-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
							echo '      <h3>'.__('Enable the Auto-Cache Engine?', plugin()->text_domain).'</h3>'."\n";
							echo '      <p>'.__('After using Quick Cache for awhile (or any other page caching plugin, for that matter); it becomes obvious that at some point (based on your configured Expiration Time) Quick Cache has to refresh itself. It does this by ditching its cached version of a page, reloading the database-driven content, and then recreating the cache with the latest data. This is a never ending regeneration cycle that is based entirely on your configured Expiration Time.', plugin()->text_domain).'</p>'."\n";
							echo '      <p>'.__('Understanding this, you can see that 99% of your visitors are going to receive a lightning fast response from your server. However, there will always be around 1% of your visitors that land on a page for the very first time (before it\'s been cached), or land on a page that needs to have its cache regenerated, because the existing cache has become outdated. We refer to this as a <em>First-Come Slow-Load Issue</em>. Not a huge problem, but if you\'re optimizing your site for every once of speed possible, the Auto-Cache Engine can help with this. The Auto-Cache Engine has been designed to combat this issue by taking on the responsibility of being that first visitor to a page that has not yet been cached, or has an expired cache. The Auto-Cache Engine is powered, in part, by <a href="http://codex.wordpress.org/Category:WP-Cron_Functions" target="_blank">WP-Cron</a> (already built into WordPress). The Auto-Cache Engine runs at 15-minute intervals via WP-Cron. It also uses the <a href="http://core.trac.wordpress.org/browser/trunk/wp-includes/http.php" target="_blank">WP_Http</a> class, which is also built into WordPress already.', plugin()->text_domain).'</p>'."\n";
							echo '      <p>'.__('The Auto-Cache Engine obtains its list of URLs to auto-cache, from two different sources. It can read an <a href="http://wordpress.org/extend/plugins/google-sitemap-generator/" target="_blank">XML Sitemap</a> and/or a list of specific URLs that you supply. If you supply both sources, it will use both sources collectively. The Auto-Cache Engine takes ALL of your other configuration options into consideration too, including your Expiration Time, as well as any cache exclusion rules.', plugin()->text_domain).'</p>'."\n";
							echo '      <p><select name="'.esc_attr(__NAMESPACE__).'[save_options][auto_cache_enable]">'."\n";
							echo '            <option value="0" selected="selected">'.__('No, leave the Auto-Cache Engine disabled please.', plugin()->text_domain).'</option>'."\n";
							echo '            <option value="1">'.__('Yes, I want the Auto-Cache Engine to keep pages cached automatically.', plugin()->text_domain).'</option>'."\n";
							echo '         </select></p>'."\n";
							echo '      <hr />'."\n";
							echo '      <div class="plugin-menu-page-panel-if-enabled">'."\n";
							echo '         <h3>'.__('XML Sitemap URL (or an XML Sitemap Index)', plugin()->text_domain).'</h3>'."\n";
							echo '         <table style="width:100%;"><tr><td style="width:1px; font-weight:bold; white-space:nowrap;">'.esc_html(site_url('/')).'</td><td><input type="text" name="'.esc_attr(__NAMESPACE__).'[save_options][auto_cache_sitemap_url]" value="" /></td></tr></table>'."\n";
							echo '         <h3>'.__('A List of URLs to Auto-Cache (One Per Line)', plugin()->text_domain).'</h3>'."\n";
							echo '         <p><textarea name="'.esc_attr(__NAMESPACE__).'[save_options][auto_cache_other_urls]" rows="5" spellcheck="false" class="monospace"></textarea></p>'."\n";
							echo '         <hr />'."\n";
							echo '         <h3>'.__('Auto-Cache User-Agent String', plugin()->text_domain).'</h3>'."\n";
							echo '         <table style="width:100%;"><tr><td><input type="text" name="'.esc_attr(__NAMESPACE__).'[save_options][auto_cache_user_agent]" value="" /></td><td style="width:1px; font-weight:bold; white-space:nowrap;">; '.esc_html(__NAMESPACE__.' '.plugin()->version).'</td></tr></table>'."\n";
							echo '         <p class="info" style="display:block;">'.__('This is how the Auto-Cache Engine identifies itself when connecting to URLs. See <a href="http://en.wikipedia.org/wiki/User_agent" target="_blank">User Agent</a> in the Wikipedia.', plugin()->text_domain).'</p>'."\n";
							echo '      </div>'."\n";
							echo '   </div>'."\n";

							echo '</div>'."\n";
						}
					if(plugin()->is_pro_preview())
						{
							echo '<div class="plugin-menu-page-panel pro-preview">'."\n";

							echo '   <div class="plugin-menu-page-panel-heading">'."\n";
							echo '      <i class="fa fa-gears"></i> '.__('HTML Compression (Experimental)', plugin()->text_domain)."\n";
							echo '   </div>'."\n";

							echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
							echo '      <i class="fa fa-question-circle fa-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
							echo '      <h3>'.__('Enable WebSharks™ HTML Compression?', plugin()->text_domain).'</h3>'."\n";
							echo '      <p class="notice" style="display:block;">'.__('This is an experimental feature, however it offers a potentially HUGE speed boost. You can <a href="https://github.com/WebSharks/HTML-Compressor" target="_blank">learn more here</a>. Please use with caution.', plugin()->text_domain).'</p>'."\n";
							echo '      <p><select name="'.esc_attr(__NAMESPACE__).'[save_options][htmlc_enable]">'."\n";
							echo '            <option value="0">'.__('No, do NOT compress HTML/CSS/JS code at runtime.', plugin()->text_domain).'</option>'."\n";
							echo '            <option value="1" selected="selected">'.__('Yes, I want to compress HTML/CSS/JS for blazing fast speeds.', plugin()->text_domain).'</option>'."\n";
							echo '         </select></p>'."\n";
							echo '      <p class="info" style="display:block;">'.__('<strong>Note:</strong> This is experimental. Please <a href="https://github.com/WebSharks/HTML-Compressor/issues" target="_blank">report issues here</a>.', plugin()->text_domain).'</p>'."\n";
							echo '      <hr />'."\n";
							echo '      <div class="plugin-menu-page-panel-if-enabled">'."\n";
							echo '         <h3>'.__('HTML Compression Options', plugin()->text_domain).'</h3>'."\n";
							echo '         <p>'.__('You can <a href="https://github.com/WebSharks/HTML-Compressor" target="_blank">learn more about all of these options here</a>.', plugin()->text_domain).'</p>'."\n";
							echo '         <p><select name="'.esc_attr(__NAMESPACE__).'[save_options][htmlc_compress_combine_head_body_css]" autocomplete="off">'."\n";
							echo '               <option value="1">'.__('Yes, combine CSS from &lt;head&gt; and &lt;body&gt; into fewer files.', plugin()->text_domain).'</option>'."\n";
							echo '               <option value="0">'.__('No, do not combine CSS from &lt;head&gt; and &lt;body&gt; into fewer files.', plugin()->text_domain).'</option>'."\n";
							echo '            </select></p>'."\n";
							echo '         <p><select name="'.esc_attr(__NAMESPACE__).'[save_options][htmlc_compress_css_code]" autocomplete="off">'."\n";
							echo '               <option value="1">'.__('Yes, compress the code in any unified CSS files.', plugin()->text_domain).'</option>'."\n";
							echo '               <option value="0">'.__('No, do not compress the code in any unified CSS files.', plugin()->text_domain).'</option>'."\n";
							echo '            </select></p>'."\n";
							echo '         <p><select name="'.esc_attr(__NAMESPACE__).'[save_options][htmlc_compress_combine_head_js]" autocomplete="off">'."\n";
							echo '               <option value="1">'.__('Yes, combine JS from &lt;head&gt; into fewer files.', plugin()->text_domain).'</option>'."\n";
							echo '               <option value="0">'.__('No, do not combine JS from &lt;head&gt; into fewer files.', plugin()->text_domain).'</option>'."\n";
							echo '            </select></p>'."\n";
							echo '         <p><select name="'.esc_attr(__NAMESPACE__).'[save_options][htmlc_compress_combine_footer_js]" autocomplete="off">'."\n";
							echo '               <option value="1">'.__('Yes, combine JS footer scripts into fewer files.', plugin()->text_domain).'</option>'."\n";
							echo '               <option value="0">'.__('No, do not combine JS footer scripts into fewer files.', plugin()->text_domain).'</option>'."\n";
							echo '            </select></p>'."\n";
							echo '         <p><select name="'.esc_attr(__NAMESPACE__).'[save_options][htmlc_compress_combine_remote_css_js]" autocomplete="off">'."\n";
							echo '               <option value="1">'.__('Yes, combine CSS/JS from remote resources too.', plugin()->text_domain).'</option>'."\n";
							echo '               <option value="0">'.__('No, do not combine CSS/JS from remote resources.', plugin()->text_domain).'</option>'."\n";
							echo '            </select></p>'."\n";
							echo '         <p><select name="'.esc_attr(__NAMESPACE__).'[save_options][htmlc_compress_js_code]" autocomplete="off">'."\n";
							echo '               <option value="1">'.__('Yes, compress the code in any unified JS files.', plugin()->text_domain).'</option>'."\n";
							echo '               <option value="0">'.__('No, do not compress the code in any unified JS files.', plugin()->text_domain).'</option>'."\n";
							echo '            </select></p>'."\n";
							echo '         <p><select name="'.esc_attr(__NAMESPACE__).'[save_options][htmlc_compress_inline_js_code]" autocomplete="off">'."\n";
							echo '               <option value="1">'.__('Yes, compress inline JavaScript snippets.', plugin()->text_domain).'</option>'."\n";
							echo '               <option value="0">'.__('No, do not compress inline JavaScript snippets.', plugin()->text_domain).'</option>'."\n";
							echo '            </select></p>'."\n";
							echo '         <p><select name="'.esc_attr(__NAMESPACE__).'[save_options][htmlc_compress_html_code]" autocomplete="off">'."\n";
							echo '               <option value="1">'.__('Yes, compress (remove extra whitespace) in the final HTML code too.', plugin()->text_domain).'</option>'."\n";
							echo '               <option value="0">'.__('No, do not compress the final HTML code.', plugin()->text_domain).'</option>'."\n";
							echo '            </select></p>'."\n";
							echo '         <hr />'."\n";
							echo '         <h3>'.__('CSS Exclusion Patterns?', plugin()->text_domain).'</h3>'."\n";
							echo '         <p>'.__('Sometimes there are special cases when a particular CSS file should NOT be consolidated or compressed in any way. This is where you will enter those if you need to (one per line). Searches are performed against the <code>&lt;link href=&quot;&quot;&gt;</code> value, and also against the contents of any inline <code>&lt;style&gt;</code> tags (case insensitive). A wildcard <code>*</code> character can also be used when necessary; e.g. <code>xy*-framework</code>; (where <code>*</code> = anything, 0 or more characters in length).', plugin()->text_domain).'</p>'."\n";
							echo '         <p><textarea name="'.esc_attr(__NAMESPACE__).'[save_options][htmlc_css_exclusions]" rows="5" spellcheck="false" class="monospace"></textarea></p>'."\n";
							echo '         <p class="info" style="display:block;">'.__('<strong>Note:</strong> please remember that your entries here should be formatted as a line-delimited list; e.g. one exclusion pattern per line.', plugin()->text_domain).'</p>'."\n";
							echo '         <h3>'.__('JavaScript Exclusion Patterns?', plugin()->text_domain).'</h3>'."\n";
							echo '         <p>'.__('Sometimes there are special cases when a particular JS file should NOT be consolidated or compressed in any way. This is where you will enter those if you need to (one per line). Searches are performed against the <code>&lt;script src=&quot;&quot;&gt;</code> value, and also against the contents of any inline <code>&lt;script&gt;</code> tags (case insensitive). A wildcard <code>*</code> character can also be used when necessary; e.g. <code>xy*-framework</code>; (where <code>*</code> = anything, 0 or more characters in length).', plugin()->text_domain).'</p>'."\n";
							echo '         <p><textarea name="'.esc_attr(__NAMESPACE__).'[save_options][htmlc_js_exclusions]" rows="5" spellcheck="false" class="monospace">.php?</textarea></p>'."\n";
							echo '         <p class="info" style="display:block;">'.__('<strong>Note:</strong> please remember that your entries here should be formatted as a line-delimited list; e.g. one exclusion pattern per line.', plugin()->text_domain).'</p>'."\n";
							echo '         <hr />'."\n";
							echo '         <h3>'.__('Public HTML Compression Cache Directory', plugin()->text_domain).'</h3>'."\n";
							echo '         <table style="width:100%;"><tr><td style="width:1px; font-weight:bold; white-space:nowrap;">'.esc_html(ABSPATH).'</td><td><input type="text" name="'.esc_attr(__NAMESPACE__).'[save_options][htmlc_cache_dir_public]" value="wp-content/htmlc/cache/public" /></td><td style="width:1px; font-weight:bold; white-space:nowrap;">/</td></tr></table>'."\n";
							echo '         <p class="notice" style="display:block;">'.__('<strong>Important:</strong> this should <strong>NOT</strong> be the same as your primary Quick Cache directory. These cache files should be given their own <strong>separate</strong> directory on the server. This is where the HTML Compressor will store any unified CSS/JS files that are still fresh.', plugin()->text_domain).'</p>'."\n";
							echo '         <h3>'.__('Private HTML Compression Cache Directory', plugin()->text_domain).'</h3>'."\n";
							echo '         <table style="width:100%;"><tr><td style="width:1px; font-weight:bold; white-space:nowrap;">'.esc_html(ABSPATH).'</td><td><input type="text" name="'.esc_attr(__NAMESPACE__).'[save_options][htmlc_cache_dir_private]" value="wp-content/htmlc/cache/private" /></td><td style="width:1px; font-weight:bold; white-space:nowrap;">/</td></tr></table>'."\n";
							echo '         <p class="notice" style="display:block;">'.__('<strong>Important:</strong> this should <strong>NOT</strong> be the same as your primary Quick Cache directory. These cache files should be given their own <strong>separate</strong> directory on the server. This is where the HTML Compressor will store any systematic cache files (for private internal use only) that are still fresh.', plugin()->text_domain).'</p>'."\n";
							echo '         <p><input type="text" name="'.esc_attr(__NAMESPACE__).'[save_options][htmlc_cache_expiration_time]" value="14 days" /></p>'."\n";
							echo '         <p class="info" style="display:block;">'.__('<strong>Tip:</strong> the value that you specify here MUST be compatible with PHP\'s <a href="http://php.net/manual/en/function.strtotime.php" target="_blank" style="text-decoration:none;"><code>strtotime()</code></a> function. Examples: <code>2 hours</code>, <code>7 days</code>, <code>6 months</code>, <code>1 year</code>.', plugin()->text_domain).'</p>'."\n";
							echo '         <p>'.__('<strong>Note:</strong> This does NOT impact the overall cache expiration time that you configure with Quick Cache. It only impacts the sub-routines provided by the HTML Compressor. In fact, this expiration time is mostly irrelevant. The HTML Compressor uses an internal checksum, and it also checks <code>filemtime()</code> before using an existing cache file. The HTML Compressor class also handles the automatic cleanup of your cache directories to keep it from growing too large over time. Therefore, unless you have VERY little disk space there is no reason to set this to a lower value (even if your site changes dynamically quite often). If anything, you might like to increase this value which could help to further reduce server load. You can <a href="https://github.com/WebSharks/HTML-Compressor" target="_blank">learn more here</a>. We recommend setting this value to at least double that of your overall Quick Cache expiration time.', plugin()->text_domain).'</p>'."\n";
							echo '      </div>'."\n";
							echo '   </div>'."\n";

							echo '</div>'."\n";
						}
					echo '<div class="plugin-menu-page-panel">'."\n";

					echo '   <div class="plugin-menu-page-panel-heading">'."\n";
					echo '      <i class="fa fa-gears"></i> '.__('GZIP Compression', plugin()->text_domain)."\n";
					echo '   </div>'."\n";

					echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
					echo '      <img src="'.esc_attr(plugin()->url('/client-s/images/gzip.png')).'" class="screenshot" />'."\n";
					echo '      <h3>'.__('<a href="https://developers.google.com/speed/articles/gzip" target="_blank">GZIP Compression</a> (Optional; Highly Recommended)', plugin()->text_domain).'</h3>'."\n";
					echo '      <p>'.__('You don\'t have to use an <code>.htaccess</code> file to enjoy the performance enhancements provided by this plugin; caching is handled automatically by WordPress/PHP alone. That being said, if you want to take advantage of the additional speed enhancements associated w/ GZIP compression (and we do recommend this), then you WILL need an <code>.htaccess</code> file to accomplish that part.', plugin()->text_domain).'</p>'."\n";
					echo '      <p>'.__('Quick Cache fully supports GZIP compression on its output. However, it does not handle GZIP compression directly. We purposely left GZIP compression out of this plugin, because GZIP compression is something that should really be enabled at the Apache level or inside your <code>php.ini</code> file. GZIP compression can be used for things like JavaScript and CSS files as well, so why bother turning it on for only WordPress-generated pages when you can enable GZIP at the server level and cover all the bases!', plugin()->text_domain).'</p>'."\n";
					echo '      <p>'.__('If you want to enable GZIP, create an <code>.htaccess</code> file in your WordPress® installation directory, and put the following few lines in it. Alternatively, if you already have an <code>.htaccess</code> file, just add these lines to it, and that is all there is to it. GZIP is now enabled in the recommended way! See also: <a href="https://developers.google.com/speed/articles/gzip" target="_blank"><i class="fa fa-youtube-play"></i> video about GZIP Compression</a>.', plugin()->text_domain).'</p>'."\n";
					echo '      <pre class="code"><code>'.esc_html(file_get_contents(dirname(__FILE__).'/gzip-htaccess.tpl.txt')).'</code></pre>'."\n";
					echo '      <hr />'."\n";
					echo '      <p class="info" style="display:block;"><strong>Or</strong>, if your server is missing <code>mod_deflate</code>/<code>mod_filter</code>; open your <strong>php.ini</strong> file and add this line: <a href="http://php.net/manual/en/zlib.configuration.php" target="_blank" style="text-decoration:none;"><code>zlib.output_compression = on</code></a></p>'."\n";
					echo '   </div>'."\n";

					echo '</div>'."\n";

					if(plugin()->is_pro_preview())
						{
							echo '<div class="plugin-menu-page-panel pro-preview">'."\n";

							echo '   <div class="plugin-menu-page-panel-heading">'."\n";
							echo '      <i class="fa fa-gears"></i> '.__('MD5 Version Salt', plugin()->text_domain)."\n";
							echo '   </div>'."\n";

							echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
							echo '      <img src="'.esc_attr(plugin()->url('/client-s/images/salt.png')).'" class="screenshot" />'."\n";
							echo '      <h3>'.__('<i class="fa fa-flask"></i> <span style="display:inline-block; padding:5px; border-radius:3px; background:#FFFFFF; color:#354913;"><span style="font-weight:bold; font-size:80%;">GEEK ALERT</span></span> This is for VERY advanced users only...', plugin()->text_domain).'</h3>'."\n";
							echo '      <p>'.__('Quick Cache stores its cache files using an md5() hash of the PROTOCOL://HOST/URI that it\'s caching. If you want to build these hash strings out of something more than this, you can add a Salt to the mix. So, instead of just <code>md5($_SERVER[\'HTTPS\'].$_SERVER[\'HTTP_HOST\'].$_SERVER[\'REQUEST_URI\'])</code>, you might prefer something like <code>md5((string)@$_COOKIE[\'my_cookie\'].$_SERVER[\'HTTPS\'].$_SERVER[\'HTTP_HOST\'].$_SERVER[\'REQUEST_URI\'])</code>; where the Version Salt is <code>(string)@$_COOKIE[\'my_cookie\']</code>.', plugin()->text_domain).'</p>'."\n";
							echo '      <p>'.__('This would create multiple cached versions of each page; based on the value of <code>$_COOKIE[\'my_cookie\']</code>. If <code>$_COOKIE[\'my_cookie\']</code> is empty, then just <code>$_SERVER[\'HTTPS\'].$_SERVER[\'HTTP_HOST\'].$_SERVER[\'REQUEST_URI\']</code> are used. In this way, a Version Salt gives you the ability to dynamically create multiple variations of the cache, and those dynamic variations will be served on subsequent visits; e.g. if a visitor has this cookie (of a certain value) they will see pages which were cached with this version (i.e. w/ this Version Salt).', plugin()->text_domain).'</p>'."\n";
							echo '      <p>'.__('A Version Salt can be a single variable like <code>$_COOKIE[\'my_cookie\']</code>, or it can be a combination of multiple variables, like <code>$_COOKIE[\'my_cookie\'].$_COOKIE[\'my_other_cookie\']</code>. When using multiple variables, please separate them with a dot, as shown in the example. Experts could even use PHP ternary expressions that evaluate into something. For example: <code>((preg_match(\'/iPhone/i\', $_SERVER[\'HTTP_USER_AGENT\'])) ? \'iPhones\' : \'\')</code>. This would force a separate version of the cache to be created for iPhones.', plugin()->text_domain).'</p>'."\n";
							echo '      <hr />'."\n";
							echo '      <h3>'.__('Create An MD5 Version Salt For Quick Cache? &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-size:90%; opacity:0.5;">150% OPTIONAL</span>', plugin()->text_domain).'</h3>'."\n";
							echo '      <table style="width:100%;"><tr><td style="width:1px; font-weight:bold; white-space:nowrap;">md5(</td><td><input type="text" name="'.esc_attr(__NAMESPACE__).'[save_options][version_salt]" value="" class="monospace" /></td><td style="width:1px; font-weight:bold; white-space:nowrap;">.$_SERVER[\'HTTPS\'].$_SERVER[\'HTTP_HOST\'].$_SERVER[\'REQUEST_URI\'])</td></tr></table>'."\n";
							echo '      <p class="info" style="display:block;">'.__('<a href="http://php.net/manual/en/language.variables.superglobals.php" target="_blank">Super Globals</a> work here; <a href="http://codex.wordpress.org/Editing_wp-config.php#table_prefix" target="_blank"><code>$GLOBALS[\'table_prefix\']</code></a> is a popular one. Or, perhaps a PHP Constant defined in <code>/wp-config.php</code>; such as <code>WPLANG</code> or <code>DB_HOST</code>.', plugin()->text_domain).'</p>'."\n";
							echo '      <p class="notice" style="display:block;">'.__('<strong>Important:</strong> your Version Salt is scanned for PHP syntax errors via <a href="http://phpcodechecker.com/" target="_blank"><code>phpCodeChecker.com</code></a>. If errors are found, you\'ll receive a notice in the Dashboard.', plugin()->text_domain).'</p>'."\n";
							echo '   </div>'."\n";

							echo '</div>'."\n";
						}
					echo '<div class="plugin-menu-page-panel">'."\n";

					echo '   <div class="plugin-menu-page-panel-heading">'."\n";
					echo '      <i class="fa fa-gears"></i> '.__('Theme/Plugin Developers', plugin()->text_domain)."\n";
					echo '   </div>'."\n";

					echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
					echo '      <i class="fa fa-puzzle-piece fa-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
					echo '      <h3>'.__('Developing a Theme or Plugin for WordPress?', plugin()->text_domain).'</h3>'."\n";
					echo '      <p>'.__('<strong>Tip:</strong> Quick Cache can be disabled temporarily. If you\'re a theme/plugin developer, you can set a flag within your PHP code to disable the cache engine at runtime. Perhaps on a specific page, or in a specific scenario. In your PHP script, set: <code>$_SERVER[\'QUICK_CACHE_ALLOWED\'] = FALSE;</code> or <code>define(\'QUICK_CACHE_ALLOWED\', FALSE)</code>. Quick Cache is also compatible with: <code>define(\'DONOTCACHEPAGE\', TRUE)</code>. It does\'t matter where or when you define one of these, because Quick Cache is the last thing to run before script execution ends.', plugin()->text_domain).'</p>'."\n";
					echo '      <hr />'."\n";
					echo '      <h3>'.__('Writing "Advanced Cache" Plugins Specifically for Quick Cache', plugin()->text_domain).'</h3>'."\n";
					echo '      <p>'.__('Theme/plugin developers can take advantage of the Quick Cache plugin architecture by creating PHP files inside this special directory: <code>/wp-content/ac-plugins/</code>. There is an <a href="https://github.com/WebSharks/Quick-Cache/blob/000000-dev/quick-cache/includes/ac-plugin.example.php" target="_blank">example plugin file @ GitHub</a> (please review it carefully and ask questions). If you develop a plugin for Quick Cache, please share it with the community by publishing it in the plugins respository at WordPress.org.', plugin()->text_domain).'</p>'."\n";
					echo '      <p class="info">'.__('<strong>Why does Quick Cache have it\'s own plugin architecture?</strong> WordPress loads the <code>advanced-cache.php</code> drop-in file (for caching purposes) very early-on; before any other plugins or a theme. For this reason, Quick Cache implements it\'s own watered-down version of functions like <code>add_action()</code>, <code>do_action()</code>, <code>add_filter()</code>, <code>apply_filters()</code>.', plugin()->text_domain).'</p>'."\n";
					echo '   </div>'."\n";

					echo '</div>'."\n";

					if(plugin()->is_pro_preview())
						{
							echo '<div class="plugin-menu-page-panel pro-preview">'."\n";

							echo '   <div class="plugin-menu-page-panel-heading">'."\n";
							echo '      <i class="fa fa-gears"></i> '.__('Import/Export Options', plugin()->text_domain)."\n";
							echo '   </div>'."\n";

							echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
							echo '      <i class="fa fa-arrow-circle-o-up fa-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
							echo '      <h3>'.__('Import Options from Another Quick Cache Installation?', plugin()->text_domain).'</h3>'."\n";
							echo '      <p>'.sprintf(__('Upload your <code>%1$s-options.json</code> file and click "Save All Changes" below. The options provided by your import file will override any that exist currently.', plugin()->text_domain), __NAMESPACE__).'</p>'."\n";
							echo '      <p><input type="file" name="'.esc_attr(__NAMESPACE__).'[import_options]" /></p>'."\n";
							echo '      <hr />'."\n";
							echo '      <h3>'.__('Export Existing Options from this Quick Cache Installation?', plugin()->text_domain).'</h3>'."\n";
							echo '      <button type="button" class="plugin-menu-page-export-options" style="float:right; margin: 0 0 0 25px;">'.
							     '         '.sprintf(__('%1$s-options.json', plugin()->text_domain), __NAMESPACE__).' <i class="fa fa-arrow-circle-o-down"></i></button>'."\n";
							echo '      <p>'.__('Download your existing options and import them all into another Quick Cache installation; saves time on future installs.', plugin()->text_domain).'</p>'."\n";
							echo '   </div>'."\n";

							echo '</div>'."\n";
						}
					echo '<div class="plugin-menu-page-save">'."\n";
					echo '   <input type="hidden" name="'.esc_attr(__NAMESPACE__).'[save_options][crons_setup]" value="'.esc_attr(plugin()->options['crons_setup']).'" autocomplete="off" />'."\n";
					echo '   <button type="submit">'.__('Save All Changes', plugin()->text_domain).' <i class="fa fa-save"></i></button>'."\n";
					echo '</div>'."\n";

					echo '</div>'."\n";
					echo '</form>';
				}
		}
	}