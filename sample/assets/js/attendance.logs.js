$(function () {
  var logs_tbl = $("#tk-logs-table");
  var limit_default = 25;
  var loading = false;
  var current_page = 1;
  
  $("#tk-search-btn").click(function(e) {
    e.preventDefault();
	getAllLogs(1);
  });
  
  $('.chosen-select').chosen({allow_single_deselect:true, width: "100%"}); 
  $('.chosen-select').trigger("chosen:updated");
  
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
	getAllLogs(1);
  });
  
  $('.input-daterange').datepicker({autoclose:true, format: "yyyy-mm-dd"});

  
  $(document).on("click","div#tk-logs-pager ul.pagination li a", function(e){
    e.preventDefault();
	getAllLogs($(this).data("page"));
  });
  
  getAllLogs(1);
  
  $(document).on("submit","#exportForm",function(e) {
    $("input[name='export-dept']").val($('button#tk-filter-dept-btn').data('id'));
	$("input[name='export-emp']").val($('select#tk-filter-emp').val());
	$("input[name='export-from']").val($('#date-from').val());
	$("input[name='export-to']").val($('#date-to').val());
  });
  
  
  function getAllLogs(page_num) {
	if(loading){
	  return false;
	}
	$("#tk-table-loader").removeClass("hidden");
	$("#tk-table-no-record").addClass("hidden");
	logs_tbl.addClass("hidden");
	loading = true;
	current_page = page_num;
    $.ajax({
	  url: base_url+"attendance/getAllLogs",
	  data: {limit: limit_default, page: page_num, department: $('button#tk-filter-dept-btn').data('id'), emp: $('select#tk-filter-emp').val(), dateFrom: $('#date-from').val(), dateTo: $('#date-to').val()},
	  cache: false,
	  dataType: "json",
	  type: "post",
	  success: function(response){
	    $("#tk-table-loader").addClass("hidden");
		if(response.total_count){
		  $("#tk-table-no-record").addClass("hidden");
		  logs_tbl.removeClass("hidden");
		  logs_tbl.handsontable({
		    width: "100%",
			colHeaders: response.header, 
			colWidths: response.width,
			fixedColumnsLeft: 3,
			data: response.data, 
			cells: function (row, col, prop) {
			  var cellProperties = {};
			  cellProperties.readOnly = true;
			  if(col > 1)
			    cellProperties.className = "htCenter";
			  return cellProperties;
			}
		  });
		}
		else {
		  $("#tk-table-no-record").removeClass("hidden");
		  logs_tbl.addClass("hidden");
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
		$("#tk-logs-pager").html(pagination_lbl);
		loading = false;
	  }
	});
  }
});