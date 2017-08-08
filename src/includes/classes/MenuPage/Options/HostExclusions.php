<?php
namespace WebSharks\CometCache\Classes\MenuPage\Options;

use WebSharks\CometCache\Classes;

/**
 * Options section.
 *
 * @since 17xxxx Refactor menu pages.
 */
class HostExclusions extends Classes\AbsBase
{
    /**
     * Constructor.
     *
     * @since 17xxxx Refactor menu pages.
     */
    public function __construct()
    {
        parent::__construct(); // Parent constructor.

        $exclude_hosts_option_enable = is_multisite() &&
            ((defined('SUBDOMAIN_INSTALL') && SUBDOMAIN_INSTALL) || $this->plugin->canConsiderDomainMapping());

        if ($this->plugin->applyWpFilters(GLOBAL_NS.'_exclude_hosts_option_enable', $exclude_hosts_option_enable)) {
            echo '<div class="plugin-menu-page-panel">'."\n";

            echo '   <a href="#" class="plugin-menu-page-panel-heading">'."\n";
            echo '      <i class="si si-ban"></i> '.__('Host Exclusions', 'comet-cache')."\n";
            echo '   </a>'."\n";

            echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
            echo '      <h3>'.__('Don\'t Cache These Special Host Exclusion Patterns?', 'comet-cache').'</h3>'."\n";
            echo '      <p>'.__('If there are specific domains that should not be cached, you can enter them here so they are excluded automatically. The easiest way to exclude a host is to enter the full domain name on a line of it\'s own in the field below, e.g., <code>site1.example.com</code>.', 'comet-cache').'</p>'."\n";
            echo '      <p>'.__('This field also supports <a href ="http://cometcache.com/r/watered-down-regex-syntax/" target="_blank" style="text-decoration:none;">Watered-Down Regex</a> syntax, which means that you can also exclude a pattern like: <code>*.example.com</code> or <code>*.example.*</code>. So for instance, if you wanted to exclude all child sites and only cache pages on the Main Site of a Network installation, you could exclude all sub-domains using: <code>*.mynetwork.com</code>. That excludes all sub-domains, but not <code>mynetwork.com</code> by itself.', 'comet-cache').'</p>'."\n";

            echo '      <p><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][exclude_hosts]" rows="5" spellcheck="false" class="monospace">'.format_to_edit($this->plugin->options['exclude_hosts']).'</textarea></p>'."\n";

            echo '      <p class="info">'.__('<strong>Note:</strong> please remember that your entries here should be formatted as a line-delimited list; e.g., one exclusion pattern per line.', 'comet-cache').'</p>'."\n";

            echo '   </div>'."\n";
            echo '</div>'."\n";
        }
    }
}
