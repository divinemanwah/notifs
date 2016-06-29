$(function () {
	
	var vio_rec_table = $('table#vio-rec-table').dataTable({
			ajax: base_url + 'violations/getAll/2',
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
						data: 12
					},
					{
						data: 14
					},
					{
						data: 13
					},
					{
						data: 15
					},
					{
						data: 11,
						render: function (d) {
								
								return '<div class="ellipsis">' + (d ? d : '--') + '</div>';
								
							}
					},
					{
						data: 3,
						render: function (d) {
								
								return moment(d, 'YYYY-MM-DD HH:mm:ss').format('MM-DD-YYYY');
							}
					},
					{
						data: 4,
						render: function (d) {
								
							return '<div class="ellipsis">' + (d ? d : '--') + '</div>';
							}
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
						data: 6,
						visible: false,
						searchable: false
					}
				],
			order: [[9, 'desc']],
			initComplete: function () {
				
				$('thead span', this).tooltip();
				
			},
			rowCallback: function (row, data) {
					
					var that = this,
						_api = this.api();
					
					setTimeout(function () {
						
						if($('td:eq(5) div', row)[0].offsetWidth < $('td:eq(5) div', row)[0].scrollWidth)
							$('td:eq(5) div', row)
								.attr('title', $('td:eq(5) div', row).text())
								.tooltip();
						
					}, 1);
						
					$('td:eq(7) a', row).tooltip();

					$('td:eq(7) a.delete-btn', row).click(function (e) {
						
						e.preventDefault();
						
						var _id = $(this).data('id');

						if(confirm('Are you sure you want to remove this entry?')) {
						
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
								base_url + 'violations/update/2',
								{
									id: _id,
									data: {
											status: 2
										}
								},
								function (_data) {
									
									if(_data.success) {
									
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
					
					$('td:eq(7) a.edit-btn', row).click(function (e) {

						e.preventDefault();
						
						window.curr_vio_row = row;
						
						$('div#vio-modal h4').text('Edit violation');
						
						$('div#vio-modal div.edit input').val(data[1]);
						
						$('div#vio-modal div.edit button')
							.html('\
								' + (data[2] == '1' ? 'Enabled' : 'Disabled') + '\
								<span class="ace-icon fa fa-caret-down icon-on-right"></span>\
							')
							.removeClass('btn-success btn-warning')
							.addClass('btn-' + (data[2] == '1' ? 'success' : 'warning'));
		
						$('button#vio-save').data({
							id: $(this).data('id')
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

											$(that)
												.popover('destroy')
												.popover({
													title: 'Information',
													content: '\
														<table class="table" width="100%" style="table-layout: fixed;">\
															<tr><td width="40%">ID</td><td width="60%">' + _data.id + '</td></tr>\
															<tr><td width="40%">Description</td><td width="60%" style="word-wrap: break-word;">' + _data.description + '</td></tr>\
															<tr><td width="40%">Status</td><td width="60%">' + (_data.status == 1 ? 'Enabled' : 'Disabled') + '</td></tr>\
															<tr><td width="40%">Rules</td><td width="60%">' + (_data.rules == null ? 'Not set' : '<a href="#">View</a>') + '</td></tr>\
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
											
										}
								).fail(function () { alert('An error occured. Please try again later.'); });
							
						});
					
				}
		});
	
	window.vio_rec_table_api = vio_rec_table.api();
	
	vio_rec_table
		.on('search.dt', function () {
			
			$('tbody td:not(.no-highlight)', this)
				.removeHighlight()
				.highlight(vio_rec_table_api.search());
			
		})
		.on('page.dt', function () {
			
			var that = this;
			
			if(!$.trim(vio_rec_table_api.search()).length)
				setTimeout(function () {
					
					$('tbody td:not(.no-highlight)', that).removeHighlight();
					
				}, 1);
			
		});
	
	var listener = function (e) {
		
			var confirmationMessage = 'Upload is still in progress. Click Cancel to resume the process.';
			
//			pauseUpload();
			
			e.returnValue = confirmationMessage;
			
			return confirmationMessage;
		};
	
	var modal = $('div#vio-import-modal');
	
	modal.on('hidden.bs.modal', function () {
		
		$('form', modal)[0].reset();
		
		$('input#vio-import')
			.data('ace_file_input')
			.reset_input();
		
		$('input#vio-import')
			.data('ace_file_input')
			.enable();
		
		$('input#vio-import').change();
	});
	
	$('input#vio-import')
		.ace_file_input({
			style:'well',
			btn_choose:'Drop files here or click to choose',
			btn_change:null,
			no_icon:'ace-icon fa fa-cloud-upload',
			droppable:true,
			thumbnail:'small',//large | fit
			//,icon_remove:null//set null, to hide remove/reset button
			/**,before_change:function(files, dropped) {
							//Check an example below
							//or examples/file-upload.html
							return true;
						}*/
			before_remove : function() {
					
					$('input:not(:file), button', modal[0]).removeAttr('disabled');
					
					$('input#vio-import').data('ace_file_input').enable();
					
					$('span.ace-file-name', $('input#vio-import').data('ace_file_input').$container).each(function (i, elem) {
						
						$('i.ace-icon', elem)
							.removeClass('cite-upload-loader fa-spinner fa-pulse fa-hourglass-half fa-check fa-times cite-upload-loader-done')
							.addClass('fa-file');
					});
					
					$('button#vio-import-upload').html('\
						<i class="ace-icon fa fa-upload"></i>\
						Upload\
					');
					
					window.removeEventListener('beforeunload', listener);
					modal.data('bs.modal').escape();
					
					$('button#vio-import-upload').attr('disabled', 'disabled');
					
					if($.ajaxq.isRunning('vio-upload')) {
						
						$.ajaxq.getActiveRequest('vio-upload').abort();
						
						$.ajaxq.clear('vio-upload');
					}
					
					return true;
				},
			preview_error : function(filename, error_code) {
				//name of the file that failed
				//error_code values
				//1 = 'FILE_LOAD_FAILED',
				//2 = 'IMAGE_LOAD_FAILED',
				//3 = 'THUMBNAIL_FAILED'
				//alert(error_code);
			},
			allowExt: ['xls', 'xlsx'],
			allowMime: ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sh']
	
		})
		.on('change', function () {
			
			var files = $(this).data('ace_input_files');
			
			console.log(files);
//			console.log($(this).data('ace_input_method'));
			
			if(files && files.length)
				$('button#vio-import-upload').removeAttr('disabled');
			else
				$('button#vio-import-upload').attr('disabled', 'disabled');
		})
		.data('ace_file_input')
			.$label.on('dragenter', function () {
				
//				console.log(arguments)
			});
	
	$('button#vio-import-upload').click(function (e) {
		
		e.preventDefault();
		
		var files = $('input#vio-import').data('ace_input_files');
		
		if(files && files.length) {
			
			$('input:not(:file), button', modal[0]).attr('disabled', 'disabled');
			
			$('input#vio-import').data('ace_file_input').disable();
			
			modal.off('keydown.dismiss.bs.modal');
			
			window.addEventListener('beforeunload', listener);
			
			$('span.ace-file-name', $('input#vio-import').data('ace_file_input').$container).each(function (i, elem) {
				
				$('i.ace-icon', elem)
					.removeClass('fa-file')
					.addClass('cite-upload-loader fa-hourglass-half');
			});
			
			$(this).html('\
				<i class="fa fa-spinner fa-pulse"></i>\
				Uploading&hellip;\
			');
			
			$.each(files, function(i, file) {
				
				var data = new FormData(),
					_icon = $('span.ace-file-name i.ace-icon:eq(' + i + ')', $('input#vio-import').data('ace_file_input').$container);
				
			    data.append('file', file);
			    data.append('total', files.length);
			    data.append('index', i);
			    data.append('generate', $('input#vio-generate').prop('checked'));
			    data.append('overwrite', $('input#vio-overwrite').prop('checked'));
			    
			    $.ajaxq('vio-upload', {
					url: base_url + 'violations/importRecords',
					data: data,
				    cache: false,
				    contentType: false,
				    processData: false,
				    type: 'POST',
				    dataType: 'json',
				    beforeSend: function () {
					    	
				    		_icon
				    			.removeClass('fa-hourglass-half')
				    			.addClass('cite-upload-loader fa-spinner fa-pulse');
					    },
//				    xhr: function () {
//				    	
//				            var myXhr = $.ajaxSettings.xhr();
//				            console.log(myXhr)
//				            if(myXhr.upload)
//				                myXhr.upload.addEventListener('progress', function () { console.log(arguments) }, false);
//				                
//				            return myXhr;
//				        },
				    success: function (data) {

					        _icon
					        	.removeClass('cite-upload-loader fa-spinner fa-pulse')
					        	.addClass('cite-upload-loader-done ' + (data.success ? 'fa-check' : 'fa-times'));
					        
					        if(data.success)
					        	_icon
						        	.parent()
						        		.attr('data-title',  _icon.parent().data('title') + ' (record count: ' + data.total + ')');
					        
					        $.ajaxq.getActiveRequest('vio-upload').done(function () {
					        	
					        	if(!$.ajaxq.isRunning('vio-upload')) {
						        	
						        	$('input:not(:file), button', modal[0]).removeAttr('disabled');
									
									$('button#vio-import-upload').html('\
										<i class="ace-icon fa fa-upload"></i>\
										Upload\
									');
									
									window.removeEventListener('beforeunload', listener);
									modal.data('bs.modal').escape();
									
									$('button#vio-import-upload').attr('disabled', 'disabled');
						        }
					        	
					        });
					    }
				});
			});
			
		}
		
	});
	
	$('input#vio-overwrite').change(function () {console.log(arguments)
		
		if($(this).prop('checked') && !confirm('Are you sure you want to replace old data? Click Cancel to append instead of overwrite.'))
			$(this).prop('checked', false);
	});
	
});