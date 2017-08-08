<?php
namespace WebSharks\CometCache\Classes\MenuPage\Options;

use WebSharks\CometCache\Classes;

/**
 * Options section.
 *
 * @since 17xxxx Refactor menu pages.
 */
class Memcached extends Classes\AbsBase
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
            echo '      <i class="si si-rocket"></i> '.__('RAM / Memcached', 'comet-cache')."\n";
            echo '   </a>'."\n";

            echo '   <div class="plugin-menu-page-panel-body clearfix">'."\n";
            echo '      <i class="si si-rocket si-4x" style="float:right; margin: 0 0 0 25px;"></i>'."\n";
            echo '      <h3>'.__('Enable Memcached for a Faster RAM-Based Cache?', 'comet-cache').'</h3>'."\n";
            echo '      <p>'.sprintf(__('This requires a server with <a href="https://cometcache.com/r/memcached/" target="_blank">Memcached</a> enabled, or a service like <a href="https://cometcache.com/r/elasticache/" target="_blank">AWS ElastiCache</a> that you pay for. Memcached is a high-performance, distributed memory object caching system, generic in nature, but intended for use in speeding up dynamic web applications by alleviating database load &amp; disk I/O. In the context of %1$s, Memcached is an effective tool that can be used to avoid repeated disk reads. By adding a Memcached server to your %1$s configuration, %1$s is able to store the cached copy of each page in RAM, and then serve the cache to future visitors in a faster, more efficient, and less CPU-intensive way — while still obeying your cache expiration time and other configuration options. In short, Memcached makes your site even faster. Over time (i.e., as RAM is used to store cache entries) it will also reduce load on your server.', 'comet-cache'), esc_html(NAME)).'</p>'."\n";
            echo '      <p class="info">'.__('<strong>Logged-In Users:</strong> User-specific cache entries for logged-in users are not stored in RAM. At this time, Memcached works to enhance speed for non-logged-in users only.', 'comet-cache').'</p>'."\n";
            echo '      <p><select name="'.esc_attr(GLOBAL_NS).'[saveOptions][memcached_enable]" data-toggle="enable-disable" data-enabled-strings="1" data-target=".-memcached-options">'."\n";
            echo '            <option value="0"'.(!IS_PRO ? '' : selected($this->plugin->options['memcached_enable'], '0', false)).'>'.__('No, serve cache files from disk (default behavior).', 'comet-cache').'</option>'."\n";
            echo '            <option value="1"'.(!IS_PRO ? ' selected' : selected($this->plugin->options['memcached_enable'], '1', false)).'>'.__('Yes, serve cached pages from RAM via Memcached (recommended).', 'comet-cache').'</option>'."\n";
            echo '         </select></p>'."\n";

            echo '      <div class="plugin-menu-page-panel-if-enabled -memcached-options"><hr />'."\n";
            echo '        <h3>'.__('Memcached Server Pool', 'comet-cache').'</h3>'."\n";
            echo '        <p>'.__('You can enter one or more Memcached servers in line-delimited format. Note that one line (one Memcached server) is adequate for most sites. Each line must contain a colon-delimited server hostname (or IP address), the port number, and then an arbitrary priority that allows you to give weight to one server over another. As an example, <code>127.0.0.1:11211:1</code> (just one line) would work for a VPS that runs a self-hosted Memcached server locally on the <code>127.0.0.1</code> loopback address.', 'comet-cache').'</p>'."\n";
            echo '        <p>'.__('We recommend a Memcached server with a minimum of 512MB of RAM available and generally no more than 5GB is necessary. That said, Memcached is very resilient and it will automatically evict older cache entries when it runs out of space. Having a smaller Memcached server just means you have a smaller capacity to hold all possible cache entries. In other words, you never have to worry about your Memcached server running out of space as your cache usage increases. This is because Memached never really runs out of space. Instead, it shifts things around automatically so it can hold as much as possible in RAM (given the memory available to it) at any given time. The larger your Memcached server is, the better, but size is never a strict requirement when it comes to Memcached.', 'comet-cache').'</p>'."\n";
            echo '        <p><em>'.sprintf(__('Note that %1$s auto-prefixes all of its Memcached cache keys internally with a software-specific namespace. So while you may want to use a dedicated Memcached instance for %1$s, that\'s not absolutely necessary. If you already have a Memcached instance running for another purpose, %1$s can easily take advantage of that instance, even if other processes are using it also.', 'comet-cache'), esc_html(NAME)).'</em></p>'."\n";
            echo '        <p><textarea name="'.esc_attr(GLOBAL_NS).'[saveOptions][memcached_servers]" rows="5" spellcheck="false" class="monospace" placeholder="127.0.0.1:11211:1">'.format_to_edit($this->plugin->options['memcached_servers']).'</textarea></p>'."\n";
            echo '      </div>'."\n";
            echo '   </div>'."\n";

            echo '</div>'."\n";
        }
    }
}
