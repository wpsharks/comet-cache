(function ($) {
  'use strict'; // Standards.

  var plugin = {
      namespace: 'comet_cache'
    },
    $window = $(window),
    $document = $(document);

  plugin.onReady = function () {
    
    plugin.hideAJAXResponseTimeout = null;
    plugin.vars = $('#' + plugin.namespace + '-admin-bar-vars').data('json');

    $('#wp-admin-bar-' + plugin.namespace + '-wipe > a').on('click', plugin.wipeCache);
    $('#wp-admin-bar-' + plugin.namespace + '-clear > a').on('click', plugin.clearCache);
    
    $document.on('click', '.' + plugin.namespace + '-ajax-response', plugin.hideAJAXResponse);
    
  };

  plugin.wipeCache = function (event) {
    plugin.preventDefault(event);
    plugin.statsData = null;

    var postVars = {
      _wpnonce: plugin.vars._wpnonce
    }; // HTTP post vars.
    postVars[plugin.namespace] = {
      ajaxWipeCache: '1'
    };
    var $wipe = $('#wp-admin-bar-' + plugin.namespace + '-wipe > a');
    var $clearOptionsLabel = $('#wp-admin-bar-' + plugin.namespace + '-clear-options-wrapper .-label');
    var $clearOptions = $('#wp-admin-bar-' + plugin.namespace + '-clear-options-wrapper .-options');

    plugin.removeAJAXResponse();
    $wipe.parent().addClass('-processing');
    $wipe.add($clearOptions.find('a')).attr('disabled', 'disabled');

    $.post(plugin.vars.ajaxURL, postVars, function (data) {
      plugin.removeAJAXResponse();
      $wipe.parent().removeClass('-processing');
      $wipe.add($clearOptions.find('a')).removeAttr('disabled');

      var $response = $('<div class="' + plugin.namespace + '-ajax-response -wipe">' + data + '</div>');
      $('body').append($response); // Append response.
      plugin.showAJAXResponse(); // Show response.
    });
  };

  plugin.clearCache = function (event, options) {
    plugin.preventDefault(event);
    

    var postVars = {
      _wpnonce: plugin.vars._wpnonce
    }; // HTTP post vars.

    var isClearOption = false;
    
     {
      postVars[plugin.namespace] = {
        ajaxClearCache: '1'
      };
    }
    var $clear = $('#wp-admin-bar-' + plugin.namespace + '-clear > a');
    
    plugin.removeAJAXResponse();

    if (isClearOption && $clearOptionsLabel.length) {
      $clearOptionsLabel.addClass('-processing');
    } else {
      $clear.parent().addClass('-processing');
    }
    $clear.add($clearOptions.find('a')).attr('disabled', 'disabled');

    $.post(plugin.vars.ajaxURL, postVars, function (data) {
      plugin.removeAJAXResponse();

      if (isClearOption && $clearOptionsLabel.length) {
        $clearOptionsLabel.removeClass('-processing');
      } else {
        $clear.parent().removeClass('-processing');
      }
      $clear.add($clearOptions.find('a')).removeAttr('disabled');

      var $response = $('<div class="' + plugin.namespace + '-ajax-response -clear">' + data + '</div>');
      $('body').append($response); // Append response.
      plugin.showAJAXResponse(); // Show response.
    });
  };
  

  plugin.showAJAXResponse = function () {
    clearTimeout(plugin.hideAJAXResponseTimeout);

    $('.' + plugin.namespace + '-ajax-response')
      .off(plugin.animationEndEvents) // Reattaching below.
      .on(plugin.animationEndEvents, function () { // Reattach.
        plugin.hideAJAXResponseTimeout = setTimeout(plugin.hideAJAXResponse, 2500);
      })
      .addClass(plugin.namespace + '-admin-bar-animation-zoom-in-down').show()
      .on('mouseover', function () { // Do not auto-hide if hovered.
        clearTimeout(plugin.hideAJAXResponseTimeout);
        $(this).addClass('-hovered');
      });
  };

  plugin.hideAJAXResponse = function (event) {
    plugin.preventDefault(event);

    clearTimeout(plugin.hideAJAXResponseTimeout);

    $('.' + plugin.namespace + '-ajax-response')
      .off(plugin.animationEndEvents) // Reattaching below.
      .on(plugin.animationEndEvents, function () { // Reattach.
        plugin.removeAJAXResponse(); // Remove completely.
      })
      .addClass(plugin.namespace + '-admin-bar-animation-zoom-out-up');
  };

  plugin.removeAJAXResponse = function () {
    clearTimeout(plugin.hideAJAXResponseTimeout);

    $('.' + plugin.namespace + '-ajax-response')
      .off(plugin.animationEndEvents).remove();
  };
  
  plugin.animationEndEvents = // All vendor prefixes.
    'webkitAnimationEnd mozAnimationEnd msAnimationEnd oAnimationEnd animationEnd';

  $document.ready(plugin.onReady); // On DOM ready.

})(jQuery);
