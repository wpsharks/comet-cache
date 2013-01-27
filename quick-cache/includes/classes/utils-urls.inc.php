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
/**/
if (!class_exists ("c_ws_plugin__qcache_utils_urls"))
	{
		class c_ws_plugin__qcache_utils_urls
			{
				public static function remote ($url = FALSE, $post_vars = FALSE, $args = FALSE, $return = FALSE)
					{
						if ($url && is_string ($url) /* We MUST have a valid full URL (string) before we do anything in this routine. */)
							{
								$args = (!is_array ($args)) ? array (): $args; /* Force array, and disable SSL verification. */
								$args["sslverify"] = (!isset ($args["sslverify"])) ? /* Off. */ false : $args["sslverify"];
								/**/
								if ((is_array ($post_vars) || is_string ($post_vars)) && !empty ($post_vars))
									$args = array_merge ($args, array ("method" => "POST", "body" => $post_vars));
								/**/
								$response = wp_remote_request ($url, $args); /* Process the remote request now. */
								/**/
								if (strcasecmp ((string)$return, "array") === 0 && !is_wp_error ($response) && is_array ($response))
									{
										$a = array ("code" => (int)wp_remote_retrieve_response_code ($response));
										$a = array_merge ($a, array ("message" => wp_remote_retrieve_response_message ($response)));
										$a = array_merge ($a, array ("headers" => wp_remote_retrieve_headers ($response)));
										$a = array_merge ($a, array ("body" => wp_remote_retrieve_body ($response)));
										$a = array_merge ($a, array ("response" => $response));
										/**/
										return /* Return array w/ ``$response`` too. */ $a;
									}
								else if (!is_wp_error ($response) && is_array ($response) /* Return body only. */)
									return /* Return ``$response`` body only. */ wp_remote_retrieve_body ($response);
								/**/
								else /* Else this remote request has failed completely. Return false. */
									return false; /* Remote request failed, return false. */
							}
						else /* Else, return false. */
							return false;
					}
				/**/
				public static function parse_url ($url_uri = FALSE, $component = FALSE, $clean_path = TRUE)
					{
						$component = ($component === false || $component === -1) ? -1 : $component;
						/**/
						if (is_string ($url_uri) && /* And, there is a query string? */ strpos ($url_uri, "?") !== false)
							{
								list ($_, $query) = preg_split ("/\?/", $url_uri, 2); /* Split @ query string marker. */
								$query = /* See: <https://bugs.php.net/bug.php?id=38143>. */ str_replace ("://", urlencode ("://"), $query);
								$url_uri = /* Put it all back together again, after the above modifications. */ $_ . "?" . $query;
								unset /* A little housekeeping here. Unset these vars. */ ($_, $query);
							}
						$parse = /* Let PHP work its magic via ``parse_url()``. */ @parse_url ($url_uri, $component);
						/**/
						if ($clean_path && isset ($parse["path"]) && is_string ($parse["path"]) && !empty ($parse["path"]))
							$parse["path"] = /* Clean up the path now. */ preg_replace ("/\/+/", "/", $parse["path"]);
						/**/
						return ($component !== -1) ? /* Force a string return value? */ (string)$parse : $parse;
					}
				/**/
				public static function parse_uri ($url_uri = FALSE)
					{
						if (is_string ($url_uri) && is_array ($parse = c_ws_plugin__qcache_utils_urls::parse_url ($url_uri)))
							{
								$parse["path"] = (!empty ($parse["path"])) ? ((strpos ($parse["path"], "/") === 0) ? $parse["path"] : "/" . $parse["path"]) : "/";
								/**/
								return (!empty ($parse["query"])) ? $parse["path"] . "?" . $parse["query"] : $parse["path"];
							}
						else /* Force a string return value here. */
							return /* Empty string. */ "";
					}
			}
	}
?>