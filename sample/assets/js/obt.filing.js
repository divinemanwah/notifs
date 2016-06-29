$(function () {
  var req_schedules_tbl = $("#tk-obt-table");
  var limit_default = 25;
  var req_loading = false;
  var current_page = 1;

  $('.timepicker','div#request-modal').mask('99:99');
  
  /* OBT Table */

  $('.input-daterange').datepicker({autoclose:true, format: "yyyy-mm-dd", startDate: new Date()});
  
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
			"obt-date": {
				required: true
			},
			"obt-time-from": {
				required: true
			},
			"obt-time-to": {
				required: true
			},
			"reason": {
				required: true
			}
	},
	messages: {
			"obt-date": {
				required: "Please select Date"
			},
			"obt-time-from": {
				required: "Please set Time From"
			},
			"obt-time-to": {
				required: "Please set Time From"
			},
			"reason": {
				required: "Please specify the reason for filing OBT"
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
  
  $("#add-obt-btn").off("click").click(function(e) {
    e.preventDefault();
	$("input, textarea, select",'div#request-modal').removeAttr("disabled").val("");
    $("#request-save",'div#request-modal').removeAttr("disabled").removeClass("hidden");
	$("#approval_header",'div#request-modal').addClass("hidden");
	$("#approval_list",'div#request-modal').addClass("hidden");
    $('.timepicker').val("");
	$("#obt-date").datepicker("setDate","").datepicker("update");
	$('div#request-modal').modal('show');
  });

  $("#request-save").off("click").click(function(e){
    if(!$("#requestForm").valid()) return false;
	$("#request-save").attr("disabled",true);
	$("#request-save").html('<i class="ace-icon fa fa-refresh fa-spin bigger-110"></i> Saving...');
	$("#obt-sub-type","#requestForm").val($("#obt-type option:selected","#requestForm").attr("sub-id"));
	$.post(
	  base_url + "obt/saveOBTApplication",
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
		
		  setTimeout(function(){ $("div.alert-danger","#request-modal").addClass("hidden"); },3000);
		  $("#request-save").removeAttr("disabled");
	    }
	  
	    $("#request-save").html('<i class="ace-icon fa fa-save bigger-110"></i> Confirm');
	  },
	  "json"
    );
  });
  
  getRequests(1);
  
  function getRequests(page_num) {
	if(req_loading){
	  return false;
	}
	$("#tk-obt-loader").removeClass("hidden");
	$("#tk-obt-no-record").addClass("hidden");
	req_schedules_tbl.addClass("hidden");
	req_loading = true;
	current_page = page_num;
    $.ajax({
	  url: base_url+"obt/getEmpOBTGrid",
	  data: {limit: limit_default, page: page_num, dateFrom: $('#date-from').val(), dateTo: $('#date-to').val()},
	  cache: false,
	  dataType: "json",
	  type: "post",
	  success: function(response){
	    $("#tk-obt-loader").addClass("hidden");
		if(response.total_count) {
		  $("#tk-obt-no-record").addClass("hidden");
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
			  if (this.view.settings.columns[c].readOnly) {
				setTimeout(function(){$("#tk-obt-no-record").handsontable('deselectCell');},100);
			  }
			}
		  });
		}
		else {
		  $("#tk-obt-no-record").removeClass("hidden");
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
		$("#tk-obt-pager").html(pagination_lbl);
		req_loading = false;
	  }
	});
  }
  
  $(document).off("click",".request-view").on("click",".request-view",function(e) {
    e.preventDefault();
	$("input, textarea, select",'div#request-modal').val("");
    $("#request-save",'div#request-modal').removeAttr("disabled");
	
	$.post(
	  base_url + "obt/getOBTApplication",
	  {request_id: $(this).data("id")},
	  function(response){
	    $("#request-id").val(response.data[0].obt_app_id);
		$("#obt-date").val(response.data[0].date).attr("disabled",true);
		$("#obt-time-from").val(response.data[0].time_in).attr("disabled",true);
		$("#obt-time-to").val(response.data[0].time_out).attr("disabled",true);
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
	$("#remarks-div").removeClass("hidden").removeClass("has-error");
	$('div#action-modal').modal('show');
	$(".modal-action-btn").data("id",$(this).data("id")).data("action","cancel");
	$("#request-action").html("cancel");
	$("#request_lbl").html($(this).data("id"));
  });
 
  $(".modal-action-btn", "#action-modal").off("click").click(function(e) {
    e.preventDefault();
	if($(this).data("action") == "delete") {
      $.post(
	    base_url + "obt/deleteOBTApplication",
	    {request_id: $('button.modal-action-btn',"div#action-modal").data("id")},
	    function(response) {
		  $('div#action-modal').modal('hide'); 
		  getRequests(1);
	    },
	    "json"
      );
	} else if($(this).data("action") == "cancel") {
	  var remarks = $.trim($("#cancel-remarks").val());
	  if(remarks == "") {
	    $("#remarks-div").addClass("has-error");
		return;
	  }
	  
      $.post(
	    base_url + "obt/cancelOBTApplication",
	    {request_id: $('button.modal-action-btn',"div#action-modal").data("id"), remarks: $("#cancel-remarks").val()},
	    function(response) {
		  $('div#action-modal').modal('hide'); 
		  $("#cancel-remarks").val("");
		  getRequests(1);
	    },
	    "json"
      );
	}
  });
  
  /* End Leave Table */
  
});