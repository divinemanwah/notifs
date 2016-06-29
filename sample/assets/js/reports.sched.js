$(function () {
  /* Balance */
  var req_schedules_tbl = $("#tk-sched-table");
  var limit_default = 25;
  var req_loading = false;
  var current_page = 1;

  $("#tk-search-btn").click(function(e) {
    e.preventDefault();
	getRequests(1);
  });
  
  $(document).on("click","div#tk-sched-pager ul.pagination li a", function(e){
    e.preventDefault();
	getRequests($(this).data("page"));
  });

  $('.chosen-select').chosen({allow_single_deselect:true, width: "100%"}); 
  $('.chosen-select').trigger("chosen:updated");

  /* Overtime Table */
  
  $('.input-daterange').datepicker({autoclose:true, format: "yyyy-mm-dd"});
  
  $('ul#tk-filter-dept a').click(function (e) {
	e.preventDefault();
	$('ul#tk-filter-dept li').removeClass('active');
	$(this).parent().addClass('active');
	
	$('button#tk-filter-dept-btn').html('\
	  <i class="ace-icon fa fa-filter"></i> Department: ' + $(this).text() + '\
	  <i class="ace-icon fa fa-chevron-down icon-on-right"></i>\
	');
		
	if($(this).data('id'))
	  $('button#tk-filter-dept-btn').data('id', $(this).data('id'));
	else
	  $('button#tk-filter-dept-btn').removeData('id');
  });
  
  $('ul#tk-filter-status a').click(function (e) {
	e.preventDefault();
	$('ul#tk-filter-status li').removeClass('active');
	$(this).parent().addClass('active');
	
	$('button#tk-filter-status-btn').html('\
	  <i class="ace-icon fa fa-filter"></i> Status: ' + $(this).text() + '\
	  <i class="ace-icon fa fa-chevron-down icon-on-right"></i>\
	');
		
	if($(this).data('id'))
	  $('button#tk-filter-status-btn').data('id', $(this).data('id'));
	else
	  $('button#tk-filter-status-btn').removeData('id');
  });
  
  function getRequests(page_num) {
	if(req_loading){
	  return false;
	}
	$("#tk-sched-loader").removeClass("hidden");
	$("#tk-sched-no-record").addClass("hidden");
	req_schedules_tbl.addClass("hidden");
	req_loading = true;
	current_page = page_num;
    $.ajax({
	  url: base_url+"reports/getEmpschedGrid",
	  data: {limit: limit_default, page: page_num, status: $('button#tk-filter-status-btn').data('id'), department: $('button#tk-filter-dept-btn').data('id'), emp: $('select#tk-filter-emp').val(),dateFrom: $('#date-from').val(), dateTo: $('#date-to').val()},
	  cache: false,
	  dataType: "json",
	  type: "post",
	  success: function(response){
	    $("#total_pending").val(response.pending_count);
	    $("#tk-sched-loader").addClass("hidden");
		if(response.total_count) {
		  $("#tk-sched-no-record").addClass("hidden");
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
			  
			}
		  });
		}
		else {
		  $("#tk-sched-no-record").removeClass("hidden");
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
		$("#tk-sched-pager").html(pagination_lbl);
		req_loading = false;
	  }
	});
  }
  
  $(document).off("click",".request-view").on("click",".request-view",function(e) {
    e.preventDefault();
	$("input, textarea, select",'div#request-modal').val("");
    $("#request-save",'div#request-modal').removeAttr("disabled");
	
	$.post(
	  base_url + "timekeeping/getUploadSchedHistory",
	  {id: $(this).data("id")},
	  function(response){
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
	  },
	  'json'
	);
	$("#request-save",'div#request-modal').addClass("hidden");
	$('div#request-modal').modal('show');
  });
 
  /* End Overtime Table */
  
});