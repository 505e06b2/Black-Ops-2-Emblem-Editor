/**
 * Ajax auto-refreshing articles
 *
 * Original by pcj of Wowpedia
 *
 * Maintenance, cleanup, style and bug fixes by:
 * - Grunny         (https://c.wikia.com/wiki/User:Grunny)
 * - Kangaroopower  (https://c.wikia.com/wiki/User:Kangaroopower)
 * - Cqm            (https://c.wikia.com/wiki/User:Cqm)
 *
 * The script follows Wikia JS guidelines or MediaWiki coding standards where the Wikia guidelines do not specify:
 * - <https://github.com/Wikia/guidelines/blob/master/JavaScript/CodingConventions.md>
 * - <https://www.mediawiki.org/wiki/Manual:Coding_conventions/JavaScript>
 *
 * Editing guidelines:
 * - This script has extremely high usage across Wikia, please do not deploy without extensive testing.
 * - Be aware that this script must be ES3 compliant due to the minifier used by ResourceLoader. 
 * - Due to the historic high usage of this script, several decisions have been made to keep it stable. As such, please pay attention to comments relating to this.
 */

/* jshint

	bitwise:true, camelcase:true, curly:true, eqeqeq:true, latedef:true, maxdepth:3,
	maxlen:120, newcap:true, noarg:true, noempty:true, nonew:true, onevar:true,
	plusplus:false, quotmark:single, undef:true, unused:true, strict:true, trailing:true,

	asi:false, boss:false, debug:false, eqnull:false, evil:false, expr:false,
	lastsemic:false, loopfunc:false, moz:false, proto:false, scripturl:false,

	browser:true, devel:false, jquery:true
*/

/*global mediaWiki:true, Wikia:true, importArticle:true */

;(function (window, $, mw, Wikia) {
	'use strict';

	var config = mw.config.get([
			'stylepath',
			'wgAction',
			'wgCanonicalSpecialPageName',
			'wgPageName',
			'wgUserLanguage'
		]),
		// use common file as it's very likely to be already cached by user
		// used in oasis sidebar loading, preview modal, etc.
		ajaxIndicator = window.ajaxIndicator || config.stylepath + '/common/images/ajax.gif',
		ajaxTimer,
		ajRefresh = window.ajaxRefresh || 60000,
		ajPages = window.ajaxPages || [],
		// WikiActivity should not be added here; use the configuration options
		// on your local wiki to add AjaxRC to your local WikiActivity page
		ajSpecialPages = window.ajaxSpecialPages || ['Recentchanges'],
		// don't load on these values of wgAction
		// @todo check if markpatrolled should be here
		disallowActions = [
			'delete',
			'edit',
			'protect',
			'revisiondelete'
		],
		// if there's a hash on the end of the url, jquery strips it
		// however, location.href keeps the hash in a url
		// so the callbacks for ajaxsend and ajaxcomplete won't fire
		// just by comparing settings.url to location.href
		href = location.href.replace(/#[\S]*/, ''),
		i18n;

	/**
	 * Get a localised message, if it exists as well as allowing it to be
	 * overridden by per-wiki/user config.
	 *
	 * @param msgKey {string} The name of the message in the i18n object above.
	 * @param globalKey {string} The name of the message in script configuration.
	 *
	 * @return {string} The localised/cutomised message.
	 */
	function getMessage(msgKey, globalKey) {
		// older versions rely on user-supplied translations mixed in with customisations
		// so check that first
		if (globalKey && typeof window[globalKey] === 'string') {
			return window[globalKey];
		}

		return i18n.msg('ajaxrc-' + msgKey).plain();
	}

	/**
	 * Set the toggle status in Local Storage.
	 *
	 * @return {boolean} The updated status of the toggle.
	 */
	function storage(setTo) {
		if (localStorage.getItem('AjaxRC-refresh') === null) {
			localStorage.setItem('AjaxRC-refresh', true);
		}

		// workaround for setTo being a jquery event on the initial load
		if (setTo === true || setTo === false) {
			localStorage.setItem('AjaxRC-refresh', setTo);
		}

		return JSON.parse(localStorage.getItem('AjaxRC-refresh'));
	}

	/**
	 * Get the element to add the AjaxRC checkbox to.
	 *
	 * @return {jQuery.object|boolean} A jQuery object representing the element or
	 *	 false if no suitable element was found.
	 */
	function getAppTo() {
		var $ret;

		// monobook
		$ret = $('.firstHeading');

		if ($ret.length) {
			return $ret;
		}

		// most oasis pages
		$ret = $('.WikiaPage .page-header__main');

		if ($ret.length) {
			return $ret;
		}

		return false;
	}

	/**
	 * Does the actual refresh
	 */
	function loadPageData() {
		var $temp = $('<div>');

		$temp.load(href + ' #mw-content-text', function() {
			var $newContent = $temp.children('#mw-content-text');

			if ($newContent.length) {
				$('#mw-content-text').replaceWith($newContent);
				// re-set mw.util.$content for any scripts that may use it
				mw.util.$content = $newContent;
			}

			ajaxTimer = setTimeout(loadPageData, ajRefresh);
		});

		$temp.remove();
	}

	/**
	 * Turn refresh on and off by toggling the checkbox
	 */
	function toggleAjaxReload() {
		if ($('#ajaxToggle').prop('checked')) {
			storage(true);
			loadPageData();
		} else {
			storage(false);
			clearTimeout(ajaxTimer);
		}
	}

	/**
	 * Main function to start the Auto-refresh process
	 */
	function preloadAJAXRL() {
		var $appTo = getAppTo(),
			$checkbox = $('<span>')
				.attr('id', 'ajaxRefresh')
				.css({
					'font-size': 'xx-small',
					'line-height': '100%',
					'margin-left': '5px'
				})
				.append(
					$('<label>')
						.attr({
							id: 'ajaxToggleText',
							// for for RL to comply with es3 rules
							'for': 'ajaxToggle',
							title: getMessage('refresh-hover', 'AjaxRCRefreshHoverText')
						})
						.text(getMessage('refresh-text', 'AjaxRCRefreshText') + ':')
						.css({
							'border-bottom': '1px dotted',
							cursor: 'help'
						}),
					$('<input>')
						.attr({
							id: 'ajaxToggle',
							type: 'checkbox'
						})
						.css('margin-bottom', 0),
					$('<span>')
						.attr('id', 'ajaxLoadProgress')
						// I think this is for a firefox bug
						// (because .hide() didn't do it properly)
						.css('display', 'none')
						.append(
							$('<img>')
								.attr({
									alt: getMessage('load-status-alt', false),
									src: ajaxIndicator
								})
								.css({
									'vertical-align': 'baseline',
									float: 'none',
									border: 0
								})
						)
				),
			$throbber;

		// fallback for pages with profile masthead
		if ($appTo === false) {
			$('#WikiaArticle').prepend($checkbox);
		} else {
			$appTo.append($checkbox);
		}

		$throbber = $checkbox.find('#ajaxLoadProgress');

		$(document).ajaxSend(function(_, _2, settings) {
			if (href === settings.url) {
				$throbber.show();
			}
		}).ajaxComplete(function(_, _2, settings) {
			var $collapsibleElements = $('#mw-content-text').find('.mw-collapsible'),
				ajCallAgain = window.ajaxCallAgain || [],
				i;

			if (href === settings.url) {
				$throbber.hide();

				if ($collapsibleElements.length) {
					$collapsibleElements.makeCollapsible();
				}

				if (config.wgCanonicalSpecialPageName === 'Recentchanges') {
					mw.special.recentchanges.init();

					if ($('.mw-recentchanges-table').find('.WikiaDropdown').length) {
						Wikia.RecentChanges.init();
					}
				}

				if (config.wgCanonicalSpecialPageName === 'WikiActivity') {
					window.WikiActivity.init();
				}

				for (i = 0; i < ajCallAgain.length; i++) {
					// check item is a function before calling it to avoid errors
					if ($.isFunction(ajCallAgain[i])) {
						ajCallAgain[i]();
					} else {
						/*jshint debug:false */
						console.log('AjaxRC Error: Could not call non-function after reload.');
						/*jshint debug:true */
					}
				}
			}
		});

		$('#ajaxToggle')
			.attr('checked', storage())
			.click(toggleAjaxReload);

		if (storage()) {
			loadPageData();
		}
	}

	/**
	 * Load the required messages
	 */
	function loadMessages() {
		mw.hook('dev.i18n').add(function (i18no) {
			i18no.loadMessages('AjaxRC').done(function (i18nd) {
				i18n = i18nd;
				preloadAJAXRL();
			});
		});

		importArticle({
			type: 'script',
			article: 'u:dev:MediaWiki:I18n-js/code.js'
		});
	}

	/**
	 * Load the script on specific pages
	 * and only on certain values for wgAction (see disallowActions above)
	 */
	$(function() {
		if (
			!window.AjaxRCLoaded &&
			(
				ajPages.indexOf(config.wgPageName) > -1 ||
				ajSpecialPages.indexOf(config.wgCanonicalSpecialPageName) > -1
			) &&
			!$('#ajaxToggle').length &&
			disallowActions.indexOf(config.wgAction) === -1
		) {
			window.AjaxRCLoaded = true;
			if ($('#mw-content-text .mw-collapsible').exists()) {
				mw.loader.using('jquery.makeCollapsible', loadMessages);
			} else {
				loadMessages();
			}
		}
	});

}(this, jQuery, mediaWiki, Wikia));