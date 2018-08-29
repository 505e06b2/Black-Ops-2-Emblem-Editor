mw.loader.implement("ext.bannerNotifications",function($){;},{},{"bannernotifications-general-ajax-failure":"The browser could not connect to FANDOM.  Try again later."});mw.loader.implement("ext.designSystem",function($){;},{},{"notifications-no-notifications-message":"No notifications yet.","notifications-mark-all-as-read":"Mark all as read","notifications-replied-by-multiple-users-with-title":"{mostRecentUser} and {number} other users <b>replied<\/b> to {postTitle}","notifications-replied-by-multiple-users-no-title":"{mostRecentUser} and {number} other users <b>replied<\/b> to a discussion you are following","notifications-replied-by-two-users-with-title":"{firstUser} and {secondUser} <b>replied<\/b> to {postTitle}","notifications-replied-by-two-users-no-title":"{firstUser} and {secondUser} <b>replied<\/b> to a discussion you are following","notifications-replied-by-with-title":"{user} <b>replied<\/b> to {postTitle}","notifications-replied-by-no-title":
"{user} <b>replied<\/b> to a discussion you are following","notifications-post-upvote-single-user-with-title":"1 user <b>upvoted<\/b> your discussion {postTitle}","notifications-post-upvote-single-user-no-title":"1 user <b>upvoted<\/b> your discussion","notifications-post-upvote-multiple-users-with-title":"{number} users <b>upvoted<\/b> your discussion {postTitle}","notifications-post-upvote-multiple-users-no-title":"{number} users  <b>upvoted<\/b> your discussion","notifications-reply-upvote-single-user-with-title":"1 user <b>upvoted<\/b> your reply to {postTitle}","notifications-reply-upvote-single-user-no-title":"1 user <b>upvoted<\/b> your reply","notifications-reply-upvote-multiple-users-with-title":"{number} users <b>upvoted<\/b> your reply to {postTitle}","notifications-reply-upvote-multiple-users-no-title":"{number} users <b>upvoted<\/b> your reply","notifications-notifications":"Notifications"});mw.loader.implement("ext.userLogin",function($){;},{},{
"usersignup-error-password-length":"Oops, your password is too long. Please choose a password that's 50 characters or less.","userlogin-error-wrongpasswordempty":"Oops, please fill in the password field."});mw.loader.implement("ext.visualEditor.track",function($){(function(){var callbacks=$.Callbacks('memory'),queue=[];ve.track=function(topic,data){queue.push({topic:topic,timeStamp:ve.now(),data:data});callbacks.fire(queue);};ve.trackSubscribe=function(topic,callback){var seen=0;callbacks.add(function(queue){var event;for(;seen<queue.length;seen++){event=queue[seen];if(event.topic.indexOf(topic)===0){callback.call(event,event.topic,event.data);}}});};ve.trackSubscribeAll=function(callback){ve.trackSubscribe('',callback);};}());;},{},{});mw.loader.implement("ext.visualEditor.ve",function($){window.ve={instances:[]};ve.now=(function(){var perf=window.performance,navStart=perf&&perf.timing&&perf.timing.navigationStart;return navStart&&typeof perf.now==='function'?function(){return navStart
+perf.now();}:Date.now;}());;},{},{});mw.loader.implement("ext.visualEditor.wikia.viewPageTarget.init",function($){(function(){var conf,tabMessages,uri,viewUri,veEditUri,isViewPage,init,support,targetDeferred,plugins=[],trackerConfig={category:'editor-ve',trackingMethod:'analytics'},spinnerTimeoutId=null,vePreferred;function initSpinner(){var $spinner=$('<div>').addClass('ve-spinner visible').attr('data-type','loading'),$content=$('<div>').addClass('content'),$icon=$('<div>').addClass('loading'),$message=$('<p>').addClass('message').text(mw.message('wikia-visualeditor-loading').plain()),$fade=$('<div>').addClass('ve-spinner-fade');$content.append($icon).append($message);$spinner.append($content).appendTo($('body')).css('opacity',1).hide();$fade.appendTo('#WikiaArticle').hide();mw.hook('ve.activationComplete').add(function hide(){if(spinnerTimeoutId){clearTimeout(spinnerTimeoutId);spinnerTimeoutId=null;}});}function showSpinner(){var $spinner=$('.ve-spinner[data-type="loading"]'),
$message=$spinner.find('p.message'),$fade=$('.ve-spinner-fade');$message.hide();$spinner.fadeIn(400);$fade.show().css('opacity',0.75);spinnerTimeoutId=setTimeout(function(){if($spinner.is(':visible')){$message.slideDown(400);}},3000);}initSpinner();function getTarget(){var loadTargetDeferred;ve.track('wikia',{action:Wikia.Tracker.ACTIONS.IMPRESSION,label:'edit-page'});showSpinner();if(!targetDeferred){targetDeferred=$.Deferred();loadTargetDeferred=$.Deferred();mw.loader.using('ext.visualEditor.wikia.oasisViewPageTarget',loadTargetDeferred.resolve,loadTargetDeferred.reject);$.when($.getResources([window.wgResourceBasePath+'/resources/wikia/libraries/vignette/vignette.js',$.getSassCommonURL('/extensions/VisualEditor/wikia/VisualEditor-Oasis.scss')]),loadTargetDeferred).done(function(){var target=new ve.init.wikia.ViewPageTarget();target.$element.insertAfter('#mw-content-text');ve.init.mw.ViewPageTarget.prototype.setupSectionEditLinks=init.setupSectionLinks;target.addPlugins(plugins);
targetDeferred.resolve(target);});}return targetDeferred.promise();}conf=mw.config.get('wgVisualEditorConfig');tabMessages=conf.tabMessages;uri=new mw.Uri();viewUri=new mw.Uri(mw.util.getUrl(mw.config.get('wgRelevantPageName')));isViewPage=(mw.config.get('wgIsArticle')&&!('diff'in uri.query));veEditUri=(isViewPage?uri:viewUri).clone().extend({veaction:'edit'});vePreferred=!!mw.config.get('wgVisualEditorPreferred');support={es5:!!(Array.isArray&&Array.prototype.filter&&Array.prototype.indexOf&&Array.prototype.map&&Date.now&&Date.prototype.toJSON&&Object.create&&Object.keys&&String.prototype.trim&&window.JSON&&JSON.parse&&JSON.stringify&&Function.prototype.bind),contentEditable:'contentEditable'in document.createElement('div'),svg:!!(document.createElementNS&&document.createElementNS('http://www.w3.org/2000/svg','svg').createSVGRect)};init={support:support,blacklist:conf.blacklist,addPlugin:function(plugin){plugins.push(plugin);},setupTabs:function(){$('#ca-ve-edit').click(init.
onEditTabClick);},setupSectionLinks:function(){$('#mw-content-text').find('.editsection a').click(init.onEditSectionLinkClick);},onEditTabClick:function(e){if((e.which&&e.which!==1)||e.shiftKey||e.altKey||e.ctrlKey||e.metaKey){return;}init.showLoading();ve.track('mwedit.init',{type:'page',mechanism:'click'});if(history.pushState&&uri.query.veaction!=='edit'){mw.hook('ve.afterVEInit').fire(veEditUri);history.replaceState({tag:'visualeditor'},document.title,uri);history.pushState({tag:'visualeditor'},document.title,veEditUri);uri=veEditUri;}e.preventDefault();Wikia.Tracker.track(trackerConfig,{action:Wikia.Tracker.ACTIONS.CLICK,category:'article',label:'ve-edit'});if(window.veTrack){veTrack({action:'ve-edit-page-start',trigger:'onEditTabClick'});}getTarget().done(function(target){target.activate().done(function(){ve.track('mwedit.ready');}).always(init.hideLoading);});},onEditSectionLinkClick:function(e){if((e.which&&e.which!==1)||e.shiftKey||e.altKey||e.ctrlKey||e.metaKey){return;}init.
showLoading();ve.track('mwedit.init',{type:'section',mechanism:'click'});if(history.pushState&&uri.query.veaction!=='edit'){history.replaceState({tag:'visualeditor'},document.title,uri);history.pushState({tag:'visualeditor'},document.title,this.href);}e.preventDefault();Wikia.Tracker.track(trackerConfig,{action:Wikia.Tracker.ACTIONS.CLICK,category:'article',label:'ve-section-edit'});if(window.veTrack){veTrack({action:'ve-edit-page-start',trigger:'onEditSectionLinkClick'});}getTarget().done(function(target){target.saveEditSection($(e.target).closest('h1, h2, h3, h4, h5, h6').get(0));target.activate().done(function(){ve.track('mwedit.ready');}).always(init.hideLoading);});},showLoading:function(){if(!init.$loading){init.$loading=$('<div class="mw-viewPageTarget-loading"></div>');}$('#firstHeading').prepend(init.$loading);},hideLoading:function(){if(init.$loading){init.$loading.detach();}}};support.visualEditor=support.es5&&support.contentEditable&&support.svg&&(('vewhitelist'in uri.query
)||!$.client.test(init.blacklist,null,true));init.isAvailable=(support.visualEditor&&$.inArray(new mw.Title(mw.config.get('wgRelevantPageName')).getNamespaceId(),conf.namespaces)!==-1);init.isInValidNamespace=function(article){return $.inArray(new mw.Title(article).getNamespaceId(),conf.namespaces)!==-1;};init.canCreatePageUsingVE=function(){return support.visualEditor&&vePreferred;};mw.libs.ve=init;function setupRedlinks(){$(document).on('mouseover click','a[href*="action=edit"][href*="&redlink"]:not([href*="veaction=edit"]), '+'a[href*="action=edit"][href*="?redlink"]:not([href*="veaction=edit"])',function(){var $element=$(this),href=$element.attr('href'),articlePath=mw.config.get('wgArticlePath').replace('$1',''),redlinkArticle=new mw.Uri(href).path.replace(articlePath,'');if(init.isInValidNamespace(decodeURIComponent(redlinkArticle))){$element.attr('href',href.replace('action=edit','veaction=edit'));}});}function removeVELink(){var $edit=$('#ca-edit'),$veEdit=$('#ca-ve-edit');$(
'html').addClass('ve-not-available');if(vePreferred&&$veEdit.length>0){$veEdit.attr('href',$edit.attr('href'));$edit.parent().remove();}else{$veEdit.parent().remove();}}if(init.isAvailable){$(function(){if(isViewPage&&uri.query.veaction==='edit'){var isSection=uri.query.vesection!==undefined;init.showLoading();ve.track('mwedit.init',{type:isSection?'section':'page',mechanism:'url'});if(window.veTrack){veTrack({action:'ve-edit-page-start',trigger:'activateOnPageLoad'});}getTarget().done(function(target){target.activate().done(function(){ve.track('mwedit.ready');}).always(init.hideLoading);});}if(isViewPage){init.setupTabs();if(vePreferred){init.setupSectionLinks();}}if(vePreferred){setupRedlinks();}});}else{removeVELink();}}());;},{},{"wikia-visualeditor-loading":"Loading...","wikia-visualeditor-anon-warning":"You are not signed in. Some features will not be available to you. $1 or $2.","wikia-visualeditor-anon-log-in":"Sign in","wikia-visualeditor-anon-register":"Register",
"accesskey-ca-editsource":"e","accesskey-ca-ve-edit":"v","accesskey-save":"s","pipe-separator":" | ","tooltip-ca-createsource":"Create the source code of this page","tooltip-ca-editsource":"Edit the source code of this page","tooltip-ca-ve-edit":"Edit this page with VisualEditor","visualeditor-ca-editsource-section":"edit source"});mw.loader.implement("ext.wikia.TimeAgoMessaging",function($){$('.timeago').timeago();;},{},{"timeago-year":"{{PLURAL:$1|a year|$1 years}} ago","timeago-month":"{{PLURAL:$1|a month|$1 months}} ago","timeago-day":"{{PLURAL:$1|a day|$1 days}} ago","timeago-hour":"{{PLURAL:$1|an hour|$1 hours}} ago","timeago-minute":"{{PLURAL:$1|a minute|$1 minutes}} ago","timeago-second":"a minute ago","timeago-day-from-now":"{{PLURAL:$1|a day|$1 days}} from now","timeago-hour-from-now":"{{PLURAL:$1|an hour|$1 hours}} from now","timeago-minute-from-now":"{{PLURAL:$1|a minute|$1 minutes}} from now","timeago-month-from-now":"{{PLURAL:$1|a month|$1 months}} from now",
"timeago-second-from-now":"a minute from now"});mw.loader.implement("mediawiki.language.data",function($){mw.language.setData("en",{"digitTransformTable":null,"separatorTransformTable":null,"grammarForms":[],"pluralRules":null,"digitGroupingPattern":null});;},{},{});

/* cache key: Callofduty:resourceloader:filter:minify-js:7:43c98359f38e1b2f8e66764ea53124e3 */