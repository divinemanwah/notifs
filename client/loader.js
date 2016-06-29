/*yepnope1.5.x|WTFPL*/
(function(a,b,c){function d(a){return"[object Function]"==o.call(a)}function e(a){return"string"==typeof a}function f(){}function g(a){return!a||"loaded"==a||"complete"==a||"uninitialized"==a}function h(){var a=p.shift();q=1,a?a.t?m(function(){("c"==a.t?B.injectCss:B.injectJs)(a.s,0,a.a,a.x,a.e,1)},0):(a(),h()):q=0}function i(a,c,d,e,f,i,j){function k(b){if(!o&&g(l.readyState)&&(u.r=o=1,!q&&h(),l.onload=l.onreadystatechange=null,b)){"img"!=a&&m(function(){t.removeChild(l)},50);for(var d in y[c])y[c].hasOwnProperty(d)&&y[c][d].onload()}}var j=j||B.errorTimeout,l=b.createElement(a),o=0,r=0,u={t:d,s:c,e:f,a:i,x:j};1===y[c]&&(r=1,y[c]=[]),"object"==a?l.data=c:(l.src=c,l.type=a),l.width=l.height="0",l.onerror=l.onload=l.onreadystatechange=function(){k.call(this,r)},p.splice(e,0,u),"img"!=a&&(r||2===y[c]?(t.insertBefore(l,s?null:n),m(k,j)):y[c].push(l))}function j(a,b,c,d,f){return q=0,b=b||"j",e(a)?i("c"==b?v:u,a,b,this.i++,c,d,f):(p.splice(this.i++,0,a),1==p.length&&h()),this}function k(){var a=B;return a.loader={load:j,i:0},a}var l=b.documentElement,m=a.setTimeout,n=b.getElementsByTagName("script")[0],o={}.toString,p=[],q=0,r="MozAppearance"in l.style,s=r&&!!b.createRange().compareNode,t=s?l:n.parentNode,l=a.opera&&"[object Opera]"==o.call(a.opera),l=!!b.attachEvent&&!l,u=r?"object":l?"script":"img",v=l?"script":u,w=Array.isArray||function(a){return"[object Array]"==o.call(a)},x=[],y={},z={timeout:function(a,b){return b.length&&(a.timeout=b[0]),a}},A,B;B=function(a){function b(a){var a=a.split("!"),b=x.length,c=a.pop(),d=a.length,c={url:c,origUrl:c,prefixes:a},e,f,g;for(f=0;f<d;f++)g=a[f].split("="),(e=z[g.shift()])&&(c=e(c,g));for(f=0;f<b;f++)c=x[f](c);return c}function g(a,e,f,g,h){var i=b(a),j=i.autoCallback;i.url.split(".").pop().split("?").shift(),i.bypass||(e&&(e=d(e)?e:e[a]||e[g]||e[a.split("/").pop().split("?")[0]]),i.instead?i.instead(a,e,f,g,h):(y[i.url]?i.noexec=!0:y[i.url]=1,f.load(i.url,i.forceCSS||!i.forceJS&&"css"==i.url.split(".").pop().split("?").shift()?"c":c,i.noexec,i.attrs,i.timeout),(d(e)||d(j))&&f.load(function(){k(),e&&e(i.origUrl,h,g),j&&j(i.origUrl,h,g),y[i.url]=2})))}function h(a,b){function c(a,c){if(a){if(e(a))c||(j=function(){var a=[].slice.call(arguments);k.apply(this,a),l()}),g(a,j,b,0,h);else if(Object(a)===a)for(n in m=function(){var b=0,c;for(c in a)a.hasOwnProperty(c)&&b++;return b}(),a)a.hasOwnProperty(n)&&(!c&&!--m&&(d(j)?j=function(){var a=[].slice.call(arguments);k.apply(this,a),l()}:j[n]=function(a){return function(){var b=[].slice.call(arguments);a&&a.apply(this,b),l()}}(k[n])),g(a[n],j,b,n,h))}else!c&&l()}var h=!!a.test,i=a.load||a.both,j=a.callback||f,k=j,l=a.complete||f,m,n;c(h?a.yep:a.nope,!!i),i&&c(i)}var i,j,l=this.yepnope.loader;if(e(a))g(a,0,l,0);else if(w(a))for(i=0;i<a.length;i++)j=a[i],e(j)?g(j,0,l,0):w(j)?B(j):Object(j)===j&&h(j,l);else Object(a)===a&&h(a,l)},B.addPrefix=function(a,b){z[a]=b},B.addFilter=function(a){x.push(a)},B.errorTimeout=1e4,null==b.readyState&&b.addEventListener&&(b.readyState="loading",b.addEventListener("DOMContentLoaded",A=function(){b.removeEventListener("DOMContentLoaded",A,0),b.readyState="complete"},0)),a.yepnope=k(),a.yepnope.executeStack=h,a.yepnope.injectJs=function(a,c,d,e,i,j){var k=b.createElement("script"),l,o,e=e||B.errorTimeout;k.src=a;for(o in d)k.setAttribute(o,d[o]);c=j?h:c||f,k.onreadystatechange=k.onload=function(){!l&&g(k.readyState)&&(l=1,c(),k.onload=k.onreadystatechange=null)},m(function(){l||(l=1,c(1))},e),i?k.onload():n.parentNode.insertBefore(k,n)},a.yepnope.injectCss=function(a,c,d,e,g,i){var e=b.createElement("link"),j,c=i?h:c||f;e.href=a,e.rel="stylesheet",e.type="text/css";for(j in d)e.setAttribute(j,d[j]);g||(n.parentNode.insertBefore(e,n),m(c,0))}})(this,document);

function WSError(m) {
	
	this.name = 'WSError';
	this.message = m;
}
WSError.prototype = Error.prototype;

if(!Array.prototype.indexOf)
	Array.prototype.indexOf = function(obj, start) {
			 for (var i = (start || 0), j = this.length; i < j; i++) {
				 if (this[i] === obj) { return i; }
			 }
			 return -1;
		};

if (!Array.prototype.filter)
    Array.prototype.filter = function(fun) {
        var len = this.length >>> 0;
        if (typeof fun != "function") {
            throw new TypeError();
        }

        var res = [];
        var thisp = arguments[1]; 
        for (var i = 0; i < len; i++) {
            if (i in this) {
                var val = this[i];
                if (fun.call(thisp, val, i, this)) {
                    res.push(val);
                }
            }
        }

        return res;
    };

if(!Array.prototype.diff)
	Array.prototype.diff = function(a) {
			return this.filter(function(i) {return a.indexOf(i) < 0;});
		};

if(!String.prototype.trim)
	String.prototype.trim=function(){return this.replace(/^\s+|\s+$/g, '');};

if (!String.prototype.endsWith)
  String.prototype.endsWith = function (searchString, position) {
      var subjectString = this.toString();
      if (position === undefined || position > subjectString.length) {
        position = subjectString.length;
      }
      position -= searchString.length;
      var lastIndex = subjectString.indexOf(searchString, position);
      return lastIndex !== -1 && lastIndex === position;
    };

if (!Array.prototype.map) {

  Array.prototype.map = function (callback, thisArg) {

    var T, A, k;

    if (this == null) {
      throw new TypeError(" this is null or not defined");
    }

    var O = Object(this);

    var len = O.length >>> 0;

    if (typeof callback !== "function") {
      throw new TypeError(callback + " is not a function");
    }

    if (arguments.length > 1) {
      T = thisArg;
    }

    A = new Array(len);

    k = 0;

    while (k < len) {

      var kValue, mappedValue;

      if (k in O) {

        kValue = O[k];

        mappedValue = callback.call(T, kValue, k, O);

        A[k] = mappedValue;
      }

      k++;
    }

    return A;
  };
}

Object.keys = Object.keys || (function () {
    var hasOwnProperty = Object.prototype.hasOwnProperty,
        hasDontEnumBug = !{toString:null}.propertyIsEnumerable("toString"),
        DontEnums = [ 
            'toString', 'toLocaleString', 'valueOf', 'hasOwnProperty',
            'isPrototypeOf', 'propertyIsEnumerable', 'constructor'
        ],
        DontEnumsLength = DontEnums.length;

    return function (o) {
        if (typeof o != "object" && typeof o != "function" || o === null)
            throw new TypeError("Object.keys called on a non-object");

        var result = [];
        for (var name in o) {
            if (hasOwnProperty.call(o, name))
                result.push(name);
        }

        if (hasDontEnumBug) {
            for (var i = 0; i < DontEnumsLength; i++) {
                if (hasOwnProperty.call(o, DontEnums[i]))
                    result.push(DontEnums[i]);
            }   
        }

        return result;
    };
})();

Object.extend = Object.extend || function(orig, obj) {
   for(i in obj)
	  orig[i] = obj[i];
	 
	return orig;
};

if(document.getElementById('ws-loader')) {

	var WS = {
			tag: null,
			user_data: {},
			onComplete: new Array(),
			ready: function (tag, uid, deps, cb, data) {
			
					if(typeof tag == 'string') {
					
						if(!(/^[a-z0-9]+$/i.test(tag)))
							throw new WSError('Tag must only be an alphanumeric string.');
						
						if(tag.trim().length)
							this.tag = tag;
						else
							throw new WSError('Tag must not be empty.');
					}
					else
						throw new WSError('Tag must be of type String.');
					
					if(typeof uid == 'string' || typeof uid == 'number') {
					
						uid = new String(uid);
					
						if(!(/^[a-z0-9]+$/i.test(uid)))
							throw new WSError('Unique ID must only be an alphanumeric string.');
						
						if(uid.trim().length)
							this.uid = uid;
						else
							throw new WSError('Unique ID must not be empty.');
					}
					else
						throw new WSError('Unique ID must be of type String or Number.');

					if(deps !== undefined) {
					
						if(typeof cb != 'function')
							throw new WSError('Callback is required and must be of type "function", not "' + (typeof cb) + '".');
						
						if(deps != null && (deps instanceof Array || typeof deps == 'string'))
							this.onComplete[this.onComplete.length] = function () {
									
									yepnope({
										load: deps,
										complete: cb
									});
									
								};
						else if(deps == null)
							this.onComplete[this.onComplete.length] = function () {
									
									cb();
									
								};
						else
							throw new WSError('Dependencies must be of type "array" or "string", not "' + (typeof deps) + '".');
						
					}
					
					if(data !== undefined && typeof data == 'object')
						this.user_data = data;
					else if(data !== undefined)
						throw new WSError('User data must not be of type "object", not "' + (typeof data) + '".');
						
				},
			triggerComplete: function () {
			
					for(var i = 0; i < this.onComplete.length; i++)
						this.onComplete[i]();
					
					for(var i = 0; i < this.registerHooks.length; i++)
						this.registerHooks[i]();
					
					if(this.processedModules != this.requestedModules.length) {
						
						var mods = this.requestedModules.diff(this.loadedModules);
					
						throw new WSError('Module' + (mods.length > 1 ? 's' : '') + ' [' + mods.join(',') + '] not found.');
					}
				},
			requestedModules: new Array(),
			loadedModules: new Array(),
			processedModules: 0,
			registerHooks: new Array(),
			registerModule: function (m, h) {
			
					if(this.loadedModules.indexOf(m.name) == -1) {
						this.loadedModules[this.loadedModules.length] = m.name;
				
						window[m.name] = m;
						
						if(Object.freeze)
							Object.freeze(window[m.name]);
						
						if(h && typeof h == 'function')
							this.registerHooks[this.registerHooks.length] = h;
				
						this.processedModules++;
							
						if(this.processedModules == this.requestedModules.length)
							this.triggerComplete();
					}
				}
		};
	
	var modules;
	
	try {	
		modules = document.getElementById('ws-loader').src.split('?module=')[1].split(',');
	} catch(e) {
		throw new WSError('Please check your syntax: <script id="ws-loader" type="text/javascript" src="/ws/loader.js?module=module1,module2" />');
	}
	
	var _load = new Array();
	
	for(var i = 0; i < modules.length; i++) {
		
		var m = modules[i].toUpperCase();
		
		WS.requestedModules[WS.requestedModules.length] = m;
		
		_load[_load.length] = {
				load: 'timeout=100!http://10.120.0.195/ws/modules/' + modules[i] + '.js'
			};
	}

	yepnope(_load);
	
	window.WS = WS;

}
else
	throw new WSError('Script with id "ws-loader" not found.');