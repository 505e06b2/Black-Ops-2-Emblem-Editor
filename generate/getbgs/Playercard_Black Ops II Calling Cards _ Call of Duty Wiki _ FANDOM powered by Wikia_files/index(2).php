/**
 * Find duplicate images
 * Code courtesy of "pcj" of WoWPedia.org.
 * Contributors: Cakemix, Grunny, Porter21, Jrooksjr, TOBBE, Kangaroopower, UltimateSupreme, Bobogoobo, Saftzie
 */
(function ($, mw) {
	'use strict';

	var
		urlAPI = mw.config.get('wgScriptPath') + '/api.php',
		urlArticle = mw.config.get('wgArticlePath'),
		// check only files with these extensions
		exts = new RegExp('\\.(?:' + mw.config.get('wgFileExtensions').join('|') + ')$', 'i'),
		dil = {}, // dup image list
		title, output;

	function findDupImages(gf) {
		var
			opts = {
				format: 'json',
				action: 'query',
				prop: 'duplicatefiles',
				dflimit: 500,
				dflocalonly: 1,
				generator: 'allimages',
				gailimit: 500
			};

		if (gf) {
			if (gf.indexOf('|') > -1) {
				opts.dfcontinue = gf;
				gf = gf.split('|')[0];
			}
			opts.gaifrom = gf;
		}

		$.post(urlAPI, opts, function (data) {
			var
				pages, pageID, x;

			if (data.query) {
				pages = data.query.pages;
				for (pageID in pages) {
					// assume missing (or unknown) exts are links to external videos
					if (exts.test(pages[pageID].title) && !dil[pages[pageID].title] && pages[pageID].duplicatefiles) {
						if (title !== pages[pageID].title) {
							// current title is not the last title, so dump the last title and re-init the list
							if (output) {
								$('#mw-dupimages').append(output + '</ul>');
							}
							title = pages[pageID].title;
							output = '<h3><a href="' + encodeURI(urlArticle.replace('$1', title).replace(/ /g, '_')) + '">' + title + '</a></h3><ul>';
						}
						for ( x = 0; x < pages[pageID].duplicatefiles.length; ++x ) {
							output += '<li><a href="' + encodeURI(urlArticle.replace('$1', 'File:' + pages[pageID].duplicatefiles[x].name).replace(/ /g, '_')) + '">File:' + pages[pageID].duplicatefiles[x].name.replace(/_/g, ' ') + '</a></li>';
							dil['File:' + pages[pageID].duplicatefiles[x].name.replace(/_/g, ' ')] = true;
						}
					}
				}

				// Wikia uses deprecated continuation
				if (data['query-continue']) {
					if (data['query-continue'].duplicatefiles) {
						// there are more duplicates of the last title
						findDupImages(data['query-continue'].duplicatefiles.dfcontinue);
					} else {
						// there are more titles to check
						findDupImages(data['query-continue'].allimages.gaifrom);
					}
				} else {
					$('#dupImagesProgress').empty(); // stop the spinner
					if (output) {
						$('#mw-dupimages').append(output + '</ul>');
					}
				}
			}
		});
	}

	$(function () {
		var
			indicator = mw.config.get('stylepath') + '/common/progress-wheel.gif';

		if ($('#mw-dupimages').length) {
			$('#mw-dupimages').prepend('<div id="dupImagesProgress" style="height: 0; text-align: center;"><img src="' + indicator + '" style="border: 0 none;" alt="In progress..." /></div>');
			findDupImages();
		}
	});
}(jQuery, mediaWiki));