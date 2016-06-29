if (!String.prototype.endsWith) {
  Object.defineProperty(String.prototype, 'endsWith', {
        value: function(searchString, position) {
      var subjectString = this.toString();
      if (position === undefined || position > subjectString.length) {
        position = subjectString.length;
      }
      position -= searchString.length;
      var lastIndex = subjectString.indexOf(searchString, position);
      return lastIndex !== -1 && lastIndex === position;
    }
  });
}

if (!String.prototype.startsWith) {
  Object.defineProperty(String.prototype, 'startsWith', {
    enumerable: false,
    configurable: false,
    writable: false,
    value: function(searchString, position) {
      position = position || 0;
      return this.lastIndexOf(searchString, position) === position;
    }
  });
}

function parse_url(str, component) {
  //       discuss at: http://phpjs.org/functions/parse_url/
  //      original by: Steven Levithan (http://blog.stevenlevithan.com)
  // reimplemented by: Brett Zamir (http://brett-zamir.me)
  //         input by: Lorenzo Pisani
  //         input by: Tony
  //      improved by: Brett Zamir (http://brett-zamir.me)
  //             note: original by http://stevenlevithan.com/demo/parseuri/js/assets/parseuri.js
  //             note: blog post at http://blog.stevenlevithan.com/archives/parseuri
  //             note: demo at http://stevenlevithan.com/demo/parseuri/js/assets/parseuri.js
  //             note: Does not replace invalid characters with '_' as in PHP, nor does it return false with
  //             note: a seriously malformed URL.
  //             note: Besides function name, is essentially the same as parseUri as well as our allowing
  //             note: an extra slash after the scheme/protocol (to allow file:/// as in PHP)
  //        example 1: parse_url('http://username:password@hostname/path?arg=value#anchor');
  //        returns 1: {scheme: 'http', host: 'hostname', user: 'username', pass: 'password', path: '/path', query: 'arg=value', fragment: 'anchor'}

  var query, key = ['source', 'scheme', 'authority', 'userInfo', 'user', 'pass', 'host', 'port',
      'relative', 'path', 'directory', 'file', 'query', 'fragment'
    ],
    ini = (this.php_js && this.php_js.ini) || {},
    mode = (ini['phpjs.parse_url.mode'] &&
      ini['phpjs.parse_url.mode'].local_value) || 'php',
    parser = {
      php: /^(?:([^:\/?#]+):)?(?:\/\/()(?:(?:()(?:([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?()(?:(()(?:(?:[^?#\/]*\/)*)()(?:[^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
      strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
      loose: /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/\/?)?((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/ // Added one optional slash to post-scheme to catch file:/// (should restrict this)
    };

  var m = parser[mode].exec(str),
    uri = {},
    i = 14;
  while (i--) {
    if (m[i]) {
      uri[key[i]] = m[i];
    }
  }

  if (component) {
    return uri[component.replace('PHP_URL_', '')
      .toLowerCase()];
  }
  if (mode !== 'php') {
    var name = (ini['phpjs.parse_url.queryKey'] &&
      ini['phpjs.parse_url.queryKey'].local_value) || 'queryKey';
    parser = /(?:^|&)([^&=]*)=?([^&]*)/g;
    uri[name] = {};
    query = uri[key[12]] || '';
    query.replace(parser, function($0, $1, $2) {
      if ($1) {
        uri[name][$1] = $2;
      }
    });
  }
  delete uri.source;
  return uri;
}

/**
 * Shim for "fixing" IE's lack of support (IE < 9) for applying slice
 * on host objects like NamedNodeMap, NodeList, and HTMLCollection
 * (technically, since host objects have been implementation-dependent,
 * at least before ES6, IE hasn't needed to work this way).
 * Also works on strings, fixes IE < 9 to allow an explicit undefined
 * for the 2nd argument (as in Firefox), and prevents errors when
 * called on other DOM objects.
 */
(function() {
  'use strict';
  var _slice = Array.prototype.slice;

  try {
    // Can't be used with DOM elements in IE < 9
    _slice.call(document.documentElement);
  } catch (e) { // Fails in IE < 9
    // This will work for genuine arrays, array-like objects, 
    // NamedNodeMap (attributes, entities, notations),
    // NodeList (e.g., getElementsByTagName), HTMLCollection (e.g., childNodes),
    // and will not fail on other DOM objects (as do DOM elements in IE < 9)
    Array.prototype.slice = function(begin, end) {
      // IE < 9 gets unhappy with an undefined end argument
      end = (typeof end !== 'undefined') ? end : this.length;

      // For native Array objects, we use the native slice function
            if (Object.prototype.toString.call(this) === '[object Array]') {
                return _slice.call(this, begin, end);
      }

      // For array like object we handle it ourselves.
      var i, cloned = [],
        size, len = this.length;

      // Handle negative value for "begin"
      var start = begin || 0;
            start = (start >= 0) ? start : len + start;

      // Handle negative value for "end"
      var upTo = (end) ? end : len;
      if (end < 0) {
        upTo = len + end;
      }

      // Actual expected size of the slice
      size = upTo - start;

      if (size > 0) {
        cloned = new Array(size);
        if (this.charAt) {
          for (i = 0; i < size; i++) {
            cloned[i] = this.charAt(start + i);
          }
        } else {
          for (i = 0; i < size; i++) {
            cloned[i] = this[start + i];
          }
        }
      }

      return cloned;
    };
  }
}());

function updatePendingCount(count) {

    if ($.isNumeric(count)) {

        if (count) {

			$('a#top-pending-alert').addClass('tooltip-error');

			$('a#top-pending-alert span')
				.text(count)
				.removeClass('hidden');

			$('a#top-pending-alert i')
				.removeClass('fa-bell-slash icon-animated-bell')
				.addClass('fa-bell icon-animated-bell');
		}
		else {

			$('a#top-pending-alert').removeClass('tooltip-error');

			$('a#top-pending-alert i')
				.removeClass('fa-bell')
				.addClass('fa-bell-slash')

			$('a#top-pending-alert span')
				.text('0')
				.addClass('hidden');

			$('a#top-pending-alert i').removeClass('icon-animated-bell');
		}
	}

}

function notifHandler(conn) {
    if (conn.count != undefined) {
    }

    if (conn.message != undefined) {

		// alert(conn.message)

		var panel = $('li#notif-panel');

		$('i.fa-bell', panel[0]).addClass('icon-animated-bell');

		var badge_ctr = parseInt($('span.badge', panel[0]).text(), 10);

		$('span.badge', panel[0]).text((badge_ctr = (badge_ctr ? badge_ctr + 1 : 1)));

		$('ul.dropdown-menu').addClass('navbar-pink');

		$('li.dropdown-header', panel[0]).html('\
			<i class="ace-icon fa fa-exclamation-triangle"></i>\
			' + badge_ctr + ' unread\
		');

		$('button#mark-all-read').removeAttr('disabled');

        $('li.dropdown-header', panel[0]).after(function() {

			var temp = $('\
					<li class="notif notif-' + conn.message.id + (conn.message.read_date ? '' : ' unread') + '">\
						<a href="#">\
							<i class="fa fa-' + notif_icons[conn.message.type] + '"></i>&nbsp; ' + conn.message.message + '\
						</a>\
					</li>\
				'),
				temp2 = $('<a class="list-group-item notif-' + conn.message.id + (conn.message.read_date ? '' : ' unread') + '" href="#"><i class="fa fa-' + notif_icons[conn.message.type] + ' fa-fw"></i>&nbsp; ' + conn.message.message + '</a>');

			temp.tooltip({
				placement: 'left',
				html: true,
                title: function() {

						return conn.message.message + '<br /><small><i class="fa fa-' + (conn.message.read_date ? 'eye' : 'eye-slash') + '"></i>&nbsp;<span class="text-muted">|</span>&nbsp;' + (ace.sizeof(conn.message.triggered_by) ? conn.message.triggered_by.mb_nick + ' (' + conn.message.triggered_by.dept_name + ')' : conn.message.type.toUpperCase()) + '&nbsp;<span class="text-muted">|</span>&nbsp;' + moment(conn.message.created_date, 'X').format('YYYY-MM-DD HH:mm:ss') + '</small>';

					},
				template: '<div class="tooltip tooltip-notifs" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
			});

			temp2.tooltip({
				container: 'body',
				html: true,
                title: function() {

						return conn.message.message + '<br /><small><i class="fa fa-' + (conn.message.read_date ? 'eye' : 'eye-slash') + '"></i>&nbsp;<span class="text-muted">|</span>&nbsp;' + (ace.sizeof(conn.message.triggered_by) ? conn.message.triggered_by.mb_nick + ' (' + conn.message.triggered_by.dept_name + ')' : conn.message.type) + '&nbsp;<span class="text-muted">|</span>&nbsp;' + moment(conn.message.created_date, 'X').format('YYYY-MM-DD HH:mm:ss') + '</small>';

					},
				template: '<div class="tooltip tooltip-notifs" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
			});

            $('a', temp[0]).click(function(e) {

				e.preventDefault();

				var that = this,
					_parent = $(this).parent();

                if (_parent.hasClass('unread'))
					$.post(
						base_url + 'notifications/read',
						{
							id: conn.message.id
						},
                    function() {

							var old = $(_parent).data('bs.tooltip').options.title(),
								wrapper = $('<div />');

							wrapper.append(old);

							$('i', wrapper[0])
								.removeClass('fa-eye-slash')
								.addClass('fa-eye');

							$(_parent)
								.removeClass('unread')
								.data('bs.tooltip').options.title = wrapper.html();

							$('div.list-group.notif a:eq(' + $('ul.dropdown-menu li:not(.dropdown-header, .dropdown-footer)', panel[0]).index(temp) + ')')
								.removeClass('unread')
								.data('bs.tooltip').options.title = wrapper.html();

							// var unread = parseInt($('span.badge', panel[0]).text(), 10);

							// if(unread)
								// unread--;

							// if(unread)
								// $('span.badge', panel[0]).text(unread);
							// else {

								// $('span.badge', panel[0]).empty();

								// $('button#mark-all-read').attr('disabled', 'disabled');
							// }

							// $('i.fa-bell', panel[0]).toggleClass('icon-animated-bell', !!unread);

							// $('ul.dropdown-menu').toggleClass('navbar-pink', !!unread);

							// $('li.dropdown-header', panel[0]).html('\
								// <i class="ace-icon fa fa-' + (!!unread ? 'exclamation-triangle' : 'check') + '"></i>\
								// ' + unread + ' unread\
							// ');

						}
					);

                if (conn.message.page)
                    History.pushState({from: 'main-nav', callback: conn.message.action, args: JSON.stringify(conn.message.args), _: moment().unix()}, document.title, base_url + conn.message.page);
                else if (conn.message.action)
					window[conn.message.action](conn.message.args);

				$.jStorage.set('notifclick', conn.message.id);

			});

            temp2.click(function(e) {

				e.preventDefault();

				var that = this;

				$('div.modal').modal('hide');

                if ($(this).hasClass('unread'))
					$.post(
						base_url + 'notifications/read',
						{
							id: conn.message.id
						},
                    function() {

							var old = $(that).data('bs.tooltip').options.title(),
								wrapper = $('<div />');

							wrapper.append(old);

							$('i', wrapper[0])
								.removeClass('fa-eye-slash')
								.addClass('fa-eye');

							$(that)
								.removeClass('unread')
								.data('bs.tooltip').options.title = wrapper.html();

							$('ul.dropdown-menu li:not(.dropdown-header, .dropdown-footer):eq(' + $('div#notifs-modal a.list-group-item').index(temp2) + ')', panel[0])
								.removeClass('unread')
								.data('bs.tooltip').options.title = wrapper.html();

							// var unread = parseInt($('span.badge', panel[0]).text(), 10);

							// if(unread)
								// unread--;

							// if(unread)
								// $('span.badge', panel[0]).text(unread);
							// else {

								// $('span.badge', panel[0]).empty();

								// $('button#mark-all-read').attr('disabled', 'disabled');
							// }

							// $('i.fa-bell', panel[0]).toggleClass('icon-animated-bell', !!unread);

							// $('ul.dropdown-menu').toggleClass('navbar-pink', !!unread);

							// $('li.dropdown-header', panel[0]).html('\
								// <i class="ace-icon fa fa-' + (!!unread ? 'exclamation-triangle' : 'check') + '"></i>\
								// ' + unread + ' unread\
							// ');

						}
					);

                if (conn.message.page)
                    History.pushState({from: 'main-nav', callback: conn.message.action, args: JSON.stringify(conn.message.args), _: moment().unix()}, document.title, base_url + conn.message.page);
                else if (conn.message.action)
					window[conn.message.action](conn.message.args);

				$.jStorage.set('notifclick', conn.message.id);

				// ret[ret.length] = temp;
				// ret2[ret2.length] = temp2;

			});

			$('div#notifs-modal div.alert-info').hide();

            if ($('div.list-group.notif').length)
				$('div.list-group.notif').prepend(temp2);
			else {

				var group = $('<div class="list-group notif" />');

				group.append(temp2);

				$('div#notifs-modal nav').before(group);
			}

			$.jGrowl(
				'<i class="fa fa-' + notif_icons[conn.message.type] + ' fa-2x pull-left"></i><div>' + conn.message.message + '</div>',
				{
					life: 120000,
					themeState: '',
					position: 'bottom-left',
                        beforeOpen: function(e, m, o) {

                            e.css('cursor', 'pointer').click(function() {

                                $(this).fadeOut(function() {

									// if(!$('ul', panel[0]).is(':visible'))
										// $('ul', panel[0]).dropdown('toggle');

                                    if (conn.message.page)
                                        History.pushState({from: 'main-nav', callback: conn.message.action, args: JSON.stringify(conn.message.args), _: moment().unix()}, document.title, base_url + conn.message.page);
                                    else if (conn.message.action)
										window[conn.message.action](conn.message.args);

									$.jStorage.set('notifclick', conn.message.id);

									$(this).remove();

								});

							});

						}
				}
			);

			return temp;

		});

		$('li.notif', panel[0]).slice(8).remove();

		$('div.list-group.notif a')
			.addClass('hidden')
			.slice(0, 8)
				.removeClass('hidden');

		$('div#notifs-modal').data('page', 1);

		$('div#notifs-modal').data().count++;

		$('div#notifs-modal li.next, div#notifs-modal li.previous').addClass('disabled');

        if (Math.floor($('div#notifs-modal').data('count') / 8) > 1)
				$('div#notifs-modal li.previous').removeClass('disabled');

	}

}

function initNotifications() {

	NOTIFS
		.subscribe('0_0', notifHandler)
		.subscribe(dept_id + '_0', notifHandler)
		.subscribe(dept_id + '_' + user_id, notifHandler)
		.subscribe('APPROVAL_' + user_id, notifApprovalHandler)
		.subscribe('SMS', smsApprovalHandler);



    if (dept_id == 24)
		NOTIFS.subscribe('pending_cites', pending_cites);

	if (NOTICE_USER != 'admin') {
        $.getScript('http://10.120.10.138/hr-notice/init.js');
    }

	// $.getScript('http://localhost/init.js');

}

function pending_cites(conn) {

    if (conn.message != undefined) {

        if (conn.message.count) {

            if ($('span#pending-cites').length)
				$('span#pending-cites')
					.attr('title', conn.message.count + ' pending cite' + (conn.message.count > 1 ? 's' : '') + ' for review')
					.html(conn.message.count)
					.tooltip();
			else {

				$('a#cite-anchor i').after('<span title="' + conn.message.count + ' pending cite' + (conn.message.count > 1 ? 's' : '') + ' for review" id="pending-cites" class="badge badge-danger tooltip-error">' + conn.message.count + '</span>');

				$('span#pending-cites').tooltip();
			}
		}
		else
			$('span#pending-cites').remove();
	}
}

function notifApprovalHandler(conn) {
    if (conn.message != undefined) {

        switch (conn.message.type) {
      case "OBT":
	    var obt_total = $("#obt_cnt").html() * 1;
		obt_total += (conn.message.count * 1);
                if (obt_total > 0)
	      $("#obt_cnt").html(obt_total);
		else
		  $("#obt_cnt").html("");
	    break;
	  case "OT":
	    var ot_total = $("#ot_cnt").html() * 1;
		ot_total += (conn.message.count * 1);
                if (ot_total > 0)
	      $("#ot_cnt").html(ot_total);
		else
		  $("#ot_cnt").html("");
	    break;
	  case "LV":
	    var lv_total = $("#lv_cnt").html() * 1;
		lv_total += (conn.message.count * 1);
                if (lv_total > 0)
	      $("#lv_cnt").html(lv_total);
		else
		  $("#lv_cnt").html("");
	    break;
	  case "CWS":
	    var cws_total = $("#cws_cnt").html() * 1;
		cws_total += (conn.message.count * 1);
                if (cws_total > 0)
	      $("#cws_cnt").html(cws_total);
		else
		  $("#cws_cnt").html("");
	    break;
            case "ATT":
                var att_total = $("#att_cnt").html() * 1;
                att_total += (conn.message.count * 1);
                if (att_total > 0)
                    $("#att_cnt").html(att_total);
                else
                    $("#att_cnt").html("");
                break;

    }
  }
}

function smsApprovalHandler() {
	getAllMessages();
}

function getAllMessages(){
	$.getJSON(
		base_url + 'sms/getTotalMessagesForTheDay',
		function (data) {
		  var sms_total = (data.recordsTotal * 1);
		  if(sms_total > 0)
	        $("#sms_cnt").html(sms_total);
		  else
		    $("#sms_cnt").html("");
		}
	);
}

$(function() {

	// $.fn.extend({
		// disableSelection: (function() {
			// var eventType = "onselectstart" in document.createElement( "div" ) ?
				// "selectstart" :
				// "mousedown";

			// return function() {
				// return this.bind( eventType + ".ui-disableSelection", function( event ) {
					// event.preventDefault();
				// });
			// };
		// })(),
		// enableSelection: function() {
			// return this.unbind( ".ui-disableSelection" );
		// }
	// });

    $('ul#main-nav a:not([href^=#])').click(function(e) {
        if ($(this).attr('target') != undefined)
		  return;
		e.preventDefault();

		$(this).parent().addClass('active open');

        History.pushState({from: 'main-nav', _: moment().unix()}, document.title, this.href);

	});

    $(window).on('statechange', function(e) {

		var state = History.getState();

        window.loaderTimeout = setTimeout(function() {

				$.blockUI({
					// message: '<img src="' + base_url + 'assets/img/ajax-loader.gif" />',
                message: $(new Spinner({color: '#438EB9'}).spin().el),
					css: {
							border: 'none',
							backgroundColor: 'transparent'
						},
					overlayCSS: {
							backgroundColor: '#fff'
						},
					centerY: false,
					ignoreIfBlocked: true,
                onBlock: function() {


						}
				});

			}, 500);

		$.getJSON(
			state.url,
			{
				mode: 'fragment'
			},
        function(data) {

				clearTimeout(window.loaderTimeout);

				document.title = data.title + ' - HR System';

            switch (state.data.from) {
					case 'main-nav':

						var breadcrumbs = '';

                    if (data.breadcrumbs.length)
							breadcrumbs = '<small>';

                    $.each(data.breadcrumbs, function(i, val) {

							breadcrumbs += '<i class="ace-icon fa fa-angle-double-right"></i>&nbsp;' + val + '&nbsp;';

						});

                    if (data.breadcrumbs.length)
							breadcrumbs += '</small>';

						$('div.page-header h1').html(data.title + breadcrumbs);

						break;
				}

				window.from_search = !!state.data.search;

				$('div#main-body').html(data.content);

            $('div#dynamic-files').html($.map(data.css, function(css) {
                return '<link type="text/css" rel="stylesheet" href="' + base_url + 'assets/css/' + css + '" />';
            }).join('') + $.map(data.js, function(js) {
                return '<script type="text/javascript" src="' + base_url + 'assets/js/' + js + '"></script>';
            }).join(''));

            if (typeof (window[state.data.callback]) == 'function')
                setTimeout(function() {

						window[state.data.callback](JSON.parse(state.data.args));

					}, 100);

				$.unblockUI();

			}
		);

        switch (state.data.from) {
			case 'main-nav':

				$('ul#main-nav li').removeClass('active open');

				$('ul#main-nav a')
                        .filter(function() {
						return $(this).attr('href') == state.url;
					})
						.parents('li.hover')
						.addClass('active open');

				break;
		}

	});

    $(document).ajaxError(function(event, jqxhr, ajaxSettings, error) {

		// if(ajaxSettings.url.endsWith('mode=fragment')) {
        if (error != 'timeout' && !ajaxSettings.url.startsWith('http://10.120.10.92/')) {

			clearTimeout(window.loaderTimeout);

			var logged_in = jqxhr.getResponseHeader('refresh') == null,
				is_fragment = ajaxSettings.url.endsWith('mode=fragment');

			$.blockUI({
				message: '<div class="alert alert-' + (logged_in ? 'danger' : 'warning') + '">\
							<button type="button" class="close close-not-found" data-dismiss="alert">\
								<i class="ace-icon fa fa-times"></i>\
							</button>\
							<strong>\
								<i class="ace-icon fa fa-' + (logged_in ? 'times' : 'warning') + '"></i>\
								' + (logged_in ? 'An error has occured!' : 'You have been logged out.') + '\
							</strong>\
							' + (is_fragment ? 'Redirecting to ' + (logged_in ? 'previous' : 'login') + ' page&hellip;' : (logged_in ? '<a href="#" id="err-view-details">View details</a><span id="err-details" class="hidden">[' + jqxhr.status + '] ' + error + '</span>' : 'Redirecting to login page &hellip;')) + '\
							<br>\
						</div>',
				css: {
						border: 'none',
						backgroundColor: 'transparent',
						cursor: is_fragment ? 'wait' : 'auto'
					},
				overlayCSS: {
						backgroundColor: '#fff',
						cursor: is_fragment ? 'wait' : 'auto'
					},
				centerY: false,
				ignoreIfBlocked: true,
                onBlock: function() {

                    $('button.close-not-found').click(function() {

								$.unblockUI();

							});

							$('div.modal').modal('hide');

                    if ('gritter' in $)
								$.gritter.removeAll();
					},
                onUnblock: function() {

                    if (logged_in && is_fragment)
							History.back();
                    else if (!logged_in)
							top.location.href = base_url;

					},
				timeout: is_fragment || !logged_in ? 2000 : 0
			});

            $('a#err-view-details').click(function(e) {

				e.preventDefault();

				$(this).remove();

				$('span#err-details').removeClass('hidden');

			});

        }
	});

    $('div#violations-quick-add').on('shown.bs.modal', function() {

		var that = this;

		$('button.btn-primary', this).attr('disabled', 'disabled');

        if (!$('button.btn-primary', this).data('emploaded'))
			$.getJSON(
				base_url + 'employees/getAll',
				{
					filters: [
							'mb_no',
							'mb_id',
							'mb_name'
						]
				},
            function(data) {

						var opts = '<option value=""></option>';

                $.each(data.data, function(i, val) {

							opts += '<option value="' + val[0] + '">' + val[1] + ' - ' + val[2] + '</option>';

						});

						$('select#vio-employees-select')
							.html(opts)
							.chosen({
								search_contains: true
							})
                        .change(function(e, params) {

									var info = $('div.info', that);

                            if (params) {

										info.html('Loading&hellip;');

										$.getJSON(
											base_url + 'employees/get/' + params.selected,
                                        function(data) {

                                            if ($.isPlainObject(data))
													info.html('\
														<table class="table table-striped table-bordered table-hover" style="table-layout: fixed;">\
															<thead>\
																<th colspan="2">Summary</th>\
															</thead>\
															<tbody>\
																<tr>\
																	<td>Name</td>\
																	<td>' + data.mb_name + '</td>\
																</tr>\
																<tr>\
																	<td>Nickname</td>\
																	<td>' + data.mb_nick + '</td>\
																</tr>\
																<tr>\
																	<td>Nationality</td>\
																	<td>' + data.mb_3 + '</td>\
																</tr>\
																<tr>\
																	<td>Gender</td>\
																	<td>' + data.mb_sex + '</td>\
																</tr>\
																<tr>\
																	<td>Department</td>\
																	<td>' + data.dept_name + '</td>\
																</tr>\
																<tr>\
																	<td>Supervisor</td>\
																	<td>' + (data.supervisor ? data.supervisor : '&mdash;') + '</td>\
																</tr>\
															</tbody>\
														</table>\
													');
                                            else if ($.isArray(data) && !data.length)
													info.text('No information available.');

											}
										);
									}
									else
										info.text('Select an Employee ID on the left to display information here.');

								});

						$('span#loading-employees').hide();

						$('button.btn-primary', that).data('emploaded', true);

                if ($('button.btn-primary', that).data('violoaded'))
							$('button.btn-primary', that).removeAttr('disabled');

					}
			);

		$('select#vio-id')
			.html('<option selected="selected">Loading&hellip;</option>')
			.attr('disabled', 'disabled');

		$('button.btn-primary', this).data('violoaded', false);

		$.getJSON(
			base_url + 'violations/getAll/1',
                function(data) {

				$('button.btn-primary', that).data('violoaded', true);

				var opts = '';

                    $.each(data.data, function(i, val) {

					opts += '<option value="' + val[0] + '">' + val[1] + '</option>';

				});

				$('select#vio-id')
					.html(opts)
					.removeAttr('disabled');

                    if ($('button.btn-primary', that).data('emploaded'))
					$('button.btn-primary', that).removeAttr('disabled');

			}
		);


		$('input#vio-doc').datetimepicker('setDate', new Date());

	});

    $('div#violations-quick-add').on('hidden.bs.modal', function() {

        if ($('button.btn-primary', this).data('emploaded')) {

			$('select#vio-employees-select')[0].selectedIndex = 0;

			$('select#vio-employees-select')
				.trigger('chosen:updated')
				.trigger('change');

		}

		$('button.btn-primary', this).html('\
			<i class="ace-icon fa fa-check"></i>\
			Save\
		');

		$('button', this).removeAttr('disabled');

		$('input', this)
			.removeAttr('readonly')
			.val('');

		$('div.alert-success, div.alert-danger', this).addClass('hidden');

	});

    $('div#violations-quick-add button.btn-primary').click(function() {

        if ($('select#vio-employees-select')[0].selectedIndex && moment($.trim($('input#vio-doc').val()), 'MM-DD-YYYY').isValid()) {

			var that = this;

			$(this).html('\
				<i class="ace-icon fa fa-clock-o"></i>\
				Saving&hellip;\
			');

			$('div#violations-quick-add button').attr('disabled', 'disabled');

			$('div#violations-quick-add input').attr('readonly', 'readonly');

			$.post(
				base_url + 'employees/violations/add',
				{
					eid: parseInt($('select#vio-employees-select').val()),
					vid: parseInt($('select#vio-id').val()),
					doc: $.trim($('input#vio-doc').val()),
					rem: $.trim($('input#vio-remarks').val())
				},
            function(data) {


                if (data.success == -1)
							$('div#violations-quick-add div.alert-warning').removeClass('hidden');
                else if (data.success) {

							$('div#violations-quick-add div.alert-success').removeClass('hidden');


//							updatePendingCount(data.count);

                    setTimeout(function() {

								$('div#violations-quick-add').modal('hide');

                        if ('vio_rec_table_api' in window)
									window.vio_rec_table_api.ajax.reload();

							}, 2000);

						}
						else
							$('div#violations-quick-add div.alert-danger').removeClass('hidden');

						$(that).html('\
							<i class="ace-icon fa fa-check"></i>\
							Save\
						');

						$('div#violations-quick-add button').removeAttr('disabled');

						$('div#violations-quick-add input').removeAttr('readonly');

					},
				'json'
			);

		}

	});

	$('input#vio-doc')

		.datetimepicker({
			useCurrent: false,
			defaultDate: moment().hour(0).minute(0),
			maxDate: moment()
			// autoclose: true,
			// todayHighlight: true,
			// endDate: '+0d'
			//maxDateType: '+0d',
			//dateFormat: 'mm-dd-yy'
		})
		.next()
            .on('click', function() {

				$(this).prev().focus();

			});

	$('span#sub-comm').tooltip();

    $('button#top-search-btn').click(function() {

        if (History.getState().url == base_url + 'employees')
			$('table#emp-table').DataTable().search($.trim($('input#top-search').val())).draw();
		else {

			$('a[href="' + base_url + 'employees"]').parent().addClass('active open');

            History.pushState({from: 'main-nav', search: true, _: moment().unix()}, document.title, base_url + 'employees');
		}

	});

	$('a#top-pending-alert')
            .click(function(e) {

			e.preventDefault();

                if (History.getState().url == base_url + 'cite')
                    $('table#rec-table').DataTable().ajax.url(base_url + 'cite/getAll/cite/1/0').load(function() {

					$('button#cite-filter-btn')
						.removeClass('btn-primary btn-yellow btn-info btn-success')
						.addClass('btn-danger')
						.html('\
							<i class="ace-icon fa fa-filter"></i> Pending\
							<i class="ace-icon fa fa-chevron-down icon-on-right"></i>\
						');

					$('ul#cite-filter-status li').removeClass('active');

					$('ul#cite-filter-status li:eq(0)').addClass('active');

				});
			else
                    History.pushState({from: 'main-nav', pending: true, _: moment().unix()}, document.title, base_url + 'cite');

		})
		.tooltip({
                title: function() {

					var count = parseInt($('span.badge', this).text()) || 0;

					return count + ' pending cite form' + (count > 1 ? 's' : '');
				},
			placement: 'bottom'
		});

    $('form#top-search-form').submit(function(e) {

		e.preventDefault();

		$('button#top-search-btn').click();

	});

	/* window.addEventListener("keydown",function (e) {
		if (e.keyCode === 114 || (e.ctrlKey && e.keyCode === 70)) { 
		
			e.preventDefault();
			
			$('input#top-search').focus();
		}
	}); */

	// $('input#top-search')
		// .focusin(function () {

			// $(this).width($(this).width() + 120);

		// })
		// .focusout(function () {

			// $(this).width($(this).width() - 120);

		// });

	$('a.navbar-brand').hover(
            function() {

				$('i.fa-child', this).addClass('red2');

			},
            function() {

				$('i.fa-child', this).removeClass('red2');

			}
	);

    $('div#sidebar-shortcuts .btn:not(.disabled)').click(function() {

        if ($(this).hasClass('btn-success'))
            History.pushState({from: 'main-nav', _: moment().unix()}, document.title, base_url);

        if ($(this).hasClass('btn-info'))
            History.pushState({from: 'main-nav', _: moment().unix()}, document.title, base_url + 'employees');

        if ($(this).hasClass('btn-warning'))
            History.pushState({from: 'main-nav', _: moment().unix()}, document.title, base_url + 'violations');

        if ($(this).hasClass('btn-danger'))
            History.pushState({from: 'main-nav', _: moment().unix()}, document.title, base_url + 'cite');

	});

    $('img.user-photo').error(function() {

		this.src = base_url + 'assets/avatars/default-avatar-male.jpg';

	});

	// Override Handsontable function
    Handsontable.editors.DateEditor.prototype.createElements = function() {
		Handsontable.editors.TextEditor.prototype.createElements.apply(this, arguments);

		this.datePicker = document.createElement('DIV');
		Handsontable.Dom.addClass(this.datePicker, 'htDatepickerHolder');
		this.datePickerStyle = this.datePicker.style;
		this.datePickerStyle.position = 'absolute';
		this.datePickerStyle.top = 0;
		this.datePickerStyle.left = 0;
		this.datePickerStyle.zIndex = 99;
		document.body.appendChild(this.datePicker);
		this.$datePicker = $(this.datePicker);

		var that = this;
		var defaultOptions = {
			dateFormat: "yy-mm-dd",
			showButtonPanel: true,
			changeMonth: true,
			changeYear: true,
            onSelect: function(dateStr) {
					that.setValue(dateStr);
					that.finishEditing(false);
				}
		};
		this.$datePicker._datepicker(defaultOptions);

		var eventManager = Handsontable.eventManager(this);

		/**
		* Prevent recognizing clicking on jQuery Datepicker as clicking outside of table
		*/
        eventManager.addEventListener(this.datePicker, 'mousedown', function(event) {
			Handsontable.helper.stopPropagation(event);
			//event.stopPropagation();
		});

		this.hideDatepicker();
	};

    if (!$.jStorage.get('online'))
		$.jStorage.set('online', true);

    $.jStorage.listenKeyChange('online', function(key, action) {

        if (action == 'deleted') {

            if (window.opener)
				window.close();
			else
				top.location.href = base_url;
		}
	});

	var panel = $('li#notif-panel');

    $.jStorage.listenKeyChange('notifclick', function(key, action) {

        if (action == 'updated') {

			$('.notif-' + $.jStorage.get('notifclick')).removeClass('unread');

			var unread = parseInt($('span.badge', panel[0]).text(), 10) || 0;

            if (unread)
				unread--;

            if (unread)
				$('span.badge', panel[0]).text(unread);
			else {

				$('span.badge', panel[0]).empty();

				$('button#mark-all-read').attr('disabled', 'disabled');
			}

			$('i.fa-bell', panel[0]).toggleClass('icon-animated-bell', !!unread);

			$('ul.dropdown-menu').toggleClass('navbar-pink', !!unread);

			$('li.dropdown-header', panel[0]).html('\
				<i class="ace-icon fa fa-' + (!!unread ? 'exclamation-triangle' : 'check') + '"></i>\
				' + unread + ' unread\
			');
		}
	});

    $.jStorage.listenKeyChange('notifreadall', function(key, action) {

		$('li#notif-panel ul.dropdown-menu li:not(.dropdown-header, .dropdown-footer), div.list-group.notif a')
			.removeClass('unread')
                .each(function(i, elem) {

				var old = typeof $(elem).data('bs.tooltip').options.title == 'function' ? $(elem).data('bs.tooltip').options.title() : $(elem).data('bs.tooltip').options.title,
					wrapper = $('<div />');

				wrapper.append(old);

				var legend = $('i', wrapper[0]);

                    if (legend.hasClass('fa-eye-slash')) {

					legend
						.removeClass('fa-eye-slash')
						.addClass('fa-eye');


					$(elem).data('bs.tooltip').options.title = wrapper.html();
				}

			});

		$('span.badge', panel[0]).empty();

		$('i.fa-bell', panel[0]).removeClass('icon-animated-bell');

		$('ul.dropdown-menu').removeClass('navbar-pink');

		$('li.dropdown-header', panel[0]).html('\
			<i class="ace-icon fa fa-check"></i>\
			0 unread\
		');

	})

	$.getJSON(
		base_url + 'notifications/getAll/' + user_id,
            function(data) {

                if (data.count) {

				$('div#notifs-modal').data('count', data.count);

                    if (data.unread) {

					$('i.fa-bell', panel[0]).addClass('icon-animated-bell');

					$('span.badge', panel[0]).text(data.unread);

					$('ul.dropdown-menu').addClass('navbar-pink');

					$('li.dropdown-header', panel[0]).html('\
						<i class="ace-icon fa fa-exclamation-triangle"></i>\
						' + data.unread + ' unread\
					');

					$('button#mark-all-read').removeAttr('disabled');
				}

                    $('li.dropdown-header', panel[0]).after(function() {

					var ret = new Array(),
						ret2 = new Array();

                        $.each(data.data, function(i, val) {

                            if (i < 8) {

							var temp = $('\
									<li class="notif notif-' + val.id + (val.read_date ? '' : ' unread') + '">\
										<a href="#">\
											<i class="fa fa-' + notif_icons[val.type] + '"></i>&nbsp; ' + val.message + '\
										</a>\
									</li>\
								');

							temp.tooltip({
								placement: 'left',
								html: true,
                                    title: function() {

										return val.message + '<br /><small><i class="fa fa-' + (val.read_date ? 'eye' : 'eye-slash') + '"></i>&nbsp;<span class="text-muted">|</span>&nbsp;' + (ace.sizeof(val.triggered_by) ? val.triggered_by.mb_nick + ' (' + val.triggered_by.dept_name + ')' : val.type.toUpperCase()) + '&nbsp;<span class="text-muted">|</span>&nbsp;' + moment(val.created_date, 'X').format('YYYY-MM-DD HH:mm:ss') + '</small>';

									},
								template: '<div class="tooltip tooltip-notifs" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
							});

                                $('a', temp[0]).click(function(e) {

								e.preventDefault();

								var that = this,
									_parent = $(this).parent();

                                    if (_parent.hasClass('unread'))
									$.post(
										base_url + 'notifications/read',
										{
											id: val.id
										},
                                        function() {

											var old = $(_parent).data('bs.tooltip').options.title(),
												wrapper = $('<div />');

											wrapper.append(old);

											$('i', wrapper[0])
												.removeClass('fa-eye-slash')
												.addClass('fa-eye');

											$(_parent)
												.removeClass('unread')
												.data('bs.tooltip').options.title = wrapper.html();

											$('div.list-group.notif a:eq(' + $('ul.dropdown-menu li:not(.dropdown-header, .dropdown-footer)', panel[0]).index(temp) + ')')
												.removeClass('unread')
												.data('bs.tooltip').options.title = wrapper.html();

											// var unread = parseInt($('span.badge', panel[0]).text(), 10);

											// if(unread)
												// unread--;

											// if(unread)
												// $('span.badge', panel[0]).text(unread);
											// else {

												// $('span.badge', panel[0]).empty();

												// $('button#mark-all-read').attr('disabled', 'disabled');
											// }

											// $('i.fa-bell', panel[0]).toggleClass('icon-animated-bell', !!unread);

											// $('ul.dropdown-menu').toggleClass('navbar-pink', !!unread);

											// $('li.dropdown-header', panel[0]).html('\
												// <i class="ace-icon fa fa-' + (!!unread ? 'exclamation-triangle' : 'check') + '"></i>\
												// ' + unread + ' unread\
											// ');

										}
									);

                                    if (val.page)
                                        History.pushState({from: 'main-nav', callback: val.action, args: JSON.stringify(val.args), _: moment().unix()}, document.title, base_url + val.page);
                                    else if (val.action)
									window[val.action](val.args);

								$.jStorage.set('notifclick', val.id);
							});

							ret[ret.length] = temp;
						}

						var temp2 = $('<a class="list-group-item notif-' + val.id + (val.read_date ? '' : ' unread') + (i < 8 ? '' : ' hidden') + '" href="#"><i class="fa fa-' + notif_icons[val.type] + ' fa-fw"></i>&nbsp; ' + val.message + '</a>');

						temp2.tooltip({
							container: 'body',
							html: true,
                                title: function() {

									return val.message + '<br /><small><i class="fa fa-' + (val.read_date ? 'eye' : 'eye-slash') + '"></i>&nbsp;<span class="text-muted">|</span>&nbsp;' + (ace.sizeof(val.triggered_by) ? val.triggered_by.mb_nick + ' (' + val.triggered_by.dept_name + ')' : val.type) + '&nbsp;<span class="text-muted">|</span>&nbsp;' + moment(val.created_date, 'X').format('YYYY-MM-DD HH:mm:ss') + '</small>';

								},
							template: '<div class="tooltip tooltip-notifs" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
						});

                            temp2.click(function(e) {

							e.preventDefault();

							var that = this;

							$('div.modal').modal('hide');

                                if ($(this).hasClass('unread'))
								$.post(
									base_url + 'notifications/read',
									{
										id: val.id
									},
                                    function() {

										var old = $(that).data('bs.tooltip').options.title(),
											wrapper = $('<div />');

										wrapper.append(old);

										$('i', wrapper[0])
											.removeClass('fa-eye-slash')
											.addClass('fa-eye');

										$(that)
											.removeClass('unread')
											.data('bs.tooltip').options.title = wrapper.html();

										$('ul.dropdown-menu li:not(.dropdown-header, .dropdown-footer):eq(' + $('div#notifs-modal a.list-group-item').index(temp2) + ')', panel[0])
											.removeClass('unread')
											.data('bs.tooltip').options.title = wrapper.html();

										// var unread = parseInt($('span.badge', panel[0]).text(), 10);

										// if(unread)
											// unread--;

										// if(unread)
											// $('span.badge', panel[0]).text(unread);
										// else {

											// $('span.badge', panel[0]).empty();

											// $('button#mark-all-read').attr('disabled', 'disabled');
										// }

										// $('i.fa-bell', panel[0]).toggleClass('icon-animated-bell', !!unread);

										// $('ul.dropdown-menu').toggleClass('navbar-pink', !!unread);

										// $('li.dropdown-header', panel[0]).html('\
											// <i class="ace-icon fa fa-' + (!!unread ? 'exclamation-triangle' : 'check') + '"></i>\
											// ' + unread + ' unread\
										// ');

									}
								);

                                if (val.page)
                                    History.pushState({from: 'main-nav', callback: val.action, args: JSON.stringify(val.args), _: moment().unix()}, document.title, base_url + val.page);
                                else if (val.action)
								window[val.action](val.args);

							$.jStorage.set('notifclick', val.id);
						});

						ret2[ret2.length] = temp2;

					});

                        if (ret2.length) {

						$('div#notifs-modal div.alert-info').hide();

						var group = $('<div class="list-group notif" />');

						group.append(ret2);

						$('div#notifs-modal nav').before(group);

                            if (ret2.length > 7)
							$('div#notifs-modal li.previous').removeClass('disabled');
					}
					else {

						$('div#notifs-modal div.alert-info').show();

						$('div#notifs-modal div.list-group').remove();
					}

					return ret;

				});
			}

			WS.ready(
				document.location.host == '10.120.10.139' ? 'hris' : 'hristest',
				user_id,
				null,
				initNotifications
			);

		}
	);

	$.getJSON(
		base_url + 'notifications/getAllForApproval',
            function(data) {
		  var obt_total = (data.obt_count * 1);
                if (obt_total > 0)
	        $("#obt_cnt").html(obt_total);
		  else
		    $("#obt_cnt").html("");
		  var ot_total = (data.ot_count * 1);
                if (ot_total > 0)
	        $("#ot_cnt").html(ot_total);
		  else
		    $("#ot_cnt").html("");
		  var lv_total = (data.lv_count * 1);
                if (lv_total > 0)
	        $("#lv_cnt").html(lv_total);
		  else
		    $("#lv_cnt").html("");
		  var cws_total = (data.cws_count * 1);
                if (cws_total > 0)
	        $("#cws_cnt").html(cws_total);
		  else
		    $("#cws_cnt").html("");
                var att_total = (data.att_count * 1);
                if (att_total > 0)
                    $("#att_cnt").html(att_total);
                else
                    $("#att_cnt").html("");
		}
	);

	getAllMessages();
    $.getJSON(
            base_url + 'sms/getTotalMessagesForTheDay',
            function(data) {
                var sms_total = (data.recordsTotal * 1);
                if (sms_total > 0)
                    $("#sms_cnt").html(sms_total);
                else
                    $("#sms_cnt").html("");
            }
    );

    $('button#mark-all-read').click(function() {

		$(this).attr('disabled', 'disabled');

		$.get(
			base_url + 'notifications/readAll',
                function() {

				$.jStorage.set('notifreadall', moment().unix());

			}
		);

	});

    $('div#notifs-modal li.previous a').click(function(e) {

		e.preventDefault();

		var page = $('div#notifs-modal').data('page');

        if (!$(this).parent().hasClass('disabled')) {

			$('div.list-group.notif a').slice(page * 8, (page * 8) + 8).removeClass('hidden');

			$('div.list-group.notif a').slice((page * 8) - 8, page * 8).addClass('hidden');

			$('div#notifs-modal').data().page++;

            if ($('div#notifs-modal').data('page') > 1)
				$('div#notifs-modal li.next').removeClass('disabled');

            if (page == Math.floor($('div#notifs-modal').data('count') / 8))
				$(this).parent().addClass('disabled');

		}

	});

    $('div#notifs-modal li.next a').click(function(e) {

		e.preventDefault();

		var page = $('div#notifs-modal').data('page') - 1;

        if (!$(this).parent().hasClass('disabled')) {

			$('div.list-group.notif a').slice(page * 8, (page * 8) + 8).addClass('hidden');

			$('div.list-group.notif a').slice((page * 8) - 8, page * 8).removeClass('hidden');

			$('div#notifs-modal').data().page--;

            if ($('div#notifs-modal').data('page') == 1)
				$(this).parent().addClass('disabled');

            if (Math.floor($('div#notifs-modal').data('count') / 8) > 1)
				$('div#notifs-modal li.previous').removeClass('disabled');

		}

	});

	// $.jGrowl.defaults.closerTemplate = null;
	$.jGrowl.defaults.closerTemplate = '<div>Close All</div>';

    $('li#subordinates-panel a').click(function(e) {

		e.preventDefault();

		$('div#subords-modal').modal('show');
    $('div#subords-modal #kpi-success-alert').addClass('hidden');

	});
    
    $('li#subordinates-panel').tooltip({
    	placement: 'bottom'
    });

	$('div#subords-modal table').dataTable({
		columns: [
				{},
				{},
				{
					orderable: false
				}
			],
        createdRow: function(row, data) {

				$('input:text', row)
					.ace_spinner({
						value: null,
						min: 0,
						max: 60,
						btn_up_class: 'btn-light',
						btn_down_class: 'btn-light'
					})
                    .change(function() {

                        if (parseInt(this.value, 10) > 60)
							$(this).ace_spinner('value', 60);

					})
                    .blur(function() {

                        if (!$.isNumeric(this.value) && this.value != '')
							$(this).ace_spinner('value', 1);

					})
                    .on('keydown', function(e) {

						var index = $('input.score-input').index(this);

                        if (e.which == 9)
							e.preventDefault();

                        if (e.which == 9 || e.which == 13)
							$('input.score-input:eq(' + (index + 1) + ')').focus();

                        if (e.shiftKey && e.which == 9)
							$('input.score-input:eq(' + (index - 1) + ')').focus();

					});
			}
	});

	$('div#subords-modal div.dataTables_wrapper div.row:eq(1) div.col-sm-12').css('padding', 0);

	$('div#subords-modal')
            .on('show.bs.modal', function() {

			var that = this;

			$('input:text', this).attr('disabled', 'disabled');

			$.getJSON(
				base_url + 'employees/getSubordinatesScores',
                        function(data) {

                            $.each(data, function(i, val) {


            subordinates[val.hr_users_id] = val.score;
						$('input:text#' + val.hr_users_id, that).ace_spinner('value', val.score);
					});

					$('input:text', that).removeAttr('disabled');

				}
			);

		})
            .on('hidden.bs.modal', function() {

			$('input:text', this).val('');

		});

    $(document).on('click', 'span#pending-cites', function() {

        if (History.getState().url == base_url + 'cite')
            $('table#rec-table').DataTable().ajax.url(base_url + 'cite/getAll/cite/1/0').load(function() {

				$('button#cite-filter-btn')
					.removeClass('btn-primary btn-yellow btn-info btn-success')
					.addClass('btn-danger')
					.html('\
						<i class="ace-icon fa fa-filter"></i> Pending\
						<i class="ace-icon fa fa-chevron-down icon-on-right"></i>\
					');

				$('ul#cite-filter-status li').removeClass('active');

				$('ul#cite-filter-status li:eq(0)').addClass('active');

			});
		else
            History.pushState({from: 'main-nav', pending: true, _: moment().unix()}, document.title, base_url + 'cite');

	});

    var subordinates = new Object();

    $('#dept-kpi-scores > tbody > tr').each(function() {
        subordinates[$(this).attr('id')] = "";
    });

    $(".score-input").change(function() {
        if ($(this).val() >= 0) {
        subordinates[$(this).closest("tr").attr("id")] = $(this).val();
      }
    });

    $("#saveDeptKpi").click(function() {

        console.log(subordinates)

        $.ajax({
            url: base_url + "employees/insertDeptKpiScore",
            type: 'POST',
            data: {'subordinates': subordinates},
            success: function() {

                  $('div#subords-modal #kpi-success-alert').removeClass('hidden');

                setTimeout(function() {
                      $('div#subords-modal').modal('hide');
                  }, 1000);

                  countScoredKPI();
            }
        });
    });

    function countScoredKPI() {

      var count = 0;
        for (var x in subordinates) {
            if (!subordinates[x] || subordinates[x] == 0) {
          count++
        }
      }

        if (count == 0) {
            $('#subordinates-panel span.badge').html('');
        } else {
      $('#subordinates-panel span.badge').html(count);
        }
    }

});
