$(function () {

  	var uploads_table = $('table#uploads-table').dataTable({
	  serverSide: true,
	  ajax: {
		  url: base_url + 'timekeeping/getAllUploads',
		  type: "POST"
	  },
	  deferRender: true,
	  autoWidth: false,
	  method: "post",
	  columns: [
	    {
		  orderable: false,
		  data: "upload_id",
		  render: function (d) {
		    return '<label class="position-relative"><input type="checkbox" class="ace" value="' + d + '" /><span class="lbl"></span></label>';
		  },
		  className: 'center'
	    },
	    { data: "group_code" },
	    { data: "period" },
	    { data: "mb_nick" },
		{ data: "updated_datetime" },
	    {
		  data: "status_lbl",
		  render: function (d,t,r,m) {
		    var label = "";
			switch(d) {
			  case "Pending":
			    label = '<span class="label label-info arrowed">'+d+'</span>';
				break;
			  case "Submitted":
			    label = '<span class="label label-warning arrowed">'+d+'</span>';
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
		  data: "upload_id",
		  render: function (d,t,r,m) {
		    return '\
			  <div class="hidden-sm hidden-xs action-buttons">\
			    <a class="blue download-file" href="'+base_url+r.file_path+'" download="'+r.org_file+'" title="Download">\
				  <i class="ace-icon fa fa-download bigger-130"></i>\
				</a>\
				'+((r.status == 0 || r.status == 2)?'<a class="green upload-submit" href="" title="Submit"><i class="ace-icon fa fa-share-square-o bigger-130"></i></a>':'')+'\
				'+((r.status != 3 && r.dirty_bit_ind == 0)?'<a class="red upload-remove" href="#" title="Delete"><i class="ace-icon fa fa-trash-o bigger-130"></i></a>':'')+'\
				'+((r.status > 0)?'<a class="blue upload-view-hist" href="#" title="View History"><i class="ace-icon fa fa-search bigger-130"></i></a>':'')+'\
			  </div>\
			  <div class="hidden-md hidden-lg">\
				<div class="inline position-relative">\
				  <button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown" data-position="auto">\
				    <i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>\
				  </button>\
				  <ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">\
				    <li>\
					  <a href="#" class="tooltip-info download-file" data-rel="tooltip" href="'+base_url+r.file_path+'" download="'+r.org_file+'" title="Download">\
					    <span class="blue">\
						  <i class="ace-icon fa fa-download bigger-120"></i>\
						</span>\
					  </a>\
					</li>\
					'+((r.status==0 || r.status==2)?'\
				    <li>\
					  <a href="#" class="tooltip-success upload-submit" data-rel="tooltip" title="Submit">\
					    <span class="green">\
						  <i class="ace-icon fa fa-share-square-o bigger-120"></i>\
						</span>\
					  </a>\
					</li>\
					\ ':'')+'\
					'+((r.status!=3 && r.dirty_bit_ind == 0)?'\
					<li>\
					  <a href="#" class="tooltip-error upload-remove" data-rel="tooltip" title="Delete">\
						<span class="red">\
						  <i class="ace-icon fa fa-trash-o bigger-120"></i>\
						</span>\
					  </a>\
					</li>\
					\ ':'')+'\
					'+((r.status > 0)?'\
					<li>\
					  <a href="#" class="tooltip-info upload-view-hist" data-rel="tooltip" title="View History">\
						<span class="blue">\
						  <i class="ace-icon fa fa-search bigger-120"></i>\
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
		  $('a.upload-submit', r).click(function (e) {
			e.preventDefault();
			$('button.modal-upload-btn',"div#delete-modal").data({id: d["upload_id"], label: d["period"], action: "submit"});
			$('div#delete-modal').modal('show');
		  });
		  $('a.upload-remove', r).click(function (e) {
			e.preventDefault();
			$('button.modal-upload-btn',"div#delete-modal").data({id: d["upload_id"], label: d["period"], action: "delete"});
			$('div#delete-modal').modal('show');
		  });
		  $('a.upload-view-hist', r).click(function (e) {
		    e.preventDefault();
			$.post(
			  base_url + "timekeeping/getUploadSchedHistory",
			  {id: d["upload_id"]},
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
			$('div#history-modal').modal('show');
		  });
	    }
	  }),
    uploads_table_api = uploads_table.api();;

  $('div#delete-modal')
  .modal({
	backdrop: 'static',
	show: false
  })
  .off('show.bs.modal')
  .on('show.bs.modal', function () {
	$("#upload_lbl").html($('button.modal-upload-btn',"div#delete-modal").data("label"));
	$("#modal-action").html($('button.modal-upload-btn',"div#delete-modal").data("action"));
  });

  var uploadReq;
  
  if(typeof($("#upload-form","div.upload-file-widget")) != "undefined") {
  $("#upload-form","div.upload-file-widget").ajaxForm({
    url: base_url+"timekeeping/uploadSchedule",
	dataType: "json",
	type: "post",
    beforeSubmit: function() {
	  if(uploadReq)
	    clearTimeout(uploadReq);
	  $("#upload-file-success-msg","div.upload-file-widget").html("");
	  $("#upload-file-err-msg","div.upload-file-widget").html("");
	  $("div.alert-success","div.upload-file-widget").addClass("hidden");
	  $("div.alert-danger","div.upload-file-widget").addClass("hidden");
	  $("#note-upload","div.upload-file-widget").addClass("text-muted");
	  $("#note-upload","div.upload-file-widget").removeClass("text-danger");
	  
	  if($("#schedule-file","div.upload-file-widget").val()=="") {
	    $("#note-upload","div.upload-file-widget").removeClass("text-muted");
	    $("#note-upload","div.upload-file-widget").addClass("text-danger");
	    return false;
	  }
	  $("#upload-file-btn").attr("disabled",true);
	  $("#upload-file-btn").html('<i class="ace-icon fa fa-spinner fa-spin bigger-110"></i> Uploading&hellip;');
	},
	error: function(response) {
	  alert("System Error");
	},
	success: function(response) {
	  if(response.success) {
	    $("#upload-file-success-msg","div.upload-file-widget").html(response.msg);
		$("div.alert-success","div.upload-file-widget").removeClass("hidden");
		uploads_table_api.ajax.url(base_url + 'timekeeping/getAllUploads').load();
	    uploadReq = setTimeout(function() {
			$("div.alert-success","div.upload-file-widget").addClass("hidden");
		}, 3000);
	  }
	  else {
	    $("#upload-file-err-msg","div.upload-file-widget").html(response.msg);
		$("div.alert-danger","div.upload-file-widget").removeClass("hidden");
		uploadReq = setTimeout(function() {
			$("div.alert-danger","div.upload-file-widget").addClass("hidden");
		}, 8000);
	  }
	  $("#upload-file-btn").removeAttr("disabled");
	  $("#upload-file-btn").html('<i class="ace-icon fa fa-upload bigger-110"></i> Upload');
	  sched_file.ace_file_input("reset_input");
	  $('#group-id :nth-child(1)').prop('selected', true);
	}
  });
  }
  
  var sched_file = $('#schedule-file');
  sched_file.ace_file_input({
	no_file:'No File ...',
	btn_choose:'Choose',
	btn_change:'Change',
	droppable:false,
	thumbnail:false,
	allowExt:'xls'
  });
  
  sched_file
  .off("file.error.ace")
  .on("file.error.ace", function(e, info) {
	sched_file.ace_file_input("reset_input");
	$("#note-upload").removeClass("text-muted");
	$("#note-upload").addClass("text-danger");
  });
  
  $("#upload-file-btn").off("click").click(function(e) {
    $("#upload-form").submit();
  });
  
  $(".modal-upload-btn", "#delete-modal").off("click").click(function(e) {
	e.preventDefault();
	if($(this).data("action") == "delete") {
	  $.post(
	    base_url + "timekeeping/deleteSchedule",
	    {upload_id: $('button.modal-upload-btn',"div#delete-modal").data("id")},
	    function(response) {
		  $("div.alert-success", "div.upload-list-widget").addClass("hidden");
		  $("#upload-list-success-msg", "div.upload-list-widget").html("");
		  $("div.alert-danger", "div.upload-list-widget").addClass("hidden");
		  $("#upload-list-err-msg", "div.upload-list-widget").html("");
		  
		  if(response.success) {
		    $("div.alert-success", "div.upload-list-widget").removeClass("hidden");
			$("#upload-list-success-msg", "div.upload-list-widget").html(response.msg);
			setTimeout(function() {
			  $("div.alert-success","div.upload-list-widget").addClass("hidden");
		    }, 3000);
		  }
		  else {
		    $("div.alert-danger", "div.upload-list-widget").removeClass("hidden");
		    $("#upload-list-err-msg", "div.upload-list-widget").html(response.msg);
			setTimeout(function() {
			  $("div.alert-danger","div.upload-list-widget").addClass("hidden");
		    }, 3000);
		  }
		  $('div#delete-modal').modal('hide'); 
		  uploads_table_api.ajax.url(base_url + 'timekeeping/getAllUploads').load();
	    },
	    "json"
	  );
	}
	else if($(this).data("action") == "submit") {
	  $.post(
	    base_url + "timekeeping/submitSchedule",
	    {upload_id: $('button.modal-upload-btn',"div#delete-modal").data("id")},
	    function(response) {
		  $('div#delete-modal').modal('hide'); 
		  uploads_table_api.ajax.url(base_url + 'timekeeping/getAllUploads').load();
	    },
	    "json"
	  );
	}
  });

  $('div#history-modal')
  .modal({
	backdrop: 'static',
	show: false
  })
  .off('show.bs.modal')
  .on('show.bs.modal', function () {
  });  
  
});