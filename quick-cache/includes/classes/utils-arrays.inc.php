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
if (!class_exists ("c_ws_plugin__qcache_utils_arrays"))
	{
		class c_ws_plugin__qcache_utils_arrays
			{
				public static function array_unique ($array = FALSE)
					{
						$array = (array)$array;
						/**/
						foreach ($array as &$value)
							$value = serialize ($value);
						/**/
						$array = array_unique ($array);
						/**/
						foreach ($array as &$value)
							$value = unserialize ($value);
						/**/
						return $array;
					}
			}
	}
?>