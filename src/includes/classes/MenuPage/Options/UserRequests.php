<?php
namespace WebSharks\CometCache\Classes\MenuPage\Options;

use WebSharks\CometCache\Classes;

/**
 * Options section.
 *
 * @since 17xxxx Refactor menu pages.
 */
class UserRequests extends Classes\AbsBase
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
            echo '      <i class="si si-octi-organization"></i> '.__('Logged-In Users', 'comet-cache')."\n";
            echo '   </a>'."\n";

            echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
            echo '      <i class="si si-group si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
            echo '      <h3>'.__('Caching Enabled for Logged-In Users &amp; Comment Authors?', 'comet-cache').'</h3>'."\n";
            echo '      <p>'.__('This should almost always be set to <code>No</code>. Most sites don\'t cache content generated while a user is logged-in. Doing so could result in a cache of dynamic content generated specifically for a particular user, where the content being cached may contain details that pertain only to the user that was logged-in when the cache was generated. In short, don\'t turn this on unless you know what you\'re doing. Note also that most sites get most (sometimes all) of their traffic from users who <em>are not</em> logged-in. When a user <em>is</em> logged-in, disabling the cache is generally a good idea because a logged-in user has a session open with your site. The content they view should remain very dynamic in this scenario.', 'comet-cache').'</p>'."\n";
            echo '      <i class="si si-sitemap si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
            echo '      <p>'.sprintf(__('<strong>Exception (Membership Sites):</strong> If you run a site with many users and the majority of your traffic comes from users who <em>are</em> logged-in, choose: <code>Yes (maintain separate cache)</code>. %1$s will operate normally, but when a user is logged-in the cache is user-specific. %1$s will intelligently refresh the cache when/if a user submits a form on your site with the GET or POST method. Or, if you make changes to their account (or another plugin makes changes to their account); including user <a href="http://codex.wordpress.org/Function_Reference/update_user_option" target="_blank">option</a>|<a href="http://codex.wordpress.org/Function_Reference/update_user_meta" target="_blank">meta</a> additions, updates &amp; deletions too. However, please note that enabling this feature (i.e., user-specific cache entries) will eat up much more disk space. That being said, the benefits of this feature for most sites will outweigh the disk overhead; i.e., it\'s not an issue in most cases. In other words, unless you\'re short on disk space, or you have thousands of users, the disk overhead is neglible.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][when_logged_in]" data-toggle="enable-disable" data-enabled-strings="1,postload" data-target=".-logged-in-users-options">'."\n";
            echo '            <option value="0"'.(!IS_PRO ? '' : selected($this->plugin->options['when_logged_in'], '0', false)).'>'.__('No, do NOT cache; or serve a cache file when a user is logged-in (safest option).', 'comet-cache').'</option>'."\n";
            echo '            <option value="postload"'.(!IS_PRO ? ' selected' : selected($this->plugin->options['when_logged_in'], 'postload', false)).'>'.__('Yes, and maintain a separate cache for each user (recommended for membership sites).', 'comet-cache').'</option>'."\n";
            if ($this->plugin->options['when_logged_in'] === '1' || get_site_option(GLOBAL_NS.'_when_logged_in_was_1')) {
                update_site_option(GLOBAL_NS.'_when_logged_in_was_1', '1');
                echo '            <option value="1"'.selected($this->plugin->options['when_logged_in'], '1', false).'>'.__('Yes, but DON\'T maintain a separate cache for each user (I know what I\'m doing).', 'comet-cache').'</option>'."\n";
            }
            echo '         </select></p>'."\n";
            if ($this->plugin->options['when_logged_in'] === '1' && $this->plugin->applyWpFilters(GLOBAL_NS.'_when_logged_in_no_admin_bar', true)) {
                echo '<p class="warning">'.sprintf(__('<strong>Warning:</strong> Whenever you enable caching for logged-in users (without a separate cache for each user), the WordPress Admin Bar <em>must</em> be disabled to prevent one user from seeing another user\'s details in the Admin Bar. <strong>Given your current configuration, %1$s will automatically hide the WordPress Admin Bar on the front-end of your site.</strong>', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            }
            echo '      <p class="info">'.sprintf(__('<strong>Note:</strong> %1$s includes comment authors as part of it\'s logged-in user check. This way comment authors will be able to see updates to comment threads immediately. And, so that any dynamically-generated messages displayed by your theme will work as intended. In short, %1$s thinks of a comment author as a logged-in user, even though technically they are not. Users who gain access to password-protected Posts/Pages are also considered by the logged-in user check.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";

            echo '      <hr />'."\n";

            echo '      <div class="plugin-menu-page-panel-if-enabled -logged-in-users-options">'."\n";
            echo '        <h3>'.__('Cache Pages Containing Nonce Values in Markup?', 'comet-cache').'</h3>'."\n";
            echo '        <p>'.sprintf(__('This should almost always be set to <code>Yes</code>. WordPress injects Nonces (<a href="https://cometcache.com/r/numbers-used-once-nonce/" target="_blank" rel="external">numbers used once</a>) into the markup on any given page that a logged-in user lands on. These Nonce values are generally used to improve security when actions are taken by a user; e.g., posting a form or clicking a link that performs an action. If you set this to <code>No</code>, any page containing an Nonce will bypass the cache and be served dynamically (a performance hit). Even the Admin Bar in WordPress injects Nonce values. That\'s reason enough to leave this at the default value of <code>Yes</code>; i.e., so Nonce values in the markup don\'t result in a cache bypass. In short, don\'t set this to <code>No</code> unless you know what you\'re doing.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            echo '        <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_nonce_values_when_logged_in]">'."\n";
            echo '           <option value="1"'.selected($this->plugin->options['cache_nonce_values_when_logged_in'], '1', false).'>'.__('Yes, for logged-in users, intelligently cache pages containing Nonce values (recommended).', 'comet-cache').'</option>'."\n";
            echo '           <option value="0"'.selected($this->plugin->options['cache_nonce_values_when_logged_in'], '0', false).'>'.__('No, for logged-in users, refuse to cache pages containing Nonce values.', 'comet-cache').'</option>'."\n";
            echo '           </select></p>'."\n";
            echo '        <p class="info">'.sprintf(__('<strong>Note:</strong> Nonce values in WordPress have a limited lifetime. They can expire just 12 hours after they were first generated. For this reason, %1$s will automatically force cache files containing Nonce values to expire once they are 12+ hours old; i.e., a new request for an expired page containing Nonce values will be rebuilt automatically, generating new Nonces that will continue to operate as expected. This rule is enforced no matter what your overall Cache Expiration Time is set to.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            echo '        <hr />'."\n";
            echo '        <h3>'.__('Static CDN Filters Enabled for Logged-In Users &amp; Comment Authors?', 'comet-cache').'</h3>'."\n";
            echo '        <p>'.__('While this defaults to a value of <code>No</code>, it should almost always be set to <code>Yes</code>. This value defaults to <code>No</code> only because Logged-In User caching (see above) defaults to <code>No</code> and setting this value to <code>Yes</code> by default can cause confusion for some site owners. Once you understand that Static CDN Filters can be applied safely for all visitors (logged-in or not logged-in), please choose <code>Yes</code> in the dropdown below. If you are not using Static CDN Filters, the value below is ignored.', 'comet-cache').'</p>'."\n";
            echo '        <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cdn_when_logged_in]">'."\n";
            echo '              <option value="0"'.selected($this->plugin->options['cdn_when_logged_in'], '0', false).'>'.__('No, disable Static CDN Filters when a user is logged-in.', 'comet-cache').'</option>'."\n";
            echo '                <option value="postload"'.selected($this->plugin->options['cdn_when_logged_in'], 'postload', false).'>'.__('Yes, enable Static CDN Filters for logged-in users (recommended) .', 'comet-cache').'</option>'."\n";
            echo '          </select></p>'."\n";
            echo '        <p class="info">'.__('<strong>Note:</strong> Static CDN Filters serve <em>static</em> resources. Static resources, are, simply put, static. Thus, it is not a problem to cache these resources for any visitor (logged-in or not logged-in). To avoid confusion, this defaults to a value of <code>No</code>, and we ask that you set it to <code>Yes</code> on your own so that you\'ll know to expect this behavior; i.e., that static resources will always be served from the CDN (logged-in or not logged-in) even though Logged-In User caching may be disabled above.', 'comet-cache').'</p>'."\n";
            echo '        <hr />'."\n";
            echo '        <h3>'.__('Enable HTML Compression for Logged-In Users?', 'comet-cache').'</h3>'."\n";
            echo '        <p>'.__('Disabled by default. This setting is only applicable when HTML Compression is enabled. HTML Compression should remain disabled for logged-in users because the user-specific cache has a much shorter Time To Live (TTL) which means their cache is likely to expire more quickly than a normal visitor. Rebuilding the HTML Compressor cache is time-consuming and doing it too frequently will actually slow things down for them. For example, if you\'re logged into the site as a user and you submit a form, that triggers a clearing of the cache for that user, including the HTML Compressor cache. Lots of little actions you take can result in a clearing of the cache. This shorter TTL is not ideal when running the HTML Compressor because it does a deep analysis of the page content and the associated resources in order to intelligently compress things. For logged-in users, it is better to skip that extra work and just cache the HTML source as-is, avoiding that extra overhead. In short, do NOT turn this on unless you know what you\'re doing.', 'comet-cache').'</p>'."\n";
            echo '        <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_when_logged_in]">'."\n";
            echo '           <option value="0"'.selected($this->plugin->options['htmlc_when_logged_in'], '0', false).'>'.__('No, disable HTML Compression for logged-in users (recommended).', 'comet-cache').'</option>'."\n";
            echo '           <option value="postload"'.selected($this->plugin->options['htmlc_when_logged_in'], 'postload', false).'>'.__('Yes, enable HTML Compression for logged-in users.', 'comet-cache').'</option>'."\n";
            echo '           </select></p>'."\n";
            echo '      </div>'."\n";
            echo '   </div>'."\n";

            echo '</div>'."\n";
        }
    }
}
