function showNewCites() {
	
	if(History.getState().url == base_url + 'cite')
		$('table#rec-table').DataTable().ajax.url(base_url + 'cite/getAllByID/' + argument[0] + '/cite/1/1').load(function () {
			
			$('button#cite-filter-btn')
				.removeClass('btn-primary btn-yellow btn-info btn-success')
				.addClass('btn-yellow')
				.html('\
					<i class="ace-icon fa fa-filter"></i> For explanation\
					<i class="ace-icon fa fa-chevron-down icon-on-right"></i>\
				');
			
			$('ul#cite-filter-status li').removeClass('active');
			
			$('ul#cite-filter-status li:eq(1)').addClass('active');
			
		});
	else
		History.pushState({ from: 'main-nav', for_explanation: true, _: moment().unix() }, document.title, base_url + 'cite/' + arguments[0]);
}

$(function () {

	var _id = $('table#rec-table').data('id'),
		display_pending = !!History.getState().data.pending,
		display_for_explanation = !!History.getState().data.for_explanation;
	
	if(display_pending) {
		
		$('button#cite-filter-btn')
			.removeClass('btn-primary btn-yellow btn-info btn-success')
			.addClass('btn-danger')
			.html('\
				<i class="ace-icon fa fa-filter"></i> Pending\
				<i class="ace-icon fa fa-chevron-down icon-on-right"></i>\
			');
		
		$('ul#cite-filter-status li').removeClass('active');
		
		$('ul#cite-filter-status li:eq(0)').addClass('active');
		
	}
	else if(display_for_explanation) {
		
		$('button#cite-filter-btn')
			.removeClass('btn-primary btn-yellow btn-info btn-success')
			.addClass('btn-yellow')
			.html('\
				<i class="ace-icon fa fa-filter"></i> For explanation\
				<i class="ace-icon fa fa-chevron-down icon-on-right"></i>\
			');
		
		$('ul#cite-filter-status li').removeClass('active');
		
		$('ul#cite-filter-status li:eq(1)').addClass('active');
	}
	
	var cite_rec_table = $('table#rec-table').dataTable({
			ajax: base_url + 'cite/' +  (_id ? 'getAllByID/' + _id : 'getAll') + '/cite/1' + (display_pending ? '/0' : (display_for_explanation ? '/1' : '')),
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
						data: 0
					},
					{
						data: 12
					},
					{
						data: 13
					},
					{
						data: 14
					},
					{
						data: 15,
						render: function (d) {
								
								return '<div class="ellipsis">' + d + '</div>';
								
							}
					},
					{
						data: 2,
						render: function (d) {
								
								return d ? d : '--';
							}
					},
					{
						data: 16
					},
					{
						data: 17
					},
					{
						data: 18
					},
					{
						data: 19,
						render: function (d) {
								
								return '<div class="ellipsis">' + d + '</div>';
								
							}
					},
					{
						data: 4,
						render: function (d) {
								
								return '<div class="ellipsis">' + (d ? moment(d, 'YYYY-MM-DD HH:mm:ss').format('MM-DD-YY HH:mm') : '--') + '</div>';
								
							}
					},
					{
						data: 20,
						render: function (d) {
								
								return '<div class="ellipsis">' + (d ? d : '--') + '</div>';
								
							}
					},
					{
						data: 6,
						searchable: false,
						render: function (d) {
						
								var status = '<span class="label label-danger">Pending</span>';
								
								switch(parseInt(d)) {
									case 0:
									
										status = '<span class="label label-danger">Pending</span>';
									
										break;
									case 1:
									
										status = '<span class="label label-yellow">For explanation</span>';
									
										break;
									case 2:
									
										status = '<span class="label label-info">For investigation</span>';
									
										break;
									case 3:
									
										status = '<span class="label label-success">Closed</span>';
									
										break;
									case 4:
									
										status = '<span class="label label">Cancelled</span>';
									
										break;
								}
								
								return status;
								
							},
						className: 'center no-highlight'
					},
					{
						data: 0,
						orderable: false,
						searchable: false,
						render: function (d) {

								return '\
										<div class="hidden-sm hidden-xs action-buttons">\
											<a class="blue view-btn" href="#" data-id="' + d + '">\
												<i class="ace-icon fa fa-search-plus bigger-130"></i>\
											</a>\
											<a class="green edit-btn" href="#" data-id="' + d + '">\
												<i class="ace-icon fa fa-pencil bigger-130"></i>\
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
												</ul>\
											</div>\
										</div>\
									';
								
							},
						className: 'center no-highlight'
					}
				],
			order: [[1, 'desc']],
			initComplete: function () {
					
					$('thead span', this).tooltip();
					
				},
			rowCallback: function (row, data) {

					setTimeout(function () {
						
						if($('td:eq(5) div', row)[0].offsetWidth < $('td:eq(5) div', row)[0].scrollWidth)
							$('td:eq(5) div', row)
								.attr('title', $('td:eq(5) div', row).text())
								.tooltip();
						
						if($('td:eq(10) div', row)[0].offsetWidth < $('td:eq(10) div', row)[0].scrollWidth)
							$('td:eq(10) div', row)
								.attr('title', $('td:eq(10) div', row).text())
								.tooltip();
						
						if($('td:eq(13) div', row)[0].offsetWidth < $('td:eq(13) div', row)[0].scrollWidth)
							$('td:eq(13) div', row)
								.attr('title', $('td:eq(13) div', row).text())
								.tooltip();
						
					}, 1);
					
					$('a.edit-btn', row).click(function (e) {
						
						e.preventDefault();
						
						$('span#cite-modal-name').text(data[13] + ' ' + data[12]);
						$('span#cite-modal-offense').text(data[19]);
						
//						$('mark#cite-similar-week').text(data[22] || '0');
//						$('mark#cite-similar-month').text(data[23] || '0');
						
						$('input[name=cite-status]')
							.prop('checked', false)
							.removeAttr('disabled');

						var _sel = $('input[name=cite-status][value=' + data[6] + ']');
						
						_sel.prop('checked', true);
						
						switch(data[6]) {
							case '0':

								$('input[name=cite-status][value=2], input[name=cite-status][value=3]').attr('disabled', 'disabled');
							
								break;
							case '1':

								$('input[name=cite-status][value=0], input[name=cite-status][value=2], input[name=cite-status][value=3]').attr('disabled', 'disabled');
							
								break;
							case '2':

								$('input[name=cite-status][value=0], input[name=cite-status][value=1]').attr('disabled', 'disabled');
							
								break;
							default:
								
								$('input[name=cite-status]')
									.not(_sel)
										.attr('disabled', 'disabled');
						}
						
						$('input#cite-code').val(data[2]);
						
						//$('input#cite-doc').val(moment(data[4], 'YYYY-MM-DD HH:mm:ss').isValid() ? moment(data[4], 'YYYY-MM-DD HH:mm:ss').format('MM-DD-YYYY') : '');
						
						var nte_date = moment(data[4], 'YYYY-MM-DD HH:mm:ss');

						if(nte_date.isValid())
							$('input#cite-nte').data('DateTimePicker').date(nte_date);
						
						$('select#cite-penalty option').removeAttr('selected');
						
						var selected_penalty = parseInt(data[5]);

						$('select#cite-penalty').data('selected', selected_penalty ? selected_penalty : 0);
						
						$('input#cite-remarks').val(data[7]);
						
						$('button#cite-save').data('id', $(this).data('id'));
						
						$('div#cite-modal').modal('show');
						
					});
					
					$('a.btn-view2', row)
						.click(function (e) {
							
							e.preventDefault();
							
							e.stopImmediatePropagation();
							
						})
						.popover({
							content: 'Please wait&hellip;',
							placement: 'left',
							html: true,
							trigger: 'focus'
						})
						.on('hide.bs.popover', function () {
							
							if(window.curr_cite_xhr)
								window.curr_cite_xhr.abort();
							
						})
						.on('shown.bs.popover', function () {
							
							var that = this;
								
							window.curr_cite_xhr = $.getJSON(
									base_url + 'employees/get/' + data[5],
									function (_data) {
									
											delete window.curr_emp_xhr;
											
											var _kpi = moment().format('YYYY') in _data.kpi && moment().format('M') in _data.kpi[moment().format('YYYY')] ? _data.kpi[moment().format('YYYY')][moment().format('M')] : base_hr_score;
											
											var _badge = 'danger';
											
											if( _kpi <= base_hr_score && _kpi > (base_hr_score / 2))
												_badge = 'success';
											
											if(_kpi <= (base_hr_score / 2) && _kpi > (base_hr_score / 4))
												_badge = 'warning';
	
											$(that).popover('destroy');
											
											setTimeout(function () {
												
												$(that)
													.popover({
														title: 'Information',
														content: '\
															<table class="table" width="100%" style="table-layout: fixed;">\
																<tr><td width="40%">ID</td><td width="60%">' + _data.mb_id + '</td></tr>\
																<tr><td width="40%">Name</td><td width="60%" style="word-wrap: break-word;">' + _data.mb_name + '</td></tr>\
																<tr><td width="40%">Job Title</td><td width="60%">' + _data.mb_2 + '</td></tr>\
																<tr><td width="40%">Created Date</td><td width="60%">' + _data.mb_datetime + '</td></tr>\
																<tr><td width="40%">KPI</td><td width="60%"><span class="badge badge-' + _badge + '">' + _kpi + '</span></td></tr>\
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
		cite_rec_table_api = cite_rec_table.api();
	
	$('input.date-picker')
		.datepicker({
			autoclose: true,
			todayHighlight: true,
			endDate: '+0d'
		})
		.next()
			.on('click', function () {
			
				$(this).prev().focus();
				
			});
	
	$('input#cite-nte')
		.on('dp.change', function (e) {
			
			if($('input[name=cite-status]:checked').val() == 0)
				$('input[name=cite-status][value=1]').prop('checked', true);

			$('input#cite-nte-deadline').val(e.date.add($('input#cite-nte-deadline').data('days'), 'd').format('MM-DD-YYYY HH:mm'));
		})
		.datetimepicker({
			minDate: moment()
		})
		.next()
			.on('click', function () {
			
				$(this).prev().focus();
				
			});
	
	$('div#cite-modal').on('show.bs.modal', function (e) {
		
		if(e.target == this) {
		
			$('select#cite-penalty')
				.empty()
				.attr('disabled', 'disabled')
				.append('<option selected="selected">Loading&hellip;</option>');
			
			$('button#cite-save').attr('disabled', 'disabled');
			
			$.getJSON(
				base_url + 'cite/getAll/penalty',
				function (data) {
				
						var opts = new Array();
						
						$.each(data.data, function (i, val) {
							
							opts[opts.length] = '<option value="' + val[0] + '">' + val[1] + '</option>';
							
						});
						
						$('select#cite-penalty')
							.html(opts)
							.removeAttr('disabled');

						if($('select#cite-penalty').data('selected'))
							$('select#cite-penalty')[0].selectedIndex = $('select#cite-penalty option').index($('select#cite-penalty option[value=' +$('select#cite-penalty').data('selected') + ']')[0]);
						
						var selected_type = 0,
							penalty_str = $.trim($('select#cite-penalty option:selected').text()).toLowerCase();
						
						if(penalty_str.indexOf('suspension') != -1)
							selected_type = 1;
						else if(penalty_str.indexOf('dismissal') != -1)
							selected_type = 2;
						else
							selected_type = 0;
						
						$('select#cite-type')[0].selectedIndex = selected_type;
						
						var _days = 2;
						
						switch(selected_type) {
							case 0:
							
								_days = 2;
							
								break;
							case 1:
							
								_days = 3;
							
								break;
							case 2:
							
								_days = 5;
							
								break;
						}
						
						var nte_date = moment($.trim($('input#cite-nte').val()), 'MM-DD-YYYY HH:mm');
						
						$('input#cite-nte-deadline')
							.data('days', _days)
							.val(nte_date.isValid() ? nte_date.add(_days, 'd').format('MM-DD-YYYY HH:mm') : '');
						
						$('button#cite-save').removeAttr('disabled');
					}
			);
		}
		
	});
	
	$('div#cite-modal').on('show.bs.modal', function () {
	
		$('div.alert-success, div.alert-danger', this).addClass('hidden');
		
		$('input:text, select:not(#cite-type), button', this).removeAttr('disabled');
		
		$('button#cite-save').html('\
			<i class="ace-icon fa fa-check"></i>\
			' + ($('textarea#explanation').length ? 'Submit' : 'Save') + '\
		');
		
	});
	
	$('button#cite-save').click(function () {
		
		if($('textarea#explanation').length && !confirm('This will be subject to further investigation.\n\nPlease review your explanation carefully before clicking OK.'))
			return false;
	
		var form = $('div#cite-modal')[0]
			that = this,
			__date_doc = $('input#cite-doc').datepicker('getDate'),
			_date_doc = moment(__date_doc),
			_date_nte = $('input#cite-nte').data('DateTimePicker').date();
		
		$(this).html('Please wait&hellip;');

		$('input, select, button', form).attr('disabled', 'disabled');
		
		$.post(
			base_url + 'cite/update',
			{
				type: 'cite',
				id: $('button#cite-save').data('id'),
				data: {
						status: parseInt($('input[name=cite-status]:checked').val()),
						cite_code: $.trim($('input#cite-code').val()),
						//commission_date: _date_doc.isValid() ? _date_doc.format('YYYY-MM-DD HH:mm:ss') : '',
						nte_date: _date_nte.isValid() ? _date_nte.format('YYYY-MM-DD HH:mm:ss') : '',
						penalty_id: parseInt($('select#cite-penalty').val()),
						remarks: $.trim($('input#cite-remarks').val())
					}
			},
			function (data) {
				
				if(data.success) {
				
					$('div.alert-success', form).removeClass('hidden');
					
					cite_rec_table_api.ajax.reload();
					
					updatePendingCount(data.count);
					
					setTimeout(function () {
						
						$('div#cite-modal').modal('hide');
						
					}, 2000);
					
				}
				else {
				
					$('div.alert-danger', form).removeClass('hidden');
					
					$('input, select:not(#cite-type), button', form).removeAttr('disabled');
		
					$(that).html('\
						<i class="ace-icon fa fa-check"></i>\
						' + ($('textarea#explanation').length ? 'Submit' : 'Save') + '\
					');
				}
				
			},
			'json'
		);
		
	});
	
	$('ul#cite-filter-status a').click(function (e) {
		
		e.preventDefault();
		
		var that = this;
		
		cite_rec_table_api.ajax.url(base_url + 'cite/getAll/cite/1' + ($(this).data('id') == undefined ? '' : '/' + $(this).data('id'))).load(function () {
		
			var c = '';
			
			switch($(that).data('id')) {
				case 0:
				
					c = 'danger';
				
					break;
				case 1:
				
					c = 'yellow';
				
					break;
				case 2:
				
					c = 'info';
				
					break;
				case 3:
				
					c = 'success';
				
					break;
				case 4:
				
					c = '';
				
					break;
				default:
				
					c = 'primary'
			}
			
			$('button#cite-filter-btn')
				.removeClass('btn-primary btn-danger btn-yellow btn-info btn-success')
				.html('\
					<i class="ace-icon fa fa-filter"></i> ' + $(that).text() + '\
					<i class="ace-icon fa fa-chevron-down icon-on-right"></i>\
				');
			
			if(c.length)
				$('button#cite-filter-btn').addClass('btn-' + c);
			
			$('ul#cite-filter-status li').removeClass('active');
			
			$(that).parent().addClass('active');
			
		});
		
	});
	
	cite_rec_table
		.on('search.dt', function () {
			
			$('tbody td:not(.no-highlight)', this)
				.removeHighlight()
				.highlight(cite_rec_table_api.search());
			
		})
		.on('page.dt', function () {
			
			var that = this;
			
			if(!$.trim(cite_rec_table_api.search()).length)
				setTimeout(function () {
					
					$('tbody td:not(.no-highlight)', that).removeHighlight();
					
				}, 1);
			
		});
	
	$('select#cite-penalty').change(function () {
		
		var selected_type = 0,
			penalty_str = $.trim($('select#cite-penalty option:selected').text()).toLowerCase();

		if(penalty_str.indexOf('suspension') != -1)
			selected_type = 1;
		else if(penalty_str.indexOf('dismissal') != -1)
			selected_type = 2;
		else
			selected_type = 0;
		
		$('select#cite-type')[0].selectedIndex = selected_type;
		
		var _days = 2;
						
		switch(selected_type) {
			case 0:
			
				_days = 2;
			
				break;
			case 1:
			
				_days = 3;
			
				break;
			case 2:
			
				_days = 5;
			
				break;
		}
		
		var nte_date = moment($.trim($('input#cite-nte').val()), 'MM-DD-YYYY HH:mm');
		
		$('input#cite-nte-deadline')
			.data('days', _days)
			.val(nte_date.isValid() ? nte_date.add(_days, 'd').format('MM-DD-YYYY HH:mm') : '');
		
	});
	
	$('input#cite-doc')
		.tag({
			placeholder: 'mm-dd-yyyy'
		})
		.next()
			.addClass('date-picker')
			.datepicker({
				format: 'mm-dd-yyyy'
			})
			.on('changeDate', function (e) {

				$(this)
					.blur()
					.focus()
					.datepicker('hide')
					.blur();
				
			});
	
	$('a#cite-details').click(function (e) {
		
		e.preventDefault();
	});
	
	$('textarea#explanation').inputlimiter();
});