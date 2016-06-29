$(function () {
    var processing = false;
  	var approval_table = $('table#approval-table').dataTable({
	  serverSide: true,
	  ajax: {
		  url: base_url + 'overtime/getAllForApproval',
		  type: "POST",
		  data: function(d) {
		    d.status = $('button#tk-filter-status-btn').data('id');
		  }
	  },
	  deferRender: true,
	  autoWidth: false,
	  method: "post",
	  columns: [
	    {
		  orderable: false,
		  data: "ot_app_id",
		  render: function (d) {
		    return '<label class="position-relative"><input type="checkbox" class="ace" value="' + d + '" /><span class="lbl"></span></label>';
		  },
		  className: 'center'
	    },
	    { data: "ot_group_code" },
	    { data: "date" },
		{ data: "time_in" },
		{ data: "time_out" },
	    { data: "sender" },
		{ data: "approver" },
	    {
		  data: "status_lbl",
		  render: function (d,t,r,m) {
		    var label = "";
			switch(d) {
			  case "Pending":
			    label = '<span class="label label-info arrowed">'+d+'</span>';
				break;
			  case "For Approval":
			    label = '<span class="label label-warning arrowed">For Approval</span>';
				break;
			  case "Rejected":
			    label = '<span class="label label-danger arrowed">'+d+'</span>';
				break;
			  case "Approved":
			    label = '<span class="label label-success arrowed">'+d+'</span>';
				break;
			  case "Cancelled":
			    label = '<span class="label label-success arrowed">'+d+'</span>';
				break;
			}
		    return label;
		  }
		},
	    { 
		  orderable: false,
		  data: "ot_app_id",
		  render: function (d,t,r,m) {
		    return '\
			  <div class="hidden-sm hidden-xs action-buttons">\
			    <a class="blue request-view" href="#" title="View"><i class="ace-icon fa fa-search bigger-130"></i></a>\
			    '+((r.status==1 && r.approved_level == r.user_level)?'<a class="green overtime-approve" href="" title="Approve"><i class="ace-icon fa fa-check bigger-130"></i></a>':'')+'\
				'+((r.status==1 && r.approved_level == r.user_level)?'<a class="red overtime-reject" href="#" title="Reject"><i class="ace-icon fa fa-remove bigger-130"></i></a>':'')+'\
			  </div>\
			  <div class="hidden-md hidden-lg">\
				<div class="inline position-relative">\
				  <button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown" data-position="auto">\
				    <i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>\
				  </button>\
				  <ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">\
				    <li>\
					  <a href="#" class="tooltip-info request-view" data-rel="tooltip" title="View">\
					    <span class="blue">\
						  <i class="ace-icon fa fa-search bigger-120"></i>\
						</span>\
					  </a>\
					</li>\
				    '+((r.status==1 && r.approved_level == r.user_level)?'\
				    <li>\
					  <a href="#" class="tooltip-success overtime-approve" data-rel="tooltip" title="Approve">\
					    <span class="green">\
						  <i class="ace-icon fa fa-check bigger-120"></i>\
						</span>\
					  </a>\
					</li>\
					\ ':'')+'\
					'+((r.status==1 && r.approved_level == r.user_level)?'\
					<li>\
					  <a href="#" class="tooltip-error overtime-reject" data-rel="tooltip" title="Reject">\
						<span class="red">\
						  <i class="ace-icon fa fa-remove bigger-120"></i>\
						</span>\
					  </a>\
					</li>\
					\ ':'')+'\
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
		  $(document).off("click",".request-view");
		  $('a.request-view', r).click(function (e) {
			e.preventDefault();
			$("input, textarea, select",'div#request-modal').val("").prop("disabled",true);
			
			$("#ot-requester").html("");
			$("#ot-approve").removeData().hide();
			$("#ot-reject").removeData().hide();
			
			$.post(
			  base_url + "overtime/getOTApplication",
			  {request_id: d["ot_app_id"]},
			  function(response){
				$("#request-id").val(response.data[0].ot_app_id);
				$("#ot-date-from").datepicker("setDate",response.data[0].date);
				$("#ot-time-from").val(response.data[0].time_in);
				$("#ot-time-to").val(response.data[0].time_out);
				$("#reason").val(response.data[0].reason);
				$("#ot-requester").html(d["sender"]);
				if(response.data[0].status == 1 && d["approved_level"] == d["user_level"]) {
				  $("#ot-approve").data({level: d["approved_level"], id: d["approval_id"], grp_id: d["ot_apprv_grp_id"], app_id: d["ot_app_id"], label: d["sender"] + " OT on ["+d["date"]+"] from "+d["time_in"]+" to "+d["time_out"], action: "approve"}).show();
				  $("#ot-reject").data({id: d["approval_id"], grp_id: d["ot_apprv_grp_id"], app_id: d["ot_app_id"], label: d["sender"] + " OT on ["+d["date"]+"] from "+d["time_in"]+" to "+d["time_out"], action: "reject"}).show();
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
		  $('a.overtime-approve', r).click(function (e) {
			e.preventDefault();
			$('button.modal-action-btn',"div#approval-modal").data({level: d["approved_level"], id: d["approval_id"], grp_id: d["ot_apprv_grp_id"], app_id: d["ot_app_id"], label: d["sender"] + " OT on ["+d["date"]+"] from "+d["time_in"]+" to "+d["time_out"], action: "approve"});
			$("p#remarks_lbl","div#approval-modal").hide();
			$('textarea#overtime-remarks',"div#approval-modal").val("").hide();
			$('div#approval-modal').modal('show');
		  });
		  $('a.overtime-reject', r).click(function (e) {
			e.preventDefault();
			$('button.modal-action-btn',"div#approval-modal").data({id: d["approval_id"], grp_id: d["ot_apprv_grp_id"], app_id: d["ot_app_id"], label: d["sender"] + " OT on ["+d["date"]+"] from "+d["time_in"]+" to "+d["time_out"], action: "reject"});
			$("p#remarks_lbl","div#approval-modal").show();
			$('textarea#overtime-remarks',"div#approval-modal").val("").show();
			$('div#approval-modal').modal('show');
		  });
	    }
	  }),
    approval_table_api = approval_table.api();
	
	$('div#approval-modal')
	.modal({
		backdrop: 'static',
		show: false
	})
	.off('show.bs.modal')
	.on('show.bs.modal', function () {
	  $("#modal-target-lbl").html($('button.modal-action-btn',"div#approval-modal").data("label"));
	  $("#modal-action").html($('button.modal-action-btn',"div#approval-modal").data("action"));
	});
	
	$('.input-daterange').datepicker({autoclose:true, format: "yyyy-mm-dd"});
	
	$('button.modal-action-btn',"div#approval-modal").off("click").click(function(e) {
	  e.preventDefault();
	  if(processing)
	    return false;
	  processing = true;
	  
	  $("#approval-modal button.btn-success").hide();
	  $("#approval-modal button.btn-danger").hide();
	  
	  if($(this).data("action") == "reject") {
	    $.post(
	      base_url + "overtime/rejectOT",
	      {ot_app_id: $('button.modal-action-btn',"div#approval-modal").data("app_id"), remarks: $('textarea#overtime-remarks',"div#approval-modal").val()},
	      function(response) {
		    processing = false;
			$("#approval-modal button.btn-success").show();
			$("#approval-modal button.btn-danger").show();
		    $("div.alert-success", "div.approval-list-widget").addClass("hidden");
		    $("#approval-list-success-msg", "div.approval-list-widget").html("");
		    $("div.alert-danger", "div.approval-list-widget").addClass("hidden");
		    $("#approval-list-err-msg", "div.approval-list-widget").html("");
		  
		    if(response.success) {
		      $("div.alert-success", "div.approval-list-widget").removeClass("hidden");
			  $("#approval-list-success-msg", "div.approval-list-widget").html(response.msg);
			  setTimeout(function() {
			    $("div.alert-success","div.approval-list-widget").addClass("hidden");
		      }, 3000);
		    }
		    else {
		      $("div.alert-danger", "div.approval-list-widget").removeClass("hidden");
		      $("#approval-list-err-msg", "div.approval-list-widget").html(response.msg);
			  setTimeout(function() {
			    $("div.alert-danger","div.approval-list-widget").addClass("hidden");
		      }, 3000);
		    }
		    $('div#approval-modal').modal('hide'); 
		    approval_table_api.ajax.reload(null,false);
	      },
	      "json"
	    );
	  }
	  else if($(this).data("action") == "approve") {
	    $.post(
	      base_url + "overtime/approveOT",
	      {
		    ot_app_id: $('button.modal-action-btn',"div#approval-modal").data("app_id"),
			approval_id: $('button.modal-action-btn',"div#approval-modal").data("id"),
			approval_level: $('button.modal-action-btn',"div#approval-modal").data("level"),
			grp_id: $('button.modal-action-btn',"div#approval-modal").data("grp_id")
		  },
	      function(response) {
		    processing = false;
			$("#approval-modal button.btn-success").show();
			$("#approval-modal button.btn-danger").show();
		    $("div.alert-success", "div.approval-list-widget").addClass("hidden");
		    $("#approval-list-success-msg", "div.approval-list-widget").html("");
		    $("div.alert-danger", "div.approval-list-widget").addClass("hidden");
		    $("#approval-list-err-msg", "div.approval-list-widget").html("");
		  
		    if(response.success) {
		      $("div.alert-success", "div.approval-list-widget").removeClass("hidden");
			  $("#approval-list-success-msg", "div.approval-list-widget").html(response.msg);
			  setTimeout(function() {
			    console.log($("div.alert-success","div.upload-file-widget"));
			    $("div.alert-success","div.approval-list-widget").addClass("hidden");
		      }, 3000);
		    }
		    else {
		      $("div.alert-danger", "div.approval-list-widget").removeClass("hidden");
		      $("#approval-list-err-msg", "div.approval-list-widget").html(response.msg);
			  setTimeout(function() {
			    $("div.alert-danger","div.approval-list-widget").addClass("hidden");
		      }, 3000);
		    }
		    $('div#approval-modal').modal('hide'); 
		    approval_table_api.ajax.reload(null,false);
	      },
	      "json"
	    );
	  }
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
	    $('button#tk-filter-status-btn').data('id', 0);
    });
	
	$('button#tk-search-btn').off("click").click(function (e) {
	  approval_table_api.ajax.reload(null,true);
	});

	$("#ot-approve").off("click").click(function(e){
	  e.preventDefault();
	  $('div#request-modal').modal('hide');
	  $('button.modal-action-btn',"div#approval-modal").data($(this).data());
	  $("p#remarks_lbl","div#approval-modal").hide();
	  $('textarea#overtime-remarks',"div#approval-modal").val("").hide();
      $('div#approval-modal').modal('show');
	});
	
	$("#ot-reject").off("click").click(function(e){
	  e.preventDefault();
	  $('div#request-modal').modal('hide');
	  $('button.modal-action-btn',"div#approval-modal").data($(this).data());
	  $("p#remarks_lbl","div#approval-modal").show();
	  $('textarea#overtime-remarks',"div#approval-modal").val("").show();
      $('div#approval-modal').modal('show');
	});
	
});