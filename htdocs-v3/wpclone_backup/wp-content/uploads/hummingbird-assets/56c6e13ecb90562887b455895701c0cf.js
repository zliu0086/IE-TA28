/**handles:jquery-ui-resizable**/
/*!
 * jQuery UI Resizable 1.11.4
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/resizable/
 */
!function(t){"function"==typeof define&&define.amd?define(["jquery","./core","./mouse","./widget"],t):t(jQuery)}(function(w){return w.widget("ui.resizable",w.ui.mouse,{version:"1.11.4",widgetEventPrefix:"resize",options:{alsoResize:!1,animate:!1,animateDuration:"slow",animateEasing:"swing",aspectRatio:!1,autoHide:!1,containment:!1,ghost:!1,grid:!1,handles:"e,s,se",helper:!1,maxHeight:null,maxWidth:null,minHeight:10,minWidth:10,zIndex:90,resize:null,start:null,stop:null},_num:function(t){return parseInt(t,10)||0},_isNumber:function(t){return!isNaN(parseInt(t,10))},_hasScroll:function(t,i){if("hidden"===w(t).css("overflow"))return!1;var e,s=i&&"left"===i?"scrollLeft":"scrollTop";return 0<t[s]||(t[s]=1,e=0<t[s],t[s]=0,e)},_create:function(){var t,i,e,s,n=this,h=this.options;if(this.element.addClass("ui-resizable"),w.extend(this,{_aspectRatio:!!h.aspectRatio,aspectRatio:h.aspectRatio,originalElement:this.element,_proportionallyResizeElements:[],_helper:h.helper||h.ghost||h.animate?h.helper||"ui-resizable-helper":null}),this.element[0].nodeName.match(/^(canvas|textarea|input|select|button|img)$/i)&&(this.element.wrap(w("<div class='ui-wrapper' style='overflow: hidden;'></div>").css({position:this.element.css("position"),width:this.element.outerWidth(),height:this.element.outerHeight(),top:this.element.css("top"),left:this.element.css("left")})),this.element=this.element.parent().data("ui-resizable",this.element.resizable("instance")),this.elementIsWrapper=!0,this.element.css({marginLeft:this.originalElement.css("marginLeft"),marginTop:this.originalElement.css("marginTop"),marginRight:this.originalElement.css("marginRight"),marginBottom:this.originalElement.css("marginBottom")}),this.originalElement.css({marginLeft:0,marginTop:0,marginRight:0,marginBottom:0}),this.originalResizeStyle=this.originalElement.css("resize"),this.originalElement.css("resize","none"),this._proportionallyResizeElements.push(this.originalElement.css({position:"static",zoom:1,display:"block"})),this.originalElement.css({margin:this.originalElement.css("margin")}),this._proportionallyResize()),this.handles=h.handles||(w(".ui-resizable-handle",this.element).length?{n:".ui-resizable-n",e:".ui-resizable-e",s:".ui-resizable-s",w:".ui-resizable-w",se:".ui-resizable-se",sw:".ui-resizable-sw",ne:".ui-resizable-ne",nw:".ui-resizable-nw"}:"e,s,se"),this._handles=w(),this.handles.constructor===String)for("all"===this.handles&&(this.handles="n,e,s,w,se,sw,ne,nw"),t=this.handles.split(","),this.handles={},i=0;i<t.length;i++)e=w.trim(t[i]),(s=w("<div class='ui-resizable-handle "+("ui-resizable-"+e)+"'></div>")).css({zIndex:h.zIndex}),"se"===e&&s.addClass("ui-icon ui-icon-gripsmall-diagonal-se"),this.handles[e]=".ui-resizable-"+e,this.element.append(s);this._renderAxis=function(t){var i,e,s,h;for(i in t=t||this.element,this.handles)this.handles[i].constructor===String?this.handles[i]=this.element.children(this.handles[i]).first().show():(this.handles[i].jquery||this.handles[i].nodeType)&&(this.handles[i]=w(this.handles[i]),this._on(this.handles[i],{mousedown:n._mouseDown})),this.elementIsWrapper&&this.originalElement[0].nodeName.match(/^(textarea|input|select|button)$/i)&&(e=w(this.handles[i],this.element),h=/sw|ne|nw|se|n|s/.test(i)?e.outerHeight():e.outerWidth(),s=["padding",/ne|nw|n/.test(i)?"Top":/se|sw|s/.test(i)?"Bottom":/^e$/.test(i)?"Right":"Left"].join(""),t.css(s,h),this._proportionallyResize()),this._handles=this._handles.add(this.handles[i])},this._renderAxis(this.element),this._handles=this._handles.add(this.element.find(".ui-resizable-handle")),this._handles.disableSelection(),this._handles.mouseover(function(){n.resizing||(this.className&&(s=this.className.match(/ui-resizable-(se|sw|ne|nw|n|e|s|w)/i)),n.axis=s&&s[1]?s[1]:"se")}),h.autoHide&&(this._handles.hide(),w(this.element).addClass("ui-resizable-autohide").mouseenter(function(){h.disabled||(w(this).removeClass("ui-resizable-autohide"),n._handles.show())}).mouseleave(function(){h.disabled||n.resizing||(w(this).addClass("ui-resizable-autohide"),n._handles.hide())})),this._mouseInit()},_destroy:function(){this._mouseDestroy();function t(t){w(t).removeClass("ui-resizable ui-resizable-disabled ui-resizable-resizing").removeData("resizable").removeData("ui-resizable").unbind(".resizable").find(".ui-resizable-handle").remove()}var i;return this.elementIsWrapper&&(t(this.element),i=this.element,this.originalElement.css({position:i.css("position"),width:i.outerWidth(),height:i.outerHeight(),top:i.css("top"),left:i.css("left")}).insertAfter(i),i.remove()),this.originalElement.css("resize",this.originalResizeStyle),t(this.originalElement),this},_mouseCapture:function(t){var i,e,s=!1;for(i in this.handles)(e=w(this.handles[i])[0])!==t.target&&!w.contains(e,t.target)||(s=!0);return!this.options.disabled&&s},_mouseStart:function(t){var i,e,s,h=this.options,n=this.element;return this.resizing=!0,this._renderProxy(),i=this._num(this.helper.css("left")),e=this._num(this.helper.css("top")),h.containment&&(i+=w(h.containment).scrollLeft()||0,e+=w(h.containment).scrollTop()||0),this.offset=this.helper.offset(),this.position={left:i,top:e},this.size=this._helper?{width:this.helper.width(),height:this.helper.height()}:{width:n.width(),height:n.height()},this.originalSize=this._helper?{width:n.outerWidth(),height:n.outerHeight()}:{width:n.width(),height:n.height()},this.sizeDiff={width:n.outerWidth()-n.width(),height:n.outerHeight()-n.height()},this.originalPosition={left:i,top:e},this.originalMousePosition={left:t.pageX,top:t.pageY},this.aspectRatio="number"==typeof h.aspectRatio?h.aspectRatio:this.originalSize.width/this.originalSize.height||1,s=w(".ui-resizable-"+this.axis).css("cursor"),w("body").css("cursor","auto"===s?this.axis+"-resize":s),n.addClass("ui-resizable-resizing"),this._propagate("start",t),!0},_mouseDrag:function(t){var i,e,s=this.originalMousePosition,h=this.axis,n=t.pageX-s.left||0,o=t.pageY-s.top||0,a=this._change[h];return this._updatePrevProperties(),a&&(i=a.apply(this,[t,n,o]),this._updateVirtualBoundaries(t.shiftKey),(this._aspectRatio||t.shiftKey)&&(i=this._updateRatio(i,t)),i=this._respectSize(i,t),this._updateCache(i),this._propagate("resize",t),e=this._applyChanges(),!this._helper&&this._proportionallyResizeElements.length&&this._proportionallyResize(),w.isEmptyObject(e)||(this._updatePrevProperties(),this._trigger("resize",t,this.ui()),this._applyChanges())),!1},_mouseStop:function(t){this.resizing=!1;var i,e,s,h,n,o,a,l=this.options,r=this;return this._helper&&(s=(e=(i=this._proportionallyResizeElements).length&&/textarea/i.test(i[0].nodeName))&&this._hasScroll(i[0],"left")?0:r.sizeDiff.height,h=e?0:r.sizeDiff.width,n={width:r.helper.width()-h,height:r.helper.height()-s},o=parseInt(r.element.css("left"),10)+(r.position.left-r.originalPosition.left)||null,a=parseInt(r.element.css("top"),10)+(r.position.top-r.originalPosition.top)||null,l.animate||this.element.css(w.extend(n,{top:a,left:o})),r.helper.height(r.size.height),r.helper.width(r.size.width),this._helper&&!l.animate&&this._proportionallyResize()),w("body").css("cursor","auto"),this.element.removeClass("ui-resizable-resizing"),this._propagate("stop",t),this._helper&&this.helper.remove(),!1},_updatePrevProperties:function(){this.prevPosition={top:this.position.top,left:this.position.left},this.prevSize={width:this.size.width,height:this.size.height}},_applyChanges:function(){var t={};return this.position.top!==this.prevPosition.top&&(t.top=this.position.top+"px"),this.position.left!==this.prevPosition.left&&(t.left=this.position.left+"px"),this.size.width!==this.prevSize.width&&(t.width=this.size.width+"px"),this.size.height!==this.prevSize.height&&(t.height=this.size.height+"px"),this.helper.css(t),t},_updateVirtualBoundaries:function(t){var i,e,s,h,n,o=this.options;n={minWidth:this._isNumber(o.minWidth)?o.minWidth:0,maxWidth:this._isNumber(o.maxWidth)?o.maxWidth:1/0,minHeight:this._isNumber(o.minHeight)?o.minHeight:0,maxHeight:this._isNumber(o.maxHeight)?o.maxHeight:1/0},(this._aspectRatio||t)&&(i=n.minHeight*this.aspectRatio,s=n.minWidth/this.aspectRatio,e=n.maxHeight*this.aspectRatio,h=n.maxWidth/this.aspectRatio,i>n.minWidth&&(n.minWidth=i),s>n.minHeight&&(n.minHeight=s),e<n.maxWidth&&(n.maxWidth=e),h<n.maxHeight&&(n.maxHeight=h)),this._vBoundaries=n},_updateCache:function(t){this.offset=this.helper.offset(),this._isNumber(t.left)&&(this.position.left=t.left),this._isNumber(t.top)&&(this.position.top=t.top),this._isNumber(t.height)&&(this.size.height=t.height),this._isNumber(t.width)&&(this.size.width=t.width)},_updateRatio:function(t){var i=this.position,e=this.size,s=this.axis;return this._isNumber(t.height)?t.width=t.height*this.aspectRatio:this._isNumber(t.width)&&(t.height=t.width/this.aspectRatio),"sw"===s&&(t.left=i.left+(e.width-t.width),t.top=null),"nw"===s&&(t.top=i.top+(e.height-t.height),t.left=i.left+(e.width-t.width)),t},_respectSize:function(t){var i=this._vBoundaries,e=this.axis,s=this._isNumber(t.width)&&i.maxWidth&&i.maxWidth<t.width,h=this._isNumber(t.height)&&i.maxHeight&&i.maxHeight<t.height,n=this._isNumber(t.width)&&i.minWidth&&i.minWidth>t.width,o=this._isNumber(t.height)&&i.minHeight&&i.minHeight>t.height,a=this.originalPosition.left+this.originalSize.width,l=this.position.top+this.size.height,r=/sw|nw|w/.test(e),p=/nw|ne|n/.test(e);return n&&(t.width=i.minWidth),o&&(t.height=i.minHeight),s&&(t.width=i.maxWidth),h&&(t.height=i.maxHeight),n&&r&&(t.left=a-i.minWidth),s&&r&&(t.left=a-i.maxWidth),o&&p&&(t.top=l-i.minHeight),h&&p&&(t.top=l-i.maxHeight),t.width||t.height||t.left||!t.top?t.width||t.height||t.top||!t.left||(t.left=null):t.top=null,t},_getPaddingPlusBorderDimensions:function(t){for(var i=0,e=[],s=[t.css("borderTopWidth"),t.css("borderRightWidth"),t.css("borderBottomWidth"),t.css("borderLeftWidth")],h=[t.css("paddingTop"),t.css("paddingRight"),t.css("paddingBottom"),t.css("paddingLeft")];i<4;i++)e[i]=parseInt(s[i],10)||0,e[i]+=parseInt(h[i],10)||0;return{height:e[0]+e[2],width:e[1]+e[3]}},_proportionallyResize:function(){if(this._proportionallyResizeElements.length)for(var t,i=0,e=this.helper||this.element;i<this._proportionallyResizeElements.length;i++)t=this._proportionallyResizeElements[i],this.outerDimensions||(this.outerDimensions=this._getPaddingPlusBorderDimensions(t)),t.css({height:e.height()-this.outerDimensions.height||0,width:e.width()-this.outerDimensions.width||0})},_renderProxy:function(){var t=this.element,i=this.options;this.elementOffset=t.offset(),this._helper?(this.helper=this.helper||w("<div style='overflow:hidden;'></div>"),this.helper.addClass(this._helper).css({width:this.element.outerWidth()-1,height:this.element.outerHeight()-1,position:"absolute",left:this.elementOffset.left+"px",top:this.elementOffset.top+"px",zIndex:++i.zIndex}),this.helper.appendTo("body").disableSelection()):this.helper=this.element},_change:{e:function(t,i){return{width:this.originalSize.width+i}},w:function(t,i){var e=this.originalSize;return{left:this.originalPosition.left+i,width:e.width-i}},n:function(t,i,e){var s=this.originalSize;return{top:this.originalPosition.top+e,height:s.height-e}},s:function(t,i,e){return{height:this.originalSize.height+e}},se:function(t,i,e){return w.extend(this._change.s.apply(this,arguments),this._change.e.apply(this,[t,i,e]))},sw:function(t,i,e){return w.extend(this._change.s.apply(this,arguments),this._change.w.apply(this,[t,i,e]))},ne:function(t,i,e){return w.extend(this._change.n.apply(this,arguments),this._change.e.apply(this,[t,i,e]))},nw:function(t,i,e){return w.extend(this._change.n.apply(this,arguments),this._change.w.apply(this,[t,i,e]))}},_propagate:function(t,i){w.ui.plugin.call(this,t,[i,this.ui()]),"resize"!==t&&this._trigger(t,i,this.ui())},plugins:{},ui:function(){return{originalElement:this.originalElement,element:this.element,helper:this.helper,position:this.position,size:this.size,originalSize:this.originalSize,originalPosition:this.originalPosition}}}),w.ui.plugin.add("resizable","animate",{stop:function(i){var e=w(this).resizable("instance"),t=e.options,s=e._proportionallyResizeElements,h=s.length&&/textarea/i.test(s[0].nodeName),n=h&&e._hasScroll(s[0],"left")?0:e.sizeDiff.height,o=h?0:e.sizeDiff.width,a={width:e.size.width-o,height:e.size.height-n},l=parseInt(e.element.css("left"),10)+(e.position.left-e.originalPosition.left)||null,r=parseInt(e.element.css("top"),10)+(e.position.top-e.originalPosition.top)||null;e.element.animate(w.extend(a,r&&l?{top:r,left:l}:{}),{duration:t.animateDuration,easing:t.animateEasing,step:function(){var t={width:parseInt(e.element.css("width"),10),height:parseInt(e.element.css("height"),10),top:parseInt(e.element.css("top"),10),left:parseInt(e.element.css("left"),10)};s&&s.length&&w(s[0]).css({width:t.width,height:t.height}),e._updateCache(t),e._propagate("resize",i)}})}}),w.ui.plugin.add("resizable","containment",{start:function(){var e,s,t,i,h,n,o,a=w(this).resizable("instance"),l=a.options,r=a.element,p=l.containment,d=p instanceof w?p.get(0):/parent/.test(p)?r.parent().get(0):p;d&&(a.containerElement=w(d),/document/.test(p)||p===document?(a.containerOffset={left:0,top:0},a.containerPosition={left:0,top:0},a.parentData={element:w(document),left:0,top:0,width:w(document).width(),height:w(document).height()||document.body.parentNode.scrollHeight}):(e=w(d),s=[],w(["Top","Right","Left","Bottom"]).each(function(t,i){s[t]=a._num(e.css("padding"+i))}),a.containerOffset=e.offset(),a.containerPosition=e.position(),a.containerSize={height:e.innerHeight()-s[3],width:e.innerWidth()-s[1]},t=a.containerOffset,i=a.containerSize.height,h=a.containerSize.width,n=a._hasScroll(d,"left")?d.scrollWidth:h,o=a._hasScroll(d)?d.scrollHeight:i,a.parentData={element:d,left:t.left,top:t.top,width:n,height:o}))},resize:function(t){var i,e,s,h,n=w(this).resizable("instance"),o=n.options,a=n.containerOffset,l=n.position,r=n._aspectRatio||t.shiftKey,p={top:0,left:0},d=n.containerElement,g=!0;d[0]!==document&&/static/.test(d.css("position"))&&(p=a),l.left<(n._helper?a.left:0)&&(n.size.width=n.size.width+(n._helper?n.position.left-a.left:n.position.left-p.left),r&&(n.size.height=n.size.width/n.aspectRatio,g=!1),n.position.left=o.helper?a.left:0),l.top<(n._helper?a.top:0)&&(n.size.height=n.size.height+(n._helper?n.position.top-a.top:n.position.top),r&&(n.size.width=n.size.height*n.aspectRatio,g=!1),n.position.top=n._helper?a.top:0),s=n.containerElement.get(0)===n.element.parent().get(0),h=/relative|absolute/.test(n.containerElement.css("position")),s&&h?(n.offset.left=n.parentData.left+n.position.left,n.offset.top=n.parentData.top+n.position.top):(n.offset.left=n.element.offset().left,n.offset.top=n.element.offset().top),i=Math.abs(n.sizeDiff.width+(n._helper?n.offset.left-p.left:n.offset.left-a.left)),e=Math.abs(n.sizeDiff.height+(n._helper?n.offset.top-p.top:n.offset.top-a.top)),i+n.size.width>=n.parentData.width&&(n.size.width=n.parentData.width-i,r&&(n.size.height=n.size.width/n.aspectRatio,g=!1)),e+n.size.height>=n.parentData.height&&(n.size.height=n.parentData.height-e,r&&(n.size.width=n.size.height*n.aspectRatio,g=!1)),g||(n.position.left=n.prevPosition.left,n.position.top=n.prevPosition.top,n.size.width=n.prevSize.width,n.size.height=n.prevSize.height)},stop:function(){var t=w(this).resizable("instance"),i=t.options,e=t.containerOffset,s=t.containerPosition,h=t.containerElement,n=w(t.helper),o=n.offset(),a=n.outerWidth()-t.sizeDiff.width,l=n.outerHeight()-t.sizeDiff.height;t._helper&&!i.animate&&/relative/.test(h.css("position"))&&w(this).css({left:o.left-s.left-e.left,width:a,height:l}),t._helper&&!i.animate&&/static/.test(h.css("position"))&&w(this).css({left:o.left-s.left-e.left,width:a,height:l})}}),w.ui.plugin.add("resizable","alsoResize",{start:function(){var t=w(this).resizable("instance").options;w(t.alsoResize).each(function(){var t=w(this);t.data("ui-resizable-alsoresize",{width:parseInt(t.width(),10),height:parseInt(t.height(),10),left:parseInt(t.css("left"),10),top:parseInt(t.css("top"),10)})})},resize:function(t,e){var i=w(this).resizable("instance"),s=i.options,h=i.originalSize,n=i.originalPosition,o={height:i.size.height-h.height||0,width:i.size.width-h.width||0,top:i.position.top-n.top||0,left:i.position.left-n.left||0};w(s.alsoResize).each(function(){var t=w(this),s=w(this).data("ui-resizable-alsoresize"),h={},i=t.parents(e.originalElement[0]).length?["width","height"]:["width","height","top","left"];w.each(i,function(t,i){var e=(s[i]||0)+(o[i]||0);e&&0<=e&&(h[i]=e||null)}),t.css(h)})},stop:function(){w(this).removeData("resizable-alsoresize")}}),w.ui.plugin.add("resizable","ghost",{start:function(){var t=w(this).resizable("instance"),i=t.options,e=t.size;t.ghost=t.originalElement.clone(),t.ghost.css({opacity:.25,display:"block",position:"relative",height:e.height,width:e.width,margin:0,left:0,top:0}).addClass("ui-resizable-ghost").addClass("string"==typeof i.ghost?i.ghost:""),t.ghost.appendTo(t.helper)},resize:function(){var t=w(this).resizable("instance");t.ghost&&t.ghost.css({position:"relative",height:t.size.height,width:t.size.width})},stop:function(){var t=w(this).resizable("instance");t.ghost&&t.helper&&t.helper.get(0).removeChild(t.ghost.get(0))}}),w.ui.plugin.add("resizable","grid",{resize:function(){var t,i=w(this).resizable("instance"),e=i.options,s=i.size,h=i.originalSize,n=i.originalPosition,o=i.axis,a="number"==typeof e.grid?[e.grid,e.grid]:e.grid,l=a[0]||1,r=a[1]||1,p=Math.round((s.width-h.width)/l)*l,d=Math.round((s.height-h.height)/r)*r,g=h.width+p,u=h.height+d,m=e.maxWidth&&e.maxWidth<g,f=e.maxHeight&&e.maxHeight<u,c=e.minWidth&&e.minWidth>g,z=e.minHeight&&e.minHeight>u;e.grid=a,c&&(g+=l),z&&(u+=r),m&&(g-=l),f&&(u-=r),/^(se|s|e)$/.test(o)?(i.size.width=g,i.size.height=u):/^(ne)$/.test(o)?(i.size.width=g,i.size.height=u,i.position.top=n.top-d):/^(sw)$/.test(o)?(i.size.width=g,i.size.height=u,i.position.left=n.left-p):((u-r<=0||g-l<=0)&&(t=i._getPaddingPlusBorderDimensions(this)),0<u-r?(i.size.height=u,i.position.top=n.top-d):(u=r-t.height,i.size.height=u,i.position.top=n.top+h.height-u),0<g-l?(i.size.width=g,i.position.left=n.left-p):(g=l-t.width,i.size.width=g,i.position.left=n.left+h.width-g))}}),w.ui.resizable});