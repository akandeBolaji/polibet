"use strict";var precacheConfig=[["/","4402d31135d44ba8ccf890c00494d62c"],["css/app.css","ae91bda39c903b9c6c5bb2d692a6c9c3"],["fonts/vendor/@fortawesome/fontawesome-free/webfa-brands-400.eot","a31c967484b343189fc58bce914ed245"],["fonts/vendor/@fortawesome/fontawesome-free/webfa-brands-400.svg","3f4d4ad447e748754e994a11ff8c6a6f"],["fonts/vendor/@fortawesome/fontawesome-free/webfa-brands-400.ttf","dec02372212aab5a2e5294f1a11756ed"],["fonts/vendor/@fortawesome/fontawesome-free/webfa-brands-400.woff","eef6051639f95300dbf0293ad216c6b0"],["fonts/vendor/@fortawesome/fontawesome-free/webfa-brands-400.woff2","e4a6cecbe2bb89b0722b5dc85090af7c"],["fonts/vendor/@fortawesome/fontawesome-free/webfa-regular-400.eot","c38ef825039cbe8ec76d4f13854f767e"],["fonts/vendor/@fortawesome/fontawesome-free/webfa-regular-400.svg","b5a61b229c9c92a6ac21f5b0e3c6e9f1"],["fonts/vendor/@fortawesome/fontawesome-free/webfa-regular-400.ttf","0e2e26fb3527ae47f9eb1c217592b706"],["fonts/vendor/@fortawesome/fontawesome-free/webfa-regular-400.woff","52899632324b722c9c7931bd38196afb"],["fonts/vendor/@fortawesome/fontawesome-free/webfa-regular-400.woff2","dd25437adf06f377113f5df3507423b2"],["fonts/vendor/@fortawesome/fontawesome-free/webfa-solid-900.eot","baa1e2496e8c71f636587c32f4016ca4"],["fonts/vendor/@fortawesome/fontawesome-free/webfa-solid-900.svg","3f759796d3d16b8434841d61cd2c007a"],["fonts/vendor/@fortawesome/fontawesome-free/webfa-solid-900.ttf","e143b57de78138e6d5963908afa7e393"],["fonts/vendor/@fortawesome/fontawesome-free/webfa-solid-900.woff","09bc9e8c7d6dfdace635ea073974db13"],["fonts/vendor/@fortawesome/fontawesome-free/webfa-solid-900.woff2","fd531d212b567d6049f400165473589f"],["fonts/vendor/@mdi/materialdesignicons-webfont.eot","41da82f6e715b98dc05e12686231e831"],["fonts/vendor/@mdi/materialdesignicons-webfont.ttf","023db9122f66b7d693bc52bcdf09e6b3"],["fonts/vendor/@mdi/materialdesignicons-webfont.woff","5d43a6f507961589ab369ccc6f6d301b"],["fonts/vendor/@mdi/materialdesignicons-webfont.woff2","6536e4067e72c89cd3f1530d83e9888c"],["fonts/vendor/font-awesome/fontawesome-webfont.eot","674f50d287a8c48dc19ba404d20fe713"],["fonts/vendor/font-awesome/fontawesome-webfont.svg","912ec66d7572ff821749319396470bde"],["fonts/vendor/font-awesome/fontawesome-webfont.ttf","b06871f281fee6b241d60582ae9369b9"],["fonts/vendor/font-awesome/fontawesome-webfont.woff","fee66e712a8a08eef5805a46892932ad"],["fonts/vendor/font-awesome/fontawesome-webfont.woff2","af7ae505a9eed503f8b8e6982036873e"],["fonts/vendor/material-design-icons-icondist/MaterialIcons-Regular.eot","016c14adad95a0e21a2ae4fbc36955c6"],["fonts/vendor/material-design-icons-icondist/MaterialIcons-Regular.ttf","d41ae58dab60e355ae3872ebf2f1403a"],["fonts/vendor/material-design-icons-icondist/MaterialIcons-Regular.woff","c38ebd3cd38c98fbd16bf31d1d24ce64"],["fonts/vendor/material-design-icons-icondist/MaterialIcons-Regular.woff2","8a9a261c8b8dfe90db11f1817a9d22e1"],["service-worker.js","ac7a8e534fa2fa152b56a92608030d5c"],["svg/403.svg","93d6475bd2581cb5aa1b527aa8152a95"],["svg/404.svg","ea2bc467605d3d3aa715c6a3655a4e42"],["svg/500.svg","f56a358742db1d15fc06934278e59703"],["svg/503.svg","bb681f2ad0a2a75161eea851ff83b4e2"]],cacheName="sw-precache-v3-pwa-"+(self.registration?self.registration.scope:""),ignoreUrlParametersMatching=[/^utm_/],addDirectoryIndex=function(e,t){var n=new URL(e);return"/"===n.pathname.slice(-1)&&(n.pathname+=t),n.toString()},cleanResponse=function(e){return e.redirected?("body"in e?Promise.resolve(e.body):e.blob()).then(function(t){return new Response(t,{headers:e.headers,status:e.status,statusText:e.statusText})}):Promise.resolve(e)},createCacheKey=function(e,t,n,r){var o=new URL(e);return r&&o.pathname.match(r)||(o.search+=(o.search?"&":"")+encodeURIComponent(t)+"="+encodeURIComponent(n)),o.toString()},isPathWhitelisted=function(e,t){if(0===e.length)return!0;var n=new URL(t).pathname;return e.some(function(e){return n.match(e)})},stripIgnoredUrlParameters=function(e,t){var n=new URL(e);return n.hash="",n.search=n.search.slice(1).split("&").map(function(e){return e.split("=")}).filter(function(e){return t.every(function(t){return!t.test(e[0])})}).map(function(e){return e.join("=")}).join("&"),n.toString()},hashParamName="_sw-precache",urlsToCacheKeys=new Map(precacheConfig.map(function(e){var t=e[0],n=e[1],r=new URL(t,self.location),o=createCacheKey(r,hashParamName,n,!1);return[r.toString(),o]}));function setOfCachedUrls(e){return e.keys().then(function(e){return e.map(function(e){return e.url})}).then(function(e){return new Set(e)})}self.addEventListener("install",function(e){e.waitUntil(caches.open(cacheName).then(function(e){return setOfCachedUrls(e).then(function(t){return Promise.all(Array.from(urlsToCacheKeys.values()).map(function(n){if(!t.has(n)){var r=new Request(n,{credentials:"same-origin"});return fetch(r).then(function(t){if(!t.ok)throw new Error("Request for "+n+" returned a response with status "+t.status);return cleanResponse(t).then(function(t){return e.put(n,t)})})}}))})}).then(function(){return self.skipWaiting()}))}),self.addEventListener("activate",function(e){var t=new Set(urlsToCacheKeys.values());e.waitUntil(caches.open(cacheName).then(function(e){return e.keys().then(function(n){return Promise.all(n.map(function(n){if(!t.has(n.url))return e.delete(n)}))})}).then(function(){return self.clients.claim()}))}),self.addEventListener("fetch",function(e){if("GET"===e.request.method){var t,n=stripIgnoredUrlParameters(e.request.url,ignoreUrlParametersMatching);(t=urlsToCacheKeys.has(n))||(n=addDirectoryIndex(n,"index.html"),t=urlsToCacheKeys.has(n));!t&&"navigate"===e.request.mode&&isPathWhitelisted([],e.request.url)&&(n=new URL("/",self.location).toString(),t=urlsToCacheKeys.has(n)),t&&e.respondWith(caches.open(cacheName).then(function(e){return e.match(urlsToCacheKeys.get(n)).then(function(e){if(e)return e;throw Error("The cached response that was expected is missing.")})}).catch(function(t){return console.warn('Couldn\'t serve response for "%s" from cache: %O',e.request.url,t),fetch(e.request)}))}}),function(e){if("object"==typeof exports&&"undefined"!=typeof module)module.exports=e();else if("function"==typeof define&&define.amd)define([],e);else{("undefined"!=typeof window?window:"undefined"!=typeof global?global:"undefined"!=typeof self?self:this).toolbox=e()}}(function(){return function e(t,n,r){function o(s,c){if(!n[s]){if(!t[s]){var i="function"==typeof require&&require;if(!c&&i)return i(s,!0);if(a)return a(s,!0);var f=new Error("Cannot find module '"+s+"'");throw f.code="MODULE_NOT_FOUND",f}var u=n[s]={exports:{}};t[s][0].call(u.exports,function(e){var n=t[s][1][e];return o(n||e)},u,u.exports,e,t,n,r)}return n[s].exports}for(var a="function"==typeof require&&require,s=0;s<r.length;s++)o(r[s]);return o}({1:[function(e,t,n){function r(e,t){((t=t||{}).debug||i.debug)&&console.log("[sw-toolbox] "+e)}function o(e){var t;return e&&e.cache&&(t=e.cache.name),t=t||i.cache.name,caches.open(t)}function a(e,t,n){var o=e.url,a=n.maxAgeSeconds,s=n.maxEntries,c=n.name,i=Date.now();return r("Updating LRU order for "+o+". Max entries is "+s+", max age is "+a),f.getDb(c).then(function(e){return f.setTimestampForUrl(e,o,i)}).then(function(e){return f.expireEntries(e,s,a,i)}).then(function(e){r("Successfully updated IDB.");var n=e.map(function(e){return t.delete(e)});return Promise.all(n).then(function(){r("Done with cache cleanup.")})}).catch(function(e){r(e)})}function s(e){var t=Array.isArray(e);if(t&&e.forEach(function(e){"string"==typeof e||e instanceof Request||(t=!1)}),!t)throw new TypeError("The precache method expects either an array of strings and/or Requests or a Promise that resolves to an array of strings and/or Requests.");return e}var c,i=e("./options"),f=e("./idb-cache-expiration");t.exports={debug:r,fetchAndCache:function(e,t){var n=(t=t||{}).successResponses||i.successResponses;return fetch(e.clone()).then(function(r){return"GET"===e.method&&n.test(r.status)&&o(t).then(function(n){n.put(e,r).then(function(){var r=t.cache||i.cache;(r.maxEntries||r.maxAgeSeconds)&&r.name&&function(e,t,n){var r=a.bind(null,e,t,n);c=c?c.then(r):r()}(e,n,r)})}),r.clone()})},openCache:o,renameCache:function(e,t,n){return r("Renaming cache: ["+e+"] to ["+t+"]",n),caches.delete(t).then(function(){return Promise.all([caches.open(e),caches.open(t)]).then(function(t){var n=t[0],r=t[1];return n.keys().then(function(e){return Promise.all(e.map(function(e){return n.match(e).then(function(t){return r.put(e,t)})}))}).then(function(){return caches.delete(e)})})})},cache:function(e,t){return o(t).then(function(t){return t.add(e)})},uncache:function(e,t){return o(t).then(function(t){return t.delete(e)})},precache:function(e){e instanceof Promise||s(e),i.preCacheItems=i.preCacheItems.concat(e)},validatePrecacheInput:s,isResponseFresh:function(e,t,n){if(!e)return!1;if(t){var r=e.headers.get("date");if(r&&new Date(r).getTime()+1e3*t<n)return!1}return!0}}},{"./idb-cache-expiration":2,"./options":4}],2:[function(e,t,n){var r="sw-toolbox-",o=1,a="store",s="url",c="timestamp",i={};t.exports={getDb:function(e){return e in i||(i[e]=function(e){return new Promise(function(t,n){var i=indexedDB.open(r+e,o);i.onupgradeneeded=function(){i.result.createObjectStore(a,{keyPath:s}).createIndex(c,c,{unique:!1})},i.onsuccess=function(){t(i.result)},i.onerror=function(){n(i.error)}})}(e)),i[e]},setTimestampForUrl:function(e,t,n){return new Promise(function(r,o){var s=e.transaction(a,"readwrite");s.objectStore(a).put({url:t,timestamp:n}),s.oncomplete=function(){r(e)},s.onabort=function(){o(s.error)}})},expireEntries:function(e,t,n,r){return function(e,t,n){return t?new Promise(function(r,o){var i=1e3*t,f=[],u=e.transaction(a,"readwrite"),h=u.objectStore(a);h.index(c).openCursor().onsuccess=function(e){var t=e.target.result;if(t&&n-i>t.value[c]){var r=t.value[s];f.push(r),h.delete(r),t.continue()}},u.oncomplete=function(){r(f)},u.onabort=o}):Promise.resolve([])}(e,n,r).then(function(n){return function(e,t){return t?new Promise(function(n,r){var o=[],i=e.transaction(a,"readwrite"),f=i.objectStore(a),u=f.index(c),h=u.count();u.count().onsuccess=function(){var e=h.result;e>t&&(u.openCursor().onsuccess=function(n){var r=n.target.result;if(r){var a=r.value[s];o.push(a),f.delete(a),e-o.length>t&&r.continue()}})},i.oncomplete=function(){n(o)},i.onabort=r}):Promise.resolve([])}(e,t).then(function(e){return n.concat(e)})})}}},{}],3:[function(e,t,n){function r(e){return e.reduce(function(e,t){return e.concat(t)},[])}e("serviceworker-cache-polyfill");var o=e("./helpers"),a=e("./router"),s=e("./options");t.exports={fetchListener:function(e){var t=a.match(e.request);t?e.respondWith(t(e.request)):a.default&&"GET"===e.request.method&&0===e.request.url.indexOf("http")&&e.respondWith(a.default(e.request))},activateListener:function(e){o.debug("activate event fired");var t=s.cache.name+"$$$inactive$$$";e.waitUntil(o.renameCache(t,s.cache.name))},installListener:function(e){var t=s.cache.name+"$$$inactive$$$";o.debug("install event fired"),o.debug("creating cache ["+t+"]"),e.waitUntil(o.openCache({cache:{name:t}}).then(function(e){return Promise.all(s.preCacheItems).then(r).then(o.validatePrecacheInput).then(function(t){return o.debug("preCache list: "+(t.join(", ")||"(none)")),e.addAll(t)})}))}}},{"./helpers":1,"./options":4,"./router":6,"serviceworker-cache-polyfill":16}],4:[function(e,t,n){var r;r=self.registration?self.registration.scope:self.scope||new URL("./",self.location).href,t.exports={cache:{name:"$$$toolbox-cache$$$"+r+"$$$",maxAgeSeconds:null,maxEntries:null},debug:!1,networkTimeoutSeconds:null,preCacheItems:[],successResponses:/^0|([123]\d\d)|(40[14567])|410$/}},{}],5:[function(e,t,n){var r=new URL("./",self.location).pathname,o=e("path-to-regexp"),a=function(e,t,n,a){t instanceof RegExp?this.fullUrlRegExp=t:(0!==t.indexOf("/")&&(t=r+t),this.keys=[],this.regexp=o(t,this.keys)),this.method=e,this.options=a,this.handler=n};a.prototype.makeHandler=function(e){var t;if(this.regexp){var n=this.regexp.exec(e);t={},this.keys.forEach(function(e,r){t[e.name]=n[r+1]})}return function(e){return this.handler(e,t,this.options)}.bind(this)},t.exports=a},{"path-to-regexp":15}],6:[function(e,t,n){var r=e("./route"),o=e("./helpers"),a=function(e,t){for(var n=e.entries(),r=n.next(),o=[];!r.done;){new RegExp(r.value[0]).test(t)&&o.push(r.value[1]),r=n.next()}return o},s=function(){this.routes=new Map,this.routes.set(RegExp,new Map),this.default=null};["get","post","put","delete","head","any"].forEach(function(e){s.prototype[e]=function(t,n,r){return this.add(e,t,n,r)}}),s.prototype.add=function(e,t,n,a){var s;a=a||{},t instanceof RegExp?s=RegExp:s=(s=a.origin||self.location.origin)instanceof RegExp?s.source:function(e){return e.replace(/[-\/\\^$*+?.()|[\]{}]/g,"\\$&")}(s),e=e.toLowerCase();var c=new r(e,t,n,a);this.routes.has(s)||this.routes.set(s,new Map);var i=this.routes.get(s);i.has(e)||i.set(e,new Map);var f=i.get(e),u=c.regexp||c.fullUrlRegExp;f.has(u.source)&&o.debug('"'+t+'" resolves to same regex as existing route.'),f.set(u.source,c)},s.prototype.matchMethod=function(e,t){var n=new URL(t),r=n.origin,o=n.pathname;return this._match(e,a(this.routes,r),o)||this._match(e,[this.routes.get(RegExp)],t)},s.prototype._match=function(e,t,n){if(0===t.length)return null;for(var r=0;r<t.length;r++){var o=t[r],s=o&&o.get(e.toLowerCase());if(s){var c=a(s,n);if(c.length>0)return c[0].makeHandler(n)}}return null},s.prototype.match=function(e){return this.matchMethod(e.method,e.url)||this.matchMethod("any",e.url)},t.exports=new s},{"./helpers":1,"./route":5}],7:[function(e,t,n){var r=e("../options"),o=e("../helpers");t.exports=function(e,t,n){return n=n||{},o.debug("Strategy: cache first ["+e.url+"]",n),o.openCache(n).then(function(t){return t.match(e).then(function(t){var a=n.cache||r.cache,s=Date.now();return o.isResponseFresh(t,a.maxAgeSeconds,s)?t:o.fetchAndCache(e,n)})})}},{"../helpers":1,"../options":4}],8:[function(e,t,n){var r=e("../options"),o=e("../helpers");t.exports=function(e,t,n){return n=n||{},o.debug("Strategy: cache only ["+e.url+"]",n),o.openCache(n).then(function(t){return t.match(e).then(function(e){var t=n.cache||r.cache,a=Date.now();if(o.isResponseFresh(e,t.maxAgeSeconds,a))return e})})}},{"../helpers":1,"../options":4}],9:[function(e,t,n){var r=e("../helpers"),o=e("./cacheOnly");t.exports=function(e,t,n){return r.debug("Strategy: fastest ["+e.url+"]",n),new Promise(function(a,s){var c=!1,i=[],f=function(e){i.push(e.toString()),c?s(new Error('Both cache and network failed: "'+i.join('", "')+'"')):c=!0},u=function(e){e instanceof Response?a(e):f("No result returned")};r.fetchAndCache(e.clone(),n).then(u,f),o(e,t,n).then(u,f)})}},{"../helpers":1,"./cacheOnly":8}],10:[function(e,t,n){t.exports={networkOnly:e("./networkOnly"),networkFirst:e("./networkFirst"),cacheOnly:e("./cacheOnly"),cacheFirst:e("./cacheFirst"),fastest:e("./fastest")}},{"./cacheFirst":7,"./cacheOnly":8,"./fastest":9,"./networkFirst":11,"./networkOnly":12}],11:[function(e,t,n){var r=e("../options"),o=e("../helpers");t.exports=function(e,t,n){var a=(n=n||{}).successResponses||r.successResponses,s=n.networkTimeoutSeconds||r.networkTimeoutSeconds;return o.debug("Strategy: network first ["+e.url+"]",n),o.openCache(n).then(function(t){var c,i,f=[];if(s){var u=new Promise(function(a){c=setTimeout(function(){t.match(e).then(function(e){var t=n.cache||r.cache,s=Date.now(),c=t.maxAgeSeconds;o.isResponseFresh(e,c,s)&&a(e)})},1e3*s)});f.push(u)}var h=o.fetchAndCache(e,n).then(function(e){if(c&&clearTimeout(c),a.test(e.status))return e;throw o.debug("Response was an HTTP error: "+e.statusText,n),i=e,new Error("Bad response")}).catch(function(r){return o.debug("Network or response error, fallback to cache ["+e.url+"]",n),t.match(e).then(function(e){if(e)return e;if(i)return i;throw r})});return f.push(h),Promise.race(f)})}},{"../helpers":1,"../options":4}],12:[function(e,t,n){var r=e("../helpers");t.exports=function(e,t,n){return r.debug("Strategy: network only ["+e.url+"]",n),fetch(e)}},{"../helpers":1}],13:[function(e,t,n){var r=e("./options"),o=e("./router"),a=e("./helpers"),s=e("./strategies"),c=e("./listeners");a.debug("Service Worker Toolbox is loading"),self.addEventListener("install",c.installListener),self.addEventListener("activate",c.activateListener),self.addEventListener("fetch",c.fetchListener),t.exports={networkOnly:s.networkOnly,networkFirst:s.networkFirst,cacheOnly:s.cacheOnly,cacheFirst:s.cacheFirst,fastest:s.fastest,router:o,options:r,cache:a.cache,uncache:a.uncache,precache:a.precache}},{"./helpers":1,"./listeners":3,"./options":4,"./router":6,"./strategies":10}],14:[function(e,t,n){t.exports=Array.isArray||function(e){return"[object Array]"==Object.prototype.toString.call(e)}},{}],15:[function(e,t,n){function r(e,t){for(var n,r=[],o=0,a=0,s="",f=t&&t.delimiter||"/";null!=(n=p.exec(e));){var u=n[0],h=n[1],l=n.index;if(s+=e.slice(a,l),a=l+u.length,h)s+=h[1];else{var d=e[a],m=n[2],w=n[3],g=n[4],v=n[5],b=n[6],x=n[7];s&&(r.push(s),s="");var y=null!=m&&null!=d&&d!==m,R="+"===b||"*"===b,E="?"===b||"*"===b,C=n[2]||f,k=g||v;r.push({name:w||o++,prefix:m||"",delimiter:C,optional:E,repeat:R,partial:y,asterisk:!!x,pattern:k?i(k):x?".*":"[^"+c(C)+"]+?"})}}return a<e.length&&(s+=e.substr(a)),s&&r.push(s),r}function o(e){return encodeURI(e).replace(/[\/?#]/g,function(e){return"%"+e.charCodeAt(0).toString(16).toUpperCase()})}function a(e){return encodeURI(e).replace(/[?#]/g,function(e){return"%"+e.charCodeAt(0).toString(16).toUpperCase()})}function s(e){for(var t=new Array(e.length),n=0;n<e.length;n++)"object"==typeof e[n]&&(t[n]=new RegExp("^(?:"+e[n].pattern+")$"));return function(n,r){for(var s="",c=n||{},i=(r||{}).pretty?o:encodeURIComponent,f=0;f<e.length;f++){var u=e[f];if("string"!=typeof u){var h,l=c[u.name];if(null==l){if(u.optional){u.partial&&(s+=u.prefix);continue}throw new TypeError('Expected "'+u.name+'" to be defined')}if(d(l)){if(!u.repeat)throw new TypeError('Expected "'+u.name+'" to not repeat, but received `'+JSON.stringify(l)+"`");if(0===l.length){if(u.optional)continue;throw new TypeError('Expected "'+u.name+'" to not be empty')}for(var p=0;p<l.length;p++){if(h=i(l[p]),!t[f].test(h))throw new TypeError('Expected all "'+u.name+'" to match "'+u.pattern+'", but received `'+JSON.stringify(h)+"`");s+=(0===p?u.prefix:u.delimiter)+h}}else{if(h=u.asterisk?a(l):i(l),!t[f].test(h))throw new TypeError('Expected "'+u.name+'" to match "'+u.pattern+'", but received "'+h+'"');s+=u.prefix+h}}else s+=u}return s}}function c(e){return e.replace(/([.+*?=^!:${}()[\]|\/\\])/g,"\\$1")}function i(e){return e.replace(/([=!:$\/()])/g,"\\$1")}function f(e,t){return e.keys=t,e}function u(e){return e.sensitive?"":"i"}function h(e,t,n){d(t)||(n=t||n,t=[]);for(var r=(n=n||{}).strict,o=!1!==n.end,a="",s=0;s<e.length;s++){var i=e[s];if("string"==typeof i)a+=c(i);else{var h=c(i.prefix),l="(?:"+i.pattern+")";t.push(i),i.repeat&&(l+="(?:"+h+l+")*"),a+=l=i.optional?i.partial?h+"("+l+")?":"(?:"+h+"("+l+"))?":h+"("+l+")"}}var p=c(n.delimiter||"/"),m=a.slice(-p.length)===p;return r||(a=(m?a.slice(0,-p.length):a)+"(?:"+p+"(?=$))?"),a+=o?"$":r&&m?"":"(?="+p+"|$)",f(new RegExp("^"+a,u(n)),t)}function l(e,t,n){return d(t)||(n=t||n,t=[]),n=n||{},e instanceof RegExp?function(e,t){var n=e.source.match(/\((?!\?)/g);if(n)for(var r=0;r<n.length;r++)t.push({name:r,prefix:null,delimiter:null,optional:!1,repeat:!1,partial:!1,asterisk:!1,pattern:null});return f(e,t)}(e,t):d(e)?function(e,t,n){for(var r=[],o=0;o<e.length;o++)r.push(l(e[o],t,n).source);return f(new RegExp("(?:"+r.join("|")+")",u(n)),t)}(e,t,n):function(e,t,n){return h(r(e,n),t,n)}(e,t,n)}var d=e("isarray");t.exports=l,t.exports.parse=r,t.exports.compile=function(e,t){return s(r(e,t))},t.exports.tokensToFunction=s,t.exports.tokensToRegExp=h;var p=new RegExp(["(\\\\.)","([\\/.])?(?:(?:\\:(\\w+)(?:\\(((?:\\\\.|[^\\\\()])+)\\))?|\\(((?:\\\\.|[^\\\\()])+)\\))([+*?])?|(\\*))"].join("|"),"g")},{isarray:14}],16:[function(e,t,n){!function(){var e=Cache.prototype.addAll,t=navigator.userAgent.match(/(Firefox|Chrome)\/(\d+\.)/);if(t)var n=t[1],r=parseInt(t[2]);e&&(!t||"Firefox"===n&&r>=46||"Chrome"===n&&r>=50)||(Cache.prototype.addAll=function(e){function t(e){this.name="NetworkError",this.code=19,this.message=e}var n=this;return t.prototype=Object.create(Error.prototype),Promise.resolve().then(function(){if(arguments.length<1)throw new TypeError;return e=e.map(function(e){return e instanceof Request?e:String(e)}),Promise.all(e.map(function(e){"string"==typeof e&&(e=new Request(e));var n=new URL(e.url).protocol;if("http:"!==n&&"https:"!==n)throw new t("Invalid scheme");return fetch(e.clone())}))}).then(function(r){if(r.some(function(e){return!e.ok}))throw new t("Incorrect response status");return Promise.all(r.map(function(t,r){return n.put(e[r],t)}))}).then(function(){})},Cache.prototype.add=function(e){return this.addAll([e])})}()},{}]},{},[13])(13)}),toolbox.router.get(/^https:\/\/fonts\.googleapis\.com\//,toolbox.cacheFirst,{}),toolbox.router.get(/^https:\/\/www\.thecocktaildb\.com\/images\/media\/drink\/(\w+)\.jpg/,toolbox.cacheFirst,{});