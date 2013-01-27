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
if (!class_exists ("c_ws_plugin__qcache_wp_cache"))
	{
		class c_ws_plugin__qcache_wp_cache
			{
				/*
				Add WP_CACHE to the config file(s).
				*/
				public static function add_wp_cache ()
					{
						do_action ("ws_plugin__qcache_before_add_wp_cache", get_defined_vars ());
						/**/
						if (!c_ws_plugin__qcache_wp_cache::delete_wp_cache ())
							{
								return apply_filters ("ws_plugin__qcache_add_wp_cache", false, get_defined_vars ());
							}
						else if (file_exists (ABSPATH . "wp-config.php") && is_writable (ABSPATH . "wp-config.php"))
							{
								$config = file_get_contents (ABSPATH . "wp-config.php");
								$config = preg_replace ("/^([\r\n\t ]*)(\<\?)(php)?/i", "<?php define('WP_CACHE', true);", $config);
								/**/
								eval ('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
								do_action ("ws_plugin__qcache_during_add_wp_cache", get_defined_vars ());
								unset ($__refs, $__v); /* Unset defined __refs, __v. */
								/**/
								file_put_contents (ABSPATH . "wp-config.php", $config);
								/**/
								return apply_filters ("ws_plugin__qcache_add_wp_cache", true, get_defined_vars ());
							}
						else if (file_exists (dirname (ABSPATH) . "/wp-config.php") && is_writable (dirname (ABSPATH) . "/wp-config.php"))
							{
								$config = file_get_contents (dirname (ABSPATH) . "/wp-config.php");
								$config = preg_replace ("/^([\r\n\t ]*)(\<\?)(php)?/i", "<?php define('WP_CACHE', true);", $config);
								/**/
								eval ('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
								do_action ("ws_plugin__qcache_during_add_wp_cache", get_defined_vars ());
								unset ($__refs, $__v); /* Unset defined __refs, __v. */
								/**/
								file_put_contents (dirname (ABSPATH) . "/wp-config.php", $config);
								/**/
								return apply_filters ("ws_plugin__qcache_add_wp_cache", true, get_defined_vars ());
							}
						else /* Defaults to false. Unable to add WP_CACHE to the wp-config.php file. */
							{
								return apply_filters ("ws_plugin__qcache_add_wp_cache", false, get_defined_vars ());
							}
					}
				/*
				Delete WP_CACHE from the config file(s).
				*/
				public static function delete_wp_cache ()
					{
						do_action ("ws_plugin__qcache_before_delete_wp_cache", get_defined_vars ());
						/**/
						if (file_exists (ABSPATH . "wp-config.php") && is_writable (ABSPATH . "wp-config.php"))
							{
								$config = file_get_contents (ABSPATH . "wp-config.php");
								$config = preg_replace ("/( ?)(define)( ?)(\()( ?)(['\"])WP_CACHE(['\"])( ?)(,)( ?)(0|1|true|false)( ?)(\))( ?);/i", "", $config);
								/**/
								eval ('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
								do_action ("ws_plugin__qcache_during_delete_wp_cache", get_defined_vars ());
								unset ($__refs, $__v); /* Unset defined __refs, __v. */
								/**/
								file_put_contents (ABSPATH . "wp-config.php", $config);
								/**/
								return apply_filters ("ws_plugin__qcache_delete_wp_cache", true, get_defined_vars ());
							}
						else if (file_exists (dirname (ABSPATH) . "/wp-config.php") && is_writable (dirname (ABSPATH) . "/wp-config.php"))
							{
								$config = file_get_contents (dirname (ABSPATH) . "/wp-config.php");
								$config = preg_replace ("/( ?)(define)( ?)(\()( ?)(['\"])WP_CACHE(['\"])( ?)(,)( ?)(0|1|true|false)( ?)(\))( ?);/i", "", $config);
								/**/
								eval ('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
								do_action ("ws_plugin__qcache_during_delete_wp_cache", get_defined_vars ());
								unset ($__refs, $__v); /* Unset defined __refs, __v. */
								/**/
								file_put_contents (dirname (ABSPATH) . "/wp-config.php", $config);
								/**/
								return apply_filters ("ws_plugin__qcache_delete_wp_cache", true, get_defined_vars ());
							}
						else if (file_exists (ABSPATH . "wp-config.php") && !is_writable (ABSPATH . "wp-config.php"))
							{
								return apply_filters ("ws_plugin__qcache_delete_wp_cache", false, get_defined_vars ());
							}
						else if (file_exists (dirname (ABSPATH) . "/wp-config.php") && !is_writable (dirname (ABSPATH) . "/wp-config.php"))
							{
								return apply_filters ("ws_plugin__qcache_delete_wp_cache", false, get_defined_vars ());
							}
						else /* Defaults to true for deletion. */
							{
								return apply_filters ("ws_plugin__qcache_delete_wp_cache", true, get_defined_vars ());
							}
					}
			}
	}
?>