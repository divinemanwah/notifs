function parse_str(str, array) {
  //       discuss at: http://phpjs.org/functions/parse_str/
  //      original by: Cagri Ekin
  //      improved by: Michael White (http://getsprink.com)
  //      improved by: Jack
  //      improved by: Brett Zamir (http://brett-zamir.me)
  //      bugfixed by: Onno Marsman
  //      bugfixed by: Brett Zamir (http://brett-zamir.me)
  //      bugfixed by: stag019
  //      bugfixed by: Brett Zamir (http://brett-zamir.me)
  //      bugfixed by: MIO_KODUKI (http://mio-koduki.blogspot.com/)
  // reimplemented by: stag019
  //         input by: Dreamer
  //         input by: Zaide (http://zaidesthings.com/)
  //         input by: David Pesta (http://davidpesta.com/)
  //         input by: jeicquest
  //             note: When no argument is specified, will put variables in global scope.
  //             note: When a particular argument has been passed, and the returned value is different parse_str of PHP. For example, a=b=c&d====c
  //             test: skip
  //        example 1: var arr = {};
  //        example 1: parse_str('first=foo&second=bar', arr);
  //        example 1: $result = arr
  //        returns 1: { first: 'foo', second: 'bar' }
  //        example 2: var arr = {};
  //        example 2: parse_str('str_a=Jack+and+Jill+didn%27t+see+the+well.', arr);
  //        example 2: $result = arr
  //        returns 2: { str_a: "Jack and Jill didn't see the well." }
  //        example 3: var abc = {3:'a'};
  //        example 3: parse_str('abc[a][b]["c"]=def&abc[q]=t+5');
  //        returns 3: {"3":"a","a":{"b":{"c":"def"}},"q":"t 5"}

  var strArr = String(str)
    .replace(/^&/, '')
    .replace(/&$/, '')
    .split('&'),
    sal = strArr.length,
    i, j, ct, p, lastObj, obj, lastIter, undef, chr, tmp, key, value,
    postLeftBracketPos, keys, keysLen,
    fixStr = function(str) {
      return decodeURIComponent(str.replace(/\+/g, '%20'));
    };

  if (!array) {
    array = this.window;
  }

  for (i = 0; i < sal; i++) {
    tmp = strArr[i].split('=');
    key = fixStr(tmp[0]);
    value = (tmp.length < 2) ? '' : fixStr(tmp[1]);

    while (key.charAt(0) === ' ') {
      key = key.slice(1);
    }
    if (key.indexOf('\x00') > -1) {
      key = key.slice(0, key.indexOf('\x00'));
    }
    if (key && key.charAt(0) !== '[') {
      keys = [];
      postLeftBracketPos = 0;
      for (j = 0; j < key.length; j++) {
        if (key.charAt(j) === '[' && !postLeftBracketPos) {
          postLeftBracketPos = j + 1;
        } else if (key.charAt(j) === ']') {
          if (postLeftBracketPos) {
            if (!keys.length) {
              keys.push(key.slice(0, postLeftBracketPos - 1));
            }
            keys.push(key.substr(postLeftBracketPos, j - postLeftBracketPos));
            postLeftBracketPos = 0;
            if (key.charAt(j + 1) !== '[') {
              break;
            }
          }
        }
      }
      if (!keys.length) {
        keys = [key];
      }
      for (j = 0; j < keys[0].length; j++) {
        chr = keys[0].charAt(j);
        if (chr === ' ' || chr === '.' || chr === '[') {
          keys[0] = keys[0].substr(0, j) + '_' + keys[0].substr(j + 1);
        }
        if (chr === '[') {
          break;
        }
      }

      obj = array;
      for (j = 0, keysLen = keys.length; j < keysLen; j++) {
        key = keys[j].replace(/^['"]/, '')
          .replace(/['"]$/, '');
        lastIter = j !== keys.length - 1;
        lastObj = obj;
        if ((key !== '' && key !== ' ') || j === 0) {
          if (obj[key] === undef) {
            obj[key] = {};
          }
          obj = obj[key];
        } else { // To insert new dimension
          ct = -1;
          for (p in obj) {
            if (obj.hasOwnProperty(p)) {
              if (+p > ct && p.match(/^\d+$/g)) {
                ct = +p;
              }
            }
          }
          key = ct + 1;
        }
      }
      lastObj[key] = value;
    }
  }
}

function getCategories(callback) {
	
	$.getJSON(
		base_url + 'violations/getCategories/' + ($('input#vio-cat-display-disabled').prop('checked') ? '/1' : ''),
		function (data) {
				
				if(callback)
					callback(data);
				
				var cats = new Array(),
					opts = '';
				
				var getNestedChildren = function (arr, parent) {
				
						var out = [];
						for(var i in arr) {
							if(arr[i].parent == parent) {
								var children = getNestedChildren(arr, arr[i].id);

								if(children.length) {
									arr[i].children = children;
								}
								out.push(arr[i]);
							}
						}
						return out
					};

				$.each(data.data, function (i, val) {

					cats[cats.length] = {
							id: parseInt(val[0], 10),
							title: val[2],
							parent: parseInt(val[1], 10) || 0
						};
					
				});
				
				var cat_list = getNestedChildren(cats, 0);

				// var generateSpacers = function (i) {
				
						// var str = '';
						
						// while(i--) {
							
							// str += '&nbsp;&nbsp;';
						// }
						
						// return str;
					// };

				var buildOpts = function (arr, d) {
				
						var opts = '',
							depth = d || 0;

						$.each(arr, function (i, val) {
							
							// opts += '<option value="' + val.id + '">' + generateSpacers(depth) + val.title + '</option>' + ($.isArray(val.children) ? buildOpts(val.children, depth + 1) : '');
							opts += '<option value="' + val.id + '"' + (depth ? ' style="padding-left: ' + (1.5 * depth) + 'em;"' : '') + '>' + val.title + '</option>' + ($.isArray(val.children) ? buildOpts(val.children, depth + 1) : '');
							
						});
						
						return opts;
						
					};
				
				opts = buildOpts(cat_list);
					
				$('button.vio-cat-add-btn').removeAttr('disabled');

				$('select#vio-category-1')
					.html('<optgroup><option value="0">None</option></optgroup><optgroup class="select-separator">' + opts + '</optgroup>')
					.removeAttr('disabled');
				
				$('select#vio-category-2')
					.html(opts)
					.removeAttr('disabled');
				
			}
	);
}

$(function () {

	var vio_table = $('table#vio-table').dataTable({
			ajax: base_url + 'violations/getAll/1' + ($('input#vio-display-disabled').prop('checked') ? '/1' : ''),
			deferRender: true,
			autoWidth: false,
			columns: [
					{
						orderable: false,
						searchable: false,
						data: 0,
						render: function (d) {
								return '<label class="position-relative"><input type="checkbox" class="ace" value="' + d + '" /><span class="lbl"></span></label>';
							},
						className: 'center'
					},
					{
						data: 1,
						className: 'ellipsis'
					},
					{
						data: 7,
						searchable: false,
						render: function (d) {
								
								return d == 1 ? '<span class="label label-sm label-success">Enabled</span>' : '<span class="label label-sm label-warning">Disabled</span>';
								
							},
						className: 'center no-highlight'
					},
					{
						orderable: false,
						searchable: false,
						data: 0,
						render: function (d) {

								// return '\
										// <div class="hidden-sm hidden-xs action-buttons">\
											// <a class="blue view-btn" href="#" data-id="' + d + '">\
												// <i class="ace-icon fa fa-search-plus bigger-130"></i>\
											// </a>\
											// <a class="purple rules-btn" href="#" data-id="' + d + '">\
												// <i class="ace-icon fa fa-book bigger-130"></i>\
											// </a>\
											// <a class="green edit-btn" href="#" data-id="' + d + '">\
												// <i class="ace-icon fa fa-pencil bigger-130"></i>\
											// </a>\
											// <a class="red delete-btn" href="#" data-id="' + d + '">\
												// <i class="ace-icon fa fa-trash-o bigger-130"></i>\
											// </a>\
										// </div>\
										// <div class="hidden-md hidden-lg">\
											// <div class="inline position-relative">\
												// <button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown" data-position="auto">\
													// <i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>\
												// </button>\
												// <ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">\
													// <li>\
														// <a href="#" class="tooltip-info view-btn" data-rel="tooltip" title="View" data-id="' + d + '">\
															// <span class="blue">\
																// <i class="ace-icon fa fa-search-plus bigger-120"></i>\
															// </span>\
														// </a>\
													// </li>\
													// <li>\
														// <a href="#" class="tooltip-warning rules-btn" data-rel="tooltip" title="Rules" data-id="' + d + '">\
															// <span class="purple">\
																// <i class="ace-icon fa fa-book bigger-120"></i>\
															// </span>\
														// </a>\
													// </li>\
													// <li>\
														// <a href="#" class="tooltip-success edit-btn" data-rel="tooltip" title="Edit" data-id="' + d + '">\
															// <span class="green">\
																// <i class="ace-icon fa fa-pencil-square-o bigger-120"></i>\
															// </span>\
														// </a>\
													// </li>\
													// <li>\
														// <a href="#" class="tooltip-error delete-btn" data-rel="tooltip" title="Delete" data-id="' + d + '">\
															// <span class="red">\
																// <i class="ace-icon fa fa-trash-o bigger-120"></i>\
															// </span>\
														// </a>\
													// </li>\
												// </ul>\
											// </div>\
										// </div>\
									// ';
									
									return '\
										<div class="hidden-sm hidden-xs action-buttons">\
											<a class="blue view-btn" href="#" data-id="' + d + '">\
												<i class="ace-icon fa fa-search-plus bigger-130"></i>\
											</a>\
											<a class="green edit-btn" href="#" data-id="' + d + '">\
												<i class="ace-icon fa fa-pencil bigger-130"></i>\
											</a>\
											<a class="red delete-btn" href="#" data-id="' + d + '">\
												<i class="ace-icon fa fa-trash-o bigger-130"></i>\
											</a>\
										</div>\
										<div class="hidden-md hidden-lg">\
											<div class="inline position-relative">\
												<button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown" data-position="auto">\
													<i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>\
												</button>\
												<ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">\
													<li>\
														<a href="#" class="tooltip-info view-btn" data-rel="tooltip" title="View" data-id="' + d + '">\
															<span class="blue">\
																<i class="ace-icon fa fa-search-plus bigger-120"></i>\
															</span>\
														</a>\
													</li>\
													<li>\
														<a href="#" class="tooltip-success edit-btn" data-rel="tooltip" title="Edit" data-id="' + d + '">\
															<span class="green">\
																<i class="ace-icon fa fa-pencil-square-o bigger-120"></i>\
															</span>\
														</a>\
													</li>\
													<li>\
														<a href="#" class="tooltip-error delete-btn" data-rel="tooltip" title="Delete" data-id="' + d + '">\
															<span class="red">\
																<i class="ace-icon fa fa-trash-o bigger-120"></i>\
															</span>\
														</a>\
													</li>\
												</ul>\
											</div>\
										</div>\
									';
								
							},
						className: 'center no-highlight'
					},
					{
						data: 8,
						visible: false,
						searchable: false
					}
				],
			order: [[4, 'desc']],
			rowCallback: function (row, data) {

					var that = this,
						_api = this.api();

					// if(parseInt(data[7]))
						// $('td:eq(2)', row).html('\
							// ' + (data[2] == 1 ? '<span class="label label-sm label-success">Enabled</span>' : '<span class="label label-sm label-warning">Disabled</span>') + '\
							// <span class="label label-sm label-purple" title="Click the purple button to set rules for this violation">\
								// <i class="ace-icon fa fa-exclamation-triangle"></i>\
								// No rules\
							// </span>\
						// ');
					// else if()
					
					// if(parseInt(data[7]))
						// $('td:eq(2)', row).html('\
							// ' + (data[7] == 1 ? '<span class="label label-sm label-success">Enabled</span>' : '<span class="label label-sm label-warning">Disabled</span>') + '\
						// ');
						
					$('td:eq(2) span', row)
						.on('shown.bs.tooltip', function () {
							
							$('td:eq(3) a.rules-btn', row).addClass('fa-2x');
							
						})
						.on('hidden.bs.tooltip', function () {
							
							$('td:eq(3) a.rules-btn', row).removeClass('fa-2x');
							
						});

					$('td:eq(2) span.label-purple, td:eq(3) a', row).tooltip();
					
					$('td:eq(3) a.delete-btn', row).click(function (e) {
						
						e.preventDefault();
						
						var _id = $(this).data('id');

						if(confirm('Are you sure you want to ' + (data[7] == 1 ? 'disable' : 'remove') + ' this entry?')) {
						
							$(row).block({
								message: $(new Spinner({ color: '#438EB9', lines: 8, length: 4, width: 3, radius: 5 }).spin().el),
								css: {
										border: 'none',
										backgroundColor: 'transparent'
									},
								overlayCSS: {
										backgroundColor: '#fff'
									}
							});
							
							$.post(
								base_url + 'violations/update/1',
								{
									id: _id,
									data: {
											status: data[7] == 1 ? 0 : 2
										}
								},
								function (_data) {
									
									if(_data.success) {
									
										if(data[7] == 1) {
										
											data[7] = 0;
										
											if($('input#vio-display-disabled').prop('checked'))
												_api.row(row).data(data).draw();
											else
												_api.row(row).remove().draw();
											
											$(row).unblock();
										}
										else
											_api.row(row).remove().draw();
									}
									else
										$(row).block({
											message: '<div class="alert alert-danger">\
														<button type="button" class="close close-not-found" data-dismiss="alert">\
															<i class="ace-icon fa fa-times"></i>\
														</button>\
														<strong>\
															<i class="ace-icon fa fa-times"></i>\
															Error!\
														</strong>\
														Data not saved.\
														<br>\
													</div>',
											css: {
													border: 'none',
													backgroundColor: 'transparent',
													left: '30%',
													width: '40%'
												},
											overlayCSS: {
													backgroundColor: '#fff'
												},
											timeout: 2000
										});
								},
								'json'
							);
						}
						
					});
					
					$('td:eq(3) a.edit-btn', row).click(function (e) {

						e.preventDefault();
						
						window.curr_vio_row = row;
						
						$('div#vio-modal h4').text('Edit violation');
						
						$('div#vio-modal div.edit input').val(data[1]);
						
						$('div#vio-modal div.edit button')
							.html('\
								' + (data[7] == '1' ? 'Enabled' : 'Disabled') + '\
								<span class="ace-icon fa fa-caret-down icon-on-right"></span>\
							')
							.removeClass('btn-success btn-warning')
							.addClass('btn-' + (data[7] == '1' ? 'success' : 'warning'));
						
						$('select#vio-category-2').prop('selectedIndex', function () {

							return $('option', this).index($('option[value="' + data[2] + '"]', this));
							
						});
						
						var p_period = data[4].split(',');
						
						$('select#infra-years').prop('selectedIndex', function () {

							return $('option', this).index($('option[value="' + p_period[0] + '"]', this));
							
						});
						
						$('select#infra-condition').prop('selectedIndex', function () {

							return $('option', this).index($('option[value="' + p_period[1] + '"]', this));
							
						}).change();
						
						setTimeout(function () {
							
							if(p_period[2])
								$('select#infra-month').prop('selectedIndex', function () {

									return $('option', this).index($('option[value="' + p_period[2] + '"]', this));
									
								});
							
						}, 100);

						if(parseInt(data[5], 10)) {
						
							$('input#no-dismissal-switch')
								.prop('checked', true)
								.change();
							
							$('input.infraction-score').ace_spinner('value', parseFloat(data[3]));
						}
						else {
							
							$('input#no-dismissal-switch')
								.prop('checked', false)
								.change();
								
							$('input#no-dismissal-score').ace_spinner('value', 1);
							
							var matrix = data[3].split(',');
							
							$.each(matrix, function (i, val) {

								if(val != 'D')
									$('input.infraction-score')
										.eq(i)
											.ace_spinner('value', parseFloat(val));
								else
									$('input.infraction-score')
										.eq(i)
											.parents('td')
											.find('input:checkbox')
												.prop('checked', true)
												.change();
							});
						}	
		
						$('button#vio-save').data({
							id: $(this).data('id'),
							cite: data[6]
						});
						
						$('div#vio-modal input.add').addClass('hidden');
						
						$('div#vio-modal div.edit').removeClass('hidden');
						
						$('div#vio-modal').modal({
							backdrop: 'static'
						});
						
					});
					
					$('td:eq(3) a.view-btn', row)
						.click(function (e) {
							
							e.preventDefault();
							
						})
						.popover({
							content: 'Please wait&hellip;',
							placement: 'left',
							html: true,
							trigger: 'focus'
						})
						.on('hide.bs.popover', function () {
							
							if(window.curr_vio_xhr)
								window.curr_vio_xhr.abort();
							
						})
						.on('shown.bs.popover', function () {

							var that = this;
								
							window.curr_vio_xhr = $.getJSON(
									base_url + 'violations/get/' + $(this).data('id'),
									function (_data) {
									
											delete window.curr_vio_xhr;

											$(that).popover('destroy');
											
											setTimeout(function () {

												$(that)
													.popover({
														title: 'Information',
														content: '\
															<table class="table" width="100%" style="table-layout: fixed;">\
																<tr><td width="40%">ID</td><td width="60%">' + _data.id + '</td></tr>\
																<tr><td width="40%">Description</td><td width="60%" style="word-wrap: break-word;">' + _data.description + '</td></tr>\
																<tr><td width="40%">Status</td><td width="60%">' + (_data.status == 1 ? 'Enabled' : 'Disabled') + '</td></tr>\
																<tr><td width="40%">Created Date</td><td width="60%">' + _data.created_date + '</td></tr>\
																<tr><td width="40%">Updated Date</td><td width="60%">' + (_data.updated_date ? _data.updated_date : '--') + '</td></tr>\
																<tr><td width="40%">Created By</td><td width="60%">' + _data.created_by_nick + '</td></tr>\
																<tr><td width="40%">Updated By</td><td width="60%">' + (_data.updated_by_nick ? _data.updated_by_nick : '--') + '</td></tr>\
															</table>',
														placement: 'left',
														html: true,
														trigger: 'focus'
													})
													.popover('show');

											}, 250);
											
										}
								).fail(function () { alert('An error occured. Please try again later.'); });
							
						});
					
					$('td:eq(3) a.rules-btn', row).click(function (e) {
					
						e.preventDefault();
						
						$('td#vio-rules-description').text(data[1]);
						
						$('button#vio-rules-save').data('vid', data[0]);
						
						$('div#vio-rules-modal')
							.data('id', $(this).data('id'))
							.modal({
								backdrop: 'static'
							});
						
					});
				}
		}),
		vio_table_api = vio_table.api();
	
	var vio_cat_table = $('table#vio-cat-table').dataTable({
			ajax: function (data, callback, settings) {

					getCategories(callback);
					
				},
			deferRender: true,
			autoWidth: false,
			columns: [
					{
						orderable: false,
						searchable: false,
						data: 0,
						render: function (d) {
								return '<label class="position-relative"><input type="checkbox" class="ace" value="' + d + '" /><span class="lbl"></span></label>';
							},
						className: 'center'
					},
					{
						data: 2,
						className: 'ellipsis'
					},
					{
						data: 3,
						searchable: false,
						render: function (d) {
								
								return d == 1 ? '<span class="label label-sm label-success">Enabled</span>' : '<span class="label label-sm label-warning">Disabled</span>';
								
							},
						className: 'center no-highlight'
					},
					{
						orderable: false,
						searchable: false,
						data: 0,
						render: function (d) {

								return '\
										<div class="hidden-sm hidden-xs action-buttons">\
											<a class="blue view-btn" href="#" data-id="' + d + '">\
												<i class="ace-icon fa fa-search-plus bigger-130"></i>\
											</a>\
											<a class="green edit-btn" href="#" data-id="' + d + '">\
												<i class="ace-icon fa fa-pencil bigger-130"></i>\
											</a>\
											<a class="red delete-btn" href="#" data-id="' + d + '">\
												<i class="ace-icon fa fa-trash-o bigger-130"></i>\
											</a>\
										</div>\
										<div class="hidden-md hidden-lg">\
											<div class="inline position-relative">\
												<button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown" data-position="auto">\
													<i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>\
												</button>\
												<ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">\
													<li>\
														<a href="#" class="tooltip-info view-btn" data-rel="tooltip" title="View" data-id="' + d + '">\
															<span class="blue">\
																<i class="ace-icon fa fa-search-plus bigger-120"></i>\
															</span>\
														</a>\
													</li>\
													<li>\
														<a href="#" class="tooltip-success edit-btn" data-rel="tooltip" title="Edit" data-id="' + d + '">\
															<span class="green">\
																<i class="ace-icon fa fa-pencil-square-o bigger-120"></i>\
															</span>\
														</a>\
													</li>\
													<li>\
														<a href="#" class="tooltip-error delete-btn" data-rel="tooltip" title="Delete" data-id="' + d + '">\
															<span class="red">\
																<i class="ace-icon fa fa-trash-o bigger-120"></i>\
															</span>\
														</a>\
													</li>\
												</ul>\
											</div>\
										</div>\
									';
								
							},
						className: 'center no-highlight'
					},
					{
						data: 5,
						visible: false,
						searchable: false
					},
				],
			order: [[4, 'desc']],
			rowCallback: function (row, data) {

					var that = this,
						_api = this.api();
					
					$('td:eq(3) a', row).tooltip();
					
					$('td:eq(3) a.delete-btn', row).click(function (e) {
						
						e.preventDefault();
						
						var _id = $(this).data('id');

						if(confirm('Are you sure you want to ' + (data[2] == 1 ? 'disable' : 'remove') + ' this entry?')) {
						
							$(row).block({
								message: $(new Spinner({ color: '#438EB9', lines: 8, length: 4, width: 3, radius: 5 }).spin().el),
								css: {
										border: 'none',
										backgroundColor: 'transparent'
									},
								overlayCSS: {
										backgroundColor: '#fff'
									}
							});
							
							$.post(
								base_url + 'violations/updateCategory',
								{
									id: _id,
									active: data[2] == 1 ? 0 : 2
								},
								function (_data) {
									
									if(_data.success) {
									
										if(data[2] == 1) {
										
											data[2] = 0;
										
											if($('input#vio-display-disabled').prop('checked'))
												_api.row(row).data(data).draw();
											else
												_api.row(row).remove().draw();
											
											$(row).unblock();
										}
										else
											_api.row(row).remove().draw();
									}
									else
										$(row).block({
											message: '<div class="alert alert-danger">\
														<button type="button" class="close close-not-found" data-dismiss="alert">\
															<i class="ace-icon fa fa-times"></i>\
														</button>\
														<strong>\
															<i class="ace-icon fa fa-times"></i>\
															Error!\
														</strong>\
														Data not saved.\
														<br>\
													</div>',
											css: {
													border: 'none',
													backgroundColor: 'transparent',
													left: '30%',
													width: '40%'
												},
											overlayCSS: {
													backgroundColor: '#fff'
												},
											timeout: 2000
										});
								},
								'json'
							);
						}
						
					});
					
					$('td:eq(3) a.edit-btn', row).click(function (e) {

						e.preventDefault();
						
						window.curr_vio_cat_row = row;
						
						$('div#vio-cat-modal h4').text('Edit category');
						
						$('div#vio-cat-modal div.edit input').val(data[2]);
						
						$('select#vio-category-1').prop('selectedIndex', function () {

							return $('option', this).index($('option[value="' + data[1] + '"]', this));
							
						});
						
						$('div#vio-cat-modal div.edit button')
							.html('\
								' + (data[3] == '1' ? 'Enabled' : 'Disabled') + '\
								<span class="ace-icon fa fa-caret-down icon-on-right"></span>\
							')
							.removeClass('btn-success btn-warning')
							.addClass('btn-' + (data[3] == '1' ? 'success' : 'warning'));
		
						$('button#vio-cat-save').data({
							id: $(this).data('id')
						});
						
						$('div#vio-cat-modal input.add').addClass('hidden');
						
						$('div#vio-cat-modal div.edit').removeClass('hidden');
						
						$('select#vio-category-1 option[value="' + data[0] + '"]').addClass('hidden');
						
						$('div#vio-cat-modal').modal({
							backdrop: 'static'
						});
						
					});
					
					$('td:eq(3) a.view-btn', row)
						.click(function (e) {
							
							e.preventDefault();
							
						})
						.popover({
							content: 'Please wait&hellip;',
							placement: 'left',
							html: true,
							trigger: 'focus'
						})
						.on('hide.bs.popover', function () {
							
							if(window.curr_vio_cat_xhr)
								window.curr_vio_cat_xhr.abort();
							
						})
						.on('shown.bs.popover', function () {

							var that = this;
								
							window.curr_vio_cat_xhr = $.getJSON(
									base_url + 'violations/getCategory/' + $(this).data('id'),
									function (_data) {
									
											delete window.curr_vio_cat_xhr;

											$(that).popover('destroy');
											
											setTimeout(function () {

												$(that)
													.popover({
														title: 'Information',
														content: '\
															<table class="table" width="100%" style="table-layout: fixed;">\
																<tr><td width="40%">ID</td><td width="60%">' + _data.id + '</td></tr>\
																<tr><td width="40%">Title</td><td width="60%" style="word-wrap: break-word;">' + _data.title + '</td></tr>\
																<tr><td width="40%">Status</td><td width="60%">' + (_data.active == 1 ? 'Enabled' : 'Disabled') + '</td></tr>\
																<tr><td width="40%">Created Date</td><td width="60%">' + moment.unix(_data.created_on).format('YYYY-MM-DD HH:mm:ss') + '</td></tr>\
																<tr><td width="40%">Updated Date</td><td width="60%">' + (_data.updated_on ? moment.unix(_data.updated_on).format('YYYY-MM-DD HH:mm:ss') : '--') + '</td></tr>\
																<tr><td width="40%">Created By</td><td width="60%">' + _data.created_by_nick + '</td></tr>\
																<tr><td width="40%">Updated By</td><td width="60%">' + (_data.updated_by_nick ? _data.updated_by_nick : '--') + '</td></tr>\
															</table>',
														placement: 'left',
														html: true,
														trigger: 'focus'
													})
													.popover('show');

											}, 250);
											
										}
								).fail(function () { alert('An error occured. Please try again later.'); });
							
						});
				}
		}),
		vio_cat_table_api = vio_cat_table.api();
	
	$('div#vio-table_wrapper div.row:eq(1) div.col-sm-12, div#vio-cat-table_wrapper div.row:eq(1) div.col-sm-12').css('padding', 0);
	
	$('div#vio-modal')
		.on('hidden.bs.modal', function () {
		
			$('button#vio-save').html('\
				<i class="ace-icon fa fa-check"></i>\
				Save\
			');
			
			$('button', this).removeAttr('disabled');
			
			$('input:text', this)
				.removeAttr('readonly')
				.val('');
			
			$('input:not(#no-dismissal-score):disabled', this)
				.closest('.ace-spinner')
					.spinner('enable');
			
			$('input#no-dismissal-switch').prop('checked', false);
			
			$('input#no-dismissal-score')
				.closest('.ace-spinner')
					.spinner('disable');
			
			$('input.infraction-score').ace_spinner('value', 1);
			
			$('select#infra-years, select#infra-condition, select#vio-category-2').prop('selectedIndex', 0);
			
			$('select#infra-month')
				.prop('selectedIndex', -1)
				.attr('disabled', 'disabled');
			
			$('input:checkbox[name^=vio-rule-switch]')
				.prop('checked', false)
				.change();
			
			$('div.alert-success, div.alert-danger, div.edit', this).addClass('hidden');
			
			$('input.add', this).removeClass('hidden');
			
		})
		.one('show.bs.modal', function () {
			
			$('select#infra-month').prop('selectedIndex', -1);
			
		});
	
	$('div#vio-cat-modal').on('hidden.bs.modal', function () {
		
		$('button#vio-cat-save').html('\
			<i class="ace-icon fa fa-check"></i>\
			Save\
		');
		
		$('button', this).removeAttr('disabled');
		
		$('input', this)
			.removeAttr('readonly')
			.val('');
		
		$('div.alert-success, div.alert-danger, div.edit', this).addClass('hidden');
		
		$('input.add', this).removeClass('hidden');
		
		$('select#vio-category-1 option').removeClass('hidden');
		
	});
	
	$('button#vio-save').click(function () {
	
		var that = this;
		var is_add = $('div#vio-modal div.edit').hasClass('hidden');
		var _desc = $.trim($('div#vio-modal input:visible').val());
		
		// if(!$('input[name^=vio-rule-]:checked').length) {
			
			// alert('At least one rule must be set.');
			
			// return;
		// }
		
		if($('select#vio-category-2').is(':disabled')) {
			
			alert('Please wait until the categories are loaded.');
			
			return;
		}
		
		if(_desc.length) {
		
			$(this).html('\
				<i class="ace-icon fa fa-clock-o"></i>\
				Saving&hellip;\
			');
			
			$('div#vio-modal button').attr('disabled', 'disabled');
			
			$('div#vio-modal input').attr('readonly', 'readonly');
			
			var details = {
					cat_id: $('select#vio-category-2').val()
				};
			
			parse_str($('div#vio-modal form').serialize(), details);
			
			$.post(
				base_url + 'violations/' + (is_add ? 'add' : 'update/1'),
				is_add ? {
					type: $(this).data('type'),
					desc: _desc,
					details: details
				} : {
					id: $(this).data('id'),
					type: $(this).data('type'),
					data: {
							description: _desc,
							status: $.trim($('div#vio-modal div.edit button').text()) == 'Enabled' ? 1 : 0,
							details: details
						}
				},
				function (data) {
						
						if(data.success) {
						
							$('div#vio-modal div.alert-success').removeClass('hidden');
							
							setTimeout(function () {
								
								$('div#vio-modal').modal('hide');
								
								vio_table_api.ajax.reload();
								
							}, 2000);
							
						}
						
						$(that).html('\
							<i class="ace-icon fa fa-check"></i>\
							Save\
						');
						
						$('div#vio-modal button').removeAttr('disabled');
						
						$('div#vio-modal input').removeAttr('readonly');
						
					},
				'json'
			);
		}
		
	});
	
	$('button#vio-cat-save').click(function () {
	
		var that = this;
		var is_add = $('div#vio-cat-modal div.edit').hasClass('hidden');
		var _desc = $.trim($('div#vio-cat-modal input:visible').val());
		
		if(_desc.length) {
		
			$(this).html('\
				<i class="ace-icon fa fa-clock-o"></i>\
				Saving&hellip;\
			');
			
			$('div#vio-cat-modal button').attr('disabled', 'disabled');
			
			$('div#vio-cat-modal input').attr('readonly', 'readonly');
			
			$.post(
				base_url + 'violations/' + (is_add ? 'addCategory' : 'updateCategory'),
				is_add ? {
					title: _desc,
					parent_id: $('select#vio-category-1').val()
				} : {
					id: $(this).data('id'),
					title: _desc,
					parent_id: $('select#vio-category-1').val(),
					active: $.trim($('div#vio-cat-modal div.edit button').text()) == 'Enabled' ? 1 : 0
				},
				function (data) {
						
						if(data.success) {
						
							$('div#vio-cat-modal div.alert-success').removeClass('hidden');
							
							setTimeout(function () {
								
								$('div#vio-cat-modal').modal('hide');
								
								getCategories(function () {
									
									vio_cat_table_api.ajax.reload();
									
								});
								
							}, 2000);
							
						}
						
						$(that).html('\
							<i class="ace-icon fa fa-check"></i>\
							Save\
						');
						
						$('div#vio-cat-modal button').removeAttr('disabled');
						
						$('div#vio-cat-modal input').removeAttr('readonly');
						
					},
				'json'
			);
		}
		
	});
	
	$('div#vio-modal div.edit a').click(function (e) {
		
		e.preventDefault();
		
		$('button', $(this).parents('div.input-group-btn')[0])
			.html('\
				' + $(this).text() + '\
				<span class="ace-icon fa fa-caret-down icon-on-right"></span>\
			')
			.removeClass('btn-success btn-warning')
			.addClass('btn-' + $(this).data('color'));
		
	});
	
	$('div#vio-cat-modal div.edit a').click(function (e) {
		
		e.preventDefault();
		
		$('button', $(this).parents('div.input-group-btn')[0])
			.html('\
				' + $(this).text() + '\
				<span class="ace-icon fa fa-caret-down icon-on-right"></span>\
			')
			.removeClass('btn-success btn-warning')
			.addClass('btn-' + $(this).data('color'));
		
	});
	
	$('input#vio-display-disabled').change(function () {
		
		vio_table_api.ajax.url(base_url + 'violations/getAll/1' + (this.checked ? '/1' : '')).load();
		
	});
	
	$('input#vio-cat-display-disabled').change(function () {
		
		vio_cat_table_api.ajax.url(base_url + 'violations/getCategories' + (this.checked ? '/1' : '')).load();
		
	});
	
	$('input#vio-rules-times')
		.ace_spinner({
			value: 1,
			min: 1,
			btn_up_class: 'btn-light',
			btn_down_class: 'btn-light'
		})
		.change(function () {

			$('span#vio-rules-times-plural').toggleClass('hidden', !(this.value > 1));
			
		})
		.blur(function () {
			
			if(!$.isNumeric(this.value))
				$(this).ace_spinner('value', 1);
			
		})
		.parents('div.ace-spinner')
			.css({
				float: 'right',
				width: '60px'
			});
	
	$('input#vio-rules-minus')
		.ace_spinner({
			value: 0,
			min: 0,
			btn_up_class: 'btn-light',
			btn_down_class: 'btn-light'
		})
		.change(function () {

			$('span#vio-rules-minus-plural').toggleClass('hidden', !(this.value > 1));
			
			$('input#vio-rules-minus2').ace_spinner('value', this.value > 0 ? parseFloat($('input#vio-rules-minus2').val()) || 0.5 : 0);
			
		})
		.blur(function () {

			if(!$.isNumeric(this.value))
				$(this).ace_spinner('value', 1);
			
		})
		.parents('div.ace-spinner')
			.css({
				float: 'right',
				width: '60px'
			});
	
	$('input#vio-rules-minus2')
		.ace_spinner({
			value: 0,
			min: 0,
			step: 0.5,
			btn_up_class: 'btn-light',
			btn_down_class: 'btn-light'
		})
		.change(function () {

			// $('span#vio-rules-minus-plural').toggleClass('hidden', !(this.value > 1));
			
			if(this.value > 0 && !parseInt($('input#vio-rules-minus').val()))
				$('input#vio-rules-minus').ace_spinner('value', 1);
			else if(this.value == 0 && parseInt($('input#vio-rules-minus').val()))
				$('input#vio-rules-minus').ace_spinner('value', 0);
			
		})
		.blur(function () {

			if(!$.isNumeric(this.value))
				$(this).ace_spinner('value', 0.5);
			
		})
		.parents('div.ace-spinner')
			.css({
				float: 'right',
				width: '60px',
				marginLeft: '5px'
			});
	
	$('div#vio-rules-modal')
		.on('show.bs.modal', function () {
	
			$('select#vio-rules-offenses')
				.html('<option selected="selected">Loading&hellip;</option>')
				.attr('disabled', 'disabled');
			
			$('button#vio-rules-save').attr('disabled', 'disabled');
			
			$('div#vio-rules-all-list').html('<p>Loading&hellip;</p>');
			
			$.getJSON(
				base_url + 'rules/get/' + $(this).data('id'),
				function (data) {
					
					if(data.length) {
					
						var list = '<div class="list-group">';
						
						$.each(data, function (i, val) {
						
							var _i = i + 1;
							
							list += '<a href="#" class="list-group-item" data-i="' + _i + '" data-id="' + val.id + '" data-repetition="' + val.repetition + '" data-type="' + val.type + '" data-offense="' + val.offense_id + '" data-minus="' + val.minus + '" data-minus2="' + val.subsequent_minus + '">Rule ' + _i + '</a>';
							
						});
						
						$('div#vio-rules-all-list').html(list + '</div>');
						
					}
					else
						$('div#vio-rules-all-list').html('<p class="alert alert-info">No rules set. Click the button below to create a new rule.</p>');
					
				}
			);
			
			$.getJSON(
				base_url + 'cite/getAll/offense',
				function (data) {
					
					var opts = '';
					
					$.each(data.data, function (i, val) {
						
						opts += '<option value="' + val[0] + '">' + val[1] + '</option>';
						
					});
					
					$('select#vio-rules-offenses')
						.html(opts)
						.removeAttr('disabled');

				}
			);
			
			$('input#vio-rules-times').ace_spinner('value', 1);
			$('input#vio-rules-minus').ace_spinner('value', 0);
			$('input#vio-rules-minus2').ace_spinner('value', 0);
			
			$('select#vio-rules-week-month')[0].selectedIndex = 0;
			
		})
		.on('hidden.bs.modal', function () {
		
			$('div.alert-success, div.alert-danger', this).addClass('hidden');
			
			$('ul#vio-rules-tab li:eq(1)').addClass('hidden');
		
			$('ul#vio-rules-tab a:eq(0)').tab('show');
			
		});
	
	$('a#vio-rules-all-add').click(function (e) {
		
		e.preventDefault();
		
		$('button#vio-rules-save').data('mode', 'add');
		
		$('ul#vio-rules-tab li:eq(1)').removeClass('hidden');
		
		var rules_count = $('div#vio-rules-all-list a.list-group-item').length;
		
		$('ul#vio-rules-tab a:eq(1)')
			.text('Rule ' + (rules_count ? rules_count + 1 : 1))
			.tab('show');
		
	});
	
	$('ul#vio-rules-tab a:eq(0)').on('show.bs.tab', function () {
		
		$('button#vio-rules-save').attr('disabled', 'disabled');
		
	});
	
	$('ul#vio-rules-tab a:eq(1)').on('show.bs.tab', function () {
		
		$('button#vio-rules-save').removeAttr('disabled');

		$('tr#vio-rules-remove').toggleClass('hidden', $('button#vio-rules-save').data('mode') == 'add');
		
	});
	
	$('button#vio-rules-save').click(function () {
	
		var that = this;
		
		var _d = $(this).data();
	
		$('div#vio-rules-modal input, div#vio-rules-modal select, div#vio-rules-modal button').attr('disabled', 'disabled');
		
		$(this).html('\
			<i class="ace-icon fa fa-check"></i>\
			Saving&hellip;\
		');
		
		$.post(
			base_url + 'rules/' + _d.mode,
			{
				id: _d.id,
				v: parseInt($('button#vio-rules-save').data('vid')),
				r: parseInt($.trim($('input#vio-rules-times').val())),
				t: $('select#vio-rules-week-month').val(),
				o: $('select#vio-rules-offenses').val(),
				m1: parseInt($.trim($('input#vio-rules-minus').val())),
				m2: parseFloat($.trim($('input#vio-rules-minus2').val()))
			},
			function (data) {
			
				if(data.success) {
				
					$('div#vio-rules-modal div.alert-success').removeClass('hidden');
					
					setTimeout(function () {

						$('div#vio-rules-modal').modal('hide');
						
						vio_table_api.ajax.reload();

					}, 2000);
					
				}
				
				$('div#vio-rules-modal input, div#vio-rules-modal select, div#vio-rules-modal button').removeAttr('disabled');
		
				$(that).html('\
					<i class="ace-icon fa fa-check"></i>\
					Save\
				');

			},
			'json'
		);
		
	});
	
	$('div#vio-rules-all-list').on('click', 'a', function (e) {
		
		e.preventDefault();
		
		var _d = $(this).data();
		
		$('ul#vio-rules-tab li:eq(1)').removeClass('hidden');
		
		$('button#vio-rules-save').data({
			mode: 'update',
			id: _d.id
		});
		
		$('ul#vio-rules-tab a:eq(1)')
			.text('Rule ' + _d.i)
			.tab('show');
		
		$('input#vio-rules-times').ace_spinner('value', _d.repetition);
		
		$('select#vio-rules-week-month option[value=' + _d.type + ']').prop('selected', true);

		$('select#vio-rules-offenses option[value=' + _d.offense + ']').prop('selected', true);
		
		$('input#vio-rules-minus').ace_spinner('value', _d.minus);
		
		$('input#vio-rules-minus2').ace_spinner('value', _d.minus2);
		
	});
	
	$('tr#vio-rules-remove a').click(function (e) {
		
		e.preventDefault();
		
		if(confirm('Are you sure you want to remove this rule?')) {
		
			var remove_timeout = setTimeout(function () {
				
					$('div#vio-rules-modal div.modal-dialog').block({
						message: 'Please wait&hellip;'
					});
					
				}, 1000);
			
			$.post(
				base_url + 'rules/remove',
				{
					id: $('button#vio-rules-save').data('id')
				},
				function (data) {
				
					clearTimeout(remove_timeout);
					
					if(data.success) {
						
						$('ul#vio-rules-tab li:eq(1)').addClass('hidden');
		
						$('ul#vio-rules-tab a:eq(0)').tab('show');
						
						$('div#vio-rules-all-list').html('<p>Loading&hellip;</p>');
			
						$.getJSON(
							base_url + 'rules/get/' + $('div#vio-rules-modal').data('id'),
							function (data) {
								
								if(data.length) {
								
									var list = '<div class="list-group">';
									
									$.each(data, function (i, val) {
									
										var _i = i + 1;
										
										list += '<a href="#" class="list-group-item" data-i="' + _i + '" data-id="' + val.id + '" data-repetition="' + val.repetition + '" data-type="' + val.type + '" data-offense="' + val.offense_id + '" data-minus="' + val.minus + '" data-minus2="' + val.subsequent_minus + '">Rule ' + _i + '</a>';
										
									});
									
									$('div#vio-rules-all-list').html(list + '</div>');
									
								}
								else {
								
									$('div#vio-rules-all-list').html('<p class="alert alert-info">No rules set. Click the button below to create a new rule.</p>');
									
									vio_table_api.ajax.reload();
									
								}
								
							}
						);
						
					}
					
				},
				'json'
			)
			
		}
		
	});
	
	vio_table
		.on('search.dt', function () {
			
			$('tbody td:not(.no-highlight)', this)
				.removeHighlight()
				.highlight(vio_table_api.search());
			
		})
		.on('page.dt', function () {
			
			var that = this;
			
			if(!$.trim(vio_table_api.search()).length)
				setTimeout(function () {
					
					$('tbody td:not(.no-highlight)', that).removeHighlight();
					
				}, 1);
			
		});
	
	vio_cat_table
		.on('search.dt', function () {
			
			$('tbody td:not(.no-highlight)', this)
				.removeHighlight()
				.highlight(vio_cat_table_api.search());
			
		})
		.on('page.dt', function () {
			
			var that = this;
			
			if(!$.trim(vio_cat_table_api.search()).length)
				setTimeout(function () {
					
					$('tbody td:not(.no-highlight)', that).removeHighlight();
					
				}, 1);
			
		});
	
	$('label.dismissed').tooltip();
	
	$('input#infraction-1')
		.ace_spinner({
			value: 1,
			min: 1,
			max: 20,
			step: .5,
			btn_up_class: 'btn-info',
			btn_down_class:'btn-info'
		})
		.closest('.ace-spinner')
			.width(60);
	
	$('input#infraction-2')
		.ace_spinner({
			value: 1,
			min: 1,
			max: 20,
			step: .5,
			btn_up_class: 'btn-info',
			btn_down_class:'btn-info'
		})
		.closest('.ace-spinner')
			.width(60);
	
	$('input#infraction-3')
		.ace_spinner({
			value: 1,
			min: 1,
			max: 20,
			step: .5,
			btn_up_class: 'btn-info',
			btn_down_class:'btn-info'
		})
		.closest('.ace-spinner')
			.width(60);
	
	$('input#infraction-4')
		.ace_spinner({
			value: 1,
			min: 1,
			max: 20,
			step: .5,
			btn_up_class: 'btn-info',
			btn_down_class:'btn-info'
		})
		.closest('.ace-spinner')
			.width(60);
	
	$('input#infraction-5')
		.ace_spinner({
			value: 1,
			min: 1,
			max: 20,
			step: .5,
			btn_up_class: 'btn-info',
			btn_down_class:'btn-info'
		})
		.closest('.ace-spinner')
			.width(60);
	
	$('input#infraction-6')
		.ace_spinner({
			value: 1,
			min: 1,
			max: 20,
			step: .5,
			btn_up_class: 'btn-info',
			btn_down_class:'btn-info'
		})
		.closest('.ace-spinner')
			.width(60);
	
	$('input#no-dismissal-score')
		.ace_spinner({
			value: 1,
			min: 1,
			max: 20,
			step: .5,
			btn_up_class: 'btn-info',
			btn_down_class:'btn-info'
		})
		.closest('.ace-spinner')
			.spinner('disable')
			.width(60)
			.addClass('middle')
			.css('margin-left', '2%');
	
	$('input.infraction-score').blur(function () {

		$(this).ace_spinner('value', Math.min(parseInt($(this).val()), $(this).closest('.ace-spinner').data('spinner').options.max) || 1);
		
	});
	
	$('input#no-dismissal-switch').change(function () {
			
		if(this.checked) {
		
			$('label.dismissed input:checkbox')
				.attr('disabled', 'disabled')
				.prop('checked', false);
			
			$('input.infraction-score:not(#no-dismissal-score)').closest('.ace-spinner').spinner('disable');
			
			$('input#no-dismissal-score').closest('.ace-spinner').spinner('enable');
			
			$('label.per-incident').removeClass('text-muted');
		}
		else {
		
			$('label.dismissed input:checkbox').removeAttr('disabled');
			
			$('input.infraction-score:not(#no-dismissal-score)').closest('.ace-spinner').spinner('enable');
			
			$('input#no-dismissal-score').closest('.ace-spinner').spinner('disable');
			
			$('label.per-incident').addClass('text-muted');
		}
	});
	
	$('label.dismissed input:checkbox').change(function () {
	
		var index = $(this).parents('tr').find('.ace-spinner').index($(this).parent().nextAll('.ace-spinner'));

		if(this.checked) {
		
			$(this).parents('tr').find('input:checkbox:gt(' + index + ')')
				.attr('disabled', 'disabled')
				.prop('checked', false);
		
			if(index)
				$(this).parents('tr').find('.ace-spinner:gt(' + (index - 1) + ')').spinner('disable');
			else
				$(this).parents('tr').find('.ace-spinner').spinner('disable');
		}
		else {
		
			$(this).parents('tr').find('input:checkbox:gt(' + index + ')').removeAttr('disabled');
		
			if(index)
				$(this).parents('tr').find('.ace-spinner:gt(' + (index - 1) + ')').spinner('enable');
			else
				$(this).parents('tr').find('.ace-spinner').spinner('enable');
		}
	});
	
	$('select#infra-condition').change(function () {
		
		if(this.value == 2)
			$('select#infra-month')
				.prop('selectedIndex', 0)
				.removeAttr('disabled');
		else
			$('select#infra-month')
				.prop('selectedIndex', -1)
				.attr('disabled', 'disabled');
		
	});
	
	$('div#vio-modal').on('show.bs.modal', function () {
	
		$('select[name^=vio-rules-offenses]')
			.html('<option selected="selected">Loading&hellip;</option>')
			.attr('disabled', 'disabled');
		
		$('button#vio-rules-save').attr('disabled', 'disabled');
		
		// $.getJSON(
			// base_url + 'rules/get/' + $(this).data('id'),
			// function (data) {
				
				// if(data.length) {
				
					// var list = '<div class="list-group">';
					
					// $.each(data, function (i, val) {
					
						// var _i = i + 1;
						
						// list += '<a href="#" class="list-group-item" data-i="' + _i + '" data-id="' + val.id + '" data-repetition="' + val.repetition + '" data-type="' + val.type + '" data-offense="' + val.offense_id + '" data-minus="' + val.minus + '" data-minus2="' + val.subsequent_minus + '">Rule ' + _i + '</a>';
						
					// });
					
					// $('div#vio-rules-all-list').html(list + '</div>');
					
				// }
				// else
					// $('div#vio-rules-all-list').html('<p class="alert alert-info">No rules set. Click the button below to create a new rule.</p>');
				
			// }
		// );

		$.getJSON(
			base_url + 'cite/getAll/offense',
			function (data) {
				
				var opts = '',
					cite = $('button#vio-save').data('cite');
				
				$.each(data.data, function (i, val) {
					
					opts += '<option value="' + val[0] + '">' + val[1] + '</option>';
					
				});
				
				$('select[name^=vio-rules-offenses]')
					.html(opts)
					.each(function () {

						if($('input:checkbox', $(this).parents('tr')).is(':checked'))
							$(this).removeAttr('disabled');
						else {
						
							$(this).prop('selectedIndex', -1);
						
							$('td:eq(1)', $(this).parents('tr')).addClass('text-muted');
						}
						
					});
				
				$('input[name^=vio-rule-switch]')
					.prop('checked', false)
					.change();
				
				$.each(cite, function (i, val) {
					
					$('input[name^=vio-rule-switch][value=' + i + ']')
						.prop('checked', true)
						.change()
						.parents('tr')
							.find('select')
								.prop('selectedIndex', function () {
									
									return $('option', this).index($('option[value="' + val + '"]', this));
									
								});
					
				});
			}
		);
			
	});
	
	$('input[name^=vio-rule-switch]').change(function () {
	
		var p = $(this).parents('tr');
	
		$('td:eq(1)', p).toggleClass('text-muted', !this.checked);
		
		if(this.checked)
			$('select', p)
				.removeAttr('disabled')
				.prop('selectedIndex', 0);
		else
			$('select', p)
				.attr('disabled', 'disabled')
				.prop('selectedIndex', -1);
		
	});
	
	$('button.vio-add-btn').click(function () {
		
		$('button#vio-save').data('cite', null);
		
	});
});