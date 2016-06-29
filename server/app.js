var sticky = require('sticky-session');

var php = require('phpjs');
var moment = require('moment');
var util = require('util');

// io.set('authorization', function (data, cb) {
	
	// console.log(data.session);
	
	// cb(null, true);
	
// });

sticky(3, function () {

	var http = require('http');
	var express = require('express'),
		cors = require('cors'),
		app = express(),
		server = http.createServer(app);
	
	app.use(cors());

	var io = require('socket.io')();
	var redis = require('redis');
	var sredis = require('socket.io-redis');
	var pub = redis.createClient();
	
	require('natural-compare-lite');
	
	io.adapter(sredis({ host: 'localhost', port: 6379 }));
	
	// var server = http.createServer(function(req, res) {

		
	// });
	
	var notifs = io.of('/notifs').on('connection', function (socket) {

		// var cookies = cookie.parse(socket.request.headers.cookie);
		var sub = redis.createClient();
		
		var subscriptions = new Array();
		
		sub.on('subscribe', function (channel, count) {

			// var data = new Array();

			// for(var k in io.sockets.connected) {
			
				// var _cookie = cookie.parse(io.sockets.connected[k].client.request.headers.cookie);

				// if('notifs-subs' in _cookie && _cookie['notifs-subs'].split(',').indexOf(channel) != -1) {
				
					// var _data = {};
					
					// php.parse_str(_cookie['notifs-data'], _data);
					
					// data[data.length] = _data;
				// }
			// }

			// io.of('/notifs').emit(channel, {
				// count: data.length ? data.length : count,
				// listeners: data
			// });
			
			// pub.sscan([channel + '_subscribers', 0, 'match', '*', 'count', 1000], function (err, reply) {

				// if(!err)
					// socket.emit(channel, {
						// count: reply[1].length,
						// listeners: reply[1].map(function (l) { return JSON.parse(l.replace(/[\w]+:/, '')); })
					// });
				
			// });
		});

		sub.on('unsubscribe', function (channel, count) {

			// var data = new Array();

			// for(var k in io.sockets.connected) {
			
				// var _cookie = cookie.parse(io.sockets.connected[k].client.request.headers.cookie);

				// if('notifs-subs' in _cookie && _cookie['notifs-subs'].split(',').indexOf(channel) != -1) {
				
					// var _data = {};
					
					// php.parse_str(_cookie['notifs-data'], _data);
					
					// data[data.length] = _data;
				// }
			// }

			// io.of('/notifs').emit(channel, {
				// count: data.length ? data.length : count,
				// listeners: data
			// });

			// io.of('/notifs').emit(channel, {
				// count: count
			// });
			
			// pub.sscan([channel + '_subscribers', 0, 'match', '*', 'count', 1000], function (err, reply) {

				// if(!err)
					// socket.emit(channel, {
						// count: reply[1].length,
						// listeners: reply[1].map(function (l) { return JSON.parse(l.replace(/[\w]+:/, '')); })
					// });
				
			// });
			
		});

		sub.on('message', function (channel, message) {

			// if(channel.endsWith('_ping')) {

				// var mess = JSON.parse(message);

				// io.of('/notifs').emit(mess.channel, {
					// connected: mess.connected
				// });
			// }
			// else
				// io.of('/notifs').emit(channel, {
					// message: JSON.parse(message)
				// });
			
			socket.emit(channel, {
				message: JSON.parse(message)
			});
			
		});

		socket.on('publish', function (data, callback) {

			pub.publish(data.channel, JSON.stringify(data.data), function (err, reply) {
						
				if(!err && data.i != null)
					socket.emit('received', {
						i: data.i,
						count: reply ? reply - 1 : 0
					});
				
			});

		});
		
		socket.on('spublish', function (data, callback) {

			var message = JSON.stringify(data.data);
			var c = data.channel + '_' + moment().format('MM-DD');
		
			pub.rpush(c, message, function () {

				pub.expire(c, moment().add(7, 'days').hour(0).minute(0).second(0).diff(moment(), 'seconds'), function () {

					pub.publish(data.channel, message, function (err, reply) {

						if(!err && data.i != null)
							socket.emit('sreceived', {
								i: data.i,
								count: reply ? reply - 1 : 0
							});
						
					});
					
				});
				
			});
		});
		
		socket.on('list', function (data) {

			if(typeof data == 'object' && 'channel' in data && 'days' in data && 'offset' in data) {

				pub.keys(data.channel + '_[0-9][0-9]-[0-9][0-9]', function (err, reply) {

					reply.sort(String.naturalCompare);

					if(!err && reply && reply instanceof Array) {
						
						var i = reply.length - data.offset;
						var ctr = 0;

						while(i-- && reply[i] && ctr < data.days) {
						
							var _i = i;

							pub.lrange([reply[i], 0, -1], function (lerr, lreply) {

									socket.emit('list', {
										channel: data.channel,
										list: lreply.map(JSON.parse),
										next: reply[_i - 1] ? reply[_i - 1].replace(data.channel + '_', '') : null,
										total: reply.length
									});
								
							});
						
							ctr++;
						}
						
					}
					
				});
				
			}
			
		});
		
		socket.on('subscribe', function (data) {

			if(data instanceof Array)
				for(var i = 0; i < data.length; i++) {
				
					// sub.subscribe(data[i] + '_ping');
				
					// pub.publish(data[i] + '_ping', JSON.stringify({
						// channel: data[i],
						// connected: socket.request._query
					// }));
					
					if(subscriptions.indexOf(data[i]) == -1)
						sub.subscribe(data[i], function () {

							pub.sscan([data[i] + '_subscribers', 0, 'match', socket.handshake.query.uid + ':*', 'count', 1000], function (err, reply) {

								if(!err && !reply[1].length)
									pub.sadd([data[i] + '_subscribers', socket.handshake.query.uid + ':' + JSON.stringify(socket.handshake.query)], function () {
										
										pub.expire(data[i] + '_subscribers', moment().add(1, 'day').hours(0).minute(0).second(0).diff(moment(), 'seconds'));
										
									});
								
								pub.sscan([data[i] + '_subscribers', 0, 'match', '*', 'count', 1000], function (err, reply2) {

									if(!err)
										notifs.emit(data[i], {
											count: reply2[1].length,
											listeners: reply2[1].map(function (l) { return JSON.parse(l.replace(/[\w]+:/, '')); })
										});
									
								});
								
							});
							
							subscriptions[subscriptions.length] = data[i];
							
						});
				}
			else {
			
				// sub.subscribe(data + '_ping');
			
				// pub.publish(data + '_ping', JSON.stringify({
					// channel: data,
					// connected: socket.request._query
				// }));

				if(subscriptions.indexOf(data) == -1)
					sub.subscribe(data, function () {

						pub.sscan([data + '_subscribers', 0, 'match', socket.handshake.query.uid + ':*', 'count', 1000], function (err, reply) {

							if(!err && !reply[1].length)
								pub.sadd([data + '_subscribers', socket.handshake.query.uid + ':' + JSON.stringify(socket.handshake.query)], function () {
									
									pub.expire(data + '_subscribers', moment().add(1, 'day').hours(0).minute(0).second(0).diff(moment(), 'seconds'));
									
								});
							
							pub.sscan([data + '_subscribers', 0, 'match', '*', 'count', 1000], function (err, reply2) {

								if(!err)
									notifs.emit(data, {
										count: reply2[1].length,
										listeners: reply2[1].map(function (l) { return JSON.parse(l.replace(/[\w]+:/, '')); })
									});
								
							});
							
						});
						
						subscriptions[subscriptions.length] = data;
						
					});
			}
			
		});
		
		socket.on('unsubscribe', function (data) {

			// if(Object.keys(socket.conn.server.clients).length == 1) {

				if(data instanceof Array)
					for(var i = 0; i < data.length; i++) {
					
						pub.sscan([data[i] + '_subscribers', 0, 'match', socket.handshake.query.uid + ':*', 'count', 1000], function (err, reply) {
						
							if(!err && !reply[1].length)
								for(var i = 0; i < reply[1].length; i++)
									pub.srem(data[i] + '_subscribers', reply[1][i]);
							
						});
					
						sub.unsubscribe(data[i]);
					}
				else {
				
					pub.sscan([data + '_subscribers', 0, 'match', socket.handshake.query.uid + ':*', 'count', 1000], function (err, reply) {
						
						if(!err && !reply[1].length)
							for(var i = 0; i < reply[1].length; i++)
								pub.srem(data + '_subscribers', reply[1][i]);
						
					});
				
					sub.unsubscribe(data);
				}
			// }
		});
		
		socket.on('disconnect', function () {

			for(var i = 0; i < subscriptions.length; i++) {
			
				var s = subscriptions[i];
				
				var scan1 = function (s, err, reply) {

						if(!err)
							for(var i2 = 0; i2 < reply[1].length; i2++) {
								
								var s2 = reply[1][i2];
								var rem = function (s, _err, _reply) {
								
										var scan2 = function (s, err2, reply2) {

												if(!err2)
													notifs.emit(s, {
														count: reply2[1].length,
														listeners: reply2[1].map(function (l) { return JSON.parse(l.replace(/[\w]+:/, '')); })
													});
												
											};

										pub.sscan([s + '_subscribers', 0, 'match', '*', 'count', 1000], scan2.bind(undefined, s));
										
									};

								pub.srem([s + '_subscribers', s2], rem.bind(undefined, s));
							}
						
					};

				pub.sscan([s + '_subscribers', 0, 'match', socket.handshake.query.uid + ':*', 'count', 1000], scan1.bind(undefined, s));
			}
			
			sub.unsubscribe(subscriptions, function () {

				setTimeout(function () {
				
					sub.quit();
					
				}, 1000);
				
			});
			// sub.quit();
			// pub.quit();

		});
		
	});

	io.listen(server);
	
	return server;
	
}).listen(81, function () {
	
	util.log('PID: ' + process.pid + ' server started on port 81');
	
});

process.on('uncaughtException', function (err) {

   util.error(moment().format('DD MMM HH:mm:ss') + ' - PID: ' + process.pid + ' ' + (err.stack || err));
   
   process.exit(1);
   
});

// process.on('SIGTERM', function () {
	
	// pub.quit();
	
// });