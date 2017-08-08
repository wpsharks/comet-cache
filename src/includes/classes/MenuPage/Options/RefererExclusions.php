<?php
namespace WebSharks\CometCache\Classes\MenuPage\Options;

use WebSharks\CometCache\Classes;

/**
 * Options section.
 *
 * @since 17xxxx Refactor menu pages.
 */
class RefererExclusions extends Classes\AbsBase
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
        echo '      <i class="si si-ban"></i> '.__('HTTP Referrer Exclusions', 'comet-cache')."\n";
        echo '   </a>'."\n";

        echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
        echo '      <h3>'.__('Don\'t Cache These Special HTTP Referrer Exclusion Patterns?', 'comet-cache').'</h3>'."\n";
        echo '      <p>'.__('Sometimes there are special cases where a particular referring URL (or referring domain) that sends you traffic; or even a particular group of referring URLs or domains that send you traffic; should result in a page being loaded on your site that is NOT from the cache (and that resulting page should never be cached). This is where you will enter those if you need to (one per line). Searches are performed against the <a href="http://www.php.net//manual/en/reserved.variables.server.php" target="_blank" style="text-decoration:none;"><code>HTTP_REFERER</code></a> (caSe insensitive). A wildcard <code>*</code> character can also be used when necessary; e.g., <code>*.domain.com</code> (where <code>*</code> = 0 or more characters that are NOT a slash <code>/</code>). Other special characters include: <code>**</code> = 0 or more characters of any kind, including <code>/</code> slashes; <code>^</code> = beginning of the string; <code>$</code> = end of the string. To learn more about this syntax, please see <a href ="http://cometcache.com/r/watered-down-regex-syntax/" target="_blank">this KB article</a>.', 'comet-cache').'</p>'."\n";
        echo '      <p><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][exclude_refs]" rows="5" spellcheck="false" class="monospace">'.format_to_edit($this->plugin->options['exclude_refs']).'</textarea></p>'."\n";
        echo '      <p class="info">'.__('<strong>Tip:</strong> let\'s use this example URL: <code>http://www.referring-domain.com/search/?q=search+terms</code>. To exclude this referring URL, you could put this line into the field above: <code>www.referring-domain.com</code>. Or, you could also just put in a small fragment, like: <code>/search/</code> or <code>q=*</code>; and that would exclude any referrer containing that word fragment.', 'comet-cache').'</p>'."\n";
        echo '      <p class="info">'.__('<strong>Note:</strong> please remember that your entries here should be formatted as a line-delimited list; e.g., one exclusion pattern per line.', 'comet-cache').'</p>'."\n";
        echo '   </div>'."\n";

        echo '</div>'."\n";
    }
}
