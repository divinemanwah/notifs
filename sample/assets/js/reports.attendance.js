$(function () {
  var attendance_tbl = $("#tk-attendance-table");
  var limit_default = 20;
  var loading = false;
  var current_page = 1;
  
  $("#tk-search-btn").off("click").click(function(e) {
    e.preventDefault();
	getSchedules(1);
  });
 
  $('.chosen-select').chosen({allow_single_deselect:true, width: "100%"}); 
  $('.chosen-select').trigger("chosen:updated");
  
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
  
  $('ul#tk-filter-status a').off("click").click(function (e) {
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
  
  
  $('.input-daterange').datepicker({autoclose:true, format: "yyyy-mm-dd"});
  
  $(document).off("click","div#tk-attendance-pager ul.pagination li a").on("click","div#tk-attendance-pager ul.pagination li a", function(e){
    e.preventDefault();
	
	getSchedules($(this).data("page"));
  });
  
  $(document).off("submit","#exportForm").on("submit","#exportForm",function(e) {
    $("input[name='export-dept']").val($('button#tk-filter-dept-btn').data('id'));
	$("input[name='export-emp']").val($('select#tk-filter-emp').val());
	$("input[name='export-from']").val($('#date-from').val());
	$("input[name='export-to']").val($('#date-to').val());
	$("input[name='export-status']").val($('button#tk-filter-status-btn').data('id'));
	$("input[name='export-emp-stat']").val($('button#tk-filter-emp-stat-btn').data('id'));
  });
  
  function getSchedules(page_num) {
	if(loading){
	  return false;
	}
	$("#tk-attendance-loader").removeClass("hidden");
	$("#tk-attendance-no-record").addClass("hidden");
	attendance_tbl.addClass("hidden");
	loading = true;
	current_page = page_num;
    $.ajax({
	  url: base_url+"reports/getAttendanceGrid",
	  data: {limit: limit_default, page: page_num, department: $('button#tk-filter-dept-btn').data('id'), emp: $('select#tk-filter-emp').val(), dateFrom: $('#date-from').val(), dateTo: $('#date-to').val(), type: $('button#tk-filter-status-btn').data('id'), emp_type: $('button#tk-filter-emp-stat-btn').data('id')},
	  cache: false,
	  dataType: "json",
	  type: "post",
	  success: function(response){
	    $("#tk-attendance-loader").addClass("hidden");
		if(response.total_count){
		  $("#tk-attendance-no-record").addClass("hidden");
		  attendance_tbl.removeClass("hidden");
		  attendance_tbl.handsontable({
		    width: "100%",
			colHeaders: response.header, 
			colWidths: response.width,
			data: response.data, 
			cells: function (row, col, prop) {
			  var cellProperties = {};
			  cellProperties.readOnly = true;
			  if(col > 1)
			    cellProperties.className = "htCenter";
			  if(prop == "action" || prop == "remarks")
			    cellProperties.renderer = "html";
			  return cellProperties;
			}
		  });
		}
		else {
		  
		  $("#tk-attendance-no-record").removeClass("hidden");
		  attendance_tbl.addClass("hidden");
		}
		var total_count = response.total_count;
		var total_pages = Math.ceil(total_count/limit_default);
		var current_page = response.page;
		var pagination_limit = 8;
		
		
		pagination_lbl = generatePagination(total_count, total_pages, current_page, pagination_limit);
		$("#tk-attendance-pager").html(pagination_lbl);
		loading = false;
	  }
	});
  }

  $(document).off("click",".request-view").on("click",".request-view",function(e) {
    $.post(
	  base_url + "reports/viewRecord",
	  {mb_no: $(this).data("id"), day: $(this).data("date")},
	  function(response){
	    $('#header-text').html("<b>"+response.data[0].fullname+" - "+response.data[0].att_date+"</b>");
		$('#shift-code').html(response.data[0].shift_code);
		$('#shift-from').html(response.data[0].shift_from);
		$('#shift-to').html(response.data[0].shift_to);
		$('#time-in').html(response.data[0].actual_in==null?"&nbsp;":"<b>"+response.data[0].actual_in+"</b>");
		$('#time-out').html(response.data[0].actual_out==null?"&nbsp;":"<b>"+response.data[0].actual_out+"</b>");
		$('#tardy').html(response.data[0].tardy==null?0:"<b>"+response.data[0].tardy+"</b>");
		$('#undertime').html(response.data[0].undertime==null?0:"<b>"+response.data[0].undertime+"</b>");
		
		if(response.leave_data.length > 0) {
		  var leave_info = "";
		  $.each(response.leave_data, function(i,v){
		    leave_info += '<span class="btn btn-white btn-success btn-sm col-sm-12 text-center" ><b>'+v.leave_code+' - '+v.leave_name+' - '+v.status_lbl+'</b></span>';
		  });
		  $("#leave-tbl").html(leave_info);
		}
		else
		  $("#leave-tbl").html('<span class="btn btn-white btn-success btn-sm col-sm-12 text-center" >No Leave</span>');
		
		if(response.obt_data.length > 0) {
		  var obt_info = "";
		  $.each(response.obt_data, function(i,v){
		    obt_info += '<span class="btn btn-white btn-success btn-sm col-sm-12 text-center" ><b>'+v.time_in+' - '+v.time_out+' - '+v.status_lbl+'</b></span>';
		  });
		  $("#obt-tbl").html(obt_info);
		}
		else
		  $("#obt-tbl").html('<span class="btn btn-white btn-success btn-sm col-sm-12 text-center" >No OBT</span>');
		
		if(response.cws_data.length > 0) {
		  var cws_info = "";
		  $.each(response.cws_data, function(i,v){
		    cws_info += '<span class="btn btn-white btn-success btn-sm col-sm-12 text-center" ><b>'+v.shift_from+' - '+v.shift_to+' - '+v.status_lbl+'</b></span>';
		  });
		  $("#cws-tbl").html(cws_info);
		}
		else
		  $("#cws-tbl").html('<span class="btn btn-white btn-success btn-sm col-sm-12 text-center" >No CWS</span>');
		
		if(response.att_data.length > 0) {
		  var att_info = "";
		  $.each(response.att_data, function(i,v){
		    att_info += '<span class="btn btn-white btn-info btn-sm col-sm-6 text-center" >'+v.time_log+'</span>';
			att_info += '<span class="btn btn-white btn-info btn-sm col-sm-6 text-center" >'+v.log_type+'</span>';
		  });
		  $("#att-tbl").html(att_info);
		}
		else
		  $("#att-tbl").html('<span class="btn btn-white btn-info btn-sm col-sm-12 text-center" >No Logs</span>');
		
		if(((response.data[0].actual_in==null && response.data[0].actual_out==null) || (response.data[0].undertime * 1 > 0)) && response.dept_no == 24)
		  $("#awol-settings").removeClass("hidden");
		else
		  $("#awol-settings").addClass("hidden");
		
                if(response.awol_history.length > 0) {
                    $("#awolhistorypage").removeClass("hidden");
                    var awol_info = "";
                    $.each(response.awol_history,function(a,b){
                        awol_info += 
                        '<div class="well well-sm alert-success text-left"><b><i>'+b.awol_date+'</i></b><br>'+b.awol_history+'</div>';
                        $('#awol-history').find('span').html(awol_info);
                    });
                    $("#awol-history").find("span").css({"overflow":"auto","height":"150px"});
                    $("#awol-history").find("span").find('.well-sm').css({'padding':'0.5em','margin-bottom':'10px'});
                }else{
                    $("#awolhistorypage").addClass("hidden");
                }
                
		if(response.awol_data.length > 0) {
		  if(response.awol_data[0].is_el==1)
		    $(".awol-radio[value='el']").prop("checked",true);
		  else
		    $(".awol-radio[value='"+response.awol_data[0].is_awol+"']").prop("checked",true);
		  $("#reason").val(response.awol_data[0].awol_reason);
		  $("#awol-save").addClass("hidden");
                  $("#awol-revert").removeClass("hidden");
                  $("#awol-update").addClass("hidden");
		}
		else {
		  $(".awol-radio").prop("checked",false);
		  $("#reason").val("");
                  $("#awol-revert").addClass("hidden");
		  $("#awol-update").addClass("hidden");
                  $("#awol-save").removeClass("hidden");
		}
		
		$("#mb_no").val(response.data[0].mb_no);
		$("#day").val(response.data[0].att_date)
		
		$(".awol-field").removeClass("has-error");
		$(".help-block").remove();
	    $('div#request-modal').modal('show');
	  },
	  'json'
	);
  });
  
  
  $("#awolForm").validate({
    errorElement: 'div',
	errorClass: 'help-block',
	focusInvalid: false,
	rules: {
			"awol-tag": {
				required: true
			},
			"awol-reason": {
				required: true
			}
	},
	messages: {
			"awol-tag": {
				required: "Please set AWOL status"
			},
			"awol-reason": {
				required: "Please set Reason"
			}
	},
	highlight: function (e) {
		$(e).closest('.awol-field').removeClass('has-info').addClass('has-error');
	},
	success: function (e) {
		$(e).closest('.awol-field').removeClass('has-error');
		$(e).remove();
	}
  });
  $("#awol-revert").off("click").click(function(e){
     e.preventDefault();
     $(".awol-radio").prop("checked",false);
     $("#reason").val("");
     $("#awol-update").removeClass("hidden");
  });

  $("#awol-update").off("click").click(function(e){
    e.preventDefault();
    $(this).attr("disabled","disabled").html('<i class="ace-icon fa fa-save"></i> Please Wait&hellip;');
    var mark1 = !$("input[name='awol-tag']:checked").val()  ? 1 : 0;
    var mark2 = !$("#awolForm #reason").val() ? 1 : 0;
    if(mark1 !== mark2) return false;
          $.post(
	    base_url + "reports/editawol",
	    {awol_mark:$(".awol-radio:checked").val(),mb_no: $("#mb_no").val(), day: $("#day").val(), is_awol: 1, reason: $("#reason").val()},
	    function(response){
                $(this).removeAttr("disabled").html('<i class="ace-icon fa fa-pencil"></i> Update');
		  $('div#request-modal').modal('hide'); 
	      getSchedules($("div#tk-attendance-pager ul.pagination li.active").find('a').data('page')?$("div#tk-attendance-pager ul.pagination li.active").find('a').data('page'):1);
	    },
	    'json'
	  );
  });

  $("#awol-save").off("click").click(function(e){
    $(this).attr("disabled","disabled").html('<i class="ace-icon fa fa-save"></i> Please Wait&hellip;');
    e.preventDefault();
    if(!$("#awolForm").valid()) return false;
	
	if($(".awol-radio:checked").val() == 1) {
	  $.post(
	    base_url + "reports/markAsAWOL",
	    {awol_mark:$(".awol-radio:checked").val(),mb_no: $("#mb_no").val(), day: $("#day").val(), is_awol: 1, reason: $("#reason").val()},
	    function(response){
                $(this).removeAttr("disabled").html('<i class="ace-icon fa fa-save"></i> Save');
		  $('div#request-modal').modal('hide'); 
	      getSchedules($("div#tk-attendance-pager ul.pagination li.active").find('a').data('page')?$("div#tk-attendance-pager ul.pagination li.active").find('a').data('page'):1);
	    },
	    'json'
	  );
	}
	else if($(".awol-radio:checked").val() == 0){
	  $.post(
	    base_url + "reports/markAsNotAWOL",
	    {awol_mark:$(".awol-radio:checked").val(),mb_no: $("#mb_no").val(), day: $("#day").val(), is_awol: 0, reason: $("#reason").val()},
	    function(response){
                $(this).removeAttr("disabled").html('<i class="ace-icon fa fa-save"></i> Save');
		  $('div#request-modal').modal('hide'); 
	      getSchedules($("div#tk-attendance-pager ul.pagination li.active").find('a').data('page')?$("div#tk-attendance-pager ul.pagination li.active").find('a').data('page'):1);
	    },
	    'json'
	  );
	}
	else {
	  $.post(
	    base_url + "reports/markAsEL",
	    {awol_mark:$(".awol-radio:checked").val(),mb_no: $("#mb_no").val(), day: $("#day").val(), reason: $("#reason").val()},
	    function(response){
                $(this).removeAttr("disabled").html('<i class="ace-icon fa fa-save"></i> Save');
		  $('div#request-modal').modal('hide'); 
	      getSchedules($("div#tk-attendance-pager ul.pagination li.active").find('a').data('page')?$("div#tk-attendance-pager ul.pagination li.active").find('a').data('page'):1);
	    },
	    'json'
	  );
	}
	
	return false;
  });
  
});


function generatePagination(total_count, total_pages, current_page, pagination_limit) {
    var max_page = pagination_limit;
	if(pagination_limit > total_pages)
	  max_page = total_pages;
		  
	var pagination_lbl = '<ul class="pagination">';
	if(total_pages > 1)
	  pagination_lbl += '<li '+(current_page==1?'class="active"':'')+'><a href="javascript:void(0)" data-page="1">1</a></li>';
	if(current_page > 5) {
	  if(total_pages > pagination_limit)
		pagination_lbl += '<li class="disabled"><a href="javascript:void(0)"><i class="ace-icon fa fa-ellipsis-h"></i></a></li>';
	  var initial = ctr = (current_page - 3 > total_pages-pagination_limit+2 ? total_pages-pagination_limit+2 : current_page - 3);
	  var last = initial+pagination_limit-2;
	  if(last > total_pages)
		last = total_pages;
          if(ctr == 0) ctr = ctr+2;
          if(ctr == 1) ctr = ctr+1;
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
	return pagination_lbl
}

