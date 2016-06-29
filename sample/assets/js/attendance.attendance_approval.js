var r_count = 0;
$(function(){
   	
	var change_att_table = $('table#change-att-table').dataTable({
	  serverSide: true,
	  ajax: {
		  url: base_url + 'attendance/getAllChangeAttendanceForApproval',
		  type: "POST",
		  data: function(d) {
		    d.status = $('button#tk-att-filter-status-btn').data('id');
		  }
	  },
	  deferRender: true,
	  autoWidth: false,
	  method: "post",
	  columns: [
	    {
		  orderable: false,
                  searchable: false,
		  data: "id",
		  render: function (d) {
		    return '<label class="position-relative"><input type="checkbox" class="ace" value="' + d + '" /><span class="lbl"></span></label>';
		  },
		  className: 'center'
	    },
	    { searchable: false, data: "mb_id" },
	    { data: "mb_name" },
            { searchable: false, data: "att_date" },
	    { searchable: false, data: "shift_code", orderable: false },
	    { searchable: false, data: "actual_in" },
            { searchable: false, data: "actual_out" },
            { searchable: false, data: "new_in" },
            { searchable: false, data: "new_out" },
            { searchable: false, data: "submitted_by" },
            { searchable: false, data: "approved_by" },
	    {
                  orderable: false,
                  searchable: false,
		  data: "att_status",
		  render: function (d,t,r,m) {
		    var label = "";
			switch(d) {
			  case "1":
			    label = '<span class="label label-warning arrowed">For Approval</span>';
				break;
			  case "0":
			    label = '<span class="label label-danger arrowed">Rejected</span>';
				break;
			  case "2":
			    label = '<span class="label label-success arrowed">Approved</span>';
				break;
			}
		    return label;
		  }
		},
	    { 
		  orderable: false,
                  searchable: false,
		  data: "att_status",
		  render: function (d,t,r,m) {
		    return '\
			  <div class="hidden-sm hidden-xs action-buttons">\
			    <a class="blue req-view" href="#" title="View"><i class="ace-icon fa fa-search bigger-130"></i></a>\
				'+((r.att_status==1)?'<a class="green change-approve" href="#" title="Approve"><i class="ace-icon fa fa-check bigger-130"></i></a>':'')+'\
				'+((r.att_status==1)?'<a class="red change-reject" href="#" title="Reject"><i class="ace-icon fa fa-remove bigger-130"></i></a>':'')+'\
			  </div>\
			  <div class="hidden-md hidden-lg">\
				<div class="inline position-relative">\
				  <button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown" data-position="auto">\
				    <i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>\
				  </button>\
				  <ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">\
				    <li>\
					  <a href="#" class="tooltip-info req-view" data-rel="tooltip" title="View">\
					    <span class="blue">\
						  <i class="ace-icon fa fa-search bigger-120"></i>\
						</span>\
					  </a>\
					</li>\
					'+((r.att_status==1)?'\
				    <li>\
					  <a href="#" class="tooltip-success change-approve" data-rel="tooltip" title="Approve">\
					    <span class="green">\
						  <i class="ace-icon fa fa-check bigger-120"></i>\
						</span>\
					  </a>\
					</li>\
					\ ':'')+'\
					'+((r.att_status==1)?'\
					<li>\
					  <a href="#" class="tooltip-error change-reject" data-rel="tooltip" title="Reject">\
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
                  
                  $(".req-view").off("click","");
		  $('a.req-view', r).click(function (e) {
			e.preventDefault();
			$("input, textarea, select",'div#req-modal').val("").prop("disabled",true);
			$("#shift-str",'div#req-modal').html("[N/A] - N/A");
			
			$("#att-requester").html("");
                         if(d['att_status'] !== 1){
                            $("#att-approve").removeData().hide();
                            $("#att-reject").removeData().hide();
                         }
                        $("#imgtbl").html("");
			
                        $.post(
                              base_url + 'attendance/AttendanceDetails/' ,
                              {'checkid':d['id'],'updateAttendance':d['att_id'],limit:1} ,
                              function (response) {

                                    $("#att_id").val(response.att.att_id);
                                    $("#att_name").html(response.emp.mb_nick+" "+response.emp.mb_lname+"  - "+response.att.att_date);
                                    $("#add-actual-in").val(response.att.actual_in);
                                    $("#add-actual-out").val(response.att.actual_out);

                                    if(response.chg && response.chg.remarks !== "" && response.chg.remarks !== undefined){
                                        if(d['att_status'] == 1){
                                        $("#att-approve").data({level: d["att_status"], id: d["id"],  label: '['+d["mb_name"]+'] New Attendance', action: "approve"}).show();
                                        $("#att-reject").data({id: d["id"],  label: '['+d["mb_name"]+'] New Attendance ', action: "reject"}).show();
                                        }
                                        
                                        $("#add-new-in").val(response.chg.new_in);
                                        $("#add-new-out").val(response.chg.new_out);
                                        $("#add-remarks").val(response.chg.remarks);

                                        var fname = response.chg.image_file;
                                        fname = fname.split(":");
                                        $("#fine-uploader-validation").hide();


                                        var thumb2 = '</div>';
                                        for(var i = 0; i<fname.length; i++){
                                            var thumb = '<li class="media"><a target="_blank" href="'+base_url+"/uploads/att_/"+fname[i]+'" ><div class="media-left">';
                                            var thumbname = '<div class="media-body"><h3 class="media-heading">'+fname[i].substring(33,fname[i].length)+'</h3></div>';
                                            $("#imgtbl").append(thumb+"<img class='media-object' src='"+base_url+"/uploads/att_/"+fname[i]+"' style='width:64px; height:64px;' >"+thumb2+thumbname+"</li></a>");
                                        }
                                    }
                              },
                              'json'
                        );
			$('div#req-modal').modal('show');
		  });
                  
		  $('a.change-approve', r).click(function (e) {
			e.preventDefault();
                        $("#att-approve").show();
			$('button.change-modal-action-btn',"div#change-att-approval-modal").data({level: d["att_status"], id: d["id"],  label: '['+d["mb_name"]+'] New Attendance', action: "approve"});
			$("p#remarks_lbl","div#change-att-approval-modal").hide();
			$('textarea#change-remarks',"div#change-att-approval-modal").val("").hide();
			$('div#change-att-approval-modal').modal('show');
		  });
                  
		  $('a.change-reject', r).click(function (e) {
			e.preventDefault();
                        $("#att-reject").show();
			$('button.change-modal-action-btn',"div#change-att-approval-modal").data({id: d["id"],  label: '['+d["mb_name"]+'] New Attendance ', action: "reject"});
			$("p#remarks_lbl","div#change-att-approval-modal").show();
			$('textarea#change-remarks',"div#change-att-approval-modal").val("").show();
			$('div#change-att-approval-modal').modal('show');
		  });
	    },
            drawCallback: function( settings ) {
                var api = new $.fn.dataTable.Api( settings );
                     var data = api.rows( {page:'current'} ).data();
                // Output the data for the visible rows to the browser's console
                // You might do something more useful with it!
                 $.each(data,function(key,val){
                     var tbl = $('table#change-att-table').find("tr:eq("+(key+1)+") td:eq(4)");
                    tbl.css({"background":val.shift_color,"text-align":"center"});
                    tbl.attr('title',val.shift_hrs);
                 });
            }
	  }),
    change_att_table_api = change_att_table.api();

        $('div#change-att-approval-modal')
	    .modal({
            backdrop: 'static',
            show: false
	})
        .off('show.bs.modal')
	.on('show.bs.modal', function () {
	  $("#change-modal-target-lbl").html($('button.change-modal-action-btn',"div#change-att-approval-modal").data("label"));
	  $("#change-modal-action").html($('button.change-modal-action-btn',"div#change-att-approval-modal").data("action"));
	});

    $('button#tk-att-search-btn').off("click").click(function (e) {
	  change_att_table_api.ajax.reload(null,true);
    });
    
    $('ul#tk-att-filter-status a').off("click").click(function (e) {
	  e.preventDefault();
	  $('ul#tk-att-filter-status li').removeClass('active');
	  $(this).parent().addClass('active');
	
	  $('button#tk-att-filter-status-btn').html('\
	    <i class="ace-icon fa fa-filter"></i> Status: ' + $(this).text() + '\
	    <i class="ace-icon fa fa-chevron-down icon-on-right"></i>\
	  ');
		//console.log($(this).data('id'));
	  if($(this).data('id') >= 0)
	    $('button#tk-att-filter-status-btn').data('id', $(this).data('id'));
	  else
	    $('button#tk-att-filter-status-btn').data('id', "");
    }); 
    
    
	$('button.change-modal-action-btn',"div#change-att-approval-modal").off("click").click(function(e) 
        {
	  e.preventDefault();
	  
	  $("#change-att-approval-modal button.btn-success").hide();
	  $("#change-att-approval-modal button.btn-danger").hide();

	  if($(this).data("action") == "reject") {
	    $.post(
	      base_url + "attendance/AttendanceApprovalChg",
	      {id: $('button.change-modal-action-btn',"div#change-att-approval-modal").data("id"),
               remarks: $('textarea#change-remarks',"div#change-att-approval-modal").val(),
               action:$(this).data("action")},
	      function(response) {
			$("#change-att-approval-modal button.btn-success").show();
			$("#change-att-approval-modal button.btn-danger").show();
		    $("div.alert-success", "div.change-att-list-widget").addClass("hidden");
		    $("#change-att-list-success-msg", "div.change-att-list-widget").html("");
		    $("div.alert-danger", "div.change-att-list-widget").addClass("hidden");
		    $("#change-sched-list-err-msg", "div.change-att-list-widget").html("");
		  
		    if(response.success) {
		      $("div.alert-success", "div.change-att-list-widget").removeClass("hidden");
			  $("#change-att-list-success-msg", "div.change-att-list-widget").html(response.msg);
			  setTimeout(function() {
			    $("div.alert-success","div.change-att-list-widget").addClass("hidden");
                            group_table_api.ajax.url(base_url + 'attendance/getAllChangeAttendanceForApproval/').load();
		      }, 3000);
		    }
		    else {
		      $("div.alert-danger", "div.change-att-list-widget").removeClass("hidden");
		      $("#change-sched-list-err-msg", "div.change-att-list-widget").html(response.msg);
			  setTimeout(function() {
			    $("div.alert-danger","div.change-att-list-widget").addClass("hidden");                          
		      }, 3000);
		    }
		    $('div#change-att-approval-modal').modal('hide'); 
			change_att_table_api.ajax.reload(null,false);
	      },
	      "json"
	    );
	  }
	  else if($(this).data("action") == "approve") {
	    $.post(
	      base_url + "attendance/AttendanceApprovalChg",
	      {id: $('button.change-modal-action-btn',"div#change-att-approval-modal").data("id"),
               remarks: $('textarea#change-remarks',"div#change-att-approval-modal").val(),
               action:$(this).data("action")},
	      function(response) {

			$("#change-att-approval-modal button.btn-success").show();
			$("#change-att-approval-modal button.btn-danger").show();
		    $("div.alert-success", "div.change-att-list-widget").addClass("hidden");
		    $("#change-att-list-success-msg", "div.change-att-list-widget").html("");
		    $("div.alert-danger", "div.change-att-list-widget").addClass("hidden");
		    $("#change-sched-list-err-msg", "div.change-att-list-widget").html("");
		  
		    if(response.success) {
		      $("div.alert-success", "div.change-att-list-widget").removeClass("hidden");
			  $("#change-att-list-success-msg", "div.change-att-list-widget").html(response.msg);
			  setTimeout(function() {
			    $("div.alert-success","div.change-att-list-widget").addClass("hidden");
                            group_table_api.ajax.url(base_url + 'attendance/getAllChangeAttendanceForApproval/').load();
		      }, 3000);
		    }
		    else {
		      $("div.alert-danger", "div.change-att-list-widget").removeClass("hidden");
		      $("#change-sched-list-err-msg", "div.change-att-list-widget").html(response.msg);
			  setTimeout(function() {
			    $("div.alert-danger","div.change-att-list-widget").addClass("hidden");
		      }, 3000);
		    }
		    $('div#change-att-approval-modal').modal('hide'); 
		    change_att_table_api.ajax.reload(null,false);
	      },
	      "json"
	    );
	  }

          
	});
    

	$("#att-approve").off("click").click(function(e){
	  e.preventDefault();
	  $('div#req-modal').modal('hide');
	  $('button.change-modal-action-btn',"div#change-att-approval-modal").data($(this).data());
	  $("p#remarks_lbl","div#change-att-approval-modal").hide();
	  $('textarea#change-remarks',"div#change-att-approval-modal").val("").hide();
	  $('div#change-att-approval-modal').modal('show');
	});
	
	$("#att-reject").off("click").click(function(e){
	  e.preventDefault();
	  $('div#req-modal').modal('hide');
	  $('button.change-modal-action-btn',"div#change-att-approval-modal").data($(this).data());
	  $("p#remarks_lbl","div#change-att-approval-modal").show();
	  $('textarea#change-remarks',"div#change-att-approval-modal").val("").show();
	  $('div#change-att-approval-modal').modal('show');
	});

    
});