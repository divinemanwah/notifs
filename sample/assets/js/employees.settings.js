$(function () {

	$('div#emp-settings-modal')
		.on('shown.bs.modal', function (e) {
		
			var that = this,
				_id = $(e.relatedTarget).data('id');
		
			if(!$('button.btn-primary', this).data('emploaded')) {
			
				$('button.btn-primary', this).attr('disabled', 'disabled');
				
				$.getJSON(
					base_url + 'employees/getAll2',
					{
						filters: [
								'u.hr_users_id',
								'm.mb_id',
								'm.mb_name'
							]
					},
					function (data) {
					
							var opts = '<option value=""></option>';
							
							$.each(data.data, function (i, val) {

								opts += '<option value="' + val[0] + '"' + (_id == val[0] ? ' selected="selected"' : '') + '>' + val[1] + ' - ' + val[2] + '</option>';
								
							});
							
							$('select#emp-settings-select')
								.html(opts)
								.chosen({
									search_contains: true
								})
								.change(function () {
									
									$('button.btn-primary', that).removeAttr('disabled');
									
								});
							
							$('span#emp-settings-loading').hide();
							
							$('button.btn-primary', that).data('emploaded', true);
							
							// $('button.btn-primary', that).removeAttr('disabled');
							
						}
				);
			}
		})
		.on('show.bs.modal', function (e) {
		
			var _id = $(e.relatedTarget).data('id');
			
			$('div.alert', this).addClass('hidden');
			
			$('button#emp-settings-save').html('\
				<i class="ace-icon fa fa-check"></i>\
				Save\
			');
			
			$('button:not(.btn-primary), select#emp-settings-select', this).removeAttr('disabled');
			
			if($('button.btn-primary', this).data('emploaded')) {
			
				$('select#emp-settings-select option').removeAttr('selected');

				$('select#emp-settings-select')[0].selectedIndex = $('select#emp-settings-select option[value="' + _id + '"]').index('select#emp-settings-select option');
				
				if($('select#emp-settings-select')[0].selectedIndex == -1)
					$('button.btn-primary', this).attr('disabled', 'disabled');
				else
					$('button.btn-primary', this).removeAttr('disabled');
			}
			
			$('select#emp-settings-select').trigger('chosen:updated');
			
			$('button#emp-settings-save').data('dept', $(e.relatedTarget).data('dept'));
		});
	
	$('button#emp-settings-save').click(function () {
	
		var that = this;
		
		$('div#emp-settings-modal div.alert').addClass('hidden');
	
		$(this).html('Please wait&hellip;');
		
		$('div#emp-settings-modal button').attr('disabled', 'disabled');
		
		$('select#emp-settings-select')
			.attr('disabled', 'disabled')
			.trigger('chosen:updated');
		
		$.post(
			base_url + 'employees/updateDeptHead',
			{
				dept_id: $(this).data('dept'),
				emp_id: $('select#emp-settings-select').val()
			},
			function (data) {

				if(data.success) {
				
					var _name = $('select#emp-settings-select option:selected').text();
					
					$('div#emp-settings-modal div.alert-success').removeClass('hidden');
					
					$('a.dept-' + $(that).data('dept'))
						.data('id', $('select#emp-settings-select').val())
						.text(_name.substr(_name.indexOf(' - ') + 3))
						.removeClass('text-muted');
					
					setTimeout(function () {
							
						$('div#emp-settings-modal').modal('hide');
						
					}, 2000);
					
				}
				else {
				
					$('div#emp-settings-modal div.alert-danger').removeClass('hidden');
					
					$(that).html('\
						<i class="ace-icon fa fa-check"></i>\
						Save\
					');
					
					$('div#emp-settings-modal button, select#emp-settings-select').removeAttr('disabled');
					
					$('select#emp-settings-select').trigger('chosen:updated');
					
				}
				
			},
			'json'
		);
	});
	
	 
});