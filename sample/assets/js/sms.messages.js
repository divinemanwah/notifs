$(function () {
	var processing = false;
  	var sms_table = $('table#sms-table').dataTable({
	  serverSide: true,
	  ajax: {
		  url: base_url + 'sms/getAllMessages',
		  type: "POST",
		  data: function(d) {
		    d.dept = $('button#sms-filter-dept-btn').data('id');
			d.emp = $('#sms-filter-emp').val();
			d.from = $('#sms-date-from').val();
			d.to = $('#sms-date-to').val();
		  }
	  },
	  deferRender: true,
	  autoWidth: false,
	  method: "post",
	  columns: [
	    {
		  orderable: false,
		  data: "sms_in_id",
		  render: function (d) {
		    return '<label class="position-relative"><input type="checkbox" class="ace" value="' + d + '" /><span class="lbl"></span></label>';
		  },
		  className: 'center'
	    },
	    { data: "mb_id" },
	    { data: function(d){
                        return d["mb_nick"]+" "+d["mb_lname"];
                    } 
            },
            { data: "dept_name" },
		{ data: "sms_in_datetime" },
		{ data: "sms_in_sender" },
		{ data: "code" },
	    { data: "status_lbl" }
	    ],
	    order: [[4, 'desc']],
	    rowCallback: function (r, d) {
		  $(document).off("click",".request-view");
		  $('a.request-view', r).click(function (e) {
			e.preventDefault();
			$("input, textarea, select",'div#request-modal').val("").prop("disabled",true);
			
			$("#lv-requester").html("");
			$("#lv-approve").removeData().hide();
			$("#lv-reject").removeData().hide();
			
			$.post(
			  base_url + "leave/getLeaveApplication",
			  {request_id: d["lv_app_id"]},
			  function(response){
			    if(response.data[0].sub_categ_id>0)
		          $("#lv-type").html("<option value='"+response.data[0].leave_id+"'>"+response.data[0].sub_categ_code+" - "+response.data[0].sub_categ_name+"</option>");
		        else
		          $("#lv-type").html("<option value='"+response.data[0].leave_id+"'>"+response.data[0].leave_code+" - "+response.data[0].leave_name+"</option>");
				$("#lv-date-from").datepicker("setDate",response.data[0].date_from);
				$("#lv-date-to").datepicker("setDate",response.data[0].date_to);
				$("#reason").val(response.data[0].reason);
				if(response.data[0].control_id != "" && response.data[0].control_id != null) {
				  $("#lv-mc").val(response.data[0].control_id);
				  $("#mc-row").removeClass("hidden");
				}
				else {
				  $("#lv-mc").val("");
				  $("#mc-row").addClass("hidden");
				}
				$("#lv-requester").html(d["creator"]);
				if(response.data[0].status == 1 && d["approved_level"] == d["user_level"]) {
				  $("#lv-approve").data({level: d["approved_level"], id: d["approval_id"], grp_id: d["lv_apprv_grp_id"], app_id: d["lv_app_id"], label: d["creator"] + " [" + d["leave_code"]+"] on "+d["date_from"]+(d["date_from"] != d["date_to"]? " up to "+d["date_to"]:""), action: "approve"}).show();
				  $("#lv-reject").data({id: d["approval_id"], grp_id: d["lv_apprv_grp_id"], app_id: d["lv_app_id"], label: d["creator"] + " ["+ d["leave_code"]+"] on "+d["date_from"]+(d["date_from"] != d["date_to"]? " up to "+d["date_to"]:""), action: "reject"}).show();
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
			  },
			  'json'
			);
			$('div#request-modal').modal('show');
		  });
		  $('a.leave-approve', r).click(function (e) {
			e.preventDefault();
			$('button.modal-action-btn',"div#approval-modal").data({level: d["approved_level"], id: d["approval_id"], grp_id: d["lv_apprv_grp_id"], app_id: d["lv_app_id"], label: d["creator"] + " [" + d["leave_code"]+"] on "+d["date_from"]+(d["date_from"] != d["date_to"]? " up to "+d["date_to"]:""), action: "approve"});
			$("p#remarks_lbl","div#approval-modal").hide();
			$('textarea#leave-remarks',"div#approval-modal").val("").hide();
			$('div#approval-modal').modal('show');
		  });
		  $('a.leave-reject', r).click(function (e) {
			e.preventDefault();
			$('button.modal-action-btn',"div#approval-modal").data({id: d["approval_id"], grp_id: d["lv_apprv_grp_id"], app_id: d["lv_app_id"], label: d["creator"] + " ["+ d["leave_code"]+"] on "+d["date_from"]+(d["date_from"] != d["date_to"]? " up to "+d["date_to"]:""), action: "reject"});
			$("p#remarks_lbl","div#approval-modal").show();
			$('textarea#leave-remarks',"div#approval-modal").val("").show();
			$('div#approval-modal').modal('show');
		  });
	    }
	  }),
    sms_table_api = sms_table.api();
	
	$('.chosen-select').chosen({allow_single_deselect:true, width: "100%"}); 
    $('.chosen-select').trigger("chosen:updated");

	$('.input-daterange').datepicker({autoclose:true, format: "yyyy-mm-dd"});
	
	$('ul#sms-filter-dept a').off("click").click(function (e) {
	  e.preventDefault();
	  $('ul#sms-filter-dept li').removeClass('active');
	  $(this).parent().addClass('active');
	
	  $('button#sms-filter-dept-btn').html('\
	    <i class="ace-icon fa fa-filter"></i> Status: ' + $(this).text() + '\
	    <i class="ace-icon fa fa-chevron-down icon-on-right"></i>\
	  ');
		
	  if($(this).data('id'))
	    $('button#sms-filter-dept-btn').data('id', $(this).data('id'));
	  else
	    $('button#sms-filter-dept-btn').data('id', 0);
    });
	
	$('button#sms-search-btn').off("click").click(function (e) {
	  sms_table_api.ajax.reload(null,true);
	});
   
	
});