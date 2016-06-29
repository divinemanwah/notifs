$(function () {
  var schedules_tbl = $("#tk-schedules-table");
  var req_schedules_tbl = $("#tk-req-table");
  var limit_default = 25;
  var shift_loading = req_loading = loading = false;
  var req_current_page = current_page = 1;
  var today = new Date();
  today.setDate(today.getDate() + 4);
  
  $('.input-daterange').datepicker({autoclose:true, format: "yyyy-mm-dd"});
  
  $('#date-from').off("change").change(function(e){
    e.preventDefault();
	if($('#date-to').val() != ""){
	  getSchedules(1);
	}
  });
  
  $('#date-to').off("change").change(function(e){
    e.preventDefault();
	if($('#date-from').val() != ""){
	  getSchedules(1);
	}
  });
  
  /* Schedules */
  $(document).off("click","div#tk-schedules-pager ul.pagination li a").on("click","div#tk-schedules-pager ul.pagination li a", function(e){
    e.preventDefault();
	getSchedules($(this).data("page"));
  });
  
  getSchedules(1);
  
  function getSchedules(page_num) {
	if(loading){
	  return false;
	}
	$("#tk-table-loader").removeClass("hidden");
	schedules_tbl.addClass("hidden");
	loading = true;
	current_page = page_num;
    $.ajax({
	  url: base_url+"timekeeping/getEmpSchedule",
	  data: {limit: limit_default, page: page_num, dateFrom: $('#date-from').val(), dateTo: $('#date-to').val()},
	  cache: false,
	  dataType: "json",
	  type: "post",
	  success: function(response){
	    $("#tk-table-loader").addClass("hidden");
		schedules_tbl.removeClass("hidden");
	    schedules_tbl.handsontable({
			colHeaders: response.header,
			colWidths: response.width,
			data: response.data,
			cells: function (row, col, prop) {
			  var cellProperties = {};
			  cellProperties.readOnly = true;
			  cellProperties.renderer = shiftCellRenderer;
			  if(col > 1)
			    cellProperties.className = "htCenter";
			  return cellProperties;
			}
		});
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
		$("#tk-schedules-pager").html(pagination_lbl);
		loading = false;
	  }
	});
  }

  function shiftCellRenderer(instance, td, row, col, prop, value, cellProperties) {
    var celldata = arguments[5].split("#");
    if(celldata.length == 2) {
	  value = celldata[0];
	  td.style.background = "#"+celldata[1];
	  td.style.color = "#000";
	}
    Handsontable.renderers.TextRenderer.apply(this, arguments);
  }

  /* End of Schedules */
  
  /* Requests */
  $('#req-date-from').off("change").change(function(e){
    e.preventDefault();
	if($('#req-date-to').val() != ""){
	  getRequests(1);
	}
  });
  
  $('#req-date-to').off("change").change(function(e){
    e.preventDefault();
	if($('#req-date-from').val() != ""){
	  getRequests(1);
	}
  });
  
  getRequests(1);
  
  function getRequests(page_num) {
	if(req_loading){
	  return false;
	}
	$("#tk-req-loader").removeClass("hidden");
	$("#tk-req-no-record").addClass("hidden");
	req_schedules_tbl.addClass("hidden");
	req_loading = true;
	current_page = page_num;
    $.ajax({
	  url: base_url+"timekeeping/getEmpChangeSchedule",
	  data: {limit: limit_default, page: page_num, dateFrom: $('#req-date-from').val(), dateTo: $('#req-date-to').val()},
	  cache: false,
	  dataType: "json",
	  type: "post",
	  success: function(response){
	    $("#tk-req-loader").addClass("hidden");
		if(response.total_count) {
		  $("#tk-req-no-record").addClass("hidden");
		  req_schedules_tbl.removeClass("hidden");
	      req_schedules_tbl.handsontable({
			colHeaders: response.header,
			data: response.data,
			cells: function (row, col, prop) {
			  var cellProperties = {};
			  cellProperties.readOnly = true;
			  cellProperties.renderer = shiftCellRenderer;
			  cellProperties.className = "htCenter";
			  if(prop == "org_shift")
			    cellProperties.renderer = "html";
			  if(prop == "action")
			    cellProperties.renderer = "html";
			  return cellProperties;
			},
			onSelection: function (r, c, r2, c2) {
			  if (this.view.settings.columns[c].readOnly) {
				setTimeout(function(){$("#tk-req-no-record").handsontable('deselectCell');},100);
			  }
			}
		  });
		}
		else {
		  $("#tk-req-no-record").removeClass("hidden");
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
		$("#tk-req-pager").html(pagination_lbl);
		req_loading = false;
	  }
	});
  }
  
  $('div#request-modal')
  .modal({
	backdrop: 'static',
	show: false
  })
  .off('show.bs.modal')
  .on('show.bs.modal', function () {
  });
  
  $("#add-request-btn").off("click").click(function(e) {
	$("input, textarea, select",'div#request-modal').removeAttr("disabled").val("");
	$("#shift-str",'div#request-modal').html("[N/A] - N/A");
    $("#request-save",'div#request-modal').removeAttr("disabled")
	$("#request-save",'div#request-modal').removeClass("hidden");
	$("#approval_header",'div#request-modal').addClass("hidden");
	$("#approval_list",'div#request-modal').addClass("hidden");
	$("#att-date-from").datepicker("setDate","").datepicker("update");
	$("#att-date-to").datepicker("setDate","").datepicker("update");
	$('div#request-modal').modal('show');
  });
  
  /* End of Requests */  
  
  /* FORM */
  $('textarea.limited').inputlimiter({
	remText: '%n character%s remaining...',
	limitText: 'Max allowed : %n.',
	lineReturnCount: 2
  });
  
  $("#requestForm").validate({
    errorElement: 'div',
	errorClass: 'help-block',
	focusInvalid: false,
	rules: {
			"att-date-from": {
				required: true
			},
			"new-shift": {
			    required: true
			},
            "att-date-to": {
				required: true
			},
			"reason": {
				required: true
			}
	},
	messages: {
			"att-date-from": {
				required: "Please select Date From"
			},
			"new-shift": {
			    required: "Please select New Shift"
			},
			"att-date-to": {
				required: "Please select Date To"
			},
			"reason": {
				required: "Please specify the reason for Change of Shift"
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
  
  $("#request-save").off("click").click(function(e){
    if(!$("#requestForm").valid()) return false;
	if($("#orig-shift-ids-str").val() == "" || $("#orig-shift-ids-str").val().indexOf("-10") > -1) {
		$(".err-msg","#request-modal").html("Cannot file CWS have N/A shift.");
		$("div.alert-danger","#request-modal").removeClass("hidden");
		return false;
	}
	
	$("#request-save").attr("disabled",true);
	$("#request-save").html('<i class="ace-icon fa fa-refresh fa-spin bigger-110"></i> Saving...');
	
	$.post(
	  base_url + "timekeeping/saveChangeShift",
	  $("#requestForm").serialize(),
	  function(response) {
	    if(response.success) {
		  $(".success-msg","#request-modal").html(response.msg);
		  $("div.alert-success","#request-modal").removeClass("hidden");
		
		  setTimeout(function(){
		    $("div.alert-success","#request-modal").addClass("hidden"); 
		    $('div#request-modal').modal('hide'); 
		  },1000);
		  getRequests(1);
	    }
	    else {
		  $(".err-msg","#request-modal").html(response.msg);
		  $("div.alert-danger","#request-modal").removeClass("hidden");
		
		  setTimeout(function(){ $("div.alert-danger","#apprv-grps-modal").addClass("hidden"); },1000);
		  $("#request-save").removeAttr("disabled");
	    }
	  
	    $("#request-save").html('<i class="ace-icon fa fa-save bigger-110"></i> Confirm');
	  },
	  "json"
    );
  });
  
  $('#att-date-from').off("change").change(function(e){
    e.preventDefault();
	if($('#att-date-to').val() != "" && $('#att-date-to').val() >= $('#att-date-from').val()){
	  if(!shift_loading)
	    getEmpShifts();
	}
  });
  
  $('#att-date-to').off("change").change(function(e){
    e.preventDefault();
	if($('#att-date-from').val() != "" != "" && $('#att-date-to').val() >= $('#att-date-from').val()){
	  if(!shift_loading)
	    getEmpShifts();
	}
  });
  
  $(document).off("click",".request-view").on("click",".request-view",function(e) {
    e.preventDefault();
	$("input, textarea, select",'div#request-modal').val("");
	$("#shift-str",'div#request-modal').html("[N/A] - N/A");
    $("#request-save",'div#request-modal').removeAttr("disabled");
	
	$.post(
	  base_url + "timekeeping/getChangeShift",
	  {request_id: $(this).data("id")},
	  function(response){
		$("#request-id").val(response.data[0].cs_req_id);
		$("#att-date-from").val(response.data[0].att_date_from).attr("disabled",true);
		$("#att-date-to").val(response.data[0].att_date_to).attr("disabled",true);
		$("#shift-str").html(response.data[0].orig_shift);
		$("#orig-shift-str").val(response.data[0].orig_shift);
		$("#orig-shift-ids-str").val(response.data[0].orig_shift_ids);
		$("#new-shift").val(response.data[0].proposed_shift_id).attr("disabled",true);
		$("#reason").val(response.data[0].reason).attr("disabled",true);
		
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
	$('div#action-modal').modal('show');
	$(".modal-action-btn").data("id",$(this).data("id")).data("action","cancel");
	$("#request-action").html("cancel");
	$("#request_lbl").html($(this).data("id"));
  });
  
  $(".modal-action-btn", "#action-modal").off("click").click(function(e) {
    e.preventDefault();
	if($(this).data("action") == "delete") {
      $.post(
	    base_url + "timekeeping/deleteChangeShift",
	    {request_id: $('button.modal-action-btn',"div#action-modal").data("id")},
	    function(response) {
		  $('div#action-modal').modal('hide'); 
		  getRequests(1);
	    },
	    "json"
      );
	} else if($(this).data("action") == "cancel") {
	  $.post(
	    base_url + "timekeeping/cancelChangeShift",
	    {request_id: $('button.modal-action-btn',"div#action-modal").data("id")},
	    function(response) {
		  $('div#action-modal').modal('hide'); 
		  getRequests(1);
	    },
	    "json"
      );
	}
  });
  
  function getEmpShifts() {
    shift_loading = true;
    $.ajax({
	  url: base_url+"timekeeping/getEmpShift",
	  data: { dateFrom: $('#att-date-from').val(), dateTo: $('#att-date-to').val() },
	  cache: false,
	  dataType: "json",
	  type: "post",
	  success: function(response) {
		var shift_ids_str = shifts_str = "";
		$.each(response.data,function(i,v){
		  if(shifts_str!="") {
		    shifts_str += "<br/>"+v.shift_time+" "+v.shift_code;
			shift_ids_str += ","+v.shift_id;
		  }
		  else {
		    shifts_str += v.shift_time+" "+v.shift_code;
			shift_ids_str += v.shift_id;
		  }
		});
	    $("#shift-str").html(shifts_str);
		$("#orig-shift-str").val(shifts_str);
		$("#orig-shift-ids-str").val(shift_ids_str);
		shift_loading = false;
	  }
	});
  }
  
  /* End of FORM */
});