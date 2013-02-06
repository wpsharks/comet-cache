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
Add the plugin Actions/Filters here.
*/
add_action ("init", "c_ws_plugin__qcache_admin_css_js::menu_pages_css", 1);
add_action ("init", "c_ws_plugin__qcache_admin_css_js::menu_pages_js", 1);
/**/
add_filter ("status_header", "c_ws_plugin__qcache_status_headers::status", 10, 2);
/**/
add_action ("admin_init", "c_ws_plugin__qcache_check_activation::check");
/**/
add_action ("admin_print_scripts", "c_ws_plugin__qcache_menu_pages::add_admin_scripts");
add_action ("admin_print_styles", "c_ws_plugin__qcache_menu_pages::add_admin_styles");
/**/
add_action ("admin_bar_menu", "c_ws_plugin__qcache_menu_pages::add_admin_bar_nodes");
add_action ("in_admin_header", "c_ws_plugin__qcache_menu_pages::old_admin_header_items");
/**/
add_action ("admin_menu", "c_ws_plugin__qcache_menu_pages::add_admin_options");
add_action ("network_admin_menu", "c_ws_plugin__qcache_menu_pages::add_network_admin_options");
/**/
add_action ("admin_notices", "c_ws_plugin__qcache_admin_notices::admin_notices");
add_action ("user_admin_notices", "c_ws_plugin__qcache_admin_notices::admin_notices");
add_action ("network_admin_notices", "c_ws_plugin__qcache_admin_notices::admin_notices");
/**/
add_action ("wp_ajax_ws_plugin__qcache_ajax_clear", "c_ws_plugin__qcache_clearing_routines::ajax_clear");
/**/
add_action ("save_post", "c_ws_plugin__qcache_clearing_routines::clear_on_post_page_creations_deletions");
add_action ("edit_post", "c_ws_plugin__qcache_clearing_routines::clear_on_post_page_creations_deletions");
add_action ("delete_post", "c_ws_plugin__qcache_clearing_routines::clear_on_post_page_creations_deletions");
/**/
add_action ("create_term", "c_ws_plugin__qcache_clearing_routines::clear_on_creations_deletions");
add_action ("edit_terms", "c_ws_plugin__qcache_clearing_routines::clear_on_creations_deletions");
add_action ("delete_term", "c_ws_plugin__qcache_clearing_routines::clear_on_creations_deletions");
/**/
add_action ("add_link", "c_ws_plugin__qcache_clearing_routines::clear_on_creations_deletions");
add_action ("edit_link", "c_ws_plugin__qcache_clearing_routines::clear_on_creations_deletions");
add_action ("delete_link", "c_ws_plugin__qcache_clearing_routines::clear_on_creations_deletions");
/**/
add_action ("switch_theme", "c_ws_plugin__qcache_clearing_routines::clear_on_theme_changes");
/**/
add_filter ("cron_schedules", "c_ws_plugin__qcache_extend_cron::extend_cron_schedules");
/**/
add_action ("ws_plugin__qcache_purge_cache_dir__schedule", "c_ws_plugin__qcache_purging_routines::purge_cache_dir", 10, 3);
add_action ("ws_plugin__qcache_garbage_collector__schedule", "c_ws_plugin__qcache_garbage_collection::garbage_collector");
add_action ("ws_plugin__qcache_auto_cache_engine__schedule", "c_ws_plugin__qcache_auto_cache::auto_cache_engine");
/*
Register the activation | de-activation routines.
*/
register_activation_hook ($GLOBALS["WS_PLUGIN__"]["qcache"]["l"], "c_ws_plugin__qcache_installation::activate");
register_deactivation_hook ($GLOBALS["WS_PLUGIN__"]["qcache"]["l"], "c_ws_plugin__qcache_installation::deactivate");
?>