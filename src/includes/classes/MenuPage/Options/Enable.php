<?php
namespace WebSharks\CometCache\Classes\MenuPage\Options;

use WebSharks\CometCache\Classes;

/**
 * Options section.
 *
 * @since 17xxxx Refactor menu pages.
 */
class Enable extends Classes\AbsBase
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

        echo '   <a href="#" class="plugin-menu-page-panel-heading'.((!$this->plugin->options['enable']) ? ' open' : '').'">'."\n";
        echo '      <i class="si si-enty-gauge"></i> '.__('Enable/Disable', 'comet-cache')."\n";
        echo '   </a>'."\n";

        echo '   <div class="plugin-menu-page-panel-body'.((!$this->plugin->options['enable']) ? ' open' : '').' clearfix">'."\n";
        echo '      <p class="speed"><img src="'.esc_attr($this->plugin->url('/src/client-s/images/tach.png')).'" style="float:right; width:100px; margin-left:1em;" />'.sprintf(__('%1$s&trade; = SPEED<em>!!</em>', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><label class="switch-primary"><input type="radio" name="'.esc_attr(GLOBAL_NS).'[saveOptions][enable]" value="1"'.checked($this->plugin->options['enable'], '1', false).' /> '.sprintf(__('Yes, enable %1$s&trade;', 'comet-cache'), esc_html(NAME)).' <i class="si si-magic si-flip-horizontal"></i></label> &nbsp;&nbsp;&nbsp; <label><input type="radio" name="'.esc_attr(GLOBAL_NS).'[saveOptions][enable]" value="0"'.checked($this->plugin->options['enable'], '0', false).' /> '.__('No, disable.', 'comet-cache').'</label></p>'."\n";
        echo '      <p class="info" style="font-family:\'Georgia\', serif; font-size:110%; margin-top:1.5em;">'.sprintf(__('<strong>HUGE Time-Saver:</strong> Approx. 95%% of all WordPress sites running %1$s, simply enable it here; and that\'s it :-) <strong>No further configuration is necessary (really).</strong> All of the other options (down below) are already tuned for the BEST performance on a typical WordPress installation. Simply enable %1$s here and click "Save All Changes". If you get any warnings please follow the instructions given. Otherwise, you\'re good <i class="si si-smile-o"></i>. This plugin is designed to run just fine like it is. Take it for a spin right away; you can always fine-tune things later if you deem necessary.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
        echo '      <hr />'."\n";
        echo '      <img src="'.esc_attr($this->plugin->url('/src/client-s/images/source-code-ss.png')).'" class="screenshot" />'."\n";
        echo '      <h3>'.sprintf(__('How Can I Tell %1$s is Working?', 'comet-cache'), esc_html(NAME)).'</h3>'."\n";
        echo '      <p>'.sprintf(__('First of all, please make sure that you\'ve enabled %1$s here; then scroll down to the bottom of this page and click "Save All Changes". All of the other options (below) are already pre-configured for typical usage. Feel free to skip them all for now. You can go back through all of these later and fine-tune things the way you like them.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p>'.sprintf(__('Once %1$s has been enabled, <strong>you\'ll need to log out (and/or clear browser cookies)</strong>. By default, cache files are NOT served to visitors who are logged-in, and that includes you too ;-) Cache files are NOT served to recent comment authors either. If you\'ve commented (or replied to a comment lately); please clear your browser cookies before testing.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p>'.sprintf(__('<strong>To verify that %1$s is working</strong>, navigate your site like a normal visitor would. Right-click on any page (choose View Source), then scroll to the very bottom of the document. At the bottom, you\'ll find comments that show %1$s stats and information. You should also notice that page-to-page navigation is <i class="si si-flash"></i> <strong>lightning fast</strong> now that %1$s is running; and it gets faster over time!', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
        echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][debugging_enable]" class="-no-if-enabled">'."\n";
        echo '            <option value="1"'.selected($this->plugin->options['debugging_enable'], '1', false).'>'.__('Yes, enable notes in the source code so I can see it\'s working (recommended).', 'comet-cache').'</option>'."\n";
        echo '            <option value="2"'.selected($this->plugin->options['debugging_enable'], '2', false).'>'.__('Yes, enable notes in the source code AND show debugging details (not recommended for production).', 'comet-cache').'</option>'."\n";
        echo '            <option value="0"'.selected($this->plugin->options['debugging_enable'], '0', false).'>'.__('No, I don\'t want my source code to contain any of these notes.', 'comet-cache').'</option>'."\n";
        echo '         </select></p>'."\n";
        echo '   </div>'."\n";

        echo '</div>'."\n";
    }
}
