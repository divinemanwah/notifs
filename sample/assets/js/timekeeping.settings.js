$(function () {
	
	var shift_table = $('table#shift-codes-table').dataTable({
			serverSide: true,
			ajax: {
				url: base_url + 'timekeeping/getAllShifts/' + ($('input#shift-display-inactive').prop('checked') ? '1' : '0'),
				type: "POST"
			},
			deferRender: true,
			autoWidth: false,
			method: "post",
			columns: [
					{
						orderable: false,
						data: "shift_id",
						render: function (d) {
								return '<label class="position-relative"><input type="checkbox" class="ace" value="' + d + '" /><span class="lbl"></span></label>';
							},
						className: 'center'
					},
					{ data: "shift_code" },
					{ data: "shift_from" },
					{ data: "shift_to" },
					{ data: "enabled_lbl" },
					{ 
						orderable: false,
						data: "shift_id",
						render: function (d,t,r,m) {
							return '\
										<div class="hidden-sm hidden-xs action-buttons">\
											<a class="green shift-edit" href="#">\
												<i class="ace-icon fa fa-pencil-square-o bigger-130"></i>\
											</a>\
										</div>\
										<div class="hidden-md hidden-lg">\
											<div class="inline position-relative">\
												<button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown" data-position="auto">\
													<i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>\
												</button>\
												<ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">\
													<li>\
														<a href="#" class="tooltip-success shift-edit" data-rel="tooltip" title="Edit">\
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
			order: [[1, 'asc']],
			rowCallback: function (r, d) {
			  $('a.shift-edit', r).click(function (e) {
				e.preventDefault();
				$('button#shift-save',"div#shifts-modal").data({id: d["shift_id"]});
				$('div#shifts-modal').modal('show');
			  });
			}
	      }),
	shift_table_api = shift_table.api();;

	$("input#shift-display-inactive").off("change").change(function(){
	  shift_table_api.ajax.url(base_url + 'timekeeping/getAllShifts/' + ($('input#shift-display-inactive').prop('checked') ? '1' : '0')).load();
	});
	
	$("#default_period").off("change").change(function() {
	  var default_day = $(this).val();
	  $("#sched-day-from").html(default_day);
	  if(default_day == 1) {
	    $("#sched-month-to").html("March");
	    $("#sched-day-to").html("31");
	  }
	  else {
	    $("#sched-month-to").html("April");
		if(default_day == 31)
		  $("#sched-day-to").html(default_day-2);
		else
		  $("#sched-day-to").html(default_day-1);
	  }
	});
	
	$('div#shifts-modal')
	.modal({
		backdrop: 'static',
		show: false
	})
	.off('show.bs.modal')
	.on('show.bs.modal', function () {
	  $(".success-msg",this).html("");
	  $('div.alert-success', this).addClass('hidden');
	  $(".err-msg",this).html("");
	  $('div.alert-danger', this).addClass('hidden');
	  $('input#shift-id'	,'div#shifts-modal').val("");
	  $('input#shift-code'	,'div#shifts-modal').val("");
	  $('input#shift-start'	,'div#shifts-modal').val("");
	  $('input#shift-end'	,'div#shifts-modal').val("");
	  $('input#shift-status','div#shifts-modal').val("");
	  $('.ace-checkbox').prop("checked", false);
	  $('#sched-user','div#shifts-modal').val("");
	  $('#sched-user','div#shifts-modal').trigger("chosen:updated");
	  
	  $.getJSON(
		base_url + 'timekeeping/getShift/' + $('button#shift-save').data('id'),
		function (response) {
		  $('input#shift-id'	,'div#shifts-modal').val(response.data[0].shift_id);
		  $('input#shift-code'	,'div#shifts-modal').val(response.data[0].shift_code);
		  $('input#shift-start'	,'div#shifts-modal').val(response.data[0].shift_from);
		  $('input#shift-end'	,'div#shifts-modal').val(response.data[0].shift_to);
		  $('input#shift-status','div#shifts-modal').val(response.data[0].enabled);
		  $('select#shift-color','div#shifts-modal').ace_colorpicker('pick', response.data[0].shift_color);
          var sched_depts = response.data[0].sched_depts;
		  if(sched_depts != null && sched_depts.length > 0) {
		    var sched_depts_arr = sched_depts.split(",");
		    $(".sched-depts").each(function(e) {
		      if($.inArray($(this).val(),sched_depts_arr) > -1)
			    $(this).prop("checked",true);
		    });
		  }
		  var cws_depts = response.data[0].cws_depts;
		  if(cws_depts != null && cws_depts.length > 0) {
		    var cws_depts_arr = cws_depts.split(",");
		    $(".cws-depts").each(function(e) {
		      if($.inArray($(this).val(),cws_depts_arr) > -1)
			    $(this).prop("checked",true);
		    });
		  }
		  
		  var sched_users = response.data[0].sched_users;
		  if(sched_users != null && sched_users.length > 0) {
		    var sched_users_arr = sched_users.split(",");
		    $.each(sched_users_arr, function(i,v) {
		      $("#sched-user option[value='"+v+"']","div#shifts-modal").prop("selected",true);
		    });
			$('#sched-user','div#shifts-modal').trigger("chosen:updated");
		  }
		  
		  $('.timepicker','div#shifts-modal').timepicker({
			minuteStep: 30,
			showSeconds: false,
			showMeridian: false,
			showInputs: false
		  }).next().on(ace.click_event, function(){
			$(this).prev().focus();
		  });
		}
	  );
	});
	
	$('div#add-shifts-modal')
	.modal({
		backdrop: 'static',
		show: false
	})
	.off('show.bs.modal')
	.on('show.bs.modal', function () {
	  $(".success-msg",this).html("");
	  $('div.alert-success', this).addClass('hidden');
	  $(".err-msg",this).html("");
	  $('div.alert-danger', this).addClass('hidden');
	  
	  $('input#shift-id'	,'div#add-shifts-modal').val("");
	  $('input#add-shift-code'	,'div#add-shifts-modal').val("");
	  $('input#add-shift-start'	,'div#add-shifts-modal').val("");
	  $('input#add-shift-end'	,'div#add-shifts-modal').val("");
	  $('input#add-shift-status','div#add-shifts-modal').val("");
	  $('.ace-checkbox').prop("checked", false);
	  
	  $('#add-sched-user','div#add-shifts-modal').val("");
	  $('#add-sched-user','div#add-shifts-modal').trigger("chosen:updated");
	});
	
	
	$("#add-shift-btn").off("click").click(function(e) {
	  $('div#add-shifts-modal').modal('show');
	});
	
	$("#gen-set-save").off("click").click(function(e) {
	  e.preventDefault();
	  
	  $(this).html('<i class="ace-icon fa fa-spinner fa-spin bigger-110"></i> Saving&hellip;');
	  $(this).attr("disabled",true);
	  
	  $(".success-msg","#gen-setting").html("");
	  $("div.alert-success","#gen-setting").addClass("hidden");
	  $(".err-msg","#gen-setting").html("");
	  $("div.alert-danger","#gen-setting").addClass("hidden");
	  
	  $.post(
	    base_url + "timekeeping/updateGeneralSettings",
		$("#gen-form").serialize(),
		function(response) {
		  if(response.success) {
		    $(".success-msg","#gen-setting").html(response.msg);
			$("div.alert-success","#gen-setting").removeClass("hidden");
			
			setTimeout(function(){ $("div.alert-success","#gen-setting").addClass("hidden"); },3000);
		  }
		  else {
		    $(".err-msg","#gen-setting").html(response.msg);
			$("div.alert-danger","#gen-setting").removeClass("hidden");
			
			setTimeout(function(){ $("div.alert-danger","#gen-setting").addClass("hidden"); },3000);
		  }
		  
		  $("#gen-set-save").html('<i class="ace-icon fa fa-save bigger-110"></i> Save');
		  $("#gen-set-save").removeAttr("disabled");
		},
		"json"
	  );
	});
	
	$("#shift-save",'div#shifts-modal').off("click").click(function(e) {
	  var form_data = $("#shift").serialize();
	  $("input, select, button",'div#shifts-modal').attr("disabled",true);
	  $("button#shift-save","div#shifts-modal").html('<i class="ace-icon fa fa-spinner fa-spin bigger-110"></i> Saving&hellip;');
	  
	  $.post(
	    base_url + "timekeeping/updateShift",
		form_data,
		function(response) {
		  if(response.success) {
		    $(".success-msg","div#shifts-modal").html(response.msg);
			$("div.alert-success","div#shifts-modal").removeClass("hidden");
			
			setTimeout(function(){
				  $("div.alert-success","div#shifts-modal").addClass("hidden"); $('div#shifts-modal').modal('hide'); 
				  shift_table_api.ajax.reload(null,false);
				},
				3000);
		  }
		  else {
		    $(".err-msg","div#shifts-modal").html(response.msg);
			$("div.alert-danger","div#shifts-modal").removeClass("hidden");
			
			setTimeout(function(){ $("div.alert-danger","div#shifts-modal").addClass("hidden"); },3000);
		  }
		  
		  $("button#shift-save","div#shifts-modal").html('<i class="ace-icon fa fa-save bigger-110"></i> Save');
		  $("input, select, button",'div#shifts-modal').removeAttr("disabled");
		},
		"json"
	  );
	});
	
	$('.colorpicker').ace_colorpicker();

    $('.timepicker','div#add-shifts-modal').timepicker({
		minuteStep: 30,
		showSeconds: false,
		showMeridian: false,
		showInputs: false,
		defaultTime: false
	}).next().on(ace.click_event, function(){
		$(this).prev().focus();
	});

	$("#default_period").trigger("change");
	
	$("#add-shift-save",'div#add-shifts-modal').off("click").click(function(e) {
	  var form_data = $("#add-shift").serialize();
	  $("input, select, button",'div#add-shifts-modal').attr("disabled",true);
	  $("button#add-shift-save","div#shifts-modal").html('<i class="ace-icon fa fa-spinner fa-spin bigger-110"></i> Saving&hellip;');
	  
	  $.post(
	    base_url + "timekeeping/insertShift",
		form_data,
		function(response) {
		  console.log(response);
		  if(response.success) {
		    $(".success-msg","div#add-shifts-modal").html(response.msg);
			$("div.alert-success","div#add-shifts-modal").removeClass("hidden");
			
			setTimeout(function(){
				  $("div.alert-success","div#add-shifts-modal").addClass("hidden"); $('div#add-shifts-modal').modal('hide'); 
				  shift_table_api.ajax.url(base_url + 'timekeeping/getAllShifts/' + ($('input#shift-display-inactive').prop('checked') ? '1' : '0')).load();
				},
				3000);
		  }
		  else {
		    $(".err-msg","div#add-shifts-modal").html(response.msg);
			$("div.alert-danger","div#add-shifts-modal").removeClass("hidden");
			
			setTimeout(function(){ $("div.alert-danger","div#add-shifts-modal").addClass("hidden"); },3000);
		  }
		  
		  $("button#add-shift-save","div#add-shifts-modal").html('<i class="ace-icon fa fa-save bigger-110"></i> Save');
		  $("input, select, button",'div#add-shifts-modal').removeAttr("disabled");
		},
		"json"
	  );
	});
	
	$("#sched-dept-all",'div#shifts-modal').off("click").click(function(e) {
	  if($(this).prop("checked"))
	    $(".sched-depts").prop("checked",true);
	  else
	    $(".sched-depts").prop("checked",false);
	});
	
	$(".sched-depts",'div#shifts-modal').off("click").click(function(e) {
	  if($(this).prop("checked") == false)
	    $("#sched-dept-all",'div#shifts-modal').prop("checked",false);
	});
	
	$("#cws-dept-all",'div#shifts-modal').off("click").click(function(e) {
	  if($(this).prop("checked"))
	    $(".cws-depts").prop("checked",true);
	  else
	    $(".cws-depts").prop("checked",false);
	});
	
	$(".cws-depts",'div#shifts-modal').off("click").click(function(e) {
	  if($(this).prop("checked") == false)
	    $("#cws-dept-all",'div#shifts-modal').prop("checked",false);
	});
	
	$('#sched-user','div#shifts-modal').chosen({width: "100%"}); 

	/* Add Shift */
	$("#add-sched-dept-all",'div#add-shifts-modal').off("click").click(function(e) {
	  if($(this).prop("checked"))
	    $(".add-sched-depts").prop("checked",true);
	  else
	    $(".add-sched-depts").prop("checked",false);
	});
	
	$(".add-sched-depts",'div#add-shifts-modal').off("click").click(function(e) {
	  if($(this).prop("checked") == false)
	    $("#add-sched-dept-all",'div#add-shifts-modal').prop("checked",false);
	});
	
	$("#add-cws-dept-all",'div#add-shifts-modal').off("click").click(function(e) {
	  if($(this).prop("checked"))
	    $(".add-cws-depts").prop("checked",true);
	  else
	    $(".add-cws-depts").prop("checked",false);
	});
	
	$(".add-cws-depts",'div#add-shifts-modal').off("click").click(function(e) {
	  if($(this).prop("checked") == false)
	    $("#add-cws-dept-all",'div#add-shifts-modal').prop("checked",false);
	});
	
	$('#add-sched-user','div#add-shifts-modal').chosen({width: "100%"}); 
	
	/* End of Add Shift */
});