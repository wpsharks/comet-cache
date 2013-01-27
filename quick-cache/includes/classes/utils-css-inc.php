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
if (!class_exists ("c_ws_plugin__qcache_utils_css"))
	{
		class c_ws_plugin__qcache_utils_css
			{
				public static function compress_css ($css = FALSE)
					{
						$c6 = "/(\:#| #)([A-Z0-9]{6})/i";
						$css = preg_replace ("/\/\*(.*?)\*\//s", "", $css);
						$css = preg_replace ("/[\r\n\t]+/", "", $css);
						$css = preg_replace ("/ {2,}/", " ", $css);
						$css = preg_replace ("/ , | ,|, /", ",", $css);
						$css = preg_replace ("/ \> | \>|\> /", ">", $css);
						$css = preg_replace ("/\[ /", "[", $css);
						$css = preg_replace ("/ \]/", "]", $css);
						$css = preg_replace ("/ \!\= | \!\=|\!\= /", "!=", $css);
						$css = preg_replace ("/ \|\= | \|\=|\|\= /", "|=", $css);
						$css = preg_replace ("/ \^\= | \^\=|\^\= /", "^=", $css);
						$css = preg_replace ("/ \$\= | \$\=|\$\= /", "$=", $css);
						$css = preg_replace ("/ \*\= | \*\=|\*\= /", "*=", $css);
						$css = preg_replace ("/ ~\= | ~\=|~\= /", "~=", $css);
						$css = preg_replace ("/ \= | \=|\= /", "=", $css);
						$css = preg_replace ("/ \+ | \+|\+ /", "+", $css);
						$css = preg_replace ("/ ~ | ~|~ /", "~", $css);
						$css = preg_replace ("/ \{ | \{|\{ /", "{", $css);
						$css = preg_replace ("/ \} | \}|\} /", "}", $css);
						$css = preg_replace ("/ \: | \:|\: /", ":", $css);
						$css = preg_replace ("/ ; | ;|; /", ";", $css);
						$css = preg_replace ("/;\}/", "}", $css);
						/**/
						return preg_replace_callback ($c6, "c_ws_plugin__qcache_utils_css::_compress_css_c3", $css);
					}
				public static function _compress_css_c3 ($m = FALSE)
					{
						if ($m[2][0] === $m[2][1] && $m[2][2] === $m[2][3] && $m[2][4] === $m[2][5])
							return $m[1] . $m[2][0] . $m[2][2] . $m[2][4];
						return $m[0];
					}
			}
	}
?>