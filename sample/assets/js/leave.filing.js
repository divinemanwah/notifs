$(function () {
  /* Balance */
  var balance_tbl = $("#tk-balances-table");
  var req_schedules_tbl = $("#tk-leave-table");
  var limit_default = 25;
  var req_loading = loading = false;
  var current_page = 1;
  var pending_leaves = 0;
  
  $("#add-leave-btn").attr("disabled",true);
  
  $(document).off("click","div#tk-balances-pager ul.pagination li a").on("click","div#tk-balances-pager ul.pagination li a", function(e){
    e.preventDefault();
	getBalance($(this).data("page"));
  });
  
  $(document).off("click","div#tk-leave-pager ul.pagination li a").on("click","div#tk-leave-pager ul.pagination li a", function(e){
    e.preventDefault();
	getRequests($(this).data("page"));
  });
  
  getBalance(1);
  
  function getBalance(page_num) {
	if(loading){ return false; }
	$("#tk-table-loader").removeClass("hidden");
	balance_tbl.addClass("hidden");
	loading = true;
	current_page = page_num;
    $.ajax({
	  url: base_url+"leave/getEmpLeaveBalancesGrid",
	  data: {limit: limit_default, page: page_num, emp: $('#emp_id').val(), year: $('#tk-year').val()},
	  cache: false,
	  dataType: "json",
	  type: "post",
	  success: function(response){
	    $("#awol_cnt").html(response.awol_cnt);
		$("#el_cnt").html(response.el_cnt);
	  
	    $("#tk-table-loader").addClass("hidden");
		balance_tbl.removeClass("hidden");
		if(response.data.length) {
		  $("#add-leave-btn").attr("disabled",false);
		  $("#leave-warning").addClass("hidden");
		  $("#tk-table-no-record").addClass("hidden");
	      balance_tbl.handsontable({
			colHeaders: response.header,
			colWidths: response.width,
			data: response.data,
			cells: function (row, col, prop) {
			  var cellProperties = {};
			  cellProperties.readOnly = true;
			  cellProperties.className = "htCenter";
			  if(prop == "action")
			    cellProperties.renderer = "html";
			  return cellProperties;
			}
		  });
		}
		else {
		  $("#add-leave-btn").attr("disabled",true);
		  $("#leave-warning").removeClass("hidden");
		  $("#tk-table-no-record").removeClass("hidden");
		  balance_tbl.addClass("hidden");
		}
		var total_count = response.total_count;
		var total_pages = Math.ceil(total_count/limit_default);
		var current_page = response.page;
		var pagination_limit = 9;
		var max_page = pagination_limit;
	    if(pagination_limit > total_pages)
		  max_page = total_pages;
		
		var pagination_lbl = '<ul class="pagination">';
		if(total_pages > 1)
		  pagination_lbl += '<li '+(current_page==1?'class="active"':'')+'><a href="javascript:void(0)" data-page="1">1</a></li>';
		if(current_page > 5) {
		  if(total_pages > pagination_limit)
		    pagination_lbl += '<li class="disabled"><a href="javascript:void(0)"><i class="ace-icon fa fa-ellipsis-h"></i></a></li>';
		  var initial = ctr = (current_page - 3>total_pages-pagination_limit+2?total_pages-pagination_limit+2:current_page - 3);
		  var last = initial+pagination_limit-2;
		  if(last > total_pages)
		    last = total_pages;
		  for(;ctr<last;ctr++){
		    pagination_lbl += '<li '+(current_page==ctr?'class="active"':'')+'><a href="javascript:void(0)" data-page="'+ctr+'">'+ctr+'</a></li>';
		  }
		}
		else {
		  for(var ctr=2;ctr<max_page;ctr++){
		    pagination_lbl += '<li '+(current_page==ctr?'class="active"':'')+'><a href="javascript:void(0)" data-page="'+ctr+'">'+ctr+'</a></li>';
		  }
		}
		if(current_page < total_pages - 4 && total_pages > pagination_limit)
		  pagination_lbl += '<li class="disabled"><a href="javascript:void(0)"><i class="ace-icon fa fa-ellipsis-h"></i></a></li>';
		if(total_pages > 1)
		  pagination_lbl += '<li '+(current_page==total_pages?'class="active"':'')+'><a href="javascript:void(0)"  data-page="'+total_pages+'">'+total_pages+'</a></li>';
		pagination_lbl += '</ul>';
		$("#tk-balances-pager").html(pagination_lbl);
		
		loading = false;
	  }
	});
  }
  
  /* End Balance */
  
  /* Leave Table */
  
  $('.input-daterange').datepicker({autoclose:true, format: "yyyy-mm-dd"});
  
  $('textarea.limited').inputlimiter({
	remText: '%n character%s remaining...',
	limitText: 'Max allowed : %n.',
	lineReturnCount: 2
  });

  $.validator.addMethod("needMedCert", function(v, e) {
    v = $.trim(v);
    return (v != "" && $("#lv-type option:selected").data("mc") == 1) || $("#lv-type option:selected").data("mc") == 0
  }, $.validator.format("Medical Certificate is required."));
  
  $.validator.addMethod("LvNotInclusive", function() {
	   lv_from = new Date($("#lv-date-from").val());
	   lv_to = new Date($("#lv-date-to").val());
	   dinner_event = new Date('2015-12-16');
	   
	   if(lv_from < dinner_event && lv_to > dinner_event){
		   return false;
	   } else {
		   return true;
	   }
	   
	}, "Cannot file leave on Dec 16, 2015.");
	
	$.validator.addMethod("lvEqualEvent", function(v,e) {

	   return v != '2015-12-16';
	   
	}, "Cannot file leave on Dec 16, 2015.");
  
  $("#requestForm").validate({
    errorElement: 'div',
	errorClass: 'help-block',
	focusInvalid: false,
	rules: {
			"lv-type": {
				required: true
			},
			"lv-date-from": {
				required: true,
				lvEqualEvent: true
			},
            "lv-date-to": {
				required: true,
				lvEqualEvent: true,
				LvNotInclusive: true
			},
			"reason": {
				required: true
			},
			"lv-mc": {
				needMedCert: true
			}
	},
	messages: {
			"lv-type": {
				required: "Please select Leave Type"
			},
			"lv-date-from": {
				required: "Please select Date From"
			},
			"lv-date-to": {
				required: "Please select Date To"
			},
			"reason": {
				required: "Please specify the reason for filing Leave"
			}
						
	},
	highlight: function (e) {
		$(e).closest('.form-group').removeClass('has-info').addClass('has-error');
		
	},
	success: function (e) {
		$(e).closest('.form-group').removeClass('has-error');
		$(e).remove();
	},
	submitHandler: function (form) {
	},
	invalidHandler: function (form) {
	}
  });

  $('div#request-modal')
  .modal({
	backdrop: 'static',
	show: false
  })
  .off('show.bs.modal')
  .on('show.bs.modal', function () {
  });
  
  $("#add-leave-btn").click(function(e) {
    e.preventDefault();
	$("div.alert","#request-modal").addClass("hidden"); 
    if($("#max_leave").val() <= $("#total_pending").val()) {
	  $("#message","div#messsage-modal").html("Still have "+$("#total_pending").val()+" pending request!");
	  $('div#messsage-modal').modal('show');
	  return false;
	}
	$("input, textarea, select",'div#request-modal').removeAttr("disabled").val("");
    $("#request-save",'div#request-modal').removeAttr("disabled").removeClass("hidden");
	$("#approval_header",'div#request-modal').addClass("hidden");
	$("#approval_list",'div#request-modal').addClass("hidden");
    $.post(
	  base_url + "leave/getLeaveTypePerEmployee",
	  {year: $('#tk-year').val()},
	  function(response) {
	    $("#lv-date-from").datepicker("setDate","").datepicker("update");
		$("#lv-date-to").datepicker("setDate","").datepicker("update");
	    $("#lv-type").html("");
	    $.each(response.data, function(i,v) {
		  var option_html = "";
		  if(v.subs.length) {
			option_html += "<option value='"+v.leave_id+"' disabled data-desc='"+v.leave_desc+"' data-mc='"+v.req_mc+"'>"+v.leave_code+" - "+v.leave_name+"</option>";
			$.each(v.subs, function(a,b) {
			  option_html += "<option value='"+v.leave_id+"' sub-id='"+b.sub_categ_id+"' data-desc='"+v.leave_desc+"' data-mc='"+b.req_mc+"'>&nbsp;&nbsp;&nbsp;"+b.sub_categ_code+" - "+b.sub_categ_name+"</option>";
			});
		  }
		  else {
		    option_html += "<option value='"+v.leave_id+"' data-desc='"+v.leave_desc+"' data-mc='"+v.req_mc+"'>"+v.leave_code+" - "+v.leave_name+"</option>";
		  }
		  $("#lv-type").append(option_html);
		  $("#lv-type").off("click").on("click", function() {
		    $("#lv-desc").html($("#lv-type option:selected").data("desc")).parent().parent().removeClass("hidden");
			if($("#lv-type option:selected").data("mc") == 1)
			  $("#mc-row").removeClass("hidden");
			else {
			  $("#mc-row").addClass("hidden");
			  $("#lv-mc").val("");
			}
		  });
		  $("#lv-type").trigger("click");
		});
	  },
	  "json"
	);
	$('div#request-modal').modal('show');
  });

  $("#request-save").click(function(e){
    if(!$("#requestForm").valid()) return false;
	$("div.alert","#request-modal").addClass("hidden"); 
	$("#request-save").attr("disabled",true);
	$("#request-save").html('<i class="ace-icon fa fa-refresh fa-spin bigger-110"></i> Saving...');
	$("#lv-sub-type","#requestForm").val($("#lv-type option:selected","#requestForm").attr("sub-id"));
	$.post(
	  base_url + "leave/saveLeaveApplication",
	  $("#requestForm").serialize(),
	  function(response) {
	    if(response.success) {
		  $(".success-msg","#request-modal").html(response.msg);
		  $("div.alert-success","#request-modal").removeClass("hidden");
		  getRequests(1);
		  getBalance(1);
		  $('div#request-modal').modal('hide');
	    }
	    else {
		  $(".err-msg","#request-modal").html(response.msg);
		  $("div.alert-danger","#request-modal").removeClass("hidden");
		  $("#request-save").removeAttr("disabled");
	    }
	    $("#request-save").html('<i class="ace-icon fa fa-save bigger-110"></i> Confirm');
		
	  },
	  "json"
    );
  });
  
  $("#tk-year").off("change").change(function(e) {
    e.preventDefault();
	getRequests(1);  
	getBalance(1);
  });
  
  getRequests(1);
  
  function getRequests(page_num) {
	if(req_loading){
	  return false;
	}
	$("#tk-leave-loader").removeClass("hidden");
	$("#tk-leave-no-record").addClass("hidden");
	req_schedules_tbl.addClass("hidden");
	req_loading = true;
	current_page = page_num;
    $.ajax({
	  url: base_url+"leave/getEmpLeavesGrid",
	  data: {limit: limit_default, page: page_num, year: $('#tk-year').val()},
	  cache: false,
	  dataType: "json",
	  type: "post",
	  success: function(response){
	    $("#total_pending").val(response.pending_count);
	    $("#tk-leave-loader").addClass("hidden");
		if(response.total_count) {
		  $("#tk-leave-no-record").addClass("hidden");
		  req_schedules_tbl.removeClass("hidden");
	      req_schedules_tbl.handsontable({
			colHeaders: response.header,
			colWidths: response.width,
			data: response.data,
			cells: function (row, col, prop) {
			  var cellProperties = {};
			  cellProperties.readOnly = true;
			  cellProperties.className = "htCenter";
			  if(prop == "org_shift")
			    cellProperties.renderer = "html";
			  if(prop == "action")
			    cellProperties.renderer = "html";
			  return cellProperties;
			},
			onSelection: function (r, c, r2, c2) {
			  // console.log(this.view.settings);
			  /*if (this.view.settings.columns[c].readOnly) {
				setTimeout(function(){$("#tk-leave-no-record").handsontable('deselectCell');},100);
			  }*/
			}
		  });
		}
		else {
		  $("#tk-leave-no-record").removeClass("hidden");
		  req_schedules_tbl.addClass("hidden");
		}
		
		var total_count = response.total_count;
		var total_pages = Math.ceil(total_count/limit_default);
		var current_page = response.page;
		var pagination_limit = 9;
		var max_page = pagination_limit;
	    if(pagination_limit > total_pages)
		  max_page = total_pages;
		
		var pagination_lbl = '<ul class="pagination">';
		if(total_pages > 1)
		  pagination_lbl += '<li '+(current_page==1?'class="active"':'')+'><a href="javascript:void(0)" data-page="1">1</a></li>';
		if(current_page > 5) {
		  if(total_pages > pagination_limit)
		    pagination_lbl += '<li class="disabled"><a href="javascript:void(0)"><i class="ace-icon fa fa-ellipsis-h"></i></a></li>';
		  var initial = ctr = (current_page - 3>total_pages-pagination_limit+2?total_pages-pagination_limit+2:current_page - 3);
		  var last = initial+pagination_limit-2;
		  if(last > total_pages)
		    last = total_pages;
		  for(;ctr<last;ctr++){
		    pagination_lbl += '<li '+(current_page==ctr?'class="active"':'')+'><a href="javascript:void(0)" data-page="'+ctr+'">'+ctr+'</a></li>';
		  }
		}
		else {
		  for(var ctr=2;ctr<max_page;ctr++){
		    pagination_lbl += '<li '+(current_page==ctr?'class="active"':'')+'><a href="javascript:void(0)" data-page="'+ctr+'">'+ctr+'</a></li>';
		  }
		}
		if(current_page < total_pages - 4 && total_pages > pagination_limit)
		  pagination_lbl += '<li class="disabled"><a href="javascript:void(0)"><i class="ace-icon fa fa-ellipsis-h"></i></a></li>';
		if(total_pages > 1)
		  pagination_lbl += '<li '+(current_page==total_pages?'class="active"':'')+'><a href="javascript:void(0)"  data-page="'+total_pages+'">'+total_pages+'</a></li>';
		pagination_lbl += '</ul>';
		$("#tk-leave-pager").html(pagination_lbl);
		req_loading = false;
	  }
	});
  }
  
  $(document).off("click",".request-view").on("click",".request-view",function(e) {
    e.preventDefault();
	$("div.alert","#request-modal").addClass("hidden"); 
	$("input, textarea, select",'div#request-modal').val("");
    $("#request-save",'div#request-modal').removeAttr("disabled");
	
	$.post(
	  base_url + "leave/getLeaveApplication",
	  {request_id: $(this).data("id")},
	  function(response){
	  
	    $("#request-id").val(response.data[0].lv_app_id);
		$("#lv-type").html("<option value='"+response.data[0].leave_id+"'>"+response.data[0].leave_code+" - "+response.data[0].leave_name+"</option>").prop("disabled",true);
		$("#lv-desc").html(response.data[0].leave_desc).parent().parent().removeClass("hidden");
		$("#lv-date-from").datepicker("setDate",response.data[0].date_from).attr("disabled",true);
		$("#lv-date-to").datepicker("setDate",response.data[0].date_to).attr("disabled",true);
		$("#reason").val(response.data[0].reason).attr("disabled",true);
		if(response.data[0].control_id != "" && response.data[0].control_id != null) {
		  $("#lv-mc").val(response.data[0].control_id).attr("disabled",true);
		  $("#mc-row").removeClass("hidden");
		}
		else {
		  $("#lv-mc").val("").attr("disabled",true);
		  $("#mc-row").addClass("hidden");
		}
		
		if(response.remarks.length) {
		  $("#approval_list").html("");
		  $.each(response.remarks, function(i,v){
			var escaped = $("<span/>").text(v.remarks).html();
			v.remarks = escaped;
			switch(v.status*1) {
			  case 1:
				$("#approval_list").append("<div class='well well-sm alert-warning'><b><i>"+v.created_datetime+"</i></b><br/>"+(v.remarks.length?v.remarks+" - ":"")+v.mb_nick+"</div>");
				break;
			  case 2:
				$("#approval_list").append("<div class='well well-sm alert-danger'><b><i>"+v.created_datetime+"</i></b><br/>"+(v.remarks.length?v.remarks+" - ":"")+v.mb_nick+"</div>");
				break;
			  case 3:
				$("#approval_list").append("<div class='well well-sm alert-success'><b><i>"+v.created_datetime+"</i></b><br/>"+(v.remarks.length?v.remarks+" - ":"")+v.mb_nick+"</div>");
				break;
			  case 4:
				$("#approval_list").append("<div class='well well-sm'><b><i>"+v.created_datetime+"</i></b><br/>"+(v.remarks.length?v.remarks+" - ":"")+v.mb_nick+"</div>");
				break;
			}
		  });
		}
		else {
		  $("#approval_list").html("None");
		}
		
		$("#approval_header",'div#request-modal').removeClass("hidden");
	    $("#approval_list",'div#request-modal').removeClass("hidden");
	  },
	  'json'
	);
	$("#request-save",'div#request-modal').addClass("hidden");
	$('div#request-modal').modal('show');
  });
  
  $(document).off("click",".request-remove").on("click",".request-remove",function(e) {
    e.preventDefault();
	$('div#action-modal').modal('show');
	$(".modal-action-btn").data("id",$(this).data("id")).data("action","delete");
	$("#request-action").html("delete");
	$("#request_lbl").html($(this).data("id"));
  });
  
  $(document).off("click",".request-cancel").on("click",".request-cancel",function(e) {
    e.preventDefault();
	$("#remarks-div").removeClass("hidden").removeClass("has-error");
	$('div#action-modal').modal('show');
	$(".modal-action-btn").data("id",$(this).data("id")).data("action","cancel");
	$("#request-action").html("cancel");
	$("#request_lbl").html($(this).data("id"));
  });
  
  $(".modal-action-btn", "#action-modal").click(function(e) {
    e.preventDefault();
	
	if($(this).data("action") == "delete") {
		$(".btn-success", "#action-modal").hide();
		$(".btn-danger", "#action-modal").hide();
		$.post(
			base_url + "leave/deleteLeaveApplication",
			{request_id: $('button.modal-action-btn',"div#action-modal").data("id")},
			function(response) {
				$('div#action-modal').modal('hide'); 
				getRequests(1);
				getBalance(1);
			},
			"json"
		);
	} else if($(this).data("action") == "cancel") {
		var remarks = $.trim($("#leave-remarks").val());
		if(remarks == "") {
			$("#remarks-div").addClass("has-error");
			return;
		}
		$(".btn-success", "#action-modal").hide();
		$(".btn-danger", "#action-modal").hide();
		$.post(
			base_url + "leave/cancelLeaveApplication",
			{request_id: $('button.modal-action-btn',"div#action-modal").data("id"), remarks: $("#leave-remarks").val()},
			function(response) {
				$('div#action-modal').modal('hide'); 
				$("#leave-remarks").val("");
				getRequests(1);
				getBalance(1);
			},
			"json"
		);
	}
  });
  
  /* End Leave Table */
  
});