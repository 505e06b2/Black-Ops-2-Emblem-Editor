// A script that adds a "Back To Top" button in the footer of the Oasis theme.
// I don't like scrolling back to top on long pages neither do you :)
// Created by Noemon from Dead Space Wiki
// Used files: [[File:BackToTopArrow_white.png]] [[File:BlackToTopArrow.png]]
(function(window, $, mw) {
    'use strict';

    var buttonStart = typeof window.BackToTopStart === 'number' ?
            window.BackToTopStart :
            window.innerHeight,
        scrollSpeed = typeof window.BackToTopSpeed === 'number' ?
            window.BackToTopSpeed :
            600,
        fadeSwitch = typeof window.BackToTopFade === 'number' ?
            window.BackToTopFade :
            600,
        $button,
        cc = mw.config.get('wgSassParams')['color-community-header'],
        theme;

    // Double-run protection
    if (window.BackToTopLoaded) {
        return;
    }
    window.BackToTopLoaded = true;

    function init() {
        $button.hide();
        $(window).scroll($.throttle(100, function() {
            if ($(this).scrollTop() > buttonStart) {
                switch (fadeSwitch) {
                    case 0:
                        $button.show();
                        break;
                    default:
                        $button.fadeIn();
                        break;
                }
            } else {
                switch (fadeSwitch) {
                    case 0:
                        $button.hide();
                        break;
                    default:
                        $button.fadeOut();
                        break;
                }
            }
        }));
    }

    function click() {
        $('body, html').animate({
            scrollTop: 0
        }, scrollSpeed);
        return false;
    }

    function modernPreload(l) {
        if (++_loaded == l) {
            modernInit(window.dev.wds, window.dev.colors);
        }
    }

    function modernInit(wds, colors) {
        cc    = colors.parse(cc);
        theme = cc.isBright() ? '#000000' : '#ffffff';
        cc    = cc.hex();
        $button = $('<div>', {
            id: 'BackToTopBtn',
            append: [
                $('<div>', {
                    css: {
                        background: cc,
                        color:      theme
                    },
                    'html': wds.icon('menu-control')
                })
            ],
            click: click
        }).appendTo(document.body);
        init();
    }

    function arrowInit() {
        $button = $('<li>', {
            click: click,
            id: 'backtotop'
        }).append(
            $('<img>', {
                src: 'https://images.wikia.nocookie.net/dev/images/' + (
                    (theme === 'black' || window.BackToTopArrowBlack) ?
                        'f/f2/BlackToTopArrow' :
                        'c/c3/BackToTopArrow_white'
                    ) + '.png'
            })
        ).appendTo('#WikiaBarWrapper .toolbar > .tools');
        init();
    }

    function oldInit(i18n) {
        $button = $('<li>', {
            click: click,
            id: 'backtotop'
        }).append(
            $('<button>', {
                css: {
                    height: '20px'
                },
                type: 'button',
                text: (typeof window.BackToTopText === 'string' && window.BackToTopText) || i18n.msg('backToTop').plain()
            })
        ).appendTo('#WikiaBarWrapper .toolbar > .tools');
        init();
    }

    if (window.BackToTopModern) {
        var _loaded = 0;
        [
            {
                h: 'wds',
                s: 'u:dev:WDSIcons/code.js'
            },
            {
                h:'colors',
                s: 'u:dev:Colors/code.js'
            }
        ].forEach(function(lib, i, a) {
            importArticle({
                type: 'script',
                article: lib.s
            });
            mw.hook('dev.' + lib.h).add(
                $.proxy(modernPreload, null, a.length)
            );
        });
    } else if (window.BackToTopArrow) {
        arrowInit();
    } else {
        importArticle({
            type: 'script',
            article: 'u:dev:MediaWiki:I18n-js/code.js'
        });
        mw.hook('dev.i18n').add(function (i18n) {
            i18n.loadMessages('BackToTopButton').done(oldInit);
        });
    }
    importArticle({
        type: 'style',
        article: 'u:dev:MediaWiki:BackToTopButton.css'
    });
}(this, jQuery, mediaWiki));