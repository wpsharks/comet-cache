<?php
namespace WebSharks\ZenCache;
/*
 * Placeholder for backwards compatibility with upgrades from v150629.
 * See https://github.com/websharks/zencache/issues/524
 */

define(__NAMESPACE__.'\\GLOBAL_NS', 'zencache');
define(__NAMESPACE__.'\\IS_PRO', '0');

class AdvCacheBackCompat
{
   public static function qcRequestVars()
   {
     return;
   }
   public static function quickCacheConstants()
   {
     return;
   }
}

class AdvancedCache{}
