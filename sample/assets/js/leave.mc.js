$(function () {

  var req_schedules_tbl = $("#tk-mc-table");
  var limit_default = 25;
  var req_loading = false;
  var current_page = 1;
  
  $(document).off("click","div#tk-mc-pager ul.pagination li a").on("click","div#tk-mc-pager ul.pagination li a", function(e){
    e.preventDefault();
	getRequests($(this).data("page"));
  });
  
  $('.chosen-select','div#request-modal').chosen({allow_single_deselect:true, width: "100%"}); 
  $('.chosen-select','select#tk-filter-emp').chosen({allow_single_deselect:true, width: "100%"}); 
  $('.chosen-select').trigger("chosen:updated");
  
  $('.input-date').datepicker({autoclose:true, format: "yyyy-mm-dd"});
  
  $("#requestForm").validate({
    errorElement: 'div',
	errorClass: 'help-block',
	focusInvalid: false,
	rules: {
			"emp-no": {
				required: true
			},
			"date-submitted": {
				required: true
			}
	},
	messages: {
			"emp-no": {
				required: "Please select Employee"
			},
			"date-submitted": {
				required: "Please select Date Submitted"
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
    $(".chosen-container").css("width","100%");
  });
  
  $("#add-mc-btn").off("click").click(function(e) {
    e.preventDefault();
	$("div.alert","#request-modal").addClass("hidden"); 
	$("input, textarea, select",'div#request-modal').removeAttr("disabled").val("");
    $("#request-save",'div#request-modal').removeAttr("disabled").removeClass("hidden");
	$("#date-submitted").datepicker("setDate","").datepicker("update");
	$('div#request-modal').modal('show');
  });

  $("#request-save").off("click").click(function(e){
    if(!$("#requestForm").valid()) return false;
	$("div.alert","#request-modal").addClass("hidden"); 
	$("#request-save").attr("disabled",true);
	$("#request-save").html('<i class="ace-icon fa fa-refresh fa-spin bigger-110"></i> Saving...');
	$.post(
	  base_url + "leave/saveMC",
	  $("#requestForm").serialize(),
	  function(response) {
	    if(response.success) {
		  $(".success-msg","#request-modal").html(response.msg);
		  $("div.alert-success","#request-modal").removeClass("hidden");
		  getRequests(1);
		  $("#control-no").val(response.mc_no);
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
  
  getRequests(1);
  
  function getRequests(page_num) {
	if(req_loading){
	  return false;
	}
	$("#tk-mc-loader").removeClass("hidden");
	$("#tk-mc-no-record").addClass("hidden");
	req_schedules_tbl.addClass("hidden");
	req_loading = true;
	current_page = page_num;
    $.ajax({
	  url: base_url+"leave/getAllMC",
	  data: {limit: limit_default, page: page_num, dateFrom: $('#date-from').val(), dateTo: $('#date-to').val(), mb_no: $("#tk-filter-emp").val()},
	  cache: false,
	  dataType: "json",
	  type: "post",
	  success: function(response){
	    $("#tk-mc-loader").addClass("hidden");
		if(response.total_count) {
		  $("#tk-mc-no-record").addClass("hidden");
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
			}
		  });
		}
		else {
		  $("#tk-mc-no-record").removeClass("hidden");
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
		$("#tk-mc-pager").html(pagination_lbl);
		req_loading = false;
	  }
	});
  }
  
  $(document).off("click",".request-edit").on("click",".request-edit",function(e) {
    e.preventDefault();
	$("input, textarea, select",'div#request-modal').removeAttr("disabled").val("");
    $("#request-save",'div#request-modal').removeAttr("disabled");
	
	$.post(
	  base_url + "leave/getMC",
	  {request_id: $(this).data("id")},
	  function(response){
		$("#request-id").val(response.data[0].mc_id);
		$("#control-no").val(response.data[0].control_id);
		$("#emp-no").val(response.data[0].mb_no);
		$("#notes").val(response.data[0].remarks);
		$("#date-submitted").datepicker("setDate",new Date(response.data[0].date_submitted)).datepicker("update");
		$("#emp-no").trigger("chosen:updated");
		$(".chosen-container").css("width","100%");
	  },
	  'json'
	);
	$("#request-save",'div#request-modal').removeClass("hidden");
	$('div#request-modal').modal('show');
  });
  
  $(document).off("click",".request-view").on("click",".request-view",function(e) {
    e.preventDefault();
	$("input, textarea, select",'div#request-modal').val("");
    $("#request-save",'div#request-modal').removeAttr("disabled");
	
	$.post(
	  base_url + "leave/getMC",
	  {request_id: $(this).data("id")},
	  function(response){
	    $("#request-id").val(response.data[0].mc_id);
		$("#control-no").val(response.data[0].control_id);
		$("#emp-no").val(response.data[0].mb_no).attr("disabled","disabled");
		$("#notes").val(response.data[0].remarks).attr("disabled","disabled");
		$("#date-submitted").datepicker("setDate",new Date(response.data[0].date_submitted)).attr("disabled","disabled");
		$("#emp-no").trigger("chosen:updated");
		$(".chosen-container").css("width","100%");
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
  
  $(".modal-action-btn", "#action-modal").off("click").click(function(e) {
    e.preventDefault();
	if($(this).data("action") == "delete") {
      $.post(
	    base_url + "leave/deleteMC",
	    {request_id: $('button.modal-action-btn',"div#action-modal").data("id")},
	    function(response) {
		  $('div#action-modal').modal('hide'); 
		  getRequests(1);
	    },
	    "json"
      );
	}
  });
  
  /* End Leave Table */
  
});