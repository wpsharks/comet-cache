/**
* Core JavaScript routines for administrative menu pages.
*
* This is the development version of the code.
* Which ultimately produces menu-pages-min.js.
*
* This file is included with all WordPress® themes/plugins by WebSharks, Inc.
*
* Copyright: © 2009-2011
* {@link http://www.websharks-inc.com/ WebSharks, Inc.}
* ( coded in the USA )
*
* Released under the terms of the GNU General Public License.
* You should have received a copy of the GNU General Public License,
* along with this software. In the main directory, see: /licensing/
* If not, see: {@link http://www.gnu.org/licenses/}.
*
* @package WebSharks\Menu Pages
* @since x.xx
*/
/*
These routines address common layout styles for menu pages.
*/
jQuery(document).ready (function($)
	{
		var $groups = $('div.ws-menu-page-group'); /* Query groups. */
		$groups.each (function(index) /* Go through each group, one at a time. */
			{
				var $this = $(this), ins = '<ins>+</ins>', $group = $this, title = $.trim ($group.attr ('title'));
				/**/
				var $header = $('<div class="ws-menu-page-group-header">' + ins + title + '</div>');
				/**/
				$header.css ({'z-index': 100 - index}); /* Stack them sequentially, top to bottom. */
				/**/
				$header.insertBefore ($group), $group.hide (), $header.click (function()
					{
						var $this = $(this), $ins = $('ins', $this), $group = $this.next ();
						/**/
						if ($group.css ('display') === 'none')
							$this.addClass ('open'), $ins.html ('-'), $group.show ();
						/**/
						else /* Else remove open class and hide this group. */
							$this.removeClass ('open'), $ins.html ('+'), $group.hide ();
						/**/
						return false;
					});
				/**/
				if ($groups.length > 1 && index === 0) /* These are the buttons for showing/hiding all groups. */
					{
						$('<div class="ws-menu-page-groups-show">+</div>').insertBefore ($header).click (function()
							{
								$('div.ws-menu-page-group-header').each (function()
									{
										var $this = $(this), $ins = $('ins', $this), $group = $this.next ();
										/**/
										$this.addClass ('open'), $ins.html ('-'), $group.show ();
										/**/
										return; /* Return for uniformity. */
									});
								/**/
								return false;
							});
						/**/
						$('<div class="ws-menu-page-groups-hide">-</div>').insertBefore ($header).click (function()
							{
								$('div.ws-menu-page-group-header').each (function()
									{
										var $this = $(this), $ins = $('ins', $this), $group = $this.next ();
										/**/
										$this.removeClass ('open'), $ins.html ('+'), $group.hide ();
										/**/
										return; /* Return for uniformity. */
									});
								/**/
								return false;
							});
					}
				/**/
				if ($group.attr ('default-state') === 'open')
					$header.trigger ('click');
				/**/
				return; /* Return for uniformity. */
			});
		/**/
		if ($groups.length > 1) /* We only apply these special margins when there are multiple groups. */
			{
				$('div.ws-menu-page-group-header:first').css ({'margin-right': '140px'});
				$('div.ws-menu-page-group:first').css ({'margin-right': '145px'});
			}
		/**/
		$('div.ws-menu-page-r-group-header').click (function()
			{
				var $this = $(this), $group = $this.next ('div.ws-menu-page-r-group');
				/**/
				if ($group.css ('display') === 'none')
					$('ins', $this).html ('-'), $this.addClass ('open'), $group.show ();
				/**/
				else /* Otherwise, we hide it. */
					{
						$('ins', $this).html ('+'), $this.removeClass ('open');
						$group.hide ();
					}
				/**/
				return false;
			});
		/**/
		$('div.ws-menu-page-group-header:first, div.ws-menu-page-r-group-header:first').css ({'margin-top': '0'});
		$('div.ws-menu-page-group > div.ws-menu-page-section:first-child > h3').css ({'margin-top': '0'});
		$('div.ws-menu-page-readme > div.readme > div.section:last-child').css ({'border-bottom-width': '0'});
		/**/
		$('input.ws-menu-page-media-btn').filter (function() /* Only those that have a rel attribute. */
			{
				return ($(this).attr ('rel')) ? true : false; /* Must have rel targeting an input id. */
			})/**/
		.click (function() /* Attach click events to media buttons with send_to_editor(). */
			{
				var $this = $(this); /* Record a reference to the media button here. */
				/**/
				window.send_to_editor = function(html) /* Works with Thickbox. */
					{
						var $inp, $txt, rel = $.trim ($this.attr ('rel'));
						/**/
						if (rel && ($inp = $('input#' + rel)).length > 0) /* And input field? */
							{
								var oBg = $inp.css ('background-color'), src = $.trim ($(html).attr ('src'));
								src = (!src) ? $.trim ($('img', html).attr ('src')) : src;
								/**/
								$inp.val (src), $inp.css ({'background-color': '#FFFFCC'}), setTimeout(function()
									{
										$inp.css ({'background-color': oBg});
									}, 2000);
								/**/
								tb_remove (); /* Close. */
								/**/
								return; /* Return for uniformity. */
							}
						else if (rel && ($txt = $('textarea#' + rel)).length > 0) /* Textarea? */
							{
								var oBg = $txt.css ('background-color'), src = $.trim ($(html).attr ('src'));
								src = (!src) ? $.trim ($('img', html).attr ('src')) : src;
								/**/
								$txt.val ($.trim ($txt.val ()) + '\n' + src), $txt.css ({'background-color': '#FFFFCC'}), setTimeout(function()
									{
										$txt.css ({'background-color': oBg});
									}, 2000);
								/**/
								tb_remove (); /* Close. */
								/**/
								return; /* Return for uniformity. */
							}
					};
				/**/
				tb_show('', './media-upload.php?type=image&TB_iframe=true');
				/**/
				return false;
			});
		/**/
		$('form#ws-mlist-form').submit (function()
			{
				var errors = ''; /* Intialize string of errors. */
				/**/
				if (!$.trim ($('input#ws-mlist-fname').val ()))
					errors += 'First Name missing, please try again.\n\n';
				/**/
				if (!$.trim ($('input#ws-mlist-lname').val ()))
					errors += 'Last Name missing, please try again.\n\n';
				/**/
				if (!$.trim ($('input#ws-mlist-email').val ()))
					errors += 'Email missing, please try again.\n\n';
				/**/
				else if (!$('input#ws-mlist-email').val ().match (/^([a-z_~0-9\+\-]+)(((\.?)([a-z_~0-9\+\-]+))*)(@)([a-z0-9]+)(((-*)([a-z0-9]+))*)(((\.)([a-z0-9]+)(((-*)([a-z0-9]+))*))*)(\.)([a-z]{2,6})$/i))
					errors += 'Invalid email address, please try again.\n\n';
				/**/
				if (errors = $.trim (errors))
					{
						alert('— Oops, you missed something: —\n\n' + errors);
						/**/
						return false;
					}
				/**/
				return true;
			});
	});