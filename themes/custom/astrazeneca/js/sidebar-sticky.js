"use strict";!function(g){g.fn.theiaStickySidebar=function(b){(b=g.extend({containerSelector:"",additionalMarginTop:0,additionalMarginBottom:0,updateSidebarHeight:!0,minWidth:0},b)).additionalMarginTop=parseInt(b.additionalMarginTop)||0,b.additionalMarginBottom=parseInt(b.additionalMarginBottom)||0,g("head").append(g('<style>.theiaStickySidebar:after {content: ""; display: table; clear: both;}</style>')),this.each(function(){function p(){i.fixedScrollTop=0,i.sidebar.css({"min-height":"1px"}),i.stickySidebar.css({position:"static",width:""})}var i={};i.sidebar=g(this),i.options=b||{},i.container=g(i.options.containerSelector),0==i.container.size()&&(i.container=i.sidebar.parent()),i.sidebar.parents().css("-webkit-transform","none"),i.sidebar.css({position:"relative",overflow:"visible","-webkit-box-sizing":"border-box","-moz-box-sizing":"border-box","box-sizing":"border-box"}),i.stickySidebar=i.sidebar.find(".theiaStickySidebar"),0==i.stickySidebar.length&&(i.sidebar.find("script").remove(),i.stickySidebar=g("<div>").addClass("theiaStickySidebar").append(i.sidebar.children()),i.sidebar.append(i.stickySidebar)),i.marginTop=parseInt(i.sidebar.css("margin-top")),i.marginBottom=parseInt(i.sidebar.css("margin-bottom")),i.paddingTop=parseInt(i.sidebar.css("padding-top")),i.paddingBottom=parseInt(i.sidebar.css("padding-bottom"));var t,o,a=i.stickySidebar.offset().top,e=i.stickySidebar.outerHeight();i.stickySidebar.css("padding-top",1),i.stickySidebar.css("padding-bottom",1),a-=i.stickySidebar.offset().top,e=i.stickySidebar.outerHeight()-e-a,0==a?(i.stickySidebar.css("padding-top",0),i.stickySidebarPaddingTop=0):i.stickySidebarPaddingTop=1,0==e?(i.stickySidebar.css("padding-bottom",0),i.stickySidebarPaddingBottom=0):i.stickySidebarPaddingBottom=1,i.previousScrollTop=null,i.fixedScrollTop=0,p(),i.onScroll=function(i){var t,o,a,e,d,s,r,n,c;i.stickySidebar.is(":visible")&&(g("body").width()<i.options.minWidth||i.sidebar.outerWidth(!0)+50>i.container.width()?p():(r="static",(t=g(document).scrollTop())>=i.container.offset().top+(i.paddingTop+i.marginTop-i.options.additionalMarginTop)&&(a=i.paddingTop+i.marginTop+b.additionalMarginTop,s=i.paddingBottom+i.marginBottom+b.additionalMarginBottom,e=i.container.offset().top,d=i.container.offset().top+(n=i.container,c=n.height(),n.children().each(function(){c=Math.max(c,g(this).height())}),c),o=0+b.additionalMarginTop,n=i.stickySidebar.outerHeight()+a+s<g(window).height()?o+i.stickySidebar.outerHeight():g(window).height()-i.marginBottom-i.paddingBottom-b.additionalMarginBottom,a=e-t+i.paddingTop+i.marginTop,s=d-t-i.paddingBottom-i.marginBottom,e=i.stickySidebar.offset().top-t,d=i.previousScrollTop-t,"fixed"==i.stickySidebar.css("position")&&(e+=d),e=0<d?Math.min(e,o):Math.max(e,n-i.stickySidebar.outerHeight()),e=Math.max(e,a),e=Math.min(e,s-i.stickySidebar.outerHeight()),r=!(s=i.container.height()==i.stickySidebar.outerHeight())&&e==o||!s&&e==n-i.stickySidebar.outerHeight()?"fixed":t+e-i.sidebar.offset().top-i.paddingTop<=b.additionalMarginTop?"static":"absolute"),"fixed"==r?i.stickySidebar.css({position:"fixed",width:i.sidebar.width(),top:e,left:i.sidebar.offset().left+parseInt(i.sidebar.css("padding-left"))}):"absolute"==r?(n={},"absolute"!=i.stickySidebar.css("position")&&(n.position="absolute",n.top=t+e-i.sidebar.offset().top-i.stickySidebarPaddingTop-i.stickySidebarPaddingBottom),n.width=i.sidebar.width(),n.left="",i.stickySidebar.css(n)):"static"==r&&p(),"static"!=r&&1==i.options.updateSidebarHeight&&i.sidebar.css({"min-height":i.stickySidebar.outerHeight()+i.stickySidebar.offset().top-i.sidebar.offset().top+i.paddingBottom}),i.previousScrollTop=t))},i.onScroll(i),g(document).scroll((o=i,function(){o.onScroll(o)})),g(window).resize((t=i,function(){t.stickySidebar.css({position:"static"}),t.onScroll(t)}))})}}(jQuery);