!function(t){var e={};function r(n){if(e[n])return e[n].exports;var a=e[n]={i:n,l:!1,exports:{}};return t[n].call(a.exports,a,a.exports,r),a.l=!0,a.exports}r.m=t,r.c=e,r.d=function(t,e,n){r.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:n})},r.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},r.t=function(t,e){if(1&e&&(t=r(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var n=Object.create(null);if(r.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var a in t)r.d(n,a,function(e){return t[e]}.bind(null,a));return n},r.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return r.d(e,"a",e),e},r.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},r.p="",r(r.s=11)}({11:function(t,e,r){"use strict";r.r(e);var n=r(8),a=r.n(n),i=document.querySelectorAll("[data-prop1][data-prop2]"),o=[];i.forEach((function(t){o.push(t.getAttribute("data-prop1").slice(2)),o.push(t.getAttribute("data-prop2").slice(2))}));var s=function(t){if(t){var e=t.getAttribute("data-prop1"),r=t.getAttribute("data-prop2"),n=a.a.readability(getComputedStyle(document.documentElement).getPropertyValue(e),getComputedStyle(document.documentElement).getPropertyValue(r));t.textContent=n.toFixed(1);var i=t.closest(".wcag");n>=7?i.classList.add("wcag-aaas"):(i.classList.remove("wcag-aaas"),n>=4.5?i.classList.add("wcag-aaa"):(i.classList.remove("wcag-aaa"),n>=3?i.classList.add("wcag-aa"):i.classList.remove("wcag-aa")))}};i.forEach((function(t){s(t)})),o.forEach((function(t){wp.customize(t,(function(e){e.bind((function(e){var r="--".concat(t);document.documentElement.style.setProperty(r,e);var n=Array.from(i).find((function(t){return t.getAttribute("data-prop1")===r||t.getAttribute("data-prop2")===r}));n&&s(n)}))}))}))},8:function(t,e,r){var n;!function(a){var i=/^\s+/,o=/\s+$/,s=0,f=a.round,u=a.min,l=a.max,c=a.random;function h(t,e){if(e=e||{},(t=t||"")instanceof h)return t;if(!(this instanceof h))return new h(t,e);var r=function(t){var e,r,n,s={r:0,g:0,b:0},f=1,c=null,h=null,g=null,d=!1,b=!1;return"string"==typeof t&&(t=function(t){t=t.replace(i,"").replace(o,"").toLowerCase();var e,r=!1;if(L[t])t=L[t],r=!0;else if("transparent"==t)return{r:0,g:0,b:0,a:0,format:"name"};return(e=U.rgb.exec(t))?{r:e[1],g:e[2],b:e[3]}:(e=U.rgba.exec(t))?{r:e[1],g:e[2],b:e[3],a:e[4]}:(e=U.hsl.exec(t))?{h:e[1],s:e[2],l:e[3]}:(e=U.hsla.exec(t))?{h:e[1],s:e[2],l:e[3],a:e[4]}:(e=U.hsv.exec(t))?{h:e[1],s:e[2],v:e[3]}:(e=U.hsva.exec(t))?{h:e[1],s:e[2],v:e[3],a:e[4]}:(e=U.hex8.exec(t))?{r:O(e[1]),g:O(e[2]),b:O(e[3]),a:I(e[4]),format:r?"name":"hex8"}:(e=U.hex6.exec(t))?{r:O(e[1]),g:O(e[2]),b:O(e[3]),format:r?"name":"hex"}:(e=U.hex4.exec(t))?{r:O(e[1]+""+e[1]),g:O(e[2]+""+e[2]),b:O(e[3]+""+e[3]),a:I(e[4]+""+e[4]),format:r?"name":"hex8"}:!!(e=U.hex3.exec(t))&&{r:O(e[1]+""+e[1]),g:O(e[2]+""+e[2]),b:O(e[3]+""+e[3]),format:r?"name":"hex"}}(t)),"object"==typeof t&&(V(t.r)&&V(t.g)&&V(t.b)?(e=t.r,r=t.g,n=t.b,s={r:255*j(e,255),g:255*j(r,255),b:255*j(n,255)},d=!0,b="%"===String(t.r).substr(-1)?"prgb":"rgb"):V(t.h)&&V(t.s)&&V(t.v)?(c=T(t.s),h=T(t.v),s=function(t,e,r){t=6*j(t,360),e=j(e,100),r=j(r,100);var n=a.floor(t),i=t-n,o=r*(1-e),s=r*(1-i*e),f=r*(1-(1-i)*e),u=n%6;return{r:255*[r,s,o,o,f,r][u],g:255*[f,r,r,s,o,o][u],b:255*[o,o,f,r,r,s][u]}}(t.h,c,h),d=!0,b="hsv"):V(t.h)&&V(t.s)&&V(t.l)&&(c=T(t.s),g=T(t.l),s=function(t,e,r){var n,a,i;function o(t,e,r){return r<0&&(r+=1),r>1&&(r-=1),r<1/6?t+6*(e-t)*r:r<.5?e:r<2/3?t+(e-t)*(2/3-r)*6:t}if(t=j(t,360),e=j(e,100),r=j(r,100),0===e)n=a=i=r;else{var s=r<.5?r*(1+e):r+e-r*e,f=2*r-s;n=o(f,s,t+1/3),a=o(f,s,t),i=o(f,s,t-1/3)}return{r:255*n,g:255*a,b:255*i}}(t.h,c,g),d=!0,b="hsl"),t.hasOwnProperty("a")&&(f=t.a)),f=P(f),{ok:d,format:t.format||b,r:u(255,l(s.r,0)),g:u(255,l(s.g,0)),b:u(255,l(s.b,0)),a:f}}(t);this._originalInput=t,this._r=r.r,this._g=r.g,this._b=r.b,this._a=r.a,this._roundA=f(100*this._a)/100,this._format=e.format||r.format,this._gradientType=e.gradientType,this._r<1&&(this._r=f(this._r)),this._g<1&&(this._g=f(this._g)),this._b<1&&(this._b=f(this._b)),this._ok=r.ok,this._tc_id=s++}function g(t,e,r){t=j(t,255),e=j(e,255),r=j(r,255);var n,a,i=l(t,e,r),o=u(t,e,r),s=(i+o)/2;if(i==o)n=a=0;else{var f=i-o;switch(a=s>.5?f/(2-i-o):f/(i+o),i){case t:n=(e-r)/f+(e<r?6:0);break;case e:n=(r-t)/f+2;break;case r:n=(t-e)/f+4}n/=6}return{h:n,s:a,l:s}}function d(t,e,r){t=j(t,255),e=j(e,255),r=j(r,255);var n,a,i=l(t,e,r),o=u(t,e,r),s=i,f=i-o;if(a=0===i?0:f/i,i==o)n=0;else{switch(i){case t:n=(e-r)/f+(e<r?6:0);break;case e:n=(r-t)/f+2;break;case r:n=(t-e)/f+4}n/=6}return{h:n,s:a,v:s}}function b(t,e,r,n){var a=[q(f(t).toString(16)),q(f(e).toString(16)),q(f(r).toString(16))];return n&&a[0].charAt(0)==a[0].charAt(1)&&a[1].charAt(0)==a[1].charAt(1)&&a[2].charAt(0)==a[2].charAt(1)?a[0].charAt(0)+a[1].charAt(0)+a[2].charAt(0):a.join("")}function p(t,e,r,n){return[q(z(n)),q(f(t).toString(16)),q(f(e).toString(16)),q(f(r).toString(16))].join("")}function m(t,e){e=0===e?0:e||10;var r=h(t).toHsl();return r.s-=e/100,r.s=E(r.s),h(r)}function _(t,e){e=0===e?0:e||10;var r=h(t).toHsl();return r.s+=e/100,r.s=E(r.s),h(r)}function v(t){return h(t).desaturate(100)}function y(t,e){e=0===e?0:e||10;var r=h(t).toHsl();return r.l+=e/100,r.l=E(r.l),h(r)}function A(t,e){e=0===e?0:e||10;var r=h(t).toRgb();return r.r=l(0,u(255,r.r-f(-e/100*255))),r.g=l(0,u(255,r.g-f(-e/100*255))),r.b=l(0,u(255,r.b-f(-e/100*255))),h(r)}function x(t,e){e=0===e?0:e||10;var r=h(t).toHsl();return r.l-=e/100,r.l=E(r.l),h(r)}function w(t,e){var r=h(t).toHsl(),n=(r.h+e)%360;return r.h=n<0?360+n:n,h(r)}function k(t){var e=h(t).toHsl();return e.h=(e.h+180)%360,h(e)}function S(t){var e=h(t).toHsl(),r=e.h;return[h(t),h({h:(r+120)%360,s:e.s,l:e.l}),h({h:(r+240)%360,s:e.s,l:e.l})]}function H(t){var e=h(t).toHsl(),r=e.h;return[h(t),h({h:(r+90)%360,s:e.s,l:e.l}),h({h:(r+180)%360,s:e.s,l:e.l}),h({h:(r+270)%360,s:e.s,l:e.l})]}function R(t){var e=h(t).toHsl(),r=e.h;return[h(t),h({h:(r+72)%360,s:e.s,l:e.l}),h({h:(r+216)%360,s:e.s,l:e.l})]}function F(t,e,r){e=e||6,r=r||30;var n=h(t).toHsl(),a=360/r,i=[h(t)];for(n.h=(n.h-(a*e>>1)+720)%360;--e;)n.h=(n.h+a)%360,i.push(h(n));return i}function C(t,e){e=e||6;for(var r=h(t).toHsv(),n=r.h,a=r.s,i=r.v,o=[],s=1/e;e--;)o.push(h({h:n,s:a,v:i})),i=(i+s)%1;return o}h.prototype={isDark:function(){return this.getBrightness()<128},isLight:function(){return!this.isDark()},isValid:function(){return this._ok},getOriginalInput:function(){return this._originalInput},getFormat:function(){return this._format},getAlpha:function(){return this._a},getBrightness:function(){var t=this.toRgb();return(299*t.r+587*t.g+114*t.b)/1e3},getLuminance:function(){var t,e,r,n=this.toRgb();return t=n.r/255,e=n.g/255,r=n.b/255,.2126*(t<=.03928?t/12.92:a.pow((t+.055)/1.055,2.4))+.7152*(e<=.03928?e/12.92:a.pow((e+.055)/1.055,2.4))+.0722*(r<=.03928?r/12.92:a.pow((r+.055)/1.055,2.4))},setAlpha:function(t){return this._a=P(t),this._roundA=f(100*this._a)/100,this},toHsv:function(){var t=d(this._r,this._g,this._b);return{h:360*t.h,s:t.s,v:t.v,a:this._a}},toHsvString:function(){var t=d(this._r,this._g,this._b),e=f(360*t.h),r=f(100*t.s),n=f(100*t.v);return 1==this._a?"hsv("+e+", "+r+"%, "+n+"%)":"hsva("+e+", "+r+"%, "+n+"%, "+this._roundA+")"},toHsl:function(){var t=g(this._r,this._g,this._b);return{h:360*t.h,s:t.s,l:t.l,a:this._a}},toHslString:function(){var t=g(this._r,this._g,this._b),e=f(360*t.h),r=f(100*t.s),n=f(100*t.l);return 1==this._a?"hsl("+e+", "+r+"%, "+n+"%)":"hsla("+e+", "+r+"%, "+n+"%, "+this._roundA+")"},toHex:function(t){return b(this._r,this._g,this._b,t)},toHexString:function(t){return"#"+this.toHex(t)},toHex8:function(t){return function(t,e,r,n,a){var i=[q(f(t).toString(16)),q(f(e).toString(16)),q(f(r).toString(16)),q(z(n))];return a&&i[0].charAt(0)==i[0].charAt(1)&&i[1].charAt(0)==i[1].charAt(1)&&i[2].charAt(0)==i[2].charAt(1)&&i[3].charAt(0)==i[3].charAt(1)?i[0].charAt(0)+i[1].charAt(0)+i[2].charAt(0)+i[3].charAt(0):i.join("")}(this._r,this._g,this._b,this._a,t)},toHex8String:function(t){return"#"+this.toHex8(t)},toRgb:function(){return{r:f(this._r),g:f(this._g),b:f(this._b),a:this._a}},toRgbString:function(){return 1==this._a?"rgb("+f(this._r)+", "+f(this._g)+", "+f(this._b)+")":"rgba("+f(this._r)+", "+f(this._g)+", "+f(this._b)+", "+this._roundA+")"},toPercentageRgb:function(){return{r:f(100*j(this._r,255))+"%",g:f(100*j(this._g,255))+"%",b:f(100*j(this._b,255))+"%",a:this._a}},toPercentageRgbString:function(){return 1==this._a?"rgb("+f(100*j(this._r,255))+"%, "+f(100*j(this._g,255))+"%, "+f(100*j(this._b,255))+"%)":"rgba("+f(100*j(this._r,255))+"%, "+f(100*j(this._g,255))+"%, "+f(100*j(this._b,255))+"%, "+this._roundA+")"},toName:function(){return 0===this._a?"transparent":!(this._a<1)&&(M[b(this._r,this._g,this._b,!0)]||!1)},toFilter:function(t){var e="#"+p(this._r,this._g,this._b,this._a),r=e,n=this._gradientType?"GradientType = 1, ":"";if(t){var a=h(t);r="#"+p(a._r,a._g,a._b,a._a)}return"progid:DXImageTransform.Microsoft.gradient("+n+"startColorstr="+e+",endColorstr="+r+")"},toString:function(t){var e=!!t;t=t||this._format;var r=!1,n=this._a<1&&this._a>=0;return e||!n||"hex"!==t&&"hex6"!==t&&"hex3"!==t&&"hex4"!==t&&"hex8"!==t&&"name"!==t?("rgb"===t&&(r=this.toRgbString()),"prgb"===t&&(r=this.toPercentageRgbString()),"hex"!==t&&"hex6"!==t||(r=this.toHexString()),"hex3"===t&&(r=this.toHexString(!0)),"hex4"===t&&(r=this.toHex8String(!0)),"hex8"===t&&(r=this.toHex8String()),"name"===t&&(r=this.toName()),"hsl"===t&&(r=this.toHslString()),"hsv"===t&&(r=this.toHsvString()),r||this.toHexString()):"name"===t&&0===this._a?this.toName():this.toRgbString()},clone:function(){return h(this.toString())},_applyModification:function(t,e){var r=t.apply(null,[this].concat([].slice.call(e)));return this._r=r._r,this._g=r._g,this._b=r._b,this.setAlpha(r._a),this},lighten:function(){return this._applyModification(y,arguments)},brighten:function(){return this._applyModification(A,arguments)},darken:function(){return this._applyModification(x,arguments)},desaturate:function(){return this._applyModification(m,arguments)},saturate:function(){return this._applyModification(_,arguments)},greyscale:function(){return this._applyModification(v,arguments)},spin:function(){return this._applyModification(w,arguments)},_applyCombination:function(t,e){return t.apply(null,[this].concat([].slice.call(e)))},analogous:function(){return this._applyCombination(F,arguments)},complement:function(){return this._applyCombination(k,arguments)},monochromatic:function(){return this._applyCombination(C,arguments)},splitcomplement:function(){return this._applyCombination(R,arguments)},triad:function(){return this._applyCombination(S,arguments)},tetrad:function(){return this._applyCombination(H,arguments)}},h.fromRatio=function(t,e){if("object"==typeof t){var r={};for(var n in t)t.hasOwnProperty(n)&&(r[n]="a"===n?t[n]:T(t[n]));t=r}return h(t,e)},h.equals=function(t,e){return!(!t||!e)&&h(t).toRgbString()==h(e).toRgbString()},h.random=function(){return h.fromRatio({r:c(),g:c(),b:c()})},h.mix=function(t,e,r){r=0===r?0:r||50;var n=h(t).toRgb(),a=h(e).toRgb(),i=r/100;return h({r:(a.r-n.r)*i+n.r,g:(a.g-n.g)*i+n.g,b:(a.b-n.b)*i+n.b,a:(a.a-n.a)*i+n.a})},h.readability=function(t,e){var r=h(t),n=h(e);return(a.max(r.getLuminance(),n.getLuminance())+.05)/(a.min(r.getLuminance(),n.getLuminance())+.05)},h.isReadable=function(t,e,r){var n,a,i,o,s,f=h.readability(t,e);switch(a=!1,(i=r,"AA"!==(o=((i=i||{level:"AA",size:"small"}).level||"AA").toUpperCase())&&"AAA"!==o&&(o="AA"),"small"!==(s=(i.size||"small").toLowerCase())&&"large"!==s&&(s="small"),n={level:o,size:s}).level+n.size){case"AAsmall":case"AAAlarge":a=f>=4.5;break;case"AAlarge":a=f>=3;break;case"AAAsmall":a=f>=7}return a},h.mostReadable=function(t,e,r){var n,a,i,o,s=null,f=0;a=(r=r||{}).includeFallbackColors,i=r.level,o=r.size;for(var u=0;u<e.length;u++)(n=h.readability(t,e[u]))>f&&(f=n,s=h(e[u]));return h.isReadable(t,s,{level:i,size:o})||!a?s:(r.includeFallbackColors=!1,h.mostReadable(t,["#fff","#000"],r))};var L=h.names={aliceblue:"f0f8ff",antiquewhite:"faebd7",aqua:"0ff",aquamarine:"7fffd4",azure:"f0ffff",beige:"f5f5dc",bisque:"ffe4c4",black:"000",blanchedalmond:"ffebcd",blue:"00f",blueviolet:"8a2be2",brown:"a52a2a",burlywood:"deb887",burntsienna:"ea7e5d",cadetblue:"5f9ea0",chartreuse:"7fff00",chocolate:"d2691e",coral:"ff7f50",cornflowerblue:"6495ed",cornsilk:"fff8dc",crimson:"dc143c",cyan:"0ff",darkblue:"00008b",darkcyan:"008b8b",darkgoldenrod:"b8860b",darkgray:"a9a9a9",darkgreen:"006400",darkgrey:"a9a9a9",darkkhaki:"bdb76b",darkmagenta:"8b008b",darkolivegreen:"556b2f",darkorange:"ff8c00",darkorchid:"9932cc",darkred:"8b0000",darksalmon:"e9967a",darkseagreen:"8fbc8f",darkslateblue:"483d8b",darkslategray:"2f4f4f",darkslategrey:"2f4f4f",darkturquoise:"00ced1",darkviolet:"9400d3",deeppink:"ff1493",deepskyblue:"00bfff",dimgray:"696969",dimgrey:"696969",dodgerblue:"1e90ff",firebrick:"b22222",floralwhite:"fffaf0",forestgreen:"228b22",fuchsia:"f0f",gainsboro:"dcdcdc",ghostwhite:"f8f8ff",gold:"ffd700",goldenrod:"daa520",gray:"808080",green:"008000",greenyellow:"adff2f",grey:"808080",honeydew:"f0fff0",hotpink:"ff69b4",indianred:"cd5c5c",indigo:"4b0082",ivory:"fffff0",khaki:"f0e68c",lavender:"e6e6fa",lavenderblush:"fff0f5",lawngreen:"7cfc00",lemonchiffon:"fffacd",lightblue:"add8e6",lightcoral:"f08080",lightcyan:"e0ffff",lightgoldenrodyellow:"fafad2",lightgray:"d3d3d3",lightgreen:"90ee90",lightgrey:"d3d3d3",lightpink:"ffb6c1",lightsalmon:"ffa07a",lightseagreen:"20b2aa",lightskyblue:"87cefa",lightslategray:"789",lightslategrey:"789",lightsteelblue:"b0c4de",lightyellow:"ffffe0",lime:"0f0",limegreen:"32cd32",linen:"faf0e6",magenta:"f0f",maroon:"800000",mediumaquamarine:"66cdaa",mediumblue:"0000cd",mediumorchid:"ba55d3",mediumpurple:"9370db",mediumseagreen:"3cb371",mediumslateblue:"7b68ee",mediumspringgreen:"00fa9a",mediumturquoise:"48d1cc",mediumvioletred:"c71585",midnightblue:"191970",mintcream:"f5fffa",mistyrose:"ffe4e1",moccasin:"ffe4b5",navajowhite:"ffdead",navy:"000080",oldlace:"fdf5e6",olive:"808000",olivedrab:"6b8e23",orange:"ffa500",orangered:"ff4500",orchid:"da70d6",palegoldenrod:"eee8aa",palegreen:"98fb98",paleturquoise:"afeeee",palevioletred:"db7093",papayawhip:"ffefd5",peachpuff:"ffdab9",peru:"cd853f",pink:"ffc0cb",plum:"dda0dd",powderblue:"b0e0e6",purple:"800080",rebeccapurple:"663399",red:"f00",rosybrown:"bc8f8f",royalblue:"4169e1",saddlebrown:"8b4513",salmon:"fa8072",sandybrown:"f4a460",seagreen:"2e8b57",seashell:"fff5ee",sienna:"a0522d",silver:"c0c0c0",skyblue:"87ceeb",slateblue:"6a5acd",slategray:"708090",slategrey:"708090",snow:"fffafa",springgreen:"00ff7f",steelblue:"4682b4",tan:"d2b48c",teal:"008080",thistle:"d8bfd8",tomato:"ff6347",turquoise:"40e0d0",violet:"ee82ee",wheat:"f5deb3",white:"fff",whitesmoke:"f5f5f5",yellow:"ff0",yellowgreen:"9acd32"},M=h.hexNames=function(t){var e={};for(var r in t)t.hasOwnProperty(r)&&(e[t[r]]=r);return e}(L);function P(t){return t=parseFloat(t),(isNaN(t)||t<0||t>1)&&(t=1),t}function j(t,e){(function(t){return"string"==typeof t&&-1!=t.indexOf(".")&&1===parseFloat(t)})(t)&&(t="100%");var r=function(t){return"string"==typeof t&&-1!=t.indexOf("%")}(t);return t=u(e,l(0,parseFloat(t))),r&&(t=parseInt(t*e,10)/100),a.abs(t-e)<1e-6?1:t%e/parseFloat(e)}function E(t){return u(1,l(0,t))}function O(t){return parseInt(t,16)}function q(t){return 1==t.length?"0"+t:""+t}function T(t){return t<=1&&(t=100*t+"%"),t}function z(t){return a.round(255*parseFloat(t)).toString(16)}function I(t){return O(t)/255}var N,$,D,U=($="[\\s|\\(]+("+(N="(?:[-\\+]?\\d*\\.\\d+%?)|(?:[-\\+]?\\d+%?)")+")[,|\\s]+("+N+")[,|\\s]+("+N+")\\s*\\)?",D="[\\s|\\(]+("+N+")[,|\\s]+("+N+")[,|\\s]+("+N+")[,|\\s]+("+N+")\\s*\\)?",{CSS_UNIT:new RegExp(N),rgb:new RegExp("rgb"+$),rgba:new RegExp("rgba"+D),hsl:new RegExp("hsl"+$),hsla:new RegExp("hsla"+D),hsv:new RegExp("hsv"+$),hsva:new RegExp("hsva"+D),hex3:/^#?([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})$/,hex6:/^#?([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/,hex4:/^#?([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})$/,hex8:/^#?([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/});function V(t){return!!U.CSS_UNIT.exec(t)}t.exports?t.exports=h:void 0===(n=function(){return h}.call(e,r,e,t))||(t.exports=n)}(Math)}});