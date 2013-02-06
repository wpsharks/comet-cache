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
if (!class_exists ("c_ws_plugin__qcache_readmes"))
	{
		class c_ws_plugin__qcache_readmes
			{
				/*
				Function that handles readme.txt parsing.
				*/
				public static function parse_readme ($specific_path = FALSE, $specific_section = FALSE, $_blank_targets = TRUE, $process_wp_syntax = FALSE)
					{
						if (!($path = $specific_path)) /* Was a specific path passed in? */
							{
								$path = dirname (dirname (dirname (__FILE__))) . "/readme.txt";
								$dev_path = dirname (dirname (dirname (__FILE__))) . "/readme-dev.txt";
								$path = (file_exists ($dev_path)) ? $dev_path : $path;
							}
						/**/
						eval('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
						do_action ("ws_plugin__qcache_before_parse_readme", get_defined_vars ());
						unset ($__refs, $__v); /* Unset defined __refs, __v. */
						/**/
						if (file_exists ($path)) /* Give hooks a chance. */
							{
								$o_pcre = @ini_get ("pcre.backtrack_limit");
								@ini_set ("pcre.backtrack_limit", 10000000);
								/**/
								if (!function_exists ("NC_Markdown"))
									include_once dirname (dirname (__FILE__)) . "/_xtnls/markdown/nc-markdown.inc.php";
								/**/
								$rm = file_get_contents ($path); /* Get readme.txt file contents. */
								$mb = function_exists ("mb_convert_encoding") ? @mb_convert_encoding ($rm, "UTF-8", $GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["mb_detection_order"]) : $rm;
								$rm = ($mb) ? $mb : $rm; /* Double check this, just in case conversion fails on an unpredicted charset. */
								/**/
								if ($specific_section) /* If we are ONLY parsing a specific section. This is a very useful way of pulling details out. */
									{
										preg_match ("/(\=\= )(" . preg_quote ($specific_section, "/") . ")( \=\=)(.+?)([\r\n]+\=\= |$)/si", $rm, $m);
										/**/
										if ($rm = trim ($m[4])) /* Looking for a specific section, indicated by `$specific_section`. */
											{
												$rm = preg_replace ("/(\=\=\=)( )(.+?)( )(\=\=\=)/", "<h4 id=\"rm-specs\">Specifications</h4>", $rm);
												$rm = preg_replace ("/(\=\=)( )(Installation)( )(\=\=)/", "<h4 id=\"rm-installation\">$3</h4>", $rm);
												$rm = preg_replace ("/(\=\=)( )(Description)( )(\=\=)/", "<h4 id=\"rm-description\">$3</h4>", $rm);
												$rm = preg_replace ("/(\=\=)( )(Screenshots)( )(\=\=)/", "<h4 id=\"rm-screenshots\">$3</h4>", $rm);
												$rm = preg_replace ("/(\=\=)( )(Frequently Asked Questions)( )(\=\=)/", "<h4 id=\"rm-faqs\">$3</h4>", $rm);
												$rm = preg_replace ("/(\=\=)( )(Changelog)( )(\=\=)/", "<h4 id=\"rm-changelog\">$3</h4>", $rm);
												$rm = preg_replace ("/(\=\=)( )(.+?)( )(\=\=)/", "<h4>$3</h4>", $rm);
												$rm = preg_replace ("/(\=)( )(.+?)( )(\=)/", "<h6>$3</h6>", $rm);
												/**/
												$y1 = "/\[youtube http\:\/\/www\.youtube\.com\/view_play_list\?p\=(.+?)[\s\/]*?\]/i";
												$y2 = "/\[youtube http\:\/\/www\.youtube\.com\/watch\?v\=(.+?)[\s\/]*?\]/i";
												/**/
												$rm = preg_replace ($y1, '<embed type="application/x-shockwave-flash" src="//www.youtube.com/p/$1?version=3&hd=1&fs=1&rel=0" style="width:320px; height:210px; float:right; margin:0 0 15px 15px;" allowscriptaccess="always" allowfullscreen="true"></embed>', $rm);
												$rm = preg_replace ($y2, '<embed type="application/x-shockwave-flash" src="//www.youtube.com/v/$1?version=3&hd=1&fs=1&rel=0" style="width:320px; height:210px; float:right; margin:0 0 15px 15px;" allowscriptaccess="always" allowfullscreen="true"></embed>', $rm);
												/**/
												$rm = NC_Markdown ($rm); /* Parse out the Markdown syntax. */
												/**/
												$r1 = "/(\<a)( href)/i"; /* Modify all links. Assume a nofollow relationship. */
												/**/
												if ($_blank_targets) /* Modify all links. Always nofollow. ( with _blank targets ? ). */
													$rm = preg_replace ($r1, "$1" . ' target="_blank" rel="nofollow external"' . "$2", $rm);
												else /* Otherwise, we don't need to set _blank targets. So external is removed also. */
													$rm = preg_replace ($r1, "$1" . ' rel="nofollow"' . "$2", $rm);
												/**/
												if ($process_wp_syntax) /* If we're processing <pre><code> tags for WP-Syntax. */
													if (function_exists ("wp_syntax_before_filter") && function_exists ("wp_syntax_before_filter"))
														{
															$rm = preg_replace ("/\<pre\>\<code\>/i", '<pre lang="php" escaped="true">', $rm);
															$rm = preg_replace ("/\<\/code\>\<\/pre\>/i", '</pre>', $rm);
															$rm = wp_syntax_after_filter (wp_syntax_before_filter ($rm));
														}
											}
										/**/
										@ini_set ("pcre.backtrack_limit", $o_pcre);
										/**/
										$readme = '<div class="readme">' . "\n";
										$readme .= $rm . "\n"; /* Content. */
										$readme .= '</div>' . "\n";
										/**/
										return apply_filters ("ws_plugin__qcache_parse_readme", $readme, get_defined_vars ());
									}
								else /* Otherwise, we're going for the entire readme file. Here we have lots of work to do. */
									{
										$rm = preg_replace ("/(\=\=\=)( )(.+?)( )(\=\=\=)/", "<h2 id=\"rm-specs\">Specifications</h2>", $rm);
										$rm = preg_replace ("/(\=\=)( )(Installation)( )(\=\=)/", "<h2 id=\"rm-installation\">$3</h2>", $rm);
										$rm = preg_replace ("/(\=\=)( )(Description)( )(\=\=)/", "<h2 id=\"rm-description\">$3</h2>", $rm);
										$rm = preg_replace ("/(\=\=)( )(Screenshots)( )(\=\=)/", "<h2 id=\"rm-screenshots\">$3</h2>", $rm);
										$rm = preg_replace ("/(\=\=)( )(Frequently Asked Questions)( )(\=\=)/", "<h2 id=\"rm-faqs\">$3</h2>", $rm);
										$rm = preg_replace ("/(\=\=)( )(Changelog)( )(\=\=)/", "<h2 id=\"rm-changelog\">$3</h2>", $rm);
										$rm = preg_replace ("/(\=\=)( )(.+?)( )(\=\=)/", "<h2>$3</h2>", $rm);
										$rm = preg_replace ("/(\=)( )(.+?)( )(\=)/", "<h3>$3</h3>", $rm);
										/**/
										$y1 = "/\[youtube http\:\/\/www\.youtube\.com\/view_play_list\?p\=(.+?)[\s\/]*?\]/i";
										$y2 = "/\[youtube http\:\/\/www\.youtube\.com\/watch\?v\=(.+?)[\s\/]*?\]/i";
										/**/
										$rm = preg_replace ($y1, '<embed type="application/x-shockwave-flash" src="//www.youtube.com/p/$1?version=3&hd=1&fs=1&rel=0" style="width:320px; height:210px; float:right; margin:0 0 15px 15px;" allowscriptaccess="always" allowfullscreen="true"></embed>', $rm);
										$rm = preg_replace ($y2, '<embed type="application/x-shockwave-flash" src="//www.youtube.com/v/$1?version=3&hd=1&fs=1&rel=0" style="width:320px; height:210px; float:right; margin:0 0 15px 15px;" allowscriptaccess="always" allowfullscreen="true"></embed>', $rm);
										/**/
										$rm = NC_Markdown ($rm); /* Parse out the Markdown syntax. */
										/**/
										$r1 = "/(\<h2(.*?)\>)(.+?)(\<\/h2\>)(.+?)(\<h2(.*?)\>|$)/si";
										$r2 = "/(\<\/div\>)(\<h2(.*?)\>)(.+?)(\<\/h2\>)(.+?)(\<div class\=\"section\"\>\<h2(.*?)\>|$)/si";
										$r3 = "/(\<div class\=\"section\"\>)(\<h2 id\=\"rm-specs\"\>)(Specifications)(\<\/h2\>)(\<div class\=\"content\"\>)(.+?)(\<\/div\>\<\/div\>)/sei";
										$r4 = "/(\<div class\=\"section\"\>)(\<h2 id\=\"rm-screenshots\"\>)(Screenshots)(\<\/h2\>)(\<div class\=\"content\"\>)(.+?)(\<\/div\>\<\/div\>)/sei";
										$r5 = "/(\<a)( href)/i"; /* Modify all links. Assume a nofollow relationship since destinations are unknown. */
										/**/
										$rm = preg_replace ($r1, '<div class="section">' . "$1$3$4" . '<div class="content">' . "$5" . '</div></div>' . "$6", $rm);
										$rm = preg_replace ($r2, "$1" . '<div class="section">' . "$2$4$5" . '<div class="content">' . "$6" . '</div></div>' . "$7", $rm);
										$rm = stripslashes (preg_replace ($r3, "'$1$2$3$4$5'.c_ws_plugin__qcache_readmes::_parse_readme_specs('$6').'$7'", $rm, 1));
										$rm = preg_replace ($r4, "", $rm, 1); /* Here we just remove the screenshots completely. */
										/**/
										if ($_blank_targets) /* Modify all links. Always nofollow. ( with _blank targets ? ). */
											$rm = preg_replace ($r5, "$1" . ' target="_blank" rel="nofollow external"' . "$2", $rm);
										else /* Otherwise, we don't need to set _blank targets. So external is removed also. */
											$rm = preg_replace ($r5, "$1" . ' rel="nofollow"' . "$2", $rm);
										/**/
										if ($process_wp_syntax) /* If we're processing <pre><code> tags for WP-Syntax. */
											if (function_exists ("wp_syntax_before_filter") && function_exists ("wp_syntax_before_filter"))
												{
													$rm = preg_replace ("/\<pre\>\<code\>/i", '<pre lang="php" escaped="true">', $rm);
													$rm = preg_replace ("/\<\/code\>\<\/pre\>/i", '</pre>', $rm);
													$rm = wp_syntax_after_filter (wp_syntax_before_filter ($rm));
												}
										/**/
										@ini_set ("pcre.backtrack_limit", $o_pcre);
										/**/
										$readme = '<div class="readme">' . "\n";
										$readme .= $rm . "\n"; /* Content. */
										$readme .= '</div>' . "\n";
										/**/
										return apply_filters ("ws_plugin__qcache_parse_readme", $readme, get_defined_vars ());
									}
							}
						else /* In case readme.txt is deleted. */
							{
								return "Unable to parse /readme.txt.";
							}
					}
				/*
				Callback function that helps readme file parsing with specs.
				*/
				public static function _parse_readme_specs ($str = FALSE)
					{
						do_action ("_ws_plugin__qcache_before_parse_readme_specs", get_defined_vars ());
						/**/
						$str = preg_replace ("/(\<p\>|^)(.+?)(\:)( )(.+?)($|\<\/p\>)/mi", "$1" . '<li><strong>' . "$2" . '</strong>' . "$3" . '&nbsp;&nbsp;&nbsp;&nbsp;<code>' . "$5" . '</code></li>' . "$6", $str);
						$str = preg_replace ("/\<p\>\<li\>/i", '<ul><li>', $str); /* Open the list items. */
						$str = preg_replace ("/\<\/li\>\<\/p\>/i", '</li></ul><br />', $str);
						/**/
						return apply_filters ("_ws_plugin__qcache_parse_readme_specs", $str, get_defined_vars ());
					}
				/*
				Function for parsing readme.txt files and returning a key value.
				*/
				public static function parse_readme_value ($key = FALSE, $specific_path = FALSE)
					{
						static $readme = array (); /* For repeated lookups across different paths. */
						/**/
						if (!($path = $specific_path)) /* Was a specific path passed in? */
							{
								$path = dirname (dirname (dirname (__FILE__))) . "/readme.txt";
								$dev_path = dirname (dirname (dirname (__FILE__))) . "/readme-dev.txt";
								$path = (file_exists ($dev_path)) ? $dev_path : $path;
							}
						/**/
						eval('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
						do_action ("ws_plugin__qcache_before_parse_readme_value", get_defined_vars ());
						unset ($__refs, $__v); /* Unset defined __refs, __v. */
						/**/
						if ($readme[$path] || file_exists ($path))
							{
								if (!$readme[$path]) /* If not already opened, we need open it up now. */
									{
										$readme[$path] = file_get_contents ($path); /* Get readme.txt file contents. */
										$mb = function_exists ("mb_convert_encoding") ? @mb_convert_encoding ($readme[$path], "UTF-8", $GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["mb_detection_order"]) : $readme[$path];
										$readme[$path] = ($mb) ? $mb : $readme[$path]; /* Double check this, just in case conversion fails on an unpredicted charset. */
									}
								/**/
								preg_match ("/(^)(" . preg_quote ($key, "/") . ")(\:)( )(.+?)($)/m", $readme[$path], $m);
								/**/
								return strlen ($m[5] = trim ($m[5])) ? apply_filters ("ws_plugin__qcache_parse_readme_value", $m[5], get_defined_vars ()) : false;
							}
						else /* Nope. */
							return false;
					}
			}
	}
?>