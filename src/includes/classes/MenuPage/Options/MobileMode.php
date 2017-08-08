<?php
namespace WebSharks\CometCache\Classes\MenuPage\Options;

use WebSharks\CometCache\Classes;

/**
 * Options section.
 *
 * @since 17xxxx Refactor menu pages.
 */
class MobileMode extends Classes\AbsBase
{
    /**
     * Constructor.
     *
     * @since 17xxxx Refactor menu pages.
     */
    public function __construct()
    {
        parent::__construct(); // Parent constructor.

        if (IS_PRO || $this->plugin->isProPreview()) {
            echo '<div class="plugin-menu-page-panel'.(!IS_PRO ? ' pro-preview' : '').'">'."\n";

            echo '   <a href="#" class="plugin-menu-page-panel-heading" data-pro-version-only="'.(!IS_PRO ? __('pro version only', 'comet-cache') : '').'">'."\n";
            echo '      <i class="si si-tablet"></i> '.__('Mobile Mode', 'comet-cache')."\n";
            echo '   </a>'."\n";

            echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
            echo '      <h3>'.__('<i class="si si-tablet"></i> <i class="si si-mobile"></i> Enable Mobile-Adaptive Mode?', 'comet-cache').'</h3>'."\n";
            echo '      <p>'.__('<em><strong>Tip:</strong> Generally speaking, you should only enable this if your WordPress theme uses an \'Adaptive\' design, as opposed to a design that\'s \'Responsive\'—the way most WordPress themes are built.', 'comet-cache').'</em></p>'."\n";
            echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][mobile_adaptive_salt_enable]" data-target=".-mobile-adaptive-options">'."\n";
            echo '          <option value="0"'.(!IS_PRO ? '' : selected($this->plugin->options['mobile_adaptive_salt_enable'], '0', false)).'>'.__('No, my theme is Responsive; i.e., not Adaptive.', 'comet-cache').'</option>'."\n";
            echo '          <option value="1"'.(!IS_PRO ? ' selected' : selected($this->plugin->options['mobile_adaptive_salt_enable'], '1', false)).'>'.__('Yes, create multiple cache variations based on mobile device type.', 'comet-cache').'</option>'."\n";
            echo '      </select></p>'."\n";

            if (!version_compare(PHP_VERSION, '5.6', '>=')) {
                echo '<p class="error">'.sprintf(__('<strong>PHP Version:</strong> This feature requires PHP v5.6 (or higher). You\'re currently running PHP v%1$s. Please contact your web hosting company for assistance.', 'comet-cache'), esc_html(PHP_VERSION)).'</p>'."\n";
            }

            echo '      <h4 style="margin-bottom:0;">'.__('What\'s the Difference Between Responsive and Adaptive?', 'comet-cache').'</h4>'."\n";
            echo '      <p>'.__('Responsive and Adaptive designs both attempt to optimize the user experience across different devices, adjusting for different viewport sizes, resolutions, usage contexts, control mechanisms, and so on. Responsive design (common for WordPress sites) works on the principle of flexibility — a single fluid website that can look good on any device. Responsive websites use media queries, flexible grids, and responsive images to create a user experience that flexes and changes based on a multitude of factors. If you have a Responsive theme, you probably do NOT need to enable Mobile-Adaptive Mode.', 'comet-cache').'</p>'."\n";
            echo '      <p>'.sprintf(__('<strong>Adaptive design</strong> detects the device and other features, and then it provides the appropriate feature and layout based on a predefined set of viewport sizes and other characteristics. Adaptive themes generally decide what to display based on a visitor\'s User-Agent (i.e., OS, device, browser, version). Since this design choice results in multiple versions of a page being served to visitors, based on the device they access the site with, it then becomes important to cache each of those variations separately. That way a visitor on an iPhone isn\'t accidentally shown the cached copy of a page that was originally viewed by another visitor who was on a desktop computer. If your theme uses an Adaptive design, you probably DO want to enable Mobile-Adaptive Mode in %1$s.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";

            echo '      <div class="plugin-menu-page-panel-if-enabled -mobile-adaptive-options"><hr />'."\n";
            echo '          <h3>'.__('Mobile-Adaptive Tokens', 'comet-cache').'</h3>'."\n";
            echo '          <p>'.sprintf(__('When %1$s runs in Mobile-Adaptive Mode and it detects that a device is Mobile (e.g., a phone, tablet), it needs to know which factors you\'d like to consider. Mobile-Adaptive Tokens make this easy. In the field below, please configure a list of Mobile-Adaptive Tokens that establish the important factors on your site. Each token must be separated by a <code>+</code> sign. You can use just one, or use them all. <strong>However, it\'s IMPORTANT to note:</strong> With each new token, you add additional permutations that can fragment the cache and eat up a lot of disk space. Enable and monitor Cache Statistics so you can keep an eye on this. See: <strong>%1$s → Plugin Options → Cache-Related Statistics</strong>', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            echo '          <p>'.__('The available Tokens are as follows:', 'comet-cache').'</p>'."\n";
            echo '          <ul style="list-style-type:disc; margin-left: 1.5em;">'."\n";
            echo '              <li>'.__('<code>os.name</code> <small><em>This token is replaced automatically with: <tt>iOS</tt>, <tt>Android</tt>, etc.</em></small>', 'comet-cache').'</li>'."\n";
            echo '              <li>'.__('<code>device.type</code> <small><em>This token is replaced with: <tt>Tablet</tt>, <tt>Mobile Device</tt>, <tt>Mobile Phone</tt>, etc.</em></small>', 'comet-cache').'</li>'."\n";
            echo '              <li>'.__('<code>browser.name</code> <small><em>This token is replaced with: <tt>Safari</tt>, <tt>Mobile Safari UIWebView</tt>, <tt>Chrome</tt>, etc.</small></em>', 'comet-cache').'</li>'."\n";
            echo '              <li>'.__('<code>browser.version</code> <small><em>Major &amp; minor version. Not recommended, many permutations. Replaced with: <tt>55.0</tt>, <tt>1.3</tt>, <tt>9383242.2392</tt>, etc.</em></small>', 'comet-cache').'</li>'."\n";
            echo '              <li>'.__('<code>browser.version.major</code> <small><em>Major version only. More feasible, fewer permutations. Replaced with: <tt>55</tt>, <tt>1</tt>, <tt>9383242</tt>, etc.</em></small>', 'comet-cache').'</li>'."\n";
            echo '          </ul>'."\n";
            echo '          <p><input type="text" id="'.esc_attr(GLOBAL_NS.'-mobile-adaptive-salt').'" name="'.esc_attr(GLOBAL_NS).'[saveOptions][mobile_adaptive_salt]" value="'.esc_attr($this->plugin->options['mobile_adaptive_salt']).'" /></p>'."\n";
            echo '          <p>'.sprintf(__('The suggested default value is: <code>%2$s</code><br />However, just: <code>os.name + device.type</code> is better, if that will do.', 'comet-cache'), esc_html(NAME), esc_html($this->plugin->default_options['mobile_adaptive_salt'])).'</p>'."\n";
            echo '          <p class="info">'.__('The special token: <code>device.is_mobile</code> (i.e., any mobile device, including tablets, excluding laptops) can be used by itself. For example, if you simply want to break the cache down into mobile vs. NOT mobile.', 'comet-cache').'</p>'."\n";
            echo '          <p><small><em>'.sprintf(__('<strong>Note:</strong> The underlying logic behind mobile detection is accomplished using a faster, precompiled version of <a href="https://cometcache.com/r/browscap/" target="_blank">Browscap Lite</a>, and Browcap data is automatically updated (and recompiled) whenever you save %1$s options and/or when upgrading %1$s to a new version.', 'comet-cache'), esc_html(NAME)).'</em></small></p>'."\n";
            echo '      </div>'."\n";

            echo '   </div>'."\n";
            echo '</div>'."\n";
        }
    }
}
