!function(e){function t(n){if(a[n])return a[n].exports;var o=a[n]={i:n,l:!1,exports:{}};return e[n].call(o.exports,o,o.exports,t),o.l=!0,o.exports}var a={};t.m=e,t.c=a,t.d=function(e,a,n){t.o(e,a)||Object.defineProperty(e,a,{configurable:!1,enumerable:!0,get:n})},t.n=function(e){var a=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(a,"a",a),a},t.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},t.p="",t(t.s=1)}([function(e,t,a){var n,o;!function(){"use strict";function a(){for(var e=[],t=0;t<arguments.length;t++){var n=arguments[t];if(n){var o=typeof n;if("string"===o||"number"===o)e.push(n);else if(Array.isArray(n)&&n.length){var r=a.apply(null,n);r&&e.push(r)}else if("object"===o)for(var i in n)l.call(n,i)&&n[i]&&e.push(i)}}return e.join(" ")}var l={}.hasOwnProperty;"undefined"!==typeof e&&e.exports?(a.default=a,e.exports=a):(n=[],void 0!==(o=function(){return a}.apply(t,n))&&(e.exports=o))}()},function(e,t,a){"use strict";Object.defineProperty(t,"__esModule",{value:!0});a(2)},function(e,t,a){"use strict";function n(e,t,a){return t in e?Object.defineProperty(e,t,{value:a,enumerable:!0,configurable:!0,writable:!0}):e[t]=a,e}var o=a(0),l=a.n(o),r=a(3),i=a(4),d=(a.n(i),a(5)),c=(a.n(d),Object.assign||function(e){for(var t=1;t<arguments.length;t++){var a=arguments[t];for(var n in a)Object.prototype.hasOwnProperty.call(a,n)&&(e[n]=a[n])}return e}),__=wp.i18n.__,s=wp.blocks.registerBlockType,m=wp.components,g=m.RangeControl,u=m.SelectControl,p=m.TextControl,b=m.ToggleControl,v=(m.Dashicon,m.IconButton,m.Button),f=(m.Toolbar,m.PanelBody),w=(m.RadioControl,m.TabPanel),h=m.FocalPointPicker,y=wp.editor.InspectorControls?wp.editor:wp.blocks,x=y.InspectorControls,C=y.BlockControls,E=(y.ColorPalette,y.AlignmentToolbar,y.RichText,y.URLInput,y.MediaUpload),k=wp.editor,B=k.PanelColorSettings,P=k.BlockAlignmentToolbar,I=k.InnerBlocks,A=wp.element.Fragment;s("gosign/gosign-background-contianer",{title:__("Gosign Background Container"),icon:{background:"#fff",foreground:"#ff6f00",src:"vault"},category:"common",keywords:[__("background"),__("container"),__("parallax")],attributes:{minHeight:{default:"no-min-height",type:"string"},customHeight:{default:170,type:"int"},insideConCustomWidth:{default:"100%",type:"int"},youtubsLink:{default:"",type:"string"},customPadding:{default:50,type:"number"},customPaddinglR:{default:50,type:"number"},customMargin:{default:50,type:"number"},customMarginlR:{default:50,type:"number"},customPaddingInside:{default:!1,type:"boolean"},customPaddingInsideT:{default:20,type:"number"},customPaddingInsideB:{default:20,type:"number"},customPaddingInsideLR:{default:20,type:"number"},padding:{default:"default-padding",type:"string"},margin:{default:"no-margin",type:"string"},insidecontainerWidth:{default:"full-width",type:"string"},align:{type:"string",default:"full"},topBorder:{type:"string",default:"no-top-border"},bottomBorder:{type:"string",default:"no-bottom-border"},scrollArrow:{type:"boolean",default:!1},arrowColor:{type:"string",default:"#000000"},diagonalBorderDirection:{type:"string",default:"left-to-right-diagonal"},diagonalBorderColor:{type:"string",default:"#000000"},diagonalBorderTopDirection:{type:"string",default:"left-to-right-diagonal"},diagonalBorderColorTop:{type:"string",default:"#000000"},sectionColor:{type:"string",default:"#000000"},mediaID:{type:"int"},mediaURL:{type:"string",default:""},mediaID2:{type:"int"},mediaURL2:{type:"string",default:""},backgroundAttachment:{type:"string",default:"scroll"},backgroundImagePosition:{type:"string",default:"top left"},backgroundRepeat:{type:"string",default:"repeat"},customBackgroundColor:{type:"string",default:""},enableOverlay:{type:"boolean",default:!1},checkAlignmentInside:{type:"boolean",default:!1},overlayColor:{type:"string",default:""},contentColor:{type:"string",default:"lightColorLayout"},contentAlignment:{type:"string",default:"verticalAlignTop"},overlayOpacity:{type:"string",default:"0.1"},colorSectionFe:{type:"string",default:"colorSectionFe"},cssID:{type:"int",default:""},focalPoint:{type:"object"}},getEditWrapperProps:function(e){var t=e.align;if("left"===t||"right"===t||"wide"===t||"full"===t)return{"data-align":t}},edit:function(e){function t(t){e.setAttributes({mediaURL:t.url,mediaID:t.id,youtubsLink:"",mediaURL2:"",mediaID2:""})}function a(){de({mediaURL:"",mediaID:""})}function o(t){e.setAttributes({youtubsLink:"",mediaURL2:t.url,mediaID2:t.id})}function r(){de({mediaURL2:"",mediaID2:""})}var i=e.attributes,d=i.youtubsLink,c=i.checkAlignmentInside,s=i.insidecontainerWidth,m=i.contentAlignment,y=i.contentColor,k=(i.colorSectionFe,i.align),R=i.minHeight,O=i.padding,L=i.margin,N=i.topBorder,D=i.bottomBorder,T=i.scrollArrow,S=i.arrowColor,j=i.customHeight,W=i.customPadding,M=i.customPaddinglR,U=i.customMargin,H=i.customMarginlR,F=i.customPaddingInside,V=i.customPaddingInsideT,_=i.customPaddingInsideB,G=i.customPaddingInsideLR,Y=i.diagonalBorderDirection,q=i.diagonalBorderColor,z=i.diagonalBorderTopDirection,J=i.diagonalBorderColorTop,K=(i.sectionColor,i.mediaID),Q=i.mediaURL,X=i.mediaID2,Z=i.mediaURL2,$=i.customBackgroundColor,ee=i.backgroundAttachment,te=(i.backgroundImagePosition,i.backgroundRepeat),ae=i.enableOverlay,ne=i.overlayColor,oe=i.overlayOpacity,le=i.insideConCustomWidth,re=(i.maxWidthDefine,i.cssID),ie=i.focalPoint,de=e.setAttributes,ce={width:400,height:100},se=function(e){},me=function(t){e.setAttributes({mediaURL:"",mediaID:""})};return wp.element.createElement("div",{className:"gosign-color-section-wrapper"},wp.element.createElement(C,null,wp.element.createElement(P,{value:k,onChange:function(t){e.setAttributes({align:t})},controls:["wide","full"]})),wp.element.createElement(x,null,wp.element.createElement(f,{title:"Section Layout",icon:"category",initialOpen:!1,className:"colorBlockIn"},wp.element.createElement(u,{label:"Section Minimum Height",value:R,options:[{label:"No minimum height, use content within section to define Section height",value:"no-min-height"},{label:"At least 100% of Browser Window height",value:"min-height-100"},{label:"At least 75% of Browser Window height",value:"min-height-75"},{label:"At least 50% of Browser Window height",value:"min-height-50"},{label:"At least 25% of Browser Window height",value:"min-height-25"},{label:"Custom height in pixel",value:"custom-height"}],onChange:function(e){de({minHeight:e})}}),"custom-height"==R&&wp.element.createElement(p,{label:"Custom Height",value:j,onChange:function(e){return de({customHeight:e})}}),wp.element.createElement(u,{label:"Section Padding",value:O,options:[{label:"Default Padding",value:"default-padding"},{label:"Small Padding",value:"small-padding"},{label:"Large Padding",value:"large-padding"},{label:"Huge Padding",value:"huge-padding"},{label:"No Padding",value:"no-padding"},{label:"Custom Padding",value:"custom-padding"}],onChange:function(e){de({padding:e})}}),"custom-padding"==O&&wp.element.createElement(A,null,wp.element.createElement(g,{label:__("Padding Top And Bottom"),value:W,min:"10",max:"200",step:"1",onChange:function(t){e.setAttributes({customPadding:t})}}),wp.element.createElement(g,{label:__("Padding Left And Right"),value:M,min:"10",max:"200",step:"1",onChange:function(t){e.setAttributes({customPaddinglR:t})}})),wp.element.createElement(u,{label:"Section Margin",value:L,options:[{label:"No Margin",value:"no-margin"},{label:"Small Margin",value:"small-margin"},{label:"Large Margin",value:"large-margin"},{label:"Custom Margin",value:"custom-margin"}],onChange:function(e){de({margin:e})}}),"custom-margin"==L&&wp.element.createElement(A,null,wp.element.createElement(g,{label:__("Margin Top And Bottom"),value:U,min:"0",max:"200",step:"1",onChange:function(t){e.setAttributes({customMargin:t})}}),wp.element.createElement(g,{label:__("Margin Left And Right"),value:H,min:"0",max:"200",step:"1",onChange:function(t){e.setAttributes({customMarginlR:t})}})),wp.element.createElement(u,{label:"Section Top Border Styling",value:N,options:[{label:"No border styling",value:"no-top-border"},{label:"Display a simple 1px top border",value:"top-border"},{label:"Diagonal section border at top",value:"top-border-extra-diagonal"},{label:"Display a small styling shadow at the top of the section",value:"top-shadow"}],onChange:function(e){de({topBorder:e})}}),"top-border-extra-diagonal"==N&&wp.element.createElement(A,null,wp.element.createElement(u,{label:"Top Diagonal Border: Direction",className:"diagonal-border-top-direction diagonalborder",value:z,options:[{label:"Slanting from left to right",value:"left-to-right-diagonal"},{label:"Slanting from right to left",value:"right-to-left-diagonal"}],onChange:function(e){de({diagonalBorderTopDirection:e})}}),wp.element.createElement(B,{title:__("Border Top Color"),initialOpen:!1,className:"border-diagonal-color-top diagonalborder",colorSettings:[{value:J,onChange:function(t){return e.setAttributes({diagonalBorderColorTop:t})},label:__("Choose Color")}]})),wp.element.createElement(u,{label:"Section Bottom Border Styling",value:D,className:"border-styling",options:[{label:"No border styling",value:"no-bottom-border"},{label:"Display a small arrow that points down to the next section",value:"border-extra-arrow-down"},{label:"Diagonal section border at bottom",value:"border-extra-diagonal"}],onChange:function(e){de({bottomBorder:e})}}),"border-extra-diagonal"==D&&wp.element.createElement(A,null,wp.element.createElement(u,{label:"Bottom Diagonal Border: Direction",className:"diagonal-border-direction diagonalborder",value:Y,options:[{label:"Slanting from left to right",value:"left-to-right-diagonal"},{label:"Slanting from right to left",value:"right-to-left-diagonal"}],onChange:function(e){de({diagonalBorderDirection:e})}}),wp.element.createElement(B,{title:__("Border Bottom Color"),initialOpen:!1,className:"border-diagonal-color diagonalborder",colorSettings:[{value:q,onChange:function(t){return e.setAttributes({diagonalBorderColor:t})},label:__("Choose Color")}]}))),wp.element.createElement(f,{title:"Section Background",icon:"category",initialOpen:!0,className:"colorBlockIn"},wp.element.createElement(w,{className:"my-tab-panel",activeClass:"active-tab",onSelect:se,tabs:[{name:"tab1",title:"Color",className:"tab-one"},{name:"tab2",title:"Image",className:"tab-two"},{name:"tab3",title:"Video",className:"tab-three"}]},function(n){return wp.element.createElement(A,null,"tab1"==n.name&&wp.element.createElement(B,{title:__("Custom Background Color"),colorSettings:[{value:$,onChange:function(t){return e.setAttributes({customBackgroundColor:t})},label:__("Choose Color")}]}),"tab2"==n.name&&wp.element.createElement(A,null,wp.element.createElement(E,{onSelect:t,type:"image",value:K,render:function(e){var t=e.open;return wp.element.createElement(A,null,wp.element.createElement(v,{className:K?"image-button":"button button-large",onClick:t},K?wp.element.createElement("img",{src:Q}):__("Upload Image")),K&&wp.element.createElement(v,{className:K?"image-button":"button button-large-remove",onClick:a},K?wp.element.createElement("div",{class:"remove-image"}," Remove Image"):" "),K&&wp.element.createElement(v,{className:K?"image-button":"button button-large-replace",onClick:t},K?wp.element.createElement("div",{class:"replace-image"}," Replace Image"):" "))}}),Q&&wp.element.createElement(h,{label:__("Focal Point Picker"),url:Q,dimensions:ce,value:ie,onChange:function(e){return de({focalPoint:e})}}),wp.element.createElement(u,{label:"Background Attachment",value:ee,options:[{label:"Scroll",value:"scroll"},{label:"Fixed",value:"fixed"},{label:"parallax",value:"parallax"},{label:"Parallax on hover",value:"parallax-mouse"}],onChange:function(e){de({backgroundAttachment:e})}}),wp.element.createElement(u,{label:"Background Repeat",value:te,options:[{label:"No Repeat",value:"no-repeat"},{label:"Repeat",value:"repeat"},{label:"Stretch to fit (stretches image to cover the element)",value:"cover"},{label:"Scale to fit (scales image so the whole image is always visible)",value:"contain"}],onChange:function(e){de({backgroundRepeat:e})}})),"tab3"==n.name&&wp.element.createElement(A,null,wp.element.createElement(w,{className:"my-tab-panel",activeClass:"active-tab",onSelect:me,tabs:[{name:"tab1",title:"Upload",className:"tab-one"},{name:"tab2",title:"Youtube",className:"tab-two"}]},function(e){return wp.element.createElement(A,null,"tab1"==e.name&&wp.element.createElement(E,{onSelect:o,type:"video",value:X,render:function(e){var t=e.open;return wp.element.createElement(A,null,wp.element.createElement(v,{className:X?"video-button":"button button-large",onClick:t},X?wp.element.createElement("video",{muted:!0,controls:!0},wp.element.createElement("source",{src:Z})):__("Select Video")),X&&wp.element.createElement(v,{className:X?"image-button":"button button-large-remove",onClick:r},X?wp.element.createElement("div",{class:"remove-image"}," Remove Video"):" "),X&&wp.element.createElement(v,{className:X?"image-button":"button button-large-replace",onClick:t},X?wp.element.createElement("div",{class:"replace-image"}," Replace Video"):" "))}}),"tab2"==e.name&&wp.element.createElement(A,null,d&&wp.element.createElement("video",{muted:!0,controls:!0},wp.element.createElement("source",{src:d})),wp.element.createElement(p,{label:"Video URL",value:d,onChange:function(e){return de({youtubsLink:e})}})))})))})),wp.element.createElement(f,{title:"Section Background Overlay",icon:"category",initialOpen:!1,className:"colorBlockIn"},wp.element.createElement(b,{label:__("Enable Overlay?"),checked:ae,onChange:function(e){return de({enableOverlay:e})}}),ae&&wp.element.createElement(A,null,wp.element.createElement(B,{title:__("Overlay Color"),colorSettings:[{value:ne,onChange:function(t){return e.setAttributes({overlayColor:t})},label:__("Overlay Color")}]}),wp.element.createElement(u,{label:"Overlay Opacity",value:oe,options:[{label:"0.1",value:"0.1"},{label:"0.2",value:"0.2"},{label:"0.3",value:"0.3"},{label:"0.4",value:"0.4"},{label:"0.5",value:"0.5"},{label:"0.6",value:"0.6"},{label:"0.7",value:"0.7"},{label:"0.8",value:"0.8"},{label:"0.9",value:"0.9"},{label:"1.0",value:"1.0"}],onChange:function(e){de({overlayOpacity:e})}}))),wp.element.createElement(f,{title:"Inside Container Options",icon:"category",initialOpen:!1,className:"colorBlockIn"},wp.element.createElement(u,{label:"Set Max Width For Inside Section",value:s,options:[{label:"Full Width",value:"full-width"},{label:"Boxed Width",value:"boxed-width"},{label:"Custom Width",value:"custom-width"}],onChange:function(e){de({insidecontainerWidth:e})}}),"custom-width"==s&&wp.element.createElement(p,{label:"Inside Container Max Width in 'px' '%'",value:le,onChange:function(e){return de({insideConCustomWidth:e})}}),wp.element.createElement(b,{label:__("Set Custom Padding For Inside Section"),checked:F,onChange:function(e){return de({customPaddingInside:e})}}),F&&wp.element.createElement(A,null,wp.element.createElement(g,{label:__("Padding Top"),value:V,min:"10",max:"200",step:"1",onChange:function(t){e.setAttributes({customPaddingInsideT:t})}}),wp.element.createElement(g,{label:__("Padding Bottom"),value:_,min:"10",max:"200",step:"1",onChange:function(t){e.setAttributes({customPaddingInsideB:t})}}),wp.element.createElement(g,{label:__("Padding Left And Right"),value:G,min:"10",max:"200",step:"1",onChange:function(t){e.setAttributes({customPaddingInsideLR:t})}})),wp.element.createElement(b,{label:__("Set Alignment for inside content"),checked:c,onChange:function(e){return de({checkAlignmentInside:e})}}),c&&wp.element.createElement(u,{label:"Content Alignment",value:m,options:[{label:"Vertical Align Top",value:"verticalAlignTop"},{label:"Vertical Align Middle",value:"verticalAlignMiddle"},{label:"Vertical Align Bottom",value:"verticalAlignBottom"}],onChange:function(e){de({contentAlignment:e})}}),wp.element.createElement(u,{label:"Content Color",value:y,options:[{label:"Light Colored Content",value:"lightColorLayout"},{label:"Dark Colored Content",value:"darkColorLayout"}],onChange:function(e){de({contentColor:e})}})),wp.element.createElement(f,{title:"HTML-Anchor(CSS Id)",initialOpen:!1,className:"colorBlockIn"},wp.element.createElement(p,{label:"Id",value:re,onChange:function(e){return de({cssID:e})}}))),wp.element.createElement("div",{style:Object.assign({},K?{backgroundImage:"url("+Q+")"}:{},ie&&{backgroundPosition:100*ie.x+"% "+100*ie.y+"%"},$?{backgroundColor:$}:{},"fixedBackground"==ee?{backgroundAttachment:"fixed"}:{}),className:l()("gosign-color-section",y,n({},"btn-"+k,!0),O,L,R,N,D,n({},""+Y,"border-extra-diagonal"==D),{"custom-arrow":T})},ae&&wp.element.createElement("div",{class:"overBg",style:Object.assign({},ne?{backgroundColor:ne}:{},oe?{opacity:oe}:{})}),wp.element.createElement("div",{className:"outer-container"},wp.element.createElement("div",{className:"inner-container"},wp.element.createElement("div",{className:"container-wrapper"},wp.element.createElement(I,null),T&&wp.element.createElement("div",{className:"bottomArrow",style:{color:S}}),Y&&wp.element.createElement("div",{className:"diagonal-border",style:{backgroundColor:q}}," "))))))},save:function(e){var t=e.attributes,a=t.youtubsLink,o=t.checkAlignmentInside,r=t.insidecontainerWidth,i=t.contentColor,d=t.colorSectionFe,s=t.align,m=t.minHeight,g=t.padding,u=t.margin,p=t.topBorder,b=t.bottomBorder,v=t.scrollArrow,f=t.arrowColor,w=t.customHeight,h=t.customPadding,y=t.customPaddinglR,x=t.customMargin,C=t.customMarginlR,E=t.customPaddingInside,k=t.customPaddingInsideT,B=t.customPaddingInsideB,P=t.customPaddingInsideLR,A=t.diagonalBorderDirection,R=t.diagonalBorderColor,O=t.diagonalBorderTopDirection,L=t.diagonalBorderColorTop,N=(t.sectionColor,t.mediaID),D=t.mediaURL,T=(t.mediaID2,t.mediaURL2),S=t.customBackgroundColor,j=t.backgroundAttachment,W=(t.backgroundImagePosition,t.backgroundRepeat),M=t.enableOverlay,U=t.overlayColor,H=t.overlayOpacity,F=t.insideConCustomWidth,V=(t.maxWidthDefine,t.contentAlignment),_=t.cssID,G=t.focalPoint,Y="";return"parallax"==j&&(Y={"data-parallax":"scroll","data-image-src":D}),wp.element.createElement("div",{id:_,className:l()("gosign-color-section",n({},"btn-"+s,!0),n({},"align-"+o,!0),n({},"def-"+V,1==o),g,u,r,m,i,p,b,d,n({},""+A,"border-extra-diagonal"==b),n({},""+O,"top-border-extra-diagonal"==p),{"custom-arrow":v},n({},"background-"+j,!0),n({},"background-"+W,!0),n({},"overlay-"+M,!0),{"defined-width":F}),style:Object.assign({},"custom-margin"==u?{marginTop:x+"px",marginBottom:x+"px"}:{})},"top-border-extra-diagonal"==p&&"left-to-right-diagonal"==O&&wp.element.createElement("div",{class:"extra-border-element top-border-extra-diagonal  diagonal-box-shadow"},wp.element.createElement("div",{class:"extra-border-outer"},wp.element.createElement("div",{class:"extra-border-inner",style:Object.assign({},L?{backgroundColor:L}:{})}))),"top-border-extra-diagonal"==p&&"right-to-left-diagonal"==O&&wp.element.createElement("div",{class:"extra-border-element top-border-extra-diagonal border-extra-diagonal-inverse "},wp.element.createElement("div",{class:"extra-border-outer"},wp.element.createElement("div",{class:"extra-border-inner",style:Object.assign({},L?{backgroundColor:L}:{})}))),a&&wp.element.createElement("div",{className:"video-container youtube-video"},wp.element.createElement("video",{muted:!0,autoPlay:!0,loop:!0},wp.element.createElement("source",{src:a}))),T&&wp.element.createElement("div",{className:"video-container upload-video"},wp.element.createElement("video",{muted:!0,autoPlay:!0,loop:!0},wp.element.createElement("source",{src:T}))),wp.element.createElement("div",c({},Y,{className:l()("outer-container",n({},"background-"+j,!0)),style:Object.assign({},"custom-height"==m?{minHeight:w+"px"}:{},N&&"parallax"!=j?{backgroundImage:"url("+D+")"}:{},"custom-padding"==g?{paddingTop:h+"px",paddingBottom:h+"px"}:{},"custom-padding"==g?{paddingLeft:y+"px",paddingRight:y+"px"}:{},"custom-margin"==u?{marginLeft:C+"px",marginRight:C+"px"}:{},E?{paddingTop:k+"px",paddingBottom:B+"px"}:{},E?{paddingLeft:P+"px",paddingRight:P+"px"}:{},G&&{backgroundPosition:100*G.x+"% "+100*G.y+"%"},"fixed"==j?{backgroundAttachment:"fixed"}:{},"no-repeat"==W||"repeat"==W?{backgroundRepeat:W}:{},S?{backgroundColor:S}:{})}),M&&wp.element.createElement("div",{class:"overBg",style:Object.assign({},U?{backgroundColor:U}:{},H?{opacity:H}:{})}),wp.element.createElement("div",{className:"inner-container"},wp.element.createElement("div",{className:"contentBlock",style:Object.assign({},"custom-width"==r?{maxWidth:F}:{})},wp.element.createElement(I.Content,null),v&&wp.element.createElement("div",{className:"bottomArrow",style:{color:f}})))),"border-extra-arrow-down"==b&&wp.element.createElement("div",{class:"extra-border-element border-extra-arrow-down"},wp.element.createElement("div",{class:"extra-border-outer"},wp.element.createElement("div",{class:"extra-border-inner",style:Object.assign({},S?{backgroundColor:S}:{})}))),"border-extra-diagonal"==b&&"left-to-right-diagonal"==A&&wp.element.createElement("div",{class:"extra-border-element border-extra-diagonal  diagonal-box-shadow"},wp.element.createElement("div",{class:"extra-border-outer"},wp.element.createElement("div",{class:"extra-border-inner",style:Object.assign({},R?{backgroundColor:R}:{})}))),"border-extra-diagonal"==b&&"right-to-left-diagonal"==A&&wp.element.createElement("div",{class:"extra-border-element border-extra-diagonal border-extra-diagonal-inverse "},wp.element.createElement("div",{class:"extra-border-outer"},wp.element.createElement("div",{class:"extra-border-inner",style:Object.assign({},R?{backgroundColor:R}:{})}))))},deprecated:r.a})},function(e,t,a){"use strict";function n(e,t,a){return t in e?Object.defineProperty(e,t,{value:a,enumerable:!0,configurable:!0,writable:!0}):e[t]=a,e}var o=a(0),l=a.n(o),r=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var a=arguments[t];for(var n in a)Object.prototype.hasOwnProperty.call(a,n)&&(e[n]=a[n])}return e},i=wp.editor.InnerBlocks,d={minHeight:{default:"no-min-height",type:"string"},customHeight:{default:170,type:"int"},insideConCustomWidth:{default:"100%",type:"int"},youtubsLink:{default:"",type:"string"},customPadding:{default:50,type:"number"},customPaddinglR:{default:50,type:"number"},customMargin:{default:50,type:"number"},customMarginlR:{default:50,type:"number"},customPaddingInside:{default:!1,type:"boolean"},customPaddingInsideT:{default:20,type:"number"},customPaddingInsideB:{default:20,type:"number"},customPaddingInsideLR:{default:20,type:"number"},padding:{default:"default-padding",type:"string"},margin:{default:"no-margin",type:"string"},insidecontainerWidth:{default:"full-width",type:"string"},align:{type:"string",default:"full"},topBorder:{type:"string",default:"no-top-border"},bottomBorder:{type:"string",default:"no-bottom-border"},scrollArrow:{type:"boolean",default:!1},arrowColor:{type:"string",default:"#000000"},diagonalBorderDirection:{type:"string",default:"left-to-right-diagonal"},diagonalBorderColor:{type:"string",default:"#000000"},diagonalBorderTopDirection:{type:"string",default:"left-to-right-diagonal"},diagonalBorderColorTop:{type:"string",default:"#000000"},sectionColor:{type:"string",default:"#000000"},mediaID:{type:"int"},mediaURL:{type:"string",default:""},mediaID2:{type:"int"},mediaURL2:{type:"string",default:""},backgroundAttachment:{type:"string",default:"scroll"},backgroundImagePosition:{type:"string",default:"top left"},backgroundRepeat:{type:"string",default:"repeat"},customBackgroundColor:{type:"string",default:""},enableOverlay:{type:"boolean",default:!1},checkAlignmentInside:{type:"boolean",default:!1},overlayColor:{type:"string",default:""},contentColor:{type:"string",default:"lightColorLayout"},contentAlignment:{type:"string",default:"verticalAlignTop"},overlayOpacity:{type:"string",default:"0.1"},colorSectionFe:{type:"string",default:"colorSectionFe"}};t.a=[{attributes:d,save:function(e){var t=e.attributes,a=t.youtubsLink,o=t.checkAlignmentInside,d=t.insidecontainerWidth,c=t.contentColor,s=t.colorSectionFe,m=t.align,g=t.minHeight,u=t.padding,p=t.margin,b=t.topBorder,v=t.bottomBorder,f=t.scrollArrow,w=t.arrowColor,h=t.customHeight,y=t.customPadding,x=t.customPaddinglR,C=t.customMargin,E=t.customMarginlR,k=t.customPaddingInside,B=t.customPaddingInsideT,P=t.customPaddingInsideB,I=t.customPaddingInsideLR,A=t.diagonalBorderDirection,R=t.diagonalBorderColor,O=t.diagonalBorderTopDirection,L=t.diagonalBorderColorTop,N=(t.sectionColor,t.mediaID),D=t.mediaURL,T=(t.mediaID2,t.mediaURL2),S=t.customBackgroundColor,j=t.backgroundAttachment,W=t.backgroundImagePosition,M=t.backgroundRepeat,U=t.enableOverlay,H=t.overlayColor,F=t.overlayOpacity,V=t.insideConCustomWidth,_=(t.maxWidthDefine,t.contentAlignment),G=t.cssID,Y="";return"parallax"==j&&(Y={"data-parallax":"scroll","data-image-src":D}),wp.element.createElement("div",{id:G,className:l()("gosign-color-section",n({},"btn-"+m,!0),n({},"align-"+o,!0),n({},"def-"+_,1==o),u,p,d,g,c,b,v,s,n({},""+A,"border-extra-diagonal"==v),n({},""+O,"top-border-extra-diagonal"==b),{"custom-arrow":f},n({},"background-"+j,!0),n({},"background-"+M,!0),n({},"overlay-"+U,!0),{"defined-width":V}),style:Object.assign({},"custom-margin"==p?{marginTop:C+"px",marginBottom:C+"px"}:{})},"top-border-extra-diagonal"==b&&"left-to-right-diagonal"==O&&wp.element.createElement("div",{class:"extra-border-element top-border-extra-diagonal  diagonal-box-shadow"},wp.element.createElement("div",{class:"extra-border-outer"},wp.element.createElement("div",{class:"extra-border-inner",style:Object.assign({},L?{backgroundColor:L}:{})}))),"top-border-extra-diagonal"==b&&"right-to-left-diagonal"==O&&wp.element.createElement("div",{class:"extra-border-element top-border-extra-diagonal border-extra-diagonal-inverse "},wp.element.createElement("div",{class:"extra-border-outer"},wp.element.createElement("div",{class:"extra-border-inner",style:Object.assign({},L?{backgroundColor:L}:{})}))),a&&wp.element.createElement("div",{className:"video-container youtube-video"},wp.element.createElement("video",{muted:!0,autoPlay:!0,loop:!0},wp.element.createElement("source",{src:a}))),T&&wp.element.createElement("div",{className:"video-container upload-video"},wp.element.createElement("video",{muted:!0,autoPlay:!0,loop:!0},wp.element.createElement("source",{src:T}))),wp.element.createElement("div",r({},Y,{className:l()("outer-container",n({},"background-"+j,!0)),style:Object.assign({},"custom-height"==g?{minHeight:h+"px"}:{},N&&"parallax"!=j?{backgroundImage:"url("+D+")"}:{},"custom-padding"==u?{paddingTop:y+"px",paddingBottom:y+"px"}:{},"custom-padding"==u?{paddingLeft:x+"px",paddingRight:x+"px"}:{},"custom-margin"==p?{marginLeft:E+"px",marginRight:E+"px"}:{},k?{paddingTop:B+"px",paddingBottom:P+"px"}:{},k?{paddingLeft:I+"px",paddingRight:I+"px"}:{},"fixed"==j?{backgroundAttachment:"fixed"}:{},W?{backgroundPosition:W}:{},"no-repeat"==M||"repeat"==M?{backgroundRepeat:M}:{},S?{backgroundColor:S}:{})}),U&&wp.element.createElement("div",{class:"overBg",style:Object.assign({},H?{backgroundColor:H}:{},F?{opacity:F}:{})}),wp.element.createElement("div",{className:"inner-container"},wp.element.createElement("div",{className:"contentBlock",style:Object.assign({},"custom-width"==d?{maxWidth:V}:{})},wp.element.createElement(i.Content,null),f&&wp.element.createElement("div",{className:"bottomArrow",style:{color:w}})))),"border-extra-arrow-down"==v&&wp.element.createElement("div",{class:"extra-border-element border-extra-arrow-down"},wp.element.createElement("div",{class:"extra-border-outer"},wp.element.createElement("div",{class:"extra-border-inner",style:Object.assign({},S?{backgroundColor:S}:{})}))),"border-extra-diagonal"==v&&"left-to-right-diagonal"==A&&wp.element.createElement("div",{class:"extra-border-element border-extra-diagonal  diagonal-box-shadow"},wp.element.createElement("div",{class:"extra-border-outer"},wp.element.createElement("div",{class:"extra-border-inner",style:Object.assign({},R?{backgroundColor:R}:{})}))),"border-extra-diagonal"==v&&"right-to-left-diagonal"==A&&wp.element.createElement("div",{class:"extra-border-element border-extra-diagonal border-extra-diagonal-inverse "},wp.element.createElement("div",{class:"extra-border-outer"},wp.element.createElement("div",{class:"extra-border-inner",style:Object.assign({},R?{backgroundColor:R}:{})}))))}},{attributes:d,save:function(e){var t=e.attributes,a=t.youtubsLink,o=t.checkAlignmentInside,d=t.insidecontainerWidth,c=t.contentColor,s=t.colorSectionFe,m=t.align,g=t.minHeight,u=t.padding,p=t.margin,b=t.topBorder,v=t.bottomBorder,f=t.scrollArrow,w=t.arrowColor,h=t.customHeight,y=t.customPadding,x=t.customPaddinglR,C=t.customMargin,E=t.customMarginlR,k=t.customPaddingInside,B=t.customPaddingInsideT,P=t.customPaddingInsideB,I=t.customPaddingInsideLR,A=t.diagonalBorderDirection,R=t.diagonalBorderColor,O=t.diagonalBorderTopDirection,L=t.diagonalBorderColorTop,N=(t.sectionColor,t.mediaID),D=t.mediaURL,T=(t.mediaID2,t.mediaURL2),S=t.customBackgroundColor,j=t.backgroundAttachment,W=t.backgroundImagePosition,M=t.backgroundRepeat,U=t.enableOverlay,H=t.overlayColor,F=t.overlayOpacity,V=t.insideConCustomWidth,_=(t.maxWidthDefine,t.contentAlignment),G="";return"parallax"==j&&(G={"data-parallax":"scroll","data-image-src":D}),wp.element.createElement("div",{className:l()("gosign-color-section",n({},"btn-"+m,!0),n({},"align-"+o,!0),n({},"def-"+_,1==o),u,p,d,g,c,b,v,s,n({},""+A,"border-extra-diagonal"==v),n({},""+O,"top-border-extra-diagonal"==b),{"custom-arrow":f},n({},"background-"+j,!0),n({},"background-"+M,!0),n({},"overlay-"+U,!0),{"defined-width":V}),style:Object.assign({},"custom-margin"==p?{marginTop:C+"px",marginBottom:C+"px"}:{})},"top-border-extra-diagonal"==b&&"left-to-right-diagonal"==O&&wp.element.createElement("div",{class:"extra-border-element top-border-extra-diagonal  diagonal-box-shadow"},wp.element.createElement("div",{class:"extra-border-outer"},wp.element.createElement("div",{class:"extra-border-inner",style:Object.assign({},L?{backgroundColor:L}:{})}))),"top-border-extra-diagonal"==b&&"right-to-left-diagonal"==O&&wp.element.createElement("div",{class:"extra-border-element top-border-extra-diagonal border-extra-diagonal-inverse "},wp.element.createElement("div",{class:"extra-border-outer"},wp.element.createElement("div",{class:"extra-border-inner",style:Object.assign({},L?{backgroundColor:L}:{})}))),a&&wp.element.createElement("div",{className:"video-container youtube-video"},wp.element.createElement("video",{muted:!0,autoPlay:!0,loop:!0},wp.element.createElement("source",{src:a}))),T&&wp.element.createElement("div",{className:"video-container upload-video"},wp.element.createElement("video",{muted:!0,autoPlay:!0,loop:!0},wp.element.createElement("source",{src:T}))),wp.element.createElement("div",r({},G,{className:l()("outer-container",n({},"background-"+j,!0)),style:Object.assign({},"custom-height"==g?{minHeight:h+"px"}:{},N&&"parallax"!=j?{backgroundImage:"url("+D+")"}:{},"custom-padding"==u?{paddingTop:y+"px",paddingBottom:y+"px"}:{},"custom-padding"==u?{paddingLeft:x+"px",paddingRight:x+"px"}:{},"custom-margin"==p?{marginLeft:E+"px",marginRight:E+"px"}:{},k?{paddingTop:B+"px",paddingBottom:P+"px"}:{},k?{paddingLeft:I+"px",paddingRight:I+"px"}:{},"fixed"==j?{backgroundAttachment:"fixed"}:{},W?{backgroundPosition:W}:{},"no-repeat"==M||"repeat"==M?{backgroundRepeat:M}:{},S?{backgroundColor:S}:{})}),U&&wp.element.createElement("div",{class:"overBg",style:Object.assign({},H?{backgroundColor:H}:{},F?{opacity:F}:{})}),wp.element.createElement("div",{className:"inner-container"},wp.element.createElement("div",{className:"contentBlock",style:Object.assign({},"custom-width"==d?{maxWidth:V}:{})},wp.element.createElement(i.Content,null),f&&wp.element.createElement("div",{className:"bottomArrow",style:{color:w}})))),"border-extra-arrow-down"==v&&wp.element.createElement("div",{class:"extra-border-element border-extra-arrow-down"},wp.element.createElement("div",{class:"extra-border-outer"},wp.element.createElement("div",{class:"extra-border-inner",style:Object.assign({},S?{backgroundColor:S}:{})}))),"border-extra-diagonal"==v&&"left-to-right-diagonal"==A&&wp.element.createElement("div",{class:"extra-border-element border-extra-diagonal  diagonal-box-shadow"},wp.element.createElement("div",{class:"extra-border-outer"},wp.element.createElement("div",{class:"extra-border-inner",style:Object.assign({},R?{backgroundColor:R}:{})}))),"border-extra-diagonal"==v&&"right-to-left-diagonal"==A&&wp.element.createElement("div",{class:"extra-border-element border-extra-diagonal border-extra-diagonal-inverse "},wp.element.createElement("div",{class:"extra-border-outer"},wp.element.createElement("div",{class:"extra-border-inner",style:Object.assign({},R?{backgroundColor:R}:{})}))))}}]},function(e,t){},function(e,t){}]);