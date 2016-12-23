(function ($) {
  'use strict'; // Standards.

  var plugin = {
      namespace: 'comet_cache'
    },
    $window = $(window),
    $document = $(document);

  plugin.onReady = function () {
    
    plugin.$menuPage = $('#plugin-menu-page');
    plugin.vars = window[plugin.namespace + '_menu_page_vars'];

    $('.plugin-menu-page-panel-heading', plugin.$menuPage).on('click', plugin.togglePanel);
    $('.plugin-menu-page-panels-open', plugin.$menuPage).on('click', plugin.toggleAllPanelsOpen);
    $('.plugin-menu-page-panels-close', plugin.$menuPage).on('click', plugin.toggleAllPanelsClose);

    $('[data-action]', plugin.$menuPage).on('click', plugin.doDataAction);
    $('[data-toggle-target]', plugin.$menuPage).on('click', plugin.doDataToggleTarget);

    $('select[name$="_enable\\]"], select[data-toggle~="enable-disable"]', plugin.$menuPage).not('.-no-if-enabled').on('change', plugin.enableDisable).trigger('change');

    

    

    

    
  };

  plugin.toggleAllPanelsOpen = function (event) {
    plugin.preventDefault(event);

    $('.plugin-menu-page-panel-heading', plugin.$menuPage).addClass('open')
      .next('.plugin-menu-page-panel-body').addClass('open');
  };

  plugin.toggleAllPanelsClose = function (event) {
    plugin.preventDefault(event);

    $('.plugin-menu-page-panel-heading', plugin.$menuPage).removeClass('open')
      .next('.plugin-menu-page-panel-body').removeClass('open');
  };

  plugin.togglePanel = function (event) {
    plugin.preventDefault(event);

    $(this).toggleClass('open') // Heading and body.
      .next('.plugin-menu-page-panel-body').toggleClass('open');
  };

  plugin.doDataAction = function (event) {
    plugin.preventDefault(event);

    var $this = $(this),
      data = $this.data();
    if (typeof data.confirmation !== 'string' || confirm(data.confirmation))
      location.href = data.action;
  };

  plugin.enableDisable = function (event) {
    var $this = $(this),
      thisValue = $this.val(),
      thisName = $this.attr('name'),
      thisEnabledStrings = String($this.data('enabledStrings') || '1,2,3,4,5').split(/,+/),
      enabled = $.inArray(thisValue, thisEnabledStrings) !== -1,

      $thisPanelBody = $this.closest('.plugin-menu-page-panel-body'),

      targetIfEnabled = $this.data('target'), // Optional specifier.
      $targetIfEnableds = targetIfEnabled ? $(targetIfEnabled, $thisPanelBody)
      .filter('.plugin-menu-page-panel-if-enabled') : null,

      $parentIfEnabled = $this.closest('.plugin-menu-page-panel-if-enabled'),
      $childIfEnableds = $parentIfEnabled.find('> .plugin-menu-page-panel-if-enabled'),

      $panelIfEnableds = $thisPanelBody.find('> .plugin-menu-page-panel-if-enabled');

    if (enabled) {
      if (targetIfEnabled) {
        $targetIfEnableds.css('opacity', 1).find(':input').removeAttr('readonly');
      } else if ($parentIfEnabled.length) {
        $childIfEnableds.css('opacity', 1).find(':input').removeAttr('readonly');
      } else {
        $panelIfEnableds.css('opacity', 1).find(':input').removeAttr('readonly');
      }
    } else {
      if (targetIfEnabled) {
        $targetIfEnableds.css('opacity', 0.4).find(':input').attr('readonly', 'readonly');
      } else if ($parentIfEnabled.length) {
        $childIfEnableds.css('opacity', 0.4).find(':input').attr('readonly', 'readonly');
      } else {
        $panelIfEnableds.css('opacity', 0.4).find(':input').attr('readonly', 'readonly');
      }
    }
  };

  plugin.doDataToggleTarget = function (event) {
    plugin.preventDefault(event);

    var $this = $(this),
      $target = $($this.data('toggleTarget'));

    if ($target.is(':visible')) {
      $target.hide();
      $this.find('.si')
        .removeClass('si-eye-slash')
        .addClass('si-eye');
    } else {
      $target.show();
      $this.find('.si')
        .removeClass('si-eye')
        .addClass('si-eye-slash');
    }
  };

  plugin.handleCacheClearAdminBarOpsChange = function (event) {
    var $select = $(this),
      val = $select.val(),
      $ss = $('.-clear-cache-ops-ss', plugin.$menuPage);
    $ss.attr('src', $ss.attr('src').replace(/ops[0-9]\-ss\.png$/, 'ops' + val + '-ss.png'));
  };

  

  plugin.handleCdnHostsChange = function (event) {
    var $cdnHosts = $(this),
      $cdnHost = $('input[name$="\[cdn_host\]"]', plugin.$menuPage);

    if ($.trim($cdnHosts.val())) {
      if ($cdnHost.val()) {
        $cdnHost.data('hiddenValue', $cdnHost.val());
      }
      $cdnHost.attr('disabled', 'disabled').val('');
    } else {
      if (!$cdnHost.val()) {
        $cdnHost.val($cdnHost.data('hiddenValue'));
      }
      $cdnHost.removeAttr('disabled');
      $cdnHosts.val('');
    }
  };

  

  

  

  plugin.bytesToSizeLabel = function (bytes, decimals) {
    if (typeof bytes !== 'number' || bytes <= 1) {
      return bytes === 1 ? '1 byte' : '0 bytes';
    } // See: <http://jas.xyz/1gOCXob>
    if (typeof decimals !== 'number' || decimals <= 0) {
      decimals = 0; // Default; integer.
    }
    var base = 1024, // 1 Kilobyte base (binary).
      baseLog = Math.floor(Math.log(bytes) / Math.log(base)),
      sizes = ['bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
      sizeInBaseLog = (bytes / Math.pow(base, baseLog));

    return sizeInBaseLog.toFixed(decimals) + ' ' + sizes[baseLog];
  };

  plugin.numberFormat = function (number, decimals) {
    if (typeof number !== 'number') {
      return String(number);
    } // See: <http://jas.xyz/1JlFD9P>
    if (typeof decimals !== 'number' || decimals <= 0) {
      decimals = 0; // Default; integer.
    }
    return number.toFixed(decimals).replace(/./g, function (m, o, s) {
      return o && m !== '.' && ((s.length - o) % 3 === 0) ? ',' + m : m;
    });
  };

  plugin.escHtml = function (string) {
    var entityMap = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#39;'
    };
    return String(string).replace(/[&<>"']/g, function (specialChar) {
      return entityMap[specialChar];
    });
  };

  plugin.preventDefault = function (event, stop) {
    if (!event) {
      return; // Not possible.
    }
    event.preventDefault(); // Always.

    if (stop) {
      event.stopImmediatePropagation();
    }
  };
  $document.ready(plugin.onReady); // On DOM ready.

})(jQuery);
