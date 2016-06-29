$(function () {
	var mm_dd = "-01-01";
	var curr_year = new Date().getFullYear();
	var prev_year = new Date().getFullYear() - 1;
	
	var init_yy = "2015"; // This should be 2010 as the start of the company.
	var init_ymd = init_yy + mm_dd;
		
	loadKPIScores(curr_year, prev_year);
	
	$('div#emp-change-pass-modal')
		.modal({
			backdrop: 'static',
			show: false
		})
		.on('shown.bs.modal', function () {

			$('input:visible:first', this).focus();
			
		})
		.on('show.bs.modal', function () {

			$('div.alert', this).addClass('hidden');
			
			$('input', this)
				.removeAttr('readonly')
				.val('');
				
			$('button', this).removeAttr('disabled');
			
			$('button#emp-change-pass-save').html('\
				<i class="ace-icon fa fa-check"></i>\
				Save\
			');
			
		});
	
	$('abbr#about').tooltip();
	
	$('a#profile-change-pass').click(function (e) {
		
		e.preventDefault();
		
		if(confirm('This will also change your Intranet password.\n\nDo you want to continue?'))
			$('div#emp-change-pass-modal').modal('show');
		
	});
	
	$('button#emp-change-pass-save').click(function () {
	
		var pass = $.trim($('input#emp-change-pass').val()),
			that = this;
		
		if(pass) {

			if(pass == $.trim($('input#emp-change-pass2').val())) {
			
				$('div#emp-change-pass-modal div.alert-danger').addClass('hidden');
				
				$('div#emp-change-pass-modal input').attr('readonly', 'readonly');
				
				$('div#emp-change-pass-modal button').attr('disabled', 'disabled');
				
				$(this).html('Please wait&hellip;');
				
				$.post(
					base_url + 'employees/update',
					{
						mb_no: $('a#profile-change-pass').data('id'),
						mb_password: pass
					},
					function (data) {
					
						if(data.success) {
							
							$('div#emp-change-pass-modal div.alert-danger').addClass('hidden');
							$('div#emp-change-pass-modal div.alert-success').removeClass('hidden');
							
							setTimeout(function () {
							
								$('div#emp-change-pass-modal').modal('hide');
								
							}, 2000);
							
						}
						else {
						
							$('div#emp-change-pass-modal div.alert-danger').removeClass('hidden');
							$('div#emp-change-pass-modal div.alert-success').addClass('hidden');
							
							$('div#emp-change-pass-modal input').removeAttr('readonly');
				
							$('div#emp-change-pass-modal button').removeAttr('disabled');
							
							$('span#emp-change-pass-err-msg').text('Unable to save the new password.');
							
							$(that).html('\
								<i class="ace-icon fa fa-check"></i>\
								Save\
							');
							
						}
					},
					'json'
				);
				
				$('div#emp-change-pass-modal div.alert-success').removeClass('hidden');
				
			}
			else {
			
				$('div#emp-change-pass-modal div.alert-success').addClass('hidden');
			
				$('div#emp-change-pass-modal div.alert-danger').removeClass('hidden');
				
				$('span#emp-change-pass-err-msg').text('Passwords must match.');
			}
		}
		
	});

	$("#kpi_year").datepicker({
		format: "yyyy",
		viewMode: "years",
		minViewMode: "years",
		orientation: "top right",
		startDate: new Date(init_ymd),
		endDate: new Date(moment().year() + mm_dd),
		autoclose: true,
		onRender: function (date) {
            return date.valueOf() < now.valueOf() ? 'disabled' : '';
        }
	}).on('changeDate', function(ev){
		console.log(ev.date.getFullYear());
		curr_year = ev.date.getFullYear();
		prev_year = ev.date.getFullYear() - 1;
		$("#kpi_year").html(ev.date.getFullYear());
		loadKPIScores(curr_year, prev_year);
	});

	/* Change SMS PIN Code */
	
	// $('#emp-change-smscode').mask('9999');
	// $('#emp-change-smscode2').mask('9999');
	
	$('div#emp-change-smscode-modal')
		.modal({
			backdrop: 'static',
			show: false
		})
		.on('shown.bs.modal', function () {

			$('input:visible:first', this).focus();
			
		})
		.on('show.bs.modal', function () {

			$('div.alert', this).addClass('hidden');
			
			$('input', this)
				.removeAttr('readonly')
				.val('');
				
			$('button', this).removeAttr('disabled');
			
			$('button#emp-change-smscode-save').html('\
				<i class="ace-icon fa fa-check"></i>\
				Save\
			');
			
		});
		
	$('a#profile-change-smscode').click(function (e) {
		$('div#emp-change-smscode-modal').modal('show');
	});
	
	$('button#emp-change-smscode-save').click(function () {
	
		var smscode = $.trim($('input#emp-change-smscode').val()),
			that = this;
		
		if(smscode) {

			if(smscode == $.trim($('input#emp-change-smscode2').val())) {
			
				$('div#emp-change-smscode-modal div.alert-danger').addClass('hidden');
				
				$('div#emp-change-smscode-modal input').attr('readonly', 'readonly');
				
				$('div#emp-change-smscode-modal button').attr('disabled', 'disabled');
				
				$(this).html('Please wait&hellip;');
				
				$.post(
					base_url + 'employees/update',
					{
						mb_no: $('a#profile-change-smscode').data('id'),
						sms_passcode: smscode
					},
					function (data) {
					
						if(data.success) {
							
							$('div#emp-change-smscode-modal div.alert-danger').addClass('hidden');
							$('div#emp-change-smscode-modal div.alert-success').removeClass('hidden');
							
							setTimeout(function () {
							
								$('div#emp-change-smscode-modal').modal('hide');
								
							}, 2000);
							
						}
						else {
						
							$('div#emp-change-smscode-modal div.alert-danger').removeClass('hidden');
							$('div#emp-change-smscode-modal div.alert-success').addClass('hidden');
							
							$('div#emp-change-smscode-modal input').removeAttr('readonly');
				
							$('div#emp-change-smscode-modal button').removeAttr('disabled');
							
							$('span#emp-change-smscode-err-msg').text('Unable to save the new SMS PIN Code.');
							
							$(that).html('\
								<i class="ace-icon fa fa-check"></i>\
								Save\
							');
							
						}
					},
					'json'
				);
				
				$('div#emp-change-smscode-modal div.alert-success').removeClass('hidden');
				
			}
			else {
			
				$('div#emp-change-smscode-modal div.alert-success').addClass('hidden');
			
				$('div#emp-change-smscode-modal div.alert-danger').removeClass('hidden');
				
				$('span#emp-change-smscode-err-msg').text('SMS PIN Codes must match.');
			}
		}
		
	});
	$('#profile-change-smscode').tooltip();

	$('div.kpi-scores span.cut-offA').qtip({
		content: {
				text: function(event, api) {
					
					loadKPIScoreCutOffA(curr_year, prev_year);
			                
			        }
			},
		position: {
				my: 'top center',
				at: 'bottom center',
				container: $('div.right-profile')
			},
		show: {
				event: 'click',
				solo: true
			},
		hide: {
				event: 'click'
			},
		style: {
				classes: 'qtip-bootstrap base-tips'
			}
	});
	
	$('div.kpi-scores span.cut-offB').qtip({
		content: {
				text: function(event, api) {
					
					loadKPIScoreCutOffB(curr_year);
			        }
			},
		position: {
				my: 'top center',
				at: 'bottom center',
				container: $('div.right-profile')
			},
		show: {
				event: 'click',
				solo: true
			},
		hide: {
				event: 'click'
			},
		style: {
				classes: 'qtip-bootstrap base-tips'
			}
	});
	
	$(document).on('click', 'a.vio-month-details', function (e) {
		
		e.preventDefault();
		
		var that = this;
		
		$(this).qtip({
			content: {
				text: 'Please wait&hellip;'
			},
			overwrite: false,
			show: {
				ready: true,
				delay: 300
			},
			style: {
				classes: 'qtip-light qtip-bootstrap'
			},
			events: {
				hide: function (event, api) {
					
					if('curr_month_details' in window)
						window.curr_month_details.abort();
					
					api.destroy(true);
				}
			}
		});
		
		window.curr_month_details = $.getJSON(
				base_url + 'violations/getAll/2/0/' + _user_id + '/' + $(that).data('ym'),
				function (data) {
					
					var tbl = 'No records found.';
					
					if(data.data.length) {
					
						tbl = $('<table class="table table-striped table-bordered table-hover" />');
						
						$.each(data.data, function (i, val) {
							
							tbl.append('<tr><td>' + val[10] + '</td><td>' + moment(val[3], 'YYYY-MM-DD HH:mm:ss').format('MM-DD-YYYY') + '</td></tr>');
							
						});
					}
					
					$(that).qtip({
						content: {
							text:tbl
						},
						show: {
							event: e.type,
							ready: true
						},
						style: {
							classes: 'qtip-light qtip-bootstrap'
						},
						events: {
							hide: function (event, api) {
								
								api.destroy(true);
							}
						}
					});
					
				}
			);
		
	});
	
	function loadKPIScores(curr_year, prev_year){
		$('span.curr_year').html(curr_year);
		$('span.prev_year').html(prev_year);
		loadKPIScoreCutOffA(curr_year, prev_year);
		loadKPIScoreCutOffB(curr_year);
	}
	
	function loadKPIScoreCutOffA(curr_year, prev_year){
		
		var container = $('div.kpi-scores span.cut-offA');
		var from = prev_year + '14';
		var to = curr_year + '05';
		
		container.qtip('option', 'content.text', function(event, api){
			$.ajax({
                url: base_url + 'employees/hr_history/' + _user_id + '/' + container.index(this) + '/' + from + '/' + to
            })
            .then(function(content) {

                api.set('content.text', content);
                
            }, function(xhr, status, error) {

                api.hide();
                
            });

			return 'Loading&hellip;';
		});
		
		loadKPIAveScores(container, from, to);

	}
	
	
	function loadKPIScoreCutOffB(curr_year){
		
		var container = $('div.kpi-scores span.cut-offB');
		var from = curr_year + '06';
		var to = curr_year + '11';
		
		container.qtip('option', 'content.text', function(event, api){
			$.ajax({
                url: base_url + 'employees/hr_history/' + _user_id + '/' + container.index(this) + '/' + from + '/' + to
            })
            .then(function(content) {

                api.set('content.text', content);
                
            }, function(xhr, status, error) {

                api.hide();
                
            });

			return 'Loading&hellip;';
		});
		
		loadKPIAveScores(container, from, to);
	}
	
	function loadKPIAveScores(container, from, to){
		
		$.ajax({
			url: base_url + 'employees/hr_kpi_data' + '/' + from + '/' + to + '/' + _user_id,
			dataType: "json"
		}).then(function(data){
			
			var kpi = data.data;
			for(var i = 0; i < 4; i++){
				container.eq(i).children('span').first().html(kpi[i]);
			}
			
		});
				
	}
});