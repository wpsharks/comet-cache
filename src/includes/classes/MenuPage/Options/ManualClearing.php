<?php
namespace WebSharks\CometCache\Classes\MenuPage\Options;

use WebSharks\CometCache\Classes;

/**
 * Options section.
 *
 * @since 17xxxx Refactor menu pages.
 */
class ManualClearing extends Classes\AbsBase
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
            echo '<div class="plugin-menu-page-panel">'."\n";

            echo '   <a href="#" class="plugin-menu-page-panel-heading" data-pro-version-only="'.(!IS_PRO ? __('pro version only', 'comet-cache') : '').'">'."\n";
            echo '      <i class="si si-broom"></i> '.__('Manual Cache Clearing', 'comet-cache')."\n";
            echo '   </a>'."\n";

            echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
            echo '      <h3>'.__('Clearing the Cache Manually', 'comet-cache').'</h3>'."\n";
            echo '      <img src="'.esc_attr($this->plugin->url('/src/client-s/images/clear-cache-ops1-ss.png')).'" class="-clear-cache-ops-ss screenshot" />'."\n";
            echo '      <p>'.sprintf(__('Once %1$s is enabled, you will find this new option in your WordPress Admin Bar (screenshot on right). Clicking this button will clear the cache and you can start fresh at anytime (e.g., you can do this manually; and as often as you wish).', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            echo '      <p>'.sprintf(__('Depending on the structure of your site, there could be many reasons to clear the cache. However, the most common reasons are related to Post/Page edits or deletions, Category/Tag edits or deletions, and Theme changes. %1$s handles most scenarios all by itself. However, many site owners like to clear the cache manually; for a variety of reasons (just to force a refresh).', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_admin_bar_enable]" data-target=".-cache-clear-admin-bar-options, .-cache-clear-admin-bar-roles-caps" style="width:auto;">'."\n";
            echo '          <option value="1"'.selected($this->plugin->options['cache_clear_admin_bar_enable'], '1', false).'>'.__('Yes, enable &quot;Clear Cache&quot; button in admin bar', 'comet-cache').'</option>'."\n";
            echo '          <option value="0"'.selected($this->plugin->options['cache_clear_admin_bar_enable'], '0', false).'>'.__('No, I don\'t intend to clear the cache manually.', 'comet-cache').'</option>'."\n";
            echo '         </select>'."\n";
            echo '         <span class="plugin-menu-page-panel-if-enabled -cache-clear-admin-bar-options"><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_admin_bar_options_enable]" class="-no-if-enabled" style="width:auto;">'."\n";
            echo '             <option value="1"'.selected($this->plugin->options['cache_clear_admin_bar_options_enable'], '1', false).'>'.__('w/ dropdown options.', 'comet-cache').'</option>'."\n";
            echo '             <option value="2"'.selected($this->plugin->options['cache_clear_admin_bar_options_enable'], '2', false).'>'.__('w/ dropdown options in split menu.', 'comet-cache').'</option>'."\n";
            echo '             <option value="0"'.selected($this->plugin->options['cache_clear_admin_bar_options_enable'], '0', false).'>'.__('w/o dropdown options.', 'comet-cache').'</option>'."\n";
            echo '         </select></span></p>'."\n";

            if (is_multisite()) {
                echo '  <div class="plugin-menu-page-panel-if-enabled -cache-clear-admin-bar-roles-caps">'."\n";
                echo '      <h4 style="margin-bottom:0;">'.__('Also allow Child Sites in a Network to clear the cache from their Admin Bar?', 'comet-cache').'</h4>'."\n";
                echo '      <p style="margin-top:2px;">'.sprintf(__('In a Multisite Network, each child site can clear its own cache. If you want child sites to see the "Clear Cache" button in their WordPress Admin Bar, you can specify a comma-delimited list of <a href="http://cometcache.com/r/wp-roles-caps/" target="_blank">Roles and/or Capabilities</a> that are allowed. For example, if I want Administrators to be capable of clearing the cache from their Admin Bar, I could enter <code>administrator</code> here. If I also want to allow Editors, I can use a comma-delimited list: <code>administrator,editor</code>. Or, I could use a single Capability of: <code>edit_others_posts</code>; which covers both Administrators &amp; Editors at the same time.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
                echo '      <p style="margin-bottom:0;"><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_admin_bar_roles_caps]" value="'.esc_attr($this->plugin->options['cache_clear_admin_bar_roles_caps']).'" /></p>'."\n";
                echo '      <p style="margin-top:0;">'.sprintf(__('<strong>Note:</strong> As a security measure, in addition to the Role(s) and/or Capabilities that you list here, each child site owner must also have the ability to <code>%1$s</code>.', 'comet-cache'), esc_html(IS_PRO ? $this->plugin->clear_min_cap : 'edit_posts')).'</p>'."\n";
                echo '  </div>'."\n";
            } else {
                echo '  <div class="plugin-menu-page-panel-if-enabled -cache-clear-admin-bar-roles-caps">'."\n";
                echo '      <h4 style="margin-bottom:0;">'.__('Also allow others to clear the cache from their Admin Bar?', 'comet-cache').'</h4>'."\n";
                echo '      <p style="margin-top:2px;">'.sprintf(__('If you want others to see the "Clear Cache" button in their WordPress Admin Bar, you can specify a comma-delimited list of <a href="http://cometcache.com/r/wp-roles-caps/" target="_blank">Roles and/or Capabilities</a> that are allowed. For example, if I want Editors to be capable of clearing the cache from their Admin Bar, I could enter <code>editor</code> here. If I also want to allow Authors, I can use a comma-delimited list: <code>editor,author</code>. Or, I could use a single Capability of: <code>publish_posts</code>; which covers both Editors &amp; Authors at the same time.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
                echo '      <p style="margin-bottom:0;"><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_admin_bar_roles_caps]" value="'.esc_attr($this->plugin->options['cache_clear_admin_bar_roles_caps']).'" /></p>'."\n";
                echo '      <p style="margin-top:0;">'.sprintf(__('<strong>Note:</strong> As a security measure, in addition to the Role(s) and/or Capabilities that you list here, each user must also have the ability to <code>%1$s</code>.', 'comet-cache'), esc_html(IS_PRO ? $this->plugin->clear_min_cap : 'edit_posts')).'</p>'."\n";
                echo '  </div>'."\n";
            }
            if ($this->plugin->functionIsPossible('opcache_reset')) {
                echo '  <hr />'."\n";
                echo '  <h3>'.__('Clear the <a href="http://cometcache.com/r/php-opcache/" target="_blank">PHP OPcache</a> Too?', 'comet-cache').'</h3>'."\n";
                echo '  <p>'.sprintf(__('If you clear the cache manually, do you want %1$s to clear the PHP OPcache too? This is not necessary, but if you want a truly clean start, this will clear all PHP files in the server\'s opcode cache also. Note: If you don\'t already know what the PHP OPcache is, it is suggested that you leave this disabled. It really is not necessary. This is just an added feature for advanced users.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
                echo '  <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_opcache_enable]" class="-no-if-enabled">'."\n";
                echo '      <option value="0"'.selected($this->plugin->options['cache_clear_opcache_enable'], '0', false).'>'.__('No, I don\'t use the PHP OPcache extension; or, I don\'t want the opcode cache cleared.', 'comet-cache').'</option>'."\n";
                echo '      <option value="1"'.selected($this->plugin->options['cache_clear_opcache_enable'], '1', false).'>'.__('Yes, if the PHP OPcache extension is enabled, also clear the entire PHP opcode cache.', 'comet-cache').'</option>'."\n";
                echo '  </select></p>'."\n";
            }
            if ($this->plugin->functionIsPossible('s2clean')) {
                echo '  <hr />'."\n";
                echo '  <h3>'.__('Clear the <a href="http://websharks-inc.com/product/s2clean/" target="_blank">s2Clean</a> Cache Too?', 'comet-cache').'</h3>'."\n";
                echo '  <p>'.sprintf(__('If the s2Clean theme is installed, and you clear the cache manually, %1$s can clear the s2Clean Markdown cache too (if you\'ve enabled Markdown processing with s2Clean).', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
                echo '  <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_s2clean_enable]" class="-no-if-enabled">'."\n";
                echo '      <option value="1"'.selected($this->plugin->options['cache_clear_s2clean_enable'], '1', false).'>'.__('Yes, if the s2Clean theme is installed, also clear s2Clean-related caches.', 'comet-cache').'</option>'."\n";
                echo '      <option value="0"'.selected($this->plugin->options['cache_clear_s2clean_enable'], '0', false).'>'.__('No, I don\'t use s2Clean; or, I don\'t want s2Clean-related caches cleared.', 'comet-cache').'</option>'."\n";
                echo '  </select></p>'."\n";
            }
            echo '      <hr />'."\n";
            echo '      <h3>'.__('Evaluate Custom PHP Code when Clearing the Cache?', 'comet-cache').'</h3>'."\n";
            echo '      <p>'.sprintf(__('If you have any custom routines you\'d like to process when the cache is cleared manually, please enter PHP code here. If your PHP code outputs a message, it will be displayed along with any other notes from %1$s itself. This feature is intended for developers, and it may come in handy if you need to clear any system caches not already covered by %1$s configuration options.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            echo '      <p style="margin-bottom:0;"><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_eval_code]" rows="5" spellcheck="false" class="monospace">'.format_to_edit($this->plugin->options['cache_clear_eval_code']).'</textarea></p>'."\n";
            echo '      <p class="info" style="margin-top:0;">'.__('<strong>Example:</strong> <code>&lt;?php apc_clear_cache(); echo \'&lt;p&gt;Also cleared APC cache.&lt;/p&gt;\'; ?&gt;</code>', 'comet-cache').'</p>'."\n";

            echo '      <hr />'."\n";
            echo '      <h3>'.__('Clear the CDN Cache Too?', 'comet-cache').'</h3>'."\n";
            echo '      <p>'.sprintf(__('If you clear the cache manually, do you want %1$s to automatically bump the CDN invalidation counter too? i.e., automatically increment the <code>?%2$s=[counter]</code> in all static CDN URLs?', 'comet-cache'), esc_html(NAME), esc_html($this->plugin->options['cdn_invalidation_var'])).'</p>'."\n";
            echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][cache_clear_cdn_enable]" class="-no-if-enabled">'."\n";
            echo '            <option value="0"'.selected($this->plugin->options['cache_clear_cdn_enable'], '0', false).'>'.__('No, I don\'t use Static CDN Filters; or, I don\'t want the CDN cache cleared.', 'comet-cache').'</option>'."\n";
            echo '            <option value="1"'.selected($this->plugin->options['cache_clear_cdn_enable'], '1', false).'>'.__('Yes, if Static CDN Filters are enabled, also clear the CDN cache.', 'comet-cache').'</option>'."\n";
            echo '      </select></p>'."\n";
            echo '   </div>'."\n";

            echo '</div>'."\n";
        }
    }
}
