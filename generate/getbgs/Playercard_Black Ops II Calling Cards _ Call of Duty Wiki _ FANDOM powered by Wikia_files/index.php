/*<syntaxhighlight lang="javascript">*/
/*
 * Copyright © 2009, Daniel Friesen
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the script nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY DANIEL FRIESEN ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL DANIEL FRIESEN BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
( function( $ ) {
    console.warn('ShowHide is deprecated, please use https://community.wikia.com/wiki/Help:Collapsing instead.');
	// CONFIG
	var config = window.ShowHideConfig = $.extend( true, {
		autoCollapse: 2,
		userLang: true,
		brackets: '[]',
		linkBefore: false,
		en: {
			show: "show",
			hide: "hide",
			showAll: "show all",
			hideAll: "hide all"
		},
		be: {
			show: "Адкрыць",
			hide: "Схаваць",
			showAll: "Адкрыць усё",
			hideAll: "Схаваць усё"
		},
                cs: {
		        show: "Zobrazit",
	         	hide: "Skrýt",
	        	showAll: "Zobrazit vše",
	        	hideAll: "Skrýt vše"
                },
		bg: {
			show: "Покажи",
			hide: "Скрий",
			showAll: "Покажи всички",
			hideAll: "Скрий всички"
		},
		de: {
			show: "anzeigen",
			hide: "verbergen",
			showAll: "alle anzeigen",
			hideAll: "alle verbergen"
		},
		es: {
			show: "Mostrar",
			hide: "Ocultar",
			showAll: "Mostrar todo",
			hideAll: "Ocultar todo"
		},
		fr: {
			show: "afficher",
			hide: "masquer",
			showAll: "tout afficher",
			hideAll: "tout masquer"
		},
		hu: {
			show: "kibontás",
			hide: "elrejtés",
			showAll: "összes kibontása",
			hideAll: "összes elrejtése"
		},
		it: {
			show: "Mostra",
			hide: "Nascondi",
			showAll: "Mostra tutti",
			hideAll: "Nascondi tutti"
		},
		ja: {
			show: "表示",
			hide: "非表示",
			showAll: "すべて表示",
			hideAll: "すべて非表示"
		},
		ko: {
			show: "보이기",
			hide: "숨기기",
			showAll: "모두 보이기",
			hideAll: "모두 숨기기"
		},
		nl: {
			show: "tonen",
			hide: "verbergen",
			showAll: "alles tonen",
			hideAll: "alles verbergen"
		},
		pl: {
			show: "Pokaż",
			hide: "Ukryj",
			showAll: "Pokaż wszystko",
			hideAll: "Ukryj wszystko"
		},
		pt: {
			show: "Mostrar",
			hide: "Esconder",
			showAll: "Expandir Todos",
			hideAll: "Esconder Todos"
		},
		'pt-br': {
			show: "Mostrar",
			hide: "Esconder",
			showAll: "Expandir Todos",
			hideAll: "Esconder Todos"
		},
		ru: {
			show: "Открыть",
			hide: "Скрыть",
			showAll: "Открыть все",
			hideAll: "Скрыть все"
		},
		uk: {
			show: "Відкрити",
			hide: "Cховати",
			showAll: "Відкрити всі",
			hideAll: "Cховати всі"
		},
		vi: {
			show: "hiện",
			hide: "ẩn",
			showAll: "hiện tất cả",
			hideAll: "ẩn tất cả"
		},
		zh: {
			show: "显示",
			hide: "隐藏",
			showAll: "全部显示",
			hideAll: "全部隐藏"
		}
		// Make a post on the talkpage if you have i18n updates
	}, window.ShowHideConfig || {} );

	// i18n function
	function msg( name ) {
		if ( config.userLang && wgUserLanguage in config && name in config[wgUserLanguage] ) {
			return config[wgUserLanguage][name];
		}
		if ( wgContentLanguage in config && name in config[wgContentLanguage] ) {
			return config[wgContentLanguage][name];
		}
		return config.en[name];
	}
	
	// common
	$.fn.onLink = function( fn ) {
		return this.bind( 'click keypress', function(e) {
			if ( e.type === 'click' || ( e.type === 'keypress' && ( e.keyCode === 13 || e.charCode === 32 ) ) ) {
				fn.call(this, e);
			}
		} );
	};

	/** Collapsible tables using jQuery
	 *
	 *  Description: Allows tables to be collapsed, showing only the header.
	 */
	function collapseTable( node, state ) {
		var	$table = $( node ),
			$button = $table.find( 'tr:first > th:last .collapseLink' );
	
		if ( !$table.length || !$button.length ) {
			return false;
		}
		
		if ( typeof state === 'boolean' ) {
			$table.toggleClass( 'collapsed', !state );
		} else {
			$table.toggleClass( 'collapsed' );
		}
		var hidden = $table.hasClass( 'collapsed' );
		$table.find( '> * > tr' ).not( ':first, .nocollapse' )[hidden?"hide":"show"]();
		$button.text( msg( hidden ? "show" : "hide" ) );
		return true;
	}

	function createCollapseButtons() {
		var NavigationBoxes = [];
		$( 'table.collapsible' ).each( function () {
			NavigationBoxes.push(this);
			var	$buttonLink = $( '<span tabIndex=0 class=collapseLink />' ).text( msg( "hide" ) ).css({ cursor: "pointer" }).onLink( function( e ) { collapseTable( $(this).closest('table') ); } ),
				$button = $( "<span class=collapseButton />" ).css( {
				"float": "right",
				textAlign: "right",
				fontWeight: "normal",
				width: "6em",
				marginLeft: "-100%"
			} );
			$button.append( document.createTextNode(config.brackets.substr(0, config.brackets.length/2)), $buttonLink, config.brackets.substr(config.brackets.length/2) );

			var $header = $( this ).find( 'tr:first > th:last' ).prepend($button);
		} );
	
		// if more Navigation Bars found than Default: hide all
		if ( $( NavigationBoxes ).filter( '.autocollapse' ).length >= config.autoCollapse ) {
			$( NavigationBoxes ).filter( '.autocollapse' ).each( function () { collapseTable( this, false ); } );
		}
		$( NavigationBoxes ).filter( '.collapsed' ).each( function () { collapseTable( this, false ); } );
	}

	$( createCollapseButtons );

	/** Dynamic Navigation Bars with jQuery
	 *
	 *  Base Description: See Wikipedia:Wikipedia:NavFrame.
	 */

	// shows and hides content and picture (if available) of navigation bars
	function toggleNavigationBar( node ) {
		var	$navFrame = $( node ),
			$navToggle = $navFrame.find( '.NavHead:first .collapseLink' );
	
		if ( !$navFrame.length || !$navToggle.length ) {
			return false;
		}
	
		$navFrame.toggleClass( 'NavVisible' );
		$navFrame.find( '.NavPic, .NavContent' ).not( $navFrame.find( '.NavFrame .NavPic' ) ).not( $navFrame.find( '.NavFrame .NavContent' ) ).slideToggle();
		$navToggle.text( msg( $navFrame.hasClass( 'NavVisible' ) ? "hide" : "show" ) );
		return true;
	}

	// adds show/hide-button to navigation bars
	function createNavigationBarToggleButton() {
		var NavFrames = $( '.NavFrame' ).addClass( 'NavVisible' ).each( function () {
			var	$navHead = $( this ).find( '.NavHead:first' ),
				$buttonLink = $( '<span tabIndex=0 class=collapseLink />' ).text( msg( "hide" ) ).onLink( function ( e ) { toggleNavigationBar( $( this ).closest( '.NavFrame' ) ); } ),
				$button = $( '<span class="NavToggle collapseButton" />' );
			$navHead.filter( 'legend' ).append( ' - ' );
			if ( config.brackets ) {
				$button.append( document.createTextNode(config.brackets.substr(0, config.brackets.length/2)), $buttonLink, config.brackets.substr(config.brackets.length/2) );
			} else {
				$button.append( $buttonLink );
			}
			$navHead[config.linkBefore?"prepend":"append"]($button);
		} );
		// if more Navigation Bars found than Default: hide all
		if ( NavFrames.length >= config.autoCollapse ) {
			NavFrames.not( '.noautocollapse' ).each( function () { toggleNavigationBar(this); } );
		} else {
			NavFrames.filter( '.collapsed' ).each( function () { toggleNavigationBar(this); } );
		}
		return true;
	}

	$( createNavigationBarToggleButton );

	$( function () {
		$( '.NavGlobal' ).each( function () {
			$( '<span class=NavGlobalShow />' ).append(
				document.createTextNode( '[' ),
				$( '<span tabIndex=0 class=collapseLink />' ).text( msg( "showAll" ) ).onLink( function ( e ) {
					$( '.NavFrame' ).each( function () { if ( !$( this ).hasClass( 'NavVisible' ) ) toggleNavigationBar(this); } );
				} ),
				']'
			).appendTo( this );
			$( this ).append( ' ' );
			$('<span class=NavGlobalHide />').append(
				document.createTextNode( '[' ),
				$( '<span tabIndex=0 class=collapseLink />' ).text( msg( "hideAll" ) ).onLink( function ( e ) {
					$( '.NavFrame' ).each( function () { if ( $( this ).hasClass( 'NavVisible' ) ) toggleNavigationBar(this); } );
				} ),
				']'
			).appendTo( this );
		} );
	} );

} )( jQuery );
/*</syntaxhighlight>*/