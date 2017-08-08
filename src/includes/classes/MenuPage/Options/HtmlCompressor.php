<?php
namespace WebSharks\CometCache\Classes\MenuPage\Options;

use WebSharks\CometCache\Classes;

/**
 * Options section.
 *
 * @since 17xxxx Refactor menu pages.
 */
class HtmlCompressor extends Classes\AbsBase
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
            echo '      <i class="si si-html5"></i> '.__('HTML Compression', 'comet-cache')."\n";
            echo '   </a>'."\n";

            echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
            echo '      <i class="si si-question-circle si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
            echo '      <h3>'.__('Enable WebSharks™ HTML Compression?', 'comet-cache').'</h3>'."\n";
            if (is_plugin_active('autoptimize/autoptimize.php')) {
                echo '      <p class="warning">'.__('<strong>Autoptimize + Comet Cache:</strong> Comet Cache has detected that you are running the Autoptimize plugin. Autoptimize and the HTML Compressor feature of Comet Cache are both designed to compress HTML, CSS, and JavaScript. Enabling the HTML Compressor alongside Autoptimize may result in unexpected behavior. If you\'re happy with Autoptimize, you can leave the HTML Compressor disabled. All other Comet Cache features run great alongside Autoptimize.', 'comet-cache').' <i class="si si-smile-o"></i></p>';
            }
            echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_enable]" data-target=".-htmlc-options">'."\n";
            echo '            <option value="0"'.(!IS_PRO ? '' : selected($this->plugin->options['htmlc_enable'], '0', false)).'>'.__('No, do NOT compress HTML/CSS/JS code at runtime.', 'comet-cache').'</option>'."\n";
            echo '            <option value="1"'.(!IS_PRO ? ' selected' : selected($this->plugin->options['htmlc_enable'], '1', false)).'>'.__('Yes, I want to compress HTML/CSS/JS for blazing fast speeds.', 'comet-cache').'</option>'."\n";
            echo '         </select></p>'."\n";
            echo '      <hr />'."\n";
            echo '      <div class="plugin-menu-page-panel-if-enabled -htmlc-options">'."\n";
            echo '         <h3>'.__('HTML Compression Options', 'comet-cache').'</h3>'."\n";
            echo '         <p>'.__('You can <a href="https://github.com/websharks/html-compressor" target="_blank">learn more about all of these options here</a>.', 'comet-cache').'</p>'."\n";
            echo '         <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_compress_combine_head_body_css]" autocomplete="off">'."\n";
            echo '               <option value="1"'.selected($this->plugin->options['htmlc_compress_combine_head_body_css'], '1', false).'>'.__('Yes, combine CSS from &lt;head&gt; and &lt;body&gt; into fewer files.', 'comet-cache').'</option>'."\n";
            echo '               <option value="0"'.selected($this->plugin->options['htmlc_compress_combine_head_body_css'], '0', false).'>'.__('No, do not combine CSS from &lt;head&gt; and &lt;body&gt; into fewer files.', 'comet-cache').'</option>'."\n";
            echo '            </select></p>'."\n";
            echo '         <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_compress_css_code]" autocomplete="off">'."\n";
            echo '               <option value="1"'.selected($this->plugin->options['htmlc_compress_css_code'], '1', false).'>'.__('Yes, compress the code in any unified CSS files.', 'comet-cache').'</option>'."\n";
            echo '               <option value="0"'.selected($this->plugin->options['htmlc_compress_css_code'], '0', false).'>'.__('No, do not compress the code in any unified CSS files.', 'comet-cache').'</option>'."\n";
            echo '            </select></p>'."\n";
            echo '         <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_compress_combine_head_js]" autocomplete="off">'."\n";
            echo '               <option value="1"'.selected($this->plugin->options['htmlc_compress_combine_head_js'], '1', false).'>'.__('Yes, combine JS from &lt;head&gt; into fewer files.', 'comet-cache').'</option>'."\n";
            echo '               <option value="0"'.selected($this->plugin->options['htmlc_compress_combine_head_js'], '0', false).'>'.__('No, do not combine JS from &lt;head&gt; into fewer files.', 'comet-cache').'</option>'."\n";
            echo '            </select></p>'."\n";
            echo '         <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_compress_combine_footer_js]" autocomplete="off">'."\n";
            echo '               <option value="1"'.selected($this->plugin->options['htmlc_compress_combine_footer_js'], '1', false).'>'.__('Yes, combine JS footer scripts into fewer files.', 'comet-cache').'</option>'."\n";
            echo '               <option value="0"'.selected($this->plugin->options['htmlc_compress_combine_footer_js'], '0', false).'>'.__('No, do not combine JS footer scripts into fewer files.', 'comet-cache').'</option>'."\n";
            echo '            </select></p>'."\n";
            echo '         <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_compress_combine_remote_css_js]" autocomplete="off">'."\n";
            echo '               <option value="1"'.selected($this->plugin->options['htmlc_compress_combine_remote_css_js'], '1', false).'>'.__('Yes, combine CSS/JS from remote resources too.', 'comet-cache').'</option>'."\n";
            echo '               <option value="0"'.selected($this->plugin->options['htmlc_compress_combine_remote_css_js'], '0', false).'>'.__('No, do not combine CSS/JS from remote resources.', 'comet-cache').'</option>'."\n";
            echo '            </select></p>'."\n";
            echo '         <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_compress_js_code]" autocomplete="off">'."\n";
            echo '               <option value="1"'.selected($this->plugin->options['htmlc_compress_js_code'], '1', false).'>'.__('Yes, compress the code in any unified JS files.', 'comet-cache').'</option>'."\n";
            echo '               <option value="0"'.selected($this->plugin->options['htmlc_compress_js_code'], '0', false).'>'.__('No, do not compress the code in any unified JS files.', 'comet-cache').'</option>'."\n";
            echo '            </select></p>'."\n";
            echo '         <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_compress_inline_js_code]" autocomplete="off">'."\n";
            echo '               <option value="1"'.selected($this->plugin->options['htmlc_compress_inline_js_code'], '1', false).'>'.__('Yes, compress inline JavaScript snippets.', 'comet-cache').'</option>'."\n";
            echo '               <option value="0"'.selected($this->plugin->options['htmlc_compress_inline_js_code'], '0', false).'>'.__('No, do not compress inline JavaScript snippets.', 'comet-cache').'</option>'."\n";
            echo '            </select></p>'."\n";
            echo '         <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_compress_html_code]" autocomplete="off">'."\n";
            echo '               <option value="1"'.selected($this->plugin->options['htmlc_compress_html_code'], '1', false).'>'.__('Yes, compress (remove extra whitespace) in the final HTML code too.', 'comet-cache').'</option>'."\n";
            echo '               <option value="0"'.selected($this->plugin->options['htmlc_compress_html_code'], '0', false).'>'.__('No, do not compress the final HTML code.', 'comet-cache').'</option>'."\n";
            echo '            </select></p>'."\n";
            echo '         <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_amp_exclusions_enable]" autocomplete="off">'."\n";
            echo '               <option value="1"'.selected($this->plugin->options['htmlc_amp_exclusions_enable'], '1', false).'>'.__('Yes, auto-detect AMP (Accelerated Mobile Pages) and selectively disable incompatible features.', 'comet-cache').'</option>'."\n";
            echo '               <option value="0"'.selected($this->plugin->options['htmlc_amp_exclusions_enable'], '0', false).'>'.__('No, do not auto-detect AMP (Accelerated Mobile Pages) and selectively disable incompatible features', 'comet-cache').'</option>'."\n";
            echo '            </select></p>'."\n";
            echo '         <hr />'."\n";
            echo '         <h3>'.__('CSS Exclusion Patterns?', 'comet-cache').'</h3>'."\n";
            echo '         <p>'.__('Sometimes there are special cases when a particular CSS file should NOT be consolidated or compressed in any way. This is where you will enter those if you need to (one per line). Searches are performed against the <code>&lt;link href=&quot;&quot;&gt;</code> value, and also against the contents of any inline <code>&lt;style&gt;</code> tags (caSe insensitive). A wildcard <code>*</code> character can also be used when necessary; e.g., <code>xy*-framework</code> (where <code>*</code> = 0 or more characters that are NOT a slash <code>/</code>). Other special characters include: <code>**</code> = 0 or more characters of any kind, including <code>/</code> slashes; <code>^</code> = beginning of the string; <code>$</code> = end of the string. To learn more about this syntax, please see <a href ="http://cometcache.com/r/watered-down-regex-syntax/" target="_blank">this KB article</a>.', 'comet-cache').'</p>'."\n";
            echo '         <p><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_css_exclusions]" rows="5" spellcheck="false" class="monospace">'.format_to_edit($this->plugin->options['htmlc_css_exclusions']).'</textarea></p>'."\n";
            echo '         <p class="info" style="display:block;">'.__('<strong>Note:</strong> please remember that your entries here should be formatted as a line-delimited list; e.g., one exclusion pattern per line.', 'comet-cache').'</p>'."\n";
            echo '         <h3>'.__('JavaScript Exclusion Patterns?', 'comet-cache').'</h3>'."\n";
            echo '         <p>'.__('Sometimes there are special cases when a particular JS file should NOT be consolidated or compressed in any way. This is where you will enter those if you need to (one per line). Searches are performed against the <code>&lt;script src=&quot;&quot;&gt;</code> value, and also against the contents of any inline <code>&lt;script&gt;</code> tags (caSe insensitive). A wildcard <code>*</code> character can also be used when necessary; e.g., <code>xy*-framework</code> (where <code>*</code> = 0 or more characters that are NOT a slash <code>/</code>). Other special characters include: <code>**</code> = 0 or more characters of any kind, including <code>/</code> slashes; <code>^</code> = beginning of the string; <code>$</code> = end of the string. To learn more about this syntax, please see <a href ="http://cometcache.com/r/watered-down-regex-syntax/" target="_blank">this KB article</a>.', 'comet-cache').'</p>'."\n";
            echo '         <p><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_js_exclusions]" rows="5" spellcheck="false" class="monospace">'.format_to_edit($this->plugin->options['htmlc_js_exclusions']).'</textarea></p>'."\n";
            echo '         <p class="info" style="display:block;">'.__('<strong>Note:</strong> please remember that your entries here should be formatted as a line-delimited list; e.g., one exclusion pattern per line.', 'comet-cache').'</p>'."\n";
            echo '         <h3>'.__('URI Exclusions for HTML Compressor?', 'comet-cache').'</h3>'."\n";
            echo '         <p>'.__('When you enable HTML Compression above, you may want to prevent certain pages on your site from being cached by the HTML Compressor. This is where you will enter those if you need to (one per line). Searches are performed against the <a href="https://gist.github.com/jaswsinc/338b6eb03a36c048c26f" target="_blank" style="text-decoration:none;"><code>REQUEST_URI</code></a>; i.e., <code>/path/?query</code> (caSe insensitive). So, don\'t put in full URLs here, just word fragments found in the file path (or query string) is all you need, excluding the http:// and domain name. A wildcard <code>*</code> character can also be used when necessary; e.g., <code>/category/abc-followed-by-*</code> (where <code>*</code> = 0 or more characters that are NOT a slash <code>/</code>). Other special characters include: <code>**</code> = 0 or more characters of any kind, including <code>/</code> slashes; <code>^</code> = beginning of the string; <code>$</code> = end of the string. To learn more about this syntax, please see <a href ="http://cometcache.com/r/watered-down-regex-syntax/" target="_blank">this KB article</a>.', 'comet-cache').'</p>'."\n";
            echo '         <p><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_uri_exclusions]" rows="5" spellcheck="false" class="monospace">'.format_to_edit($this->plugin->options['htmlc_uri_exclusions']).'</textarea></p>'."\n";
            echo '         <p class="info">'.__('<strong>Tip:</strong> let\'s use this example URL: <code>http://www.example.com/post/example-post-123</code>. To exclude this URL, you would put this line into the field above: <code>/post/example-post-123</code>. Or, you could also just put in a small fragment, like: <code>example</code> or <code>example-*-123</code> and that would exclude any URI containing that word fragment.', 'comet-cache').'</p>'."\n";
            echo '         <p class="info">'.__('<strong>Note:</strong> please remember that your entries here should be formatted as a line-delimited list; e.g., one exclusion pattern per line.', 'comet-cache').'</p>'."\n";
            echo '         <hr />'."\n";
            echo '         <h3>'.__('HTML Compression Cache Expiration', 'comet-cache').'</h3>'."\n";
            echo '         <p><input type="text" name="'.esc_attr(GLOBAL_NS).'[saveOptions][htmlc_cache_expiration_time]" value="'.esc_attr($this->plugin->options['htmlc_cache_expiration_time']).'" /></p>'."\n";
            echo '         <p class="info" style="display:block;">'.__('<strong>Tip:</strong> the value that you specify here MUST be compatible with PHP\'s <a href="http://php.net/manual/en/function.strtotime.php" target="_blank" style="text-decoration:none;"><code>strtotime()</code></a> function. Examples: <code>2 hours</code>, <code>7 days</code>, <code>6 months</code>, <code>1 year</code>.', 'comet-cache').'</p>'."\n";
            echo '         <p>'.sprintf(__('<strong>Note:</strong> This does NOT impact the overall cache expiration time that you configure with %1$s. It only impacts the sub-routines provided by the HTML Compressor. In fact, this expiration time is mostly irrelevant. The HTML Compressor uses an internal checksum, and it also checks <code>filemtime()</code> before using an existing cache file. The HTML Compressor class also handles the automatic cleanup of your cache directories to keep it from growing too large over time. Therefore, unless you have VERY little disk space there is no reason to set this to a lower value (even if your site changes dynamically quite often). If anything, you might like to increase this value which could help to further reduce server load. You can <a href="https://github.com/websharks/HTML-Compressor" target="_blank">learn more here</a>. We recommend setting this value to at least double that of your overall %1$s expiration time.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            echo '      </div>'."\n";
            echo '   </div>'."\n";

            echo '</div>'."\n";
        }
    }
}
