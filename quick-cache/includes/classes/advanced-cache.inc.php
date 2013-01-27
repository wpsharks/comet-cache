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
	exit ("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__qcache_advanced_cache"))
	{
		class c_ws_plugin__qcache_advanced_cache
			{
				/*
				Build/re-build, and add the advanced-cache.php file.
				*/
				public static function add_advanced ()
					{
						do_action ("ws_plugin__qcache_before_add_advanced", get_defined_vars ());
						/**/
						if (!c_ws_plugin__qcache_advanced_cache::delete_advanced ()) /* Do not proceed if unable to delete. */
							{
								return apply_filters ("ws_plugin__qcache_add_advanced", false, get_defined_vars ());
							}
						/**/
						else if (is_writable (WP_CONTENT_DIR) && (!file_exists (WP_CONTENT_DIR . "/advanced-cache.php") || is_writable (WP_CONTENT_DIR . "/advanced-cache.php")) && ($handler = file_get_contents (dirname (dirname (__FILE__)) . "/templates/handler.tpl.php")))
							{
								$handler = preg_replace ("/\"%%QUICK_CACHE_ENABLED%%\"/", $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["enabled"], $handler);
								$handler = preg_replace ("/\"%%QUICK_CACHE_ENABLE_DEBUGGING%%\"/", $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["enable_debugging"], $handler);
								$handler = preg_replace ("/\"%%QUICK_CACHE_DONT_CACHE_WHEN_LOGGED_IN%%\"/", $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["dont_cache_when_logged_in"], $handler);
								$handler = preg_replace ("/\"%%QUICK_CACHE_DONT_CACHE_QUERY_STRING_REQUESTS%%\"/", $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["dont_cache_query_string_requests"], $handler);
								$handler = preg_replace ("/\"%%QUICK_CACHE_EXPIRATION%%\"/", $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["expiration"], $handler);
								$handler = preg_replace ("/\"%%QUICK_CACHE_ALLOW_BROWSER_CACHE%%\"/", $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["allow_browser_cache"], $handler);
								/**/
								$handler = preg_replace ("/%%QUICK_CACHE_USE_FLOCK_OR_SEM%%/", $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["use_flock_or_sem"], $handler);
								/**/
								foreach (preg_split ("/[\r\n\t]+/", $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["dont_cache_these_uris"]) as $uri)
									if ($uri = trim ($uri))
										$uris .= "|" . preg_quote ($uri, "/");
								if ($uris = trim ($uris, " \r\n\t\0\x0B|"))
									$uris = "/" . c_ws_plugin__qcache_utils_strings::esc_dq ($uris) . "/";
								$handler = preg_replace ("/%%QUICK_CACHE_DONT_CACHE_THESE_URIS%%/", $uris, $handler);
								/**/
								foreach (preg_split ("/[\r\n\t]+/", $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["dont_cache_these_refs"]) as $ref)
									if ($ref = trim ($ref))
										$refs .= "|" . preg_quote ($ref, "/");
								if ($refs = trim ($refs, " \r\n\t\0\x0B|"))
									$refs = "/" . c_ws_plugin__qcache_utils_strings::esc_dq ($refs) . "/i";
								$handler = preg_replace ("/%%QUICK_CACHE_DONT_CACHE_THESE_REFS%%/", $refs, $handler);
								/**/
								foreach (preg_split ("/[\r\n\t]+/", $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["dont_cache_these_agents"]) as $agent)
									if ($agent = trim ($agent))
										$agents .= "|" . preg_quote ($agent, "/");
								if ($agents = trim ($agents, " \r\n\t\0\x0B|"))
									$agents = "/" . c_ws_plugin__qcache_utils_strings::esc_dq ($agents) . "/i";
								$handler = preg_replace ("/%%QUICK_CACHE_DONT_CACHE_THESE_AGENTS%%/", $agents, $handler);
								/**/
								if (strlen ($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["version_salt"]))
									if (file_put_contents (WP_CONTENT_DIR . "/qcache-salt-ok.php", '<?php error_reporting(0); $v = ' . $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["version_salt"] . '; echo "ok"; ?>')):
										$salt_ok = ( ($syntax_check = c_ws_plugin__qcache_utils_urls::remote (WP_CONTENT_URL . "/qcache-salt-ok.php")) === "ok") ? true : false;
										if (!$salt_ok) /* If we could not validate the syntax of their salt, we need to notify them that it will not be used. */
											c_ws_plugin__qcache_admin_notices::enqueue_admin_notice ("<strong>Quick Cache:</strong> Your MD5 Version Salt may contain syntax errors. Please check it and try again. Otherwise, if you are unable to correct the problem, your Salt will simply be ignored. Quick Cache will continue to function properly using its default setting.", "blog|network:ws-plugin--qcache-options", true);
										unlink (WP_CONTENT_DIR . "/qcache-salt-ok.php");
									endif;
								$handler = preg_replace ("/\"%%QUICK_CACHE_VERSION_SALT%%\"/", (($salt_ok) ? $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["version_salt"] : '""'), $handler);
								/**/
								eval ('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
								do_action ("ws_plugin__qcache_during_add_advanced", get_defined_vars ());
								unset ($__refs, $__v); /* Unset defined __refs, __v. */
								/**/
								file_put_contents (WP_CONTENT_DIR . "/advanced-cache.php", trim ($handler));
								/**/
								return apply_filters ("ws_plugin__qcache_add_advanced", true, get_defined_vars ());
							}
						else /* Defaults to false. Unable to create the advanced-cache.php file. */
							{
								return apply_filters ("ws_plugin__qcache_add_advanced", false, get_defined_vars ());
							}
					}
				/*
				Delete the advanced-cache.php file.
				*/
				public static function delete_advanced ()
					{
						do_action ("ws_plugin__qcache_before_delete_advanced", get_defined_vars ());
						/**/
						if (file_exists (WP_CONTENT_DIR . "/advanced-cache.php") && is_writable (WP_CONTENT_DIR . "/advanced-cache.php"))
							{
								unlink (WP_CONTENT_DIR . "/advanced-cache.php");
								/**/
								do_action ("ws_plugin__qcache_during_delete_advanced", get_defined_vars ());
								/**/
								return apply_filters ("ws_plugin__qcache_delete_advanced", true, get_defined_vars ());
							}
						else if (file_exists (WP_CONTENT_DIR . "/advanced-cache.php") && !is_writable (WP_CONTENT_DIR . "/advanced-cache.php"))
							{
								return apply_filters ("ws_plugin__qcache_delete_advanced", false, get_defined_vars ());
							}
						else /* Defaults to true for deletion. */
							{
								return apply_filters ("ws_plugin__qcache_delete_advanced", true, get_defined_vars ());
							}
					}
			}
	}
?>