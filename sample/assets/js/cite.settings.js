$(function () {

	var off_table = $('table#off-table').dataTable({
			ajax: base_url + 'cite/getAll/offense' + ($('input#off-display-disabled').prop('checked') ? '/1' : ''),
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
						className: 'center',
					},
					{
						data: 1,
						className: 'ellipsis'
					},
					{
						data: 2,
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
						data: 3,
						visible: false,
						searchable: false
					}
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
								base_url + 'cite/update',
								{
									type: 'offense',
									id: _id,
									data: {
											status: data[2] == 1 ? 0 : 2
										}
								},
								function (_data) {
									
									if(_data.success) {
									
										if(data[2] == 1) {
										
											data[2] = 0;
										
											if($('input#off-display-disabled').prop('checked'))
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
						
						window.curr_off_row = row;
						
						$('div#off-pen-modal h4').text('Edit offense');
						
						$('div#off-pen-modal div.edit input').val(data[1]);
						
						$('div#off-pen-modal div.edit button')
							.html('\
								' + (data[2] == '1' ? 'Enabled' : 'Disabled') + '\
								<span class="ace-icon fa fa-caret-down icon-on-right"></span>\
							')
							.removeClass('btn-success btn-warning')
							.addClass('btn-' + (data[2] == '1' ? 'success' : 'warning'));
		
						$('button#off-pen-save').data({
							type: 'offense',
							id: $(this).data('id')
						});
						
						var off_type = 'Minor';
						
						switch(data[2]) {
							case '1':
							
								off_type = 'Minor';
							
								break;
							case '2':
							
								off_type = 'Major';
							
								break;
							case '3':
							
								off_type = 'Zero tolerance';
							
								break;
						}

						$('button#off-pen-type').html('\
							' + off_type + '\
							<span class="ace-icon fa fa-caret-down icon-on-right"></span>\
						');
						
						$('div#off-pen-modal input.add').addClass('hidden');
						
						$('div#off-pen-modal div.edit').removeClass('hidden');
						
						$('div#off-pen-modal').modal({
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
							
							if(window.curr_off_xhr)
								window.curr_off_xhr.abort();
							
						})
						.on('shown.bs.popover', function () {
							
							var that = this;
								
							window.curr_off_xhr = $.getJSON(
									base_url + 'cite/get/offense/' + $(this).data('id'),
									function (_data) {
									
											delete window.curr_off_xhr;
											
											var _type = 'Minor';
											
											switch(_data.type) {
												case '1':
												
													_type = 'Minor';
												
													break;
												case '2':
												
													_type = 'Major';
												
													break;
												case '3':
												
													_type = 'Zero tolerance';
												
													break;
											}

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
				}
		}),
		off_table_api = off_table.api();
	
	var pen_table = $('table#pen-table').dataTable({
			ajax: base_url + 'cite/getAll/penalty' + ($('input#pen-display-disabled').prop('checked') ? '/1' : ''),
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
						className: 'center',
					},
					{
						data: 1,
						className: 'ellipsis'
					},
					{
						data: 2,
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
						data: 3,
						visible: false,
						searchable: false
					}
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
								base_url + 'cite/update',
								{
									type: 'offense',
									id: _id,
									data: {
											status: data[2] == 1 ? 0 : 2
										}
								},
								function (_data) {
									
									if(_data.success) {
									
										if(data[2] == 1) {
										
											data[2] = 0;
										
											if($('input#off-display-disabled').prop('checked'))
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
						
						window.curr_off_row = row;
						
						$('div#off-pen-modal h4').text('Edit penalty');
						
						$('div#off-pen-modal div.edit input').val(data[1]);
						
						$('div#off-pen-modal div.edit button')
							.html('\
								' + (data[2] == '1' ? 'Enabled' : 'Disabled') + '\
								<span class="ace-icon fa fa-caret-down icon-on-right"></span>\
							')
							.removeClass('btn-success btn-warning')
							.addClass('btn-' + (data[2] == '1' ? 'success' : 'warning'));
		
						$('button#off-pen-save').data({
							type: 'penalty',
							id: $(this).data('id')
						});
						
						$('div#off-pen-modal input.add').addClass('hidden');
						
						$('div#off-pen-modal div.edit').removeClass('hidden');
						
						$('div#off-pen-modal').modal({
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
							
							if(window.curr_pen_xhr)
								window.curr_pen_xhr.abort();
							
						})
						.on('shown.bs.popover', function () {
							
							var that = this;
								
							window.curr_pen_xhr = $.getJSON(
									base_url + 'cite/get/penalty/' + $(this).data('id'),
									function (_data) {
									
											delete window.curr_pen_xhr;

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
				}
		}),
		pen_table_api = pen_table.api();
	
	$('button.off-pen-add-btn').click(function () {
		
		$('div#off-pen-modal h4').text('Add new ' + $(this).data('type'));
		
		$('button#off-pen-save').data('type', $(this).data('type'));
		
	});
	
	$('div#off-pen-modal')
		.on('hidden.bs.modal', function () {
			
			$('button#off-pen-save').html('\
				<i class="ace-icon fa fa-check"></i>\
				Save\
			');
			
			$('div#off-pen-modal button').removeAttr('disabled');
			
			$('div#off-pen-modal input').removeAttr('readonly');
			
			$('input', this).val('');
			
			$('div.alert-success, div.alert-danger, div.edit', this).addClass('hidden');
			
			$('input.add').removeClass('hidden');
			
		})
		.on('show.bs.modal', function () {
			
			$('div.off-type').toggle($('button#off-pen-save').data('type') == 'offense');
			
		});
	
	$('button#off-pen-save').click(function () {
	
		var that = this;
		var is_add = $('div#off-pen-modal div.edit').hasClass('hidden');
		var _desc = $.trim($('div#off-pen-modal input:visible').val());
		
		if(_desc.length) {
		
			$(this).html('\
				<i class="ace-icon fa fa-clock-o"></i>\
				Saving&hellip;\
			');
			
			$('div#off-pen-modal button').attr('disabled', 'disabled');
			
			$('div#off-pen-modal input').attr('readonly', 'readonly');
			
			$.post(
				base_url + 'cite/' + (is_add ? 'add' : 'update'),
				is_add ? {
					type: $(this).data('type'),
					desc: _desc,
					type2: $('button#off-pen-type').data('id')
				} : {
					id: $(this).data('id'),
					type: $(this).data('type'),
					data: {
							description: _desc,
							type: $('button#off-pen-type').data('id'),
							status: $.trim($('div#off-pen-modal div.edit button').text()) == 'Enabled' ? 1 : 0
						}
				},
				function (data) {
						
						if(data.success) {
						
							$('div#off-pen-modal div.alert-success').removeClass('hidden');
							
							setTimeout(function () {
								
								$('div#off-pen-modal').modal('hide');
								
								if($(that).data('type') == 'offense')
									off_table_api.ajax.reload();
								else
									pen_table_api.ajax.reload();
								
							}, 2000);
							
						}
						
						$(that).html('\
							<i class="ace-icon fa fa-check"></i>\
							Save\
						');
						
						$('div#off-pen-modal button').removeAttr('disabled');
						
						$('div#off-pen-modal input').removeAttr('readonly');
						
					},
				'json'
			);
		}
		
	});
	
	$('div#off-pen-modal div.edit a').click(function (e) {
		
		e.preventDefault();
		
		$('button', $(this).parents('div.input-group-btn')[0])
			.html('\
				' + $(this).text() + '\
				<span class="ace-icon fa fa-caret-down icon-on-right"></span>\
			')
			.removeClass('btn-success btn-warning')
			.addClass('btn-' + $(this).data('color'));
		
	});
	
	$('input#off-display-disabled').change(function () {
		
		off_table_api.ajax.url(base_url + 'cite/getAll/offense' + (this.checked ? '/1' : '')).load();
		
	});
	
	$('input#pen-display-disabled').change(function () {
		
		pen_table_api.ajax.url(base_url + 'cite/getAll/penalty' + (this.checked ? '/1' : '')).load();
		
	});
	
	off_table
		.on('search.dt', function () {
		
			$('tbody td:not(.no-highlight)', this)
				.removeHighlight()
				.highlight(off_table_api.search());
			
		})
		.on('page.dt', function () {
			
			var that = this;
			
			if(!$.trim(off_table_api.search()).length)
				setTimeout(function () {
					
					$('tbody td:not(.no-highlight)', that).removeHighlight();
					
				}, 1);
			
		});
	
	pen_table
		.on('search.dt', function () {
		
			$('tbody td:not(.no-highlight)', this)
				.removeHighlight()
				.highlight(pen_table_api.search());
			
		})
		.on('page.dt', function () {
			
			var that = this;
			
			if(!$.trim(pen_table_api.search()).length)
				setTimeout(function () {

					$('tbody td:not(.no-highlight)', that).removeHighlight();
					
				}, 1);
			
		});
	
	$('a.off-type-select').click(function (e) {
		
		e.preventDefault();
		
		$('button#off-pen-type')
			.html('\
				' + $(this).text() + '\
				<span class="ace-icon fa fa-caret-down icon-on-right"></span>\
			')
			.data('id', $(this).data('id'));
	
	});
});