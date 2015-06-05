(function($)
{
	'use strict'; // Standards.

	var plugin = {namespace: 'zencache'},
		$window = $(window), $document = $(document);

	plugin.hideAJAXResponseTimeoutDelay = 0;

	plugin.removeAJAXResponse = function()
	{
		clearTimeout(plugin.hideAJAXResponseTimeoutDelay),
			$('#' + plugin.namespace + '-ajax-response').remove();
	};
	plugin.showAJAXResponse = function()
	{
		clearTimeout(plugin.hideAJAXResponseTimeoutDelay),
			$('#' + plugin.namespace + '-ajax-response') // Animate this.
				.animate({'top': '50px'}, 400, function(){ plugin.hideAJAXResponse(null, true); });
	};
	plugin.hideAJAXResponse = function(event, delay)
	{
		clearTimeout(plugin.hideAJAXResponseTimeoutDelay);

		if(typeof delay === 'boolean' && delay) // Wait?
		{
			plugin.hideAJAXResponseTimeoutDelay = setTimeout(plugin.hideAJAXResponse, 5000);
			return; // Stop here; wait for delay.
		}
		$('#' + plugin.namespace + '-ajax-response') // Animate.
			.animate({'top': '-100%'}, 200, plugin.removeAJAXResponse);
	};
	plugin.wipeCache = function()
	{
		var postVars = {}; // HTTP post vars.
		postVars[plugin.namespace] = {ajaxWipeCache: '1'};
		postVars['_wpnonce'] = plugin.vars._wpnonce;

		var $wipe = $('#wp-admin-bar-' + plugin.namespace + '-wipe > a');

		plugin.removeAJAXResponse(), $wipe.parent().addClass('wiping'), $wipe.attr('disabled', 'disabled'),
			$.post(plugin.vars.ajaxURL, postVars, function(data)
			{
				plugin.removeAJAXResponse(), $wipe.parent().removeClass('wiping'), $wipe.removeAttr('disabled');
				var $response = $('<div id="' + plugin.namespace + '-ajax-response" class="' + plugin.namespace + '-wipe">' + data + '</div>');
				$('body').append($response), plugin.showAJAXResponse();
			});
	};
	plugin.clearCache = function()
	{
		var postVars = {}; // HTTP post vars.
		postVars[plugin.namespace] = {ajaxClearCache: '1'};
		postVars['_wpnonce'] = plugin.vars._wpnonce;

		var $clear = $('#wp-admin-bar-' + plugin.namespace + '-clear > a');

		plugin.removeAJAXResponse(), $clear.parent().addClass('clearing'), $clear.attr('disabled', 'disabled'),
			$.post(plugin.vars.ajaxURL, postVars, function(data)
			{
				plugin.removeAJAXResponse(), $clear.parent().removeClass('clearing'), $clear.removeAttr('disabled');
				var $response = $('<div id="' + plugin.namespace + '-ajax-response" class="' + plugin.namespace + '-clear">' + data + '</div>');
				$('body').append($response), plugin.showAJAXResponse();
			});
	};
	plugin.onReady = function() // DOM ready event handler.
	{
		plugin.vars = $('#' + plugin.namespace + '-vars').data('json');

		$('#wp-admin-bar-' + plugin.namespace + '-wipe > a').on('click', plugin.wipeCache);
		$('#wp-admin-bar-' + plugin.namespace + '-clear > a').on('click', plugin.clearCache);

		$document.on('click', '#' + plugin.namespace + '-ajax-response', plugin.hideAJAXResponse);
	};
	$document.ready(plugin.onReady); // On DOM ready.
})(jQuery);
