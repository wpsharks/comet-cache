<?php
namespace WebSharks\CometCache\Classes\MenuPage\Options;

use WebSharks\CometCache\Classes;

/**
 * Options section.
 *
 * @since 17xxxx Refactor menu pages.
 */
class UriExclusions extends Classes\AbsBase
{
    /**
     * Constructor.
     *
     * @since 17xxxx Refactor menu pages.
     */
    public function __construct()
    {
        parent::__construct(); // Parent constructor.

        echo '<div class="plugin-menu-page-panel">'."\n";

        echo '   <a href="#" class="plugin-menu-page-panel-heading">'."\n";
        echo '      <i class="si si-ban"></i> '.__('URI Exclusions', 'comet-cache')."\n";
        echo '   </a>'."\n";

        echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
        echo '      <h3>'.__('Don\'t Cache These Special URI Exclusion Patterns?', 'comet-cache').'</h3>'."\n";
        echo '      <p>'.__('Sometimes there are certain cases where a particular file, or a particular group of files, should never be cached. This is where you will enter those if you need to (one per line). Searches are performed against the <a href="https://gist.github.com/jaswsinc/338b6eb03a36c048c26f" target="_blank" style="text-decoration:none;"><code>REQUEST_URI</code></a>; i.e., <code>/path/?query</code> (caSe insensitive). So, don\'t put in full URLs here, just word fragments found in the file path (or query string) is all you need, excluding the http:// and domain name. A wildcard <code>*</code> character can also be used when necessary; e.g., <code>/category/abc-followed-by-*</code> (where <code>*</code> = 0 or more characters that are NOT a slash <code>/</code>). Other special characters include: <code>**</code> = 0 or more characters of any kind, including <code>/</code> slashes; <code>^</code> = beginning of the string; <code>$</code> = end of the string. To learn more about this syntax, please see <a href ="http://cometcache.com/r/watered-down-regex-syntax/" target="_blank">this KB article</a>.', 'comet-cache').'</p>'."\n";
        echo '      <p><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][exclude_uris]" rows="5" spellcheck="false" class="monospace">'.format_to_edit($this->plugin->options['exclude_uris']).'</textarea></p>'."\n";

        echo '      <p class="info">'.__('<strong>Tip:</strong> let\'s use this example URL: <code>http://www.example.com/post/example-post-123</code>. To exclude this URL, you would put this line into the field above: <code>/post/example-post-123</code>. Or, you could also just put in a small fragment, like: <code>example</code> or <code>example-*-123</code> and that would exclude any URI containing that word fragment.', 'comet-cache').'</p>'."\n";
        echo '      <p class="info">'.__('<strong>Note:</strong> please remember that your entries here should be formatted as a line-delimited list; e.g., one exclusion pattern per line.', 'comet-cache').'</p>'."\n";
        if (is_multisite() && defined('SUBDOMAIN_INSTALL') && !SUBDOMAIN_INSTALL) {
            echo '      <p class="info">'.__('<strong>Multisite Network w/ Sub-Directories:</strong> You can also use URI Exclusion Patterns to exclude specific sites from being cached, e.g., <code>/site1/*</code>.', 'comet-cache').'</p>'."\n";
        }
        echo '   </div>'."\n";

        echo '</div>'."\n";
    }
}
