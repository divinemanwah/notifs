$(function () {
  var leave_tbl = $("#tk-leave-table");
  var limit_default = 25;
  var loading = false;
  var current_page = 1;
  
  $("#tk-search-btn").off("click").click(function(e) {
    e.preventDefault();
	getleave(1);
  });
  
  $('ul#tk-filter-dept a').off("click").click(function (e) {
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
  
  $('ul#tk-filter-le a').off("click").click(function (e) {
	e.preventDefault();
	$('ul#tk-filter-le li').removeClass('active');
	$(this).parent().addClass('active');
	
	$('button#tk-filter-le-btn').html('\
	  <i class="ace-icon fa fa-filter"></i> Type: ' + $(this).text() + '\
	  <i class="ace-icon fa fa-chevron-down icon-on-right"></i>\
	');
		
	if($(this).data('id'))
	  $('button#tk-filter-le-btn').data('id', $(this).data('id'));
	else
	  $('button#tk-filter-le-btn').removeData('id');
  });
  
  $('.chosen-select').chosen({allow_single_deselect:true, width: "100%"}); 
  
  $(document).off("click","div#tk-leave-pager ul.pagination li a").on("click","div#tk-leave-pager ul.pagination li a", function(e){
    e.preventDefault();
	getleave($(this).data("page"));
  });
  
  $('ul#tk-filter-emp-stat a').off("click").click(function (e) {
	e.preventDefault();
	$('ul#tk-filter-emp-stat li').removeClass('active');
	$(this).parent().addClass('active');
	
	$('button#tk-filter-emp-stat-btn').html('\
	  <i class="ace-icon fa fa-filter"></i> Employment: ' + $(this).text() + '\
	  <i class="ace-icon fa fa-chevron-down icon-on-right"></i>\
	');
		
	if($(this).data('id'))
	  $('button#tk-filter-emp-stat-btn').data('id', $(this).data('id'));
	else
	  $('button#tk-filter-emp-stat-btn').removeData('id');
  });
  
  getleave(1);
  
  $(document).off("submit","#exportForm").on("submit","#exportForm",function(e) {
    $("input[name='export-dept']").val($('button#tk-filter-dept-btn').data('id'));
	$("input[name='export-emp']").val($('select#tk-filter-emp').val());
	$("input[name='export-type']").val($('button#tk-filter-le-btn').data('id'));
	$("input[name='export-emp-stat']").val($('button#tk-filter-emp-stat-btn').data('id'));
	$("input[name='export-year']").val($('select#tk-filter-year').val());
  });
  
  function getleave(page_num) {
	if(loading){
	  return false;
	}
	$("#tk-table-loader").removeClass("hidden");
	leave_tbl.addClass("hidden");
	loading = true;
	current_page = page_num;
    $.ajax({
	  url: base_url+"leave/getAllLeaveBalances",
	  data: {limit: limit_default, page: page_num, department: $('button#tk-filter-dept-btn').data('id'), emp: $('select#tk-filter-emp').val(), type: $('button#tk-filter-le-btn').data('id'), emp_type: $('button#tk-filter-emp-stat-btn').data('id'), year: $('select#tk-filter-year').val()},
	  cache: false,
	  dataType: "json",
	  type: "post",
	  success: function(response){
	    $("#tk-table-loader").addClass("hidden");
		leave_tbl.removeClass("hidden");
	    leave_tbl.handsontable({
			colHeaders: response.header,
			fixedColumnsLeft: 3,
			data: response.data,
			cells: function (row, col, prop) {
			  var cellProperties = {};
			  cellProperties.readOnly = true;
			  //cellProperties.renderer = shiftCellRenderer;
			  if(col > 1)
			    cellProperties.className = "htCenter";
			  if(prop == "action")
			    cellProperties.renderer = "html";
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
		$("#tk-leave-pager").html(pagination_lbl);
		loading = false;
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
  
  $(document).off("click",".request-edit").on("click",".request-edit",function(e) {
    e.preventDefault();
	$("input, textarea, select",'div#request-modal').removeAttr("disabled").val("");
    $("#request-save",'div#request-modal').removeAttr("disabled").removeAttr("disabled");
	
	$.post(
	  base_url + "leave/getEmpLeaveBalances",
	  {emp_id: $(this).data("id"), year: $(this).data("year")},
	  function(response){
		$("#mb-no").val(response.emp[0].mb_no);
		$("#lv-year").val(response.year);
		$("#leave-table").html("");
		$.each(response.data, function(i,v) {
		  $("#leave-table").append("<tr>"+
		                        "<td style='vertical-align: middle; text-align: center;'>"+
								  ((v.leave_id && v.leave_code != "EL")?"<input type='hidden' name='leave_id[]' value='"+v.leave_id+"'/>":"")+
								  v.leave_code+
								"</td style='vertical-align: middle; text-align: center;'>"+
								"<td style='vertical-align: middle; text-align: center;'>"+v.leave_name+"</td>"+
								"<td style='text-align: center;'>"+
								  ((v.leave_id && v.leave_code != "EL")?"<input style='text-align: center;' class='form-control input-sm input-mini text-right input-bal' type='text' name='leave_bal[]' value='"+v.bal+"'/>":v.bal)+
								"</td>"+
								"<td style='vertical-align: middle; text-align: center;'>"+v.pending+"</td>"+
								"<td style='vertical-align: middle; text-align: center;'>"+v.allocated+"</td>"+
								"<td style='vertical-align: middle; text-align: center;'>"+v.used+"</td>"+
								"<td style='text-align: center;'>"+
								  ((v.leave_id && v.leave_code != "EL")?"<input style='text-align: center;' class='form-control input-sm input-mini text-right input-bal' type='text' name='leave_paid[]' value='"+v.paid+"'/>":v.paid)+
								"</td>"+
								"<td style='text-align: center;'>"+
								  ((v.leave_id && v.leave_code != "EL")?"<input style='text-align: center;' class='form-control input-sm input-mini text-right input-bal' type='text' name='leave_forfeit[]' value='"+v.forfeited+"'/>":v.forfeited)+
								"</td>"+
		                      "</tr>");
		});
		$("#emp-header").html((response.emp[0].mb_3=="Expat"?response.emp[0].mb_nick:response.emp[0].mb_fname)+" "+response.emp[0].mb_lname + " - " + response.year);
	  },
	  'json'
	);
	$("#request-save",'div#request-modal').removeClass("hidden");
	$('div#request-modal').modal('show');
  });
  
  $("#request-save").off("click").click(function(e){
    e.preventDefault();
	var valid = true;
	$.each($("div#request-modal input.input-bal"),function(){
	  if($(this).val() == "" || isNaN($(this).val())) {
	    $(this).parent().addClass("has-error");
	    valid = false;
	  }
	  else {
	    $(this).parent().removeClass("has-error");
	  }
	});
	
	if(!valid)
	  return false;
	
	$("#request-save").attr("disabled",true);
	$("#request-save").html('<i class="ace-icon fa fa-refresh fa-spin bigger-110"></i> Saving...');
	
	$.post(
	  base_url + "leave/saveLeaveBalance",
	  $("#requestForm").serialize(),
	  function(response) {
	    if(response.success) {
		  $(".success-msg","#request-modal").html(response.msg);
		  $("div.alert-success","#request-modal").removeClass("hidden");
		
		  setTimeout(function(){
		    $("div.alert-success","#request-modal").addClass("hidden"); 
		    $('div#request-modal').modal('hide'); 
		  },1000);
		  getleave(1);
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
 
});