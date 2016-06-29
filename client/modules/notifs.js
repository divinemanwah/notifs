"object"!=typeof JSON&&(JSON={}),function(){"use strict"
function f(t){return 10>t?"0"+t:t}function quote(t){return escapable.lastIndex=0,escapable.test(t)?'"'+t.replace(escapable,function(t){var e=meta[t]
return"string"==typeof e?e:"\\u"+("0000"+t.charCodeAt(0).toString(16)).slice(-4)})+'"':'"'+t+'"'}function str(t,e){var r,n,o,f,u,p=gap,i=e[t]
switch(i&&"object"==typeof i&&"function"==typeof i.toJSON&&(i=i.toJSON(t)),"function"==typeof rep&&(i=rep.call(e,t,i)),typeof i){case"string":return quote(i)
case"number":return isFinite(i)?i+"":"null"
case"boolean":case"null":return i+""
case"object":if(!i)return"null"
if(gap+=indent,u=[],"[object Array]"===Object.prototype.toString.apply(i)){for(f=i.length,r=0;f>r;r+=1)u[r]=str(r,i)||"null"
return o=0===u.length?"[]":gap?"[\n"+gap+u.join(",\n"+gap)+"\n"+p+"]":"["+u.join(",")+"]",gap=p,o}if(rep&&"object"==typeof rep)for(f=rep.length,r=0;f>r;r+=1)"string"==typeof rep[r]&&(n=rep[r],o=str(n,i),o&&u.push(quote(n)+(gap?": ":":")+o))
else for(n in i)Object.prototype.hasOwnProperty.call(i,n)&&(o=str(n,i),o&&u.push(quote(n)+(gap?": ":":")+o))
return o=0===u.length?"{}":gap?"{\n"+gap+u.join(",\n"+gap)+"\n"+p+"}":"{"+u.join(",")+"}",gap=p,o}}"function"!=typeof Date.prototype.toJSON&&(Date.prototype.toJSON=function(){return isFinite(this.valueOf())?this.getUTCFullYear()+"-"+f(this.getUTCMonth()+1)+"-"+f(this.getUTCDate())+"T"+f(this.getUTCHours())+":"+f(this.getUTCMinutes())+":"+f(this.getUTCSeconds())+"Z":null},String.prototype.toJSON=Number.prototype.toJSON=Boolean.prototype.toJSON=function(){return this.valueOf()})
var cx=/[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,escapable=/[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,gap,indent,meta={"\b":"\\b","	":"\\t","\n":"\\n","\f":"\\f","\r":"\\r",'"':'\\"',"\\":"\\\\"},rep
"function"!=typeof JSON.stringify&&(JSON.stringify=function(t,e,r){var n
if(gap="",indent="","number"==typeof r)for(n=0;r>n;n+=1)indent+=" "
else"string"==typeof r&&(indent=r)
if(rep=e,e&&"function"!=typeof e&&("object"!=typeof e||"number"!=typeof e.length))throw Error("JSON.stringify")
return str("",{"":t})}),"function"!=typeof JSON.parse&&(JSON.parse=function(text,reviver){function walk(t,e){var r,n,o=t[e]
if(o&&"object"==typeof o)for(r in o)Object.prototype.hasOwnProperty.call(o,r)&&(n=walk(o,r),void 0!==n?o[r]=n:delete o[r])
return reviver.call(t,e,o)}var j
if(text+="",cx.lastIndex=0,cx.test(text)&&(text=text.replace(cx,function(t){return"\\u"+("0000"+t.charCodeAt(0).toString(16)).slice(-4)})),/^[\],:{}\s]*$/.test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g,"@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,"]").replace(/(?:^|:|,)(?:\s*\[)+/g,"")))return j=eval("("+text+")"),"function"==typeof reviver?walk({"":j},""):j
throw new SyntaxError("JSON.parse")})}();

function http_build_query(e,t,n){var r,i,s=[],o=this;var u=function(e,t,n){var r,i=[];if(t===true){t="1"}else if(t===false){t="0"}if(t!=null){if(typeof t==="object"){for(r in t){if(t[r]!=null){i.push(u(e+"["+r+"]",t[r],n))}}return i.join(n)}else if(typeof t!=="function"){return o.urlencode(e)+"="+o.urlencode(t)}else{throw new Error("There was an error processing for http_build_query().")}}else{return""}};if(!n){n="&"}for(i in e){r=e[i];if(t&&!isNaN(i)){i=String(t)+i}var a=u(i,r,n);if(a!==""){s.push(a)}}return s.join(n)}

(function(){function e(){}function t(e,t){for(var n=e.length;n--;)if(e[n].listener===t)return n;return-1}function n(e){return function(){return this[e].apply(this,arguments)}}var i=e.prototype,r=this,o=r.EventEmitter;i.getListeners=function(e){var t,n,i=this._getEvents();if("object"==typeof e){t={};for(n in i)i.hasOwnProperty(n)&&e.test(n)&&(t[n]=i[n])}else t=i[e]||(i[e]=[]);return t},i.flattenListeners=function(e){var t,n=[];for(t=0;e.length>t;t+=1)n.push(e[t].listener);return n},i.getListenersAsObject=function(e){var t,n=this.getListeners(e);return n instanceof Array&&(t={},t[e]=n),t||n},i.addListener=function(e,n){var i,r=this.getListenersAsObject(e),o="object"==typeof n;for(i in r)r.hasOwnProperty(i)&&-1===t(r[i],n)&&r[i].push(o?n:{listener:n,once:!1});return this},i.on=n("addListener"),i.addOnceListener=function(e,t){return this.addListener(e,{listener:t,once:!0})},i.once=n("addOnceListener"),i.defineEvent=function(e){return this.getListeners(e),this},i.defineEvents=function(e){for(var t=0;e.length>t;t+=1)this.defineEvent(e[t]);return this},i.removeListener=function(e,n){var i,r,o=this.getListenersAsObject(e);for(r in o)o.hasOwnProperty(r)&&(i=t(o[r],n),-1!==i&&o[r].splice(i,1));return this},i.off=n("removeListener"),i.addListeners=function(e,t){return this.manipulateListeners(!1,e,t)},i.removeListeners=function(e,t){return this.manipulateListeners(!0,e,t)},i.manipulateListeners=function(e,t,n){var i,r,o=e?this.removeListener:this.addListener,s=e?this.removeListeners:this.addListeners;if("object"!=typeof t||t instanceof RegExp)for(i=n.length;i--;)o.call(this,t,n[i]);else for(i in t)t.hasOwnProperty(i)&&(r=t[i])&&("function"==typeof r?o.call(this,i,r):s.call(this,i,r));return this},i.removeEvent=function(e){var t,n=typeof e,i=this._getEvents();if("string"===n)delete i[e];else if("object"===n)for(t in i)i.hasOwnProperty(t)&&e.test(t)&&delete i[t];else delete this._events;return this},i.removeAllListeners=n("removeEvent"),i.emitEvent=function(e,t){var n,i,r,o,s=this.getListenersAsObject(e);for(r in s)if(s.hasOwnProperty(r))for(i=s[r].length;i--;)n=s[r][i],n.once===!0&&this.removeListener(e,n.listener),o=n.listener.apply(this,t||[]),o===this._getOnceReturnValue()&&this.removeListener(e,n.listener);return this},i.trigger=n("emitEvent"),i.emit=function(e){var t=Array.prototype.slice.call(arguments,1);return this.emitEvent(e,t)},i.setOnceReturnValue=function(e){return this._onceReturnValue=e,this},i._getOnceReturnValue=function(){return this.hasOwnProperty("_onceReturnValue")?this._onceReturnValue:!0},i._getEvents=function(){return this._events||(this._events={})},e.noConflict=function(){return r.EventEmitter=o,e},"function"==typeof define&&define.amd?define("eventEmitter/EventEmitter",[],function(){return e}):"object"==typeof module&&module.exports?module.exports=e:this.EventEmitter=e}).call(this),function(e){function t(t){var n=e.event;return n.target=n.target||n.srcElement||t,n}var n=document.documentElement,i=function(){};n.addEventListener?i=function(e,t,n){e.addEventListener(t,n,!1)}:n.attachEvent&&(i=function(e,n,i){e[n+i]=i.handleEvent?function(){var n=t(e);i.handleEvent.call(i,n)}:function(){var n=t(e);i.call(e,n)},e.attachEvent("on"+n,e[n+i])});var r=function(){};n.removeEventListener?r=function(e,t,n){e.removeEventListener(t,n,!1)}:n.detachEvent&&(r=function(e,t,n){e.detachEvent("on"+t,e[t+n]);try{delete e[t+n]}catch(i){e[t+n]=void 0}});var o={bind:i,unbind:r};"function"==typeof define&&define.amd?define("eventie/eventie",o):e.eventie=o}(this),function(e,t){"function"==typeof define&&define.amd?define(["eventEmitter/EventEmitter","eventie/eventie"],function(n,i){return t(e,n,i)}):"object"==typeof exports?module.exports=t(e,require("wolfy87-eventemitter"),require("eventie")):e.imagesLoaded=t(e,e.EventEmitter,e.eventie)}(window,function(e,t,n){function i(e,t){for(var n in t)e[n]=t[n];return e}function r(e){return"[object Array]"===d.call(e)}function o(e){var t=[];if(r(e))t=e;else if("number"==typeof e.length)for(var n=0,i=e.length;i>n;n++)t.push(e[n]);else t.push(e);return t}function s(e,t,n){if(!(this instanceof s))return new s(e,t);"string"==typeof e&&(e=document.querySelectorAll(e)),this.elements=o(e),this.options=i({},this.options),"function"==typeof t?n=t:i(this.options,t),n&&this.on("always",n),this.getImages(),a&&(this.jqDeferred=new a.Deferred);var r=this;setTimeout(function(){r.check()})}function f(e){this.img=e}function c(e){this.src=e,v[e]=this}var a=e.jQuery,u=e.console,h=u!==void 0,d=Object.prototype.toString;s.prototype=new t,s.prototype.options={},s.prototype.getImages=function(){this.images=[];for(var e=0,t=this.elements.length;t>e;e++){var n=this.elements[e];"IMG"===n.nodeName&&this.addImage(n);var i=n.nodeType;if(i&&(1===i||9===i||11===i))for(var r=n.querySelectorAll("img"),o=0,s=r.length;s>o;o++){var f=r[o];this.addImage(f)}}},s.prototype.addImage=function(e){var t=new f(e);this.images.push(t)},s.prototype.check=function(){function e(e,r){return t.options.debug&&h&&u.log("confirm",e,r),t.progress(e),n++,n===i&&t.complete(),!0}var t=this,n=0,i=this.images.length;if(this.hasAnyBroken=!1,!i)return this.complete(),void 0;for(var r=0;i>r;r++){var o=this.images[r];o.on("confirm",e),o.check()}},s.prototype.progress=function(e){this.hasAnyBroken=this.hasAnyBroken||!e.isLoaded;var t=this;setTimeout(function(){t.emit("progress",t,e),t.jqDeferred&&t.jqDeferred.notify&&t.jqDeferred.notify(t,e)})},s.prototype.complete=function(){var e=this.hasAnyBroken?"fail":"done";this.isComplete=!0;var t=this;setTimeout(function(){if(t.emit(e,t),t.emit("always",t),t.jqDeferred){var n=t.hasAnyBroken?"reject":"resolve";t.jqDeferred[n](t)}})},a&&(a.fn.imagesLoaded=function(e,t){var n=new s(this,e,t);return n.jqDeferred.promise(a(this))}),f.prototype=new t,f.prototype.check=function(){var e=v[this.img.src]||new c(this.img.src);if(e.isConfirmed)return this.confirm(e.isLoaded,"cached was confirmed"),void 0;if(this.img.complete&&void 0!==this.img.naturalWidth)return this.confirm(0!==this.img.naturalWidth,"naturalWidth"),void 0;var t=this;e.on("confirm",function(e,n){return t.confirm(e.isLoaded,n),!0}),e.check()},f.prototype.confirm=function(e,t){this.isLoaded=e,this.emit("confirm",this,t)};var v={};return c.prototype=new t,c.prototype.check=function(){if(!this.isChecked){var e=new Image;n.bind(e,"load",this),n.bind(e,"error",this),e.src=this.src,this.isChecked=!0}},c.prototype.handleEvent=function(e){var t="on"+e.type;this[t]&&this[t](e)},c.prototype.onload=function(e){this.confirm(!0,"onload"),this.unbindProxyEvents(e)},c.prototype.onerror=function(e){this.confirm(!1,"onerror"),this.unbindProxyEvents(e)},c.prototype.confirm=function(e,t){this.isConfirmed=!0,this.isLoaded=e,this.emit("confirm",this,t)},c.prototype.unbindProxyEvents=function(e){n.unbind(e.target,"load",this),n.unbind(e.target,"error",this)},s});

function initNotifs() {

	var socket;

	if('addEventListener' in window)
		window.addEventListener('beforeunload', function () {

			if('disconnect' in socket)
				socket.disconnect();

		}, false);
	else
		window.attachEvent('onbeforeunload', function () {
		
			if('disconnect' in socket)
				socket.disconnect();

		});

	/**
	 * Author:	Ron Oliver Santor <rsantor@pacificseainvests.com>
	 * Usage:	USAGE-notifs.txt
	 */
	var NOTIFS = (function () {

			var subscriptions = new Array();
			
			var clientData = {};
			var subscribe_count_info = {};

			var events = {
					connect: function () {},
					error: function () {},
					disconnect: function () {},
					reconnect: function () {},
					reconnect_attempt: function () {},
					reconnecting: function () {},
					reconnect_error: function () {},
					reconnect_failed: function () {},
					publish: {},
					spublish: {},
					list: {}
				};
				
			var queue = {
					connect: new Array(),
					list: new Array(),
					subscribe: new Array(),
					publish: new Array(),
					spublish: new Array(),
					flush: function (type) {

						if(type == 'connect' || type == undefined)
							for(var i = null; i = this.connect.shift(); i(), socket.on('connect', i));
					
						if(type == 'list' || type == undefined)
							for(var i = null; i = this.list.shift(); socket.emit('list', i));
					
						if(type == 'subscribe' || type == undefined)
							for(var i = null; i = this.subscribe.shift(); socket.on(i.channel, function (i, data) { if(data.count) subscribe_count_info[i.channel.replace((global ? (top.location.hostname == 'localhost' ? 'globaltest' : 'global') : WS.tag) + '_', '')] = data.count; i.callback(data); }.bind(undefined, i)).emit('subscribe', i.channel));

						if(type == 'publish' || type == undefined)
							for(var i = null; i = this.publish.shift(); socket.emit('publish', i));
						
						if(type == 'spublish' || type == undefined)
							for(var i = null; i = this.spublish.shift(); socket.emit('spublish', i));
						
					}
				};
			
			var connecting = false;
			
			var global = false;
			
			var connect = function () {

					if(connecting)
						return;
					else
						connecting = true;

					if(typeof socket == 'object' && 'io' in socket && socket.disconnected)
						socket.io.reconnect();
					else {
					
						var _transports = typeof WebSocket == 'function' ? ['websocket'] : ['polling'];
			
						socket = io.connect('ws://10.120.0.195:81/notifs', {
								transports: _transports,
								// query: Object.extend({ tag: WS.tag }, NOTIFS.get_data())
								query: Object.extend({ tag: WS.tag, uid: WS.uid }, WS.user_data)
							});

						socket.on('connect', function () {

							queue.flush();

							connecting = false;
							
						});
						
						socket.on('error', function (e) {

							NOTIFS.getEvents('error')(e);
							
						});
						
						socket.on('disconnect', function () {
						
							// NOTIFS.unsubscribeAll();
							
							NOTIFS.getEvents('disconnect')();
							
						});
						
						socket.on('reconnect', function (n) {

							NOTIFS.getEvents('reconnect')(n);
							
						});
						
						socket.on('reconnect_attempt', function () {

							NOTIFS.getEvents('reconnect_attempt')();
							
						});
						
						socket.on('reconnecting', function (n) {

							NOTIFS.getEvents('reconnecting')(n);
						
						});
						
						socket.on('reconnect_error', function (e) {

							NOTIFS.getEvents('reconnect_error')(e);
							
						});
						
						socket.on('reconnect_failed', function () {

							NOTIFS.getEvents('reconnect_failed')();
							
						});
						
						socket.on('received', function (data) {

							NOTIFS.getEvents('publish')[data.i](data.count);
							
							delete NOTIFS.getEvents('publish')[data.i];
							
						});
						
						socket.on('sreceived', function (data) {

							NOTIFS.getEvents('spublish')[data.i](data.count);
							
							NOTIFS.removeEvent('spublish', data.i);
							
						});
						
						socket.on('list', function (data) {

							setTimeout(function () {
								
								NOTIFS.getEvents('list')[data.channel](data);
								
							}, 100);
							
						});
					}
				};
			
			return {
					name: 'NOTIFS',
					version: '1.1',
					getEvents: function (e) {
							
							return e ? events[e] : events;
						},
					removeEvent: function (e, i) {
							
							delete events[e][i];
						},
					flushEvents: function (e) {
							
							queue.flush(e);
						},
					getSubscribeCountInfo: function (channel) {
							
							return channel ? subscribe_count_info[channel] : subscribe_count_info;
						},
					isConnected: function () {
					
							return typeof socket == 'object' && 'connected' in socket && socket.connected;
						},
					onconnect: function (fn) {

							if(typeof fn == 'function')
								queue.connect[queue.connect.length] = fn;

							if(this.isConnected())
								queue.flush('connect');

							return this;
						},
					onerror: function (fn) {
							
							if(typeof fn == 'function')
								events.error = fn;
							
							return this;
						},
					ondisconnect: function (fn) {
					
							if(typeof fn == 'function')
								events.disconnect = fn;
							
							return this;
						},
					onreconnect: function (fn) {
							
							if(typeof fn == 'function')
								events.reconnect = fn;
							
							return this;
						},
					onreconnect_attempt: function (fn) {
							
							if(typeof fn == 'function')
								events.reconnect_attempt = fn;
							
							return this;
						},
					onreconnecting: function (fn) {
							
							if(typeof fn == 'function')
								events.reconnecting = fn;
							
							return this;
						},
					onreconnect_error: function (fn) {
							
							if(typeof fn == 'function')
								events.reconnect_error = fn;
							
							return this;
						},
					onreconnect_failed: function (fn) {
							
							if(typeof fn == 'function')
								events.reconnect_failed = fn;
							
							return this;
						},
					publish: function (channel, data, callback) {

							var i = null;

							if(typeof callback == 'function') {
								
								i = Object.keys(events.publish).length;
							
								events.publish[i] = callback;
							}
							
							queue.publish[queue.publish.length] = {
									channel: (global ? (top.location.hostname == 'localhost' ? 'globaltest' : 'global') : WS.tag) + '_' + channel,
									data: data,
									i: i
								};
							
							if(this.isConnected())
								queue.flush('publish');
							else
								connect();
							
							return this;
							
						},
					spublish: function (channel, data, callback) {
					
							var i = null;

							if(typeof callback == 'function') {
								
								i = Object.keys(events.spublish).length;
							
								events.spublish[i] = callback;
							}
							
							queue.spublish[queue.spublish.length] = {
									channel: (global ? (top.location.hostname == 'localhost' ? 'globaltest' : 'global') : WS.tag) + '_' + channel,
									data: data,
									i: i
								};
							
							if(this.isConnected())
								queue.flush('spublish');
							else
								connect();
							
							return this;
							
						},
					list: function (channel, fn, offset, days) {

							if(channel && typeof channel == 'string' && channel.trim().length) {
							
								if(typeof fn == 'function')
									events.list[(global ? (top.location.hostname == 'localhost' ? 'globaltest' : 'global') : WS.tag) + '_' + channel] = fn;
								
								queue.list[queue.list.length] = {
										channel: (global ? (top.location.hostname == 'localhost' ? 'globaltest' : 'global') : WS.tag) + '_' + channel,
										offset: offset || 0,
										days: days || 1
									};
								
								if(this.isConnected())
									queue.flush('list');
								else
									connect();
							}
							
							return this;
							
						},
					subscribe: function () {

							if(arguments.length == 1 && typeof arguments[0] == 'object') {
							
								var processed = 0;
								
								for(var k in arguments[0]) {
								
									k = (global ? (top.location.hostname == 'localhost' ? 'globaltest' : 'global') : WS.tag) + '_' + k;
								
									if(subscriptions.indexOf(k) == -1 && typeof arguments[0][k] == 'function') {
										
										subscriptions[subscriptions.length] = k;
										
										queue.subscribe[queue.subscribe.length] = {
												channel: k,
												callback: arguments[0][k]
											};
										
										processed++;
									}
								}
								
								if(processed) {
									
									if(this.isConnected())
										queue.flush('subscribe');
									else
										connect();
									
								}
							}
							else if(arguments.length == 2 && subscriptions.indexOf((global ? (top.location.hostname == 'localhost' ? 'globaltest' : 'global') : WS.tag) + '_' + arguments[0]) == -1 && typeof arguments[1] == 'function') {

								var c = (global ? (top.location.hostname == 'localhost' ? 'globaltest' : 'global') : WS.tag) + '_' + arguments[0];
							
								subscriptions[subscriptions.length] = c;
							
								queue.subscribe[queue.subscribe.length] = {
										channel: c,
										callback: arguments[1]
									};
								
								if(this.isConnected())
									queue.flush('subscribe');
								else
									connect();
							}
							else
								throw new WSError('Incorrect arguments for NOTIFS.subscribe');
							
							return this;
						},
					unsubscribe: function (channels) {
							
							if(this.isConnected()) {
								if(channels instanceof Array)
									for(var i = 0; i < channels.length; i++) {
									
										var c = (global ? (top.location.hostname == 'localhost' ? 'globaltest' : 'global') : WS.tag) + '_' + channels[i];
									
										socket.off(c);
										
										var i2 = subscriptions.indexOf(c);
								
										if(i2 > -1)
											subscriptions.splice(i2, 1);
											
									}
								else if(typeof channels == 'string') {
									
									var c = (global ? (top.location.hostname == 'localhost' ? 'globaltest' : 'global') : WS.tag) + '_' + channels;
								
									socket.off(c);
									
									channels = [channels];
									
									var i = subscriptions.indexOf(c);
								
									if(i > -1)
										subscriptions.splice(i, 1);
									
								}
								
								socket.emit('unsubscribe', channels.map(function (c) { return (global ? (top.location.hostname == 'localhost' ? 'globaltest' : 'global') : WS.tag) + '_' + c; }));
							}
							else
								throw new WSError('Must be connected to unsubscribe');
							
							return this;
						},
					getSubscriptions: function () {
							
							return subscriptions.map(function (c) { return c.replace((global ? (top.location.hostname == 'localhost' ? 'globaltest' : 'global') : WS.tag) + '_', ''); });
						},
					unsubscribeAll: function () {
					
							if(this.isConnected()) {
							
								for(var i = 0; i < subscriptions.length; i++)
									socket.off(subscriptions[i]);
								
								socket.emit('unsubscribe', subscriptions);
								
								subscriptions = [];
							}
							else
								throw new WSError('Must be connected to unsubscribe');
							
							return this;
						},
					set_data: function (obj) {

							if(typeof obj == 'object')
								clientData = obj;
							
							return this;
						},
					get_data: function () {
							
							return clientData;
							
						},
					global: function (b) {
					
							global = !!b;
							
							return this;
						}
				};
			
		}());
	
	WS.registerModule(NOTIFS);

}

setTimeout(function () {

	yepnope({
		test: window.io,
		nope: 'timeout=500!https://cdn.socket.io/socket.io-1.2.0.js',
		complete: function () {

				yepnope({
					test: window.io,
					nope: 'timeout=2000!http://10.120.0.195:81/socket.io/socket.io.js',
					complete: function () {
					
							SOCKET_LOCAL === undefined ? null : SOCKET_LOCAL;

							yepnope({
								test: window.io,
								nope: SOCKET_LOCAL,
								complete: function () {

										if(window.io)
											initNotifs();
										else
											throw new WSError('Could not load [NOTIFS] dependency [socket.io]');
									}
							});
						}
				});
			}
	});
	
}, 0);
