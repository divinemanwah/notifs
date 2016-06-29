$(function () {
		
	var emp_table = $('table#emp-table').dataTable({
			ajax: base_url + 'employees/getAll/' + ($('input#emp-display-inactive').prop('checked') ? '1' : '0') + '/1',
			deferRender: true,
			autoWidth: false,
			columns: [
					{
						orderable: false,
						data: 5,
						render: function (d) {
								return '<label class="position-relative"><input type="checkbox" class="ace" value="' + d + '" /><span class="lbl"></span></label>';
							},
						className: 'center'
					},
					{ data: 0 },
					{ data: 1 },
					{ data: 2 },
					{ data: 3 },
					{
						data: 4,
						render: function (d) {
								
								return d == 1 ? '<span class="label label-sm label-success">Active</span>' : '<span class="label label-sm label-warning">Inactive</span>';
								
							},
						className: 'center no-highlight',
						searchable: false
					},
					{
						orderable: false,
						data: 5,
						render: function (d) {
								
								return '\
										<div class="hidden-sm hidden-xs action-buttons">\
											<a class="blue expat-info" href="#">\
												<i class="ace-icon fa fa-search-plus bigger-130"></i>\
											</a>\
											<a class="green expat-edit" href="#">\
												<i class="ace-icon fa fa-pencil bigger-130"></i>\
											</a>\
											<a class="red expat-remove" href="#">\
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
														<a href="#" class="tooltip-info expat-info" data-rel="tooltip" title="View">\
															<span class="blue">\
																<i class="ace-icon fa fa-search-plus bigger-120"></i>\
															</span>\
														</a>\
													</li>\
													<li>\
														<a href="#" class="tooltip-success expat-edit" data-rel="tooltip" title="Edit">\
															<span class="green">\
																<i class="ace-icon fa fa-pencil-square-o bigger-120"></i>\
															</span>\
														</a>\
													</li>\
													<li>\
														<a href="#" class="tooltip-error expat-remove" data-rel="tooltip" title="Delete">\
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
					}
				],
			order: [[1, 'desc']],
			// createdRow: function (row, data, index) {
					
					// $('td:eq(6) a', row).tooltip();
					
				// },
			rowCallback: function (row, data) {
					
					$('td:eq(6) a', row).tooltip();
					
					$('td:eq(6) a.expat-edit', row).click(function (e) {
						
						e.preventDefault();
						
						$('button#expat-save').data({
							id: data[5],
							mode: 'edit'
						});
						
						$('div#expat-modal div.modal-dialog').addClass('modal-lg');
						
						$('div#expat-modal div.modal-header h4').text('Update account');
						
						$('div#expat-modal div.expat-update').show();
						
						$('div#expat-modal').modal('show');
						
					});

					$('td:eq(6) a.expat-remove', row).click(function (e) {
						
						e.preventDefault();
						
						$('button#expat-save').data({
							id: data[5],
							mode: 'remove'
						});
						
						$('div#expat-modal div.modal-dialog').removeClass('modal-lg');
						
						$('div#expat-modal div.modal-header h4').text((parseInt(data[4]) ? 'Disable' : 'Enable') + ' account');
						
						$('div#expat-modal div.expat-update').hide();
						
						$('div#expat-modal').modal('show');
						
					});
					
				},
			initComplete: function (settings, json) {
					
					if('from_search' in window && window.from_search)
						$(settings.nTable).DataTable().search($.trim($('input#top-search').val())).draw();
					
				}
		}),
		emp_table_api = emp_table.api();
		
	$('table#emp-table thead input:checkbox').change(function () {
		
		// emp_table.rows().
		
		// console.log($(this).prop('checked'))
		
	});
	
	$('input#emp-display-inactive').change(function () {
		
		emp_table_api.ajax.url(base_url + 'employees/getAll/' + (this.checked ? '1' : '0') + '/1').load();
		
	});
	
	emp_table
		.on('search.dt', function () {

			$('tbody td:not(.no-highlight)', this)
				.removeHighlight()
				.highlight(emp_table_api.search());
				
		})
		.on('page.dt', function () {
			
			var that = this;
			
			if(!$.trim(emp_table_api.search()).length)
				setTimeout(function () {
					
					$('tbody td:not(.no-highlight)', that).removeHighlight();
					
				}, 1);
			
		});
	
	$('div#expat-modal')
		.modal({
			backdrop: 'static',
			show: false
		})
		.on('show.bs.modal', function () {
		
			$('div.alert-success').addClass('hidden');
			$('div.alert-danger').addClass('hidden');
		
			$('input, button', this).removeAttr('disabled');
			
			$('input:password', this).val('');
			
			$('button#expat-save').html('\
				<i class="ace-icon fa fa-lock"></i>\
				Confirm\
			');
			
		});
	
	$('form#expat-password').submit(function (e) {
		
		e.preventDefault();
		
	});
	
	$('button#expat-save').click(function () {
	
		var _pass = $.trim($('form#expat-password input:password').val());
		
		if(_pass.length) {
	
			var data = {};
			
			if($(this).data('mode') == 'remove') {
			
				data.mb_no = $(this).data('id');
				
				data.password = _pass;
				
				data.mb_status = 0;
			}
			
			if(ace.sizeof(data)) {
			
				var that = this;
			
				$('div#expat-modal input, div#expat-modal button').attr('disabled', 'disabled');
			
				$(this).html('Please wait&hellip;');
			
				$.post(
					base_url + 'employees/update',
					data,
					function (data) {
						
						if(data.success) {
							
							$('div.alert-success').removeClass('hidden');
							$('div.alert-danger').addClass('hidden');
							
							setTimeout(function () {
								
								$('div#expat-modal').modal('hide');
								
							}, 2000);
						}
						else {
						
							var msg = 'An unknown error occured.';
							
							switch(data.error) {
								case 1:
								
									msg = 'Invalid request.';
								
									break;
								case 2:
								
									msg = 'Record not saved.';
								
									break;
								case 3:
								
									msg = 'Incorrect password.';
								
									break;
							}
						
							$('span#expat-err-msg').text(msg);
						
							$('div.alert-success').addClass('hidden');
							$('div.alert-danger').removeClass('hidden');
							
							$('div#expat-modal input, div#expat-modal button').removeAttr('disabled');
			
							$(that).html('\
								<i class="ace-icon fa fa-lock"></i>\
								Confirm\
							');
						}
					},
					'json'
				);
			}
			else
				alert('Invalid action.');
		}
	});

});