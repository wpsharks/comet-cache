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
/*
Info page.
*/
echo '<div class="wrap ws-menu-page">' . "\n";
/**/
echo '<div id="icon-plugins" class="icon32"><br /></div>' . "\n";
echo '<h2>Quick Cache Information</h2>' . "\n";
/**/
echo '<table class="ws-menu-page-table">' . "\n";
echo '<tbody class="ws-menu-page-table-tbody">' . "\n";
echo '<tr class="ws-menu-page-table-tr">' . "\n";
echo '<td class="ws-menu-page-table-l">' . "\n";
/**/
echo '<img src="' . esc_attr ($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["dir_url"]) . '/images/brand-icon.png" class="ws-menu-page-brand-icon" alt="." />' . "\n";
/**/
echo '<a href="' . esc_attr (add_query_arg ("c_check_ver", urlencode (c_ws_plugin__qcache_readmes::parse_readme_value ("Version")), c_ws_plugin__qcache_readmes::parse_readme_value ("Plugin URI"))) . '" target="_blank"><img src="' . esc_attr ($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["dir_url"]) . '/images/brand-updates.png" class="ws-menu-page-brand-updates" alt="." /></a>' . "\n";
/**/
do_action ("ws_plugin__qcache_during_info_page_before_left_sections", get_defined_vars ());
/**/
if (apply_filters ("ws_plugin__qcache_during_info_page_during_left_sections_display_readme", true, get_defined_vars ()))
	{
		do_action ("ws_plugin__qcache_during_info_page_during_left_sections_before_readme", get_defined_vars ());
		/**/
		echo '<div class="ws-menu-page-readme">' . "\n";
		echo c_ws_plugin__qcache_readmes::parse_readme () . "\n";
		echo '</div>' . "\n";
		/**/
		do_action ("ws_plugin__qcache_during_info_page_during_left_sections_after_readme", get_defined_vars ());
	}
/**/
do_action ("ws_plugin__qcache_during_info_page_after_left_sections", get_defined_vars ());
/**/
echo '</td>' . "\n";
/**/
echo '<td class="ws-menu-page-table-r">' . "\n";
/**/
do_action ("ws_plugin__qcache_during_info_page_before_right_sections", get_defined_vars ());
do_action ("ws_plugin__qcache_during_menu_pages_before_right_sections", get_defined_vars ());
/**/
echo ($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["menu_pages"]["installation"]) ? '<div class="ws-menu-page-installation"><a href="' . esc_attr (c_ws_plugin__qcache_readmes::parse_readme_value ("Professional Installation URI")) . '" target="_blank"><img src="' . esc_attr ($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["dir_url"]) . '/images/brand-installation.png" alt="." /></a></div>' . "\n" : '';
echo ($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["menu_pages"]["tools"]) ? '<div class="ws-menu-page-tools"><img src="' . esc_attr ($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["dir_url"]) . '/images/brand-tools.png" alt="." /></div>' . "\n" : '';
echo ($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["menu_pages"]["videos"]) ? '<div class="ws-menu-page-videos"><a href="' . esc_attr (c_ws_plugin__qcache_readmes::parse_readme_value ("Video Tutorials")) . '" target="_blank"><img src="' . esc_attr ($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["dir_url"]) . '/images/brand-videos.png" alt="." /></a></div>' . "\n" : '';
echo ($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["menu_pages"]["support"]) ? '<div class="ws-menu-page-support"><a href="' . esc_attr (c_ws_plugin__qcache_readmes::parse_readme_value ("Forum URI")) . '" target="_blank"><img src="' . esc_attr ($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["dir_url"]) . '/images/brand-support.png" alt="." /></a></div>' . "\n" : '';
echo ($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["menu_pages"]["donations"]) ? '<div class="ws-menu-page-donations"><a href="' . esc_attr (c_ws_plugin__qcache_readmes::parse_readme_value ("Donate link")) . '" target="_blank"><img src="' . esc_attr ($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["dir_url"]) . '/images/brand-donations.png" alt="." /></a></div>' . "\n" : '';
/**/
do_action ("ws_plugin__qcache_during_menu_pages_after_right_sections", get_defined_vars ());
do_action ("ws_plugin__qcache_during_info_page_after_right_sections", get_defined_vars ());
/**/
echo '</td>' . "\n";
/**/
echo '</tr>' . "\n";
echo '</tbody>' . "\n";
echo '</table>' . "\n";
/**/
echo '</div>' . "\n";
?>