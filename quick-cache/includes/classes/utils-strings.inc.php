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
if (!class_exists ("c_ws_plugin__qcache_utils_strings"))
	{
		class c_ws_plugin__qcache_utils_strings
			{
				public static function esc_dq ($string = FALSE, $times = FALSE)
					{
						$times = (is_numeric ($times) && $times >= 0) ? (int)$times : 1;
						/**/
						return str_replace ('"', str_repeat ("\\", $times) . '"', (string)$string);
					}
				/**/
				public static function esc_sq ($string = FALSE, $times = FALSE)
					{
						$times = (is_numeric ($times) && $times >= 0) ? (int)$times : 1;
						/**/
						return str_replace ("'", str_repeat ("\\", $times) . "'", (string)$string);
					}
				/**/
				public static function esc_js_sq ($string = FALSE, $times = FALSE)
					{
						$times = (is_numeric ($times) && $times >= 0) ? (int)$times : 1;
						/**/
						return str_replace ("'", str_repeat ("\\", $times) . "'", str_replace (array ("\r", "\n"), array ("", '\\n'), str_replace ("\'", "'", (string)$string)));
					}
				/**/
				public static function esc_ds ($string = FALSE, $times = FALSE)
					{
						$times = (is_numeric ($times) && $times >= 0) ? (int)$times : 1;
						/**/
						return str_replace ('$', str_repeat ("\\", $times) . '$', (string)$string);
					}
				/**/
				public static function trim ($value = FALSE, $chars = FALSE, $extra_chars = FALSE)
					{
						return c_ws_plugin__qcache_utils_strings::trim_deep ($value, $chars, $extra_chars);
					}
				/**/
				public static function trim_deep ($value = FALSE, $chars = FALSE, $extra_chars = FALSE)
					{
						$chars = /* List of chars to be trimmed by this routine. */ (is_string ($chars)) ? $chars : " \t\n\r\0\x0B";
						$chars = (is_string ($extra_chars) /* Adding additional chars? */) ? $chars . $extra_chars : $chars;
						/**/
						if (is_array ($value)) /* Handles all types of arrays.
						Note, we do NOT use ``array_map()`` here, because multiple args to ``array_map()`` causes a loss of string keys.
						For further details, see: <http://php.net/manual/en/function.array-map.php>. */
							{
								foreach ($value as &$r) /* Reference. */
									$r = c_ws_plugin__qcache_utils_strings::trim_deep ($r, $chars);
								return $value; /* Return modified array. */
							}
						return trim ((string)$value, $chars);
					}
			}
	}
?>