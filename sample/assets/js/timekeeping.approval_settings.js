$(function () {
	
	var uploader_del_arr = new Array();
	var approver_del_arr = new Array();
	var cws_approver_del_arr =  new Array();
	
	var group_table = $('table#group-codes-table').dataTable({
			serverSide: true,
			ajax: {
				url: base_url + 'timekeeping/getAllApprovalGroups/' + ($('input#app-grps-display-inactive').prop('checked') ? '1' : '0'),
				type: "POST"
			},
			deferRender: true,
			autoWidth: false,
			method: "post",
			columns: [
					{
						orderable: false,
						data: "apprv_grp_id",
						render: function (d) {
								return '<label class="position-relative"><input type="checkbox" class="ace" value="' + d + '" /><span class="lbl"></span></label>';
							},
						className: 'center'
					},
					{ data: "group_code" },
					{ data: "updated_datetime" },
					{ data: "enabled_lbl" },
					{ 
						orderable: false,
						data: "apprv_grp_id",
						render: function (d,t,r,m) {
						  return '\
										<div class="hidden-sm hidden-xs action-buttons">\
											<a class="green group-edit" href="#">\
												<i class="ace-icon fa fa-pencil-square-o bigger-130"></i>\
											</a>\
											<a class="red group-remove" href="#">\
												<i class="ace-icon fa fa-trash-o bigger-130"></i>\
											</a>\
										</div>\
										<div class="hidden-md hidden-lg">\
											<div class="inline position-relative">\
												<button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown" data-position="auto">\
													<i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>\
												</button>\
												<ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">\
													<li>\
														<a href="#" class="tooltip-success group-edit" data-rel="tooltip" title="Edit">\
															<span class="green">\
																<i class="ace-icon fa fa-pencil-square-o bigger-120"></i>\
															</span>\
														</a>\
													</li>\
													<li>\
														<a href="#" class="tooltip-error group-remove" data-rel="tooltip" title="Delete">\
															<span class="red">\
																<i class="ace-icon fa fa-trash-o bigger-120"></i>\
															</span>\
														</a>\
													</li>\
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
			  $('a.group-edit', r).click(function (e) {
				e.preventDefault();
				$('button#apprv-grp-save',"div#apprv-grps-modal").data({id: d["apprv_grp_id"]});
				$('div#apprv-grps-modal').modal('show');
			  });
			  $('a.group-remove', r).click(function (e) {
				e.preventDefault();
				console.log(d);
				$('button.delete-apprv-btn',"div#delete-modal").data({id: d["apprv_grp_id"], label: d["group_code"]});
				$('div#delete-modal').modal('show');
			  });
			}
	      }),
	group_table_api = group_table.api();;

	$("input#app-grps-display-inactive").off("change").change(function(){
	  group_table_api.ajax.url(base_url + 'timekeeping/getAllApprovalGroups/' + ($('input#app-grps-display-inactive').prop('checked') ? '1' : '0')).load();
	});
	
	$('div#apprv-grps-modal')
	.modal({
		backdrop: 'static',
		show: false
	})
	.off('show.bs.modal')
	.on('show.bs.modal', function () {
	  $(".success-msg",this).html("");
	  $('div.alert-success', this).addClass('hidden');
	  $(".err-msg",this).html("");
	  $('div.alert-danger', this).addClass('hidden');
	  
	  $.getJSON(
		base_url + 'timekeeping/getApprovalGroup/' + $('button#apprv-grp-save').data('id'),
		function (response) {
		  $('input#apprv-grp-id'	,'div#apprv-grps-modal').val(response.data[0].apprv_grp_id);
		  $('input#apprv-grp-code'	,'div#apprv-grps-modal').val(response.data[0].group_code);
		  $('input#apprv-grp-status','div#apprv-grps-modal').val(response.data[0].enabled);
		  
		  var approver_list = uploader_list = "<option value=''></option>";
		  $.each(response.emp, function(i,v) {
		    approver_list += "<option value='"+v.mb_no+"'>"+(v.mb_3=="Expat"?v.mb_nick:v.mb_fname)+" "+v.mb_lname+"</option>";
			uploader_list += "<option value='"+v.mb_no+"'>"+(v.mb_3=="Expat"?v.mb_nick:v.mb_fname)+" "+v.mb_lname+"</option>";
		  });
		  $("#approver",'div#apprv-grps-modal').html(approver_list);
		  $("#uploader",'div#apprv-grps-modal').html(uploader_list);
		  
		  if(response.data[0].uploaders.length) {
		    var table_dtl = "";
		    $.each(response.data[0].uploaders, function(i,v) {
		      $("#uploader option[value='"+v.mb_id+"']","div#apprv-grps-modal").attr("disabled",true);
			  table_dtl += "<tr>\
							  <td>"+(v.mb_3=="Expat"?v.mb_nick:v.mb_fname)+" "+v.mb_lname+"</td>\
							  <td class='center'>\
							    <span class='label label-success arrowed'>\
								  <i class='fa fa-check'></i>  Active\
								</span>\
							  </td>\
							  <td class='center'>\
							    <a class='red uploader-remove' href='#' data-id='"+v.mb_id+"'>\
								  <i class='ace-icon fa fa-trash-o bigger-130'></i>\
								</a>\
							  </td>\
							</tr>";
		    });
			$("#uploader_tbl",'div#apprv-grps-modal').html(table_dtl);
		  }
		  else {
		    $("#uploader_tbl",'div#apprv-grps-modal').html("<tr id='no_assignment'><td colspan='3' class='center'>No Record Found</td></tr>");
		  }
		  
		  if(response.data[0].approvers.length) {
		    var table_dtl = "";
		    $.each(response.data[0].approvers, function(i,v) {
		      $("#approver option[value='"+v.mb_id+"']","div#apprv-grps-modal").attr("disabled",true);
			  table_dtl += "<tr>\
							  <td>"+(v.mb_3=="Expat"?v.mb_nick:v.mb_fname)+" "+v.mb_lname+"</td>\
							  <td class='center'>"+v.level+"</td>\
							  <td class='center'>\
							    <span class='label label-success arrowed'>\
								  <i class='fa fa-check'></i>  Active\
								</span>\
							  </td>\
							  <td class='center'>\
							    <a class='red approver-remove' href='#' data-id='"+v.mb_id+"'>\
								  <i class='ace-icon fa fa-trash-o bigger-130'></i>\
								</a>\
							  </td>\
							</tr>";
		    });
			$("#approver_tbl",'div#apprv-grps-modal').html(table_dtl);
		  }
		  else {
		    $("#approver_tbl",'div#apprv-grps-modal').html("<tr id='no_assignment'><td colspan='4' class='center'>No Record Found</td></tr>");
		  }
		  $('.chosen-select','div#apprv-grps-modal').chosen({allow_single_deselect:true, width: "100%"}); 
		  $('.chosen-select','div#apprv-grps-modal').trigger("chosen:updated");
		  $("#uploader_chosen").css("width","100%");
		  $("#approver_chosen").css("width","100%");
		}
	  );
	});
	
	$('div#add-apprv-grps-modal')
	.modal({
		backdrop: 'static',
		show: false
	})
	.off('show.bs.modal')
	.on('show.bs.modal', function () {
	  $(".success-msg",this).html("");
	  $('div.alert-success', this).addClass('hidden');
	  $(".err-msg",this).html("");
	  $('div.alert-danger', this).addClass('hidden');
	  
	  $.getJSON(
		base_url + 'timekeeping/getApprovalGroupFields',
		function (response) {
		  $('input#add-apprv-grp-code'	,'div#add-apprv-grps-modal').val("");
		  $('input#add-apprv-grp-status','div#add-apprv-grps-modal').val("");
		  var approver_list = uploader_list = "<option value=''></option>";
		  $.each(response.emp, function(i,v) {
		    approver_list += "<option value='"+v.mb_no+"'>"+(v.mb_3=="Expat"?v.mb_nick:v.mb_fname)+" "+v.mb_lname+"</option>";
			uploader_list += "<option value='"+v.mb_no+"'>"+(v.mb_3=="Expat"?v.mb_nick:v.mb_fname)+" "+v.mb_lname+"</option>";
		  });
		  $("#add-approver",'div#add-apprv-grps-modal').html(approver_list);
		  $("#add-uploader",'div#add-apprv-grps-modal').html(uploader_list);
		  $("#add-uploader_tbl",'div#add-apprv-grps-modal').html("<tr id='no_assignment'><td colspan='3' class='center'>No Record Found</td></tr>");
		  $("#add-approver_tbl",'div#add-apprv-grps-modal').html("<tr id='no_assignment'><td colspan='4' class='center'>No Record Found</td></tr>");
		  $('.chosen-select','div#add-apprv-grps-modal').chosen({allow_single_deselect:true, width: "100%"}); 
		  $('.chosen-select','div#add-apprv-grps-modal').trigger("chosen:updated");
		}
	  );
	});
	
	$('div#delete-modal')
	.modal({
		backdrop: 'static',
		show: false
	})
	.off('show.bs.modal')
	.on('show.bs.modal', function () {
	  $("#apprv_lbl").html($('button.delete-apprv-btn',"div#delete-modal").data("label"));
	});
	
	$(document).off("click","a.uploader-remove").on("click", "a.uploader-remove", function(e) {
	  e.preventDefault();
	  
	  if(typeof $(this).data("id") !== "undefined")
	    if($.inArray($(this).data("id"),uploader_del_arr) == -1)
	      uploader_del_arr.push($(this).data("id"));
	  
	  $("#uploader option[value='"+$(this).data("id")+"']","div#apprv-grps-modal").removeAttr("disabled");
	  
	  $(this).parent().parent().remove();
	  
	  if($("#uploader_tbl").html() == "")
	    $("#uploader_tbl").html("<tr id='no_assignment'><td colspan='3' class='center'>No Record Found</td></tr>");
		
	  $("#uploader",'div#apprv-grps-modal').trigger("chosen:updated");
	});
	
	$(document).off("click","a.add-uploader-remove").on("click", "a.add-uploader-remove", function(e) {
	  e.preventDefault();
	  
	  $("#add-uploader option[value='"+$(this).data("id")+"']","div#add-apprv-grps-modal").removeAttr("disabled");
	  
	  $(this).parent().parent().remove();
	  
	  if($("#add-uploader_tbl").html() == "")
	    $("#add-uploader_tbl").html("<tr id='no_assignment'><td colspan='3' class='center'>No Record Found</td></tr>");
		
	  $("#add-uploader",'div#add-apprv-grps-modal').trigger("chosen:updated");
	});
	
	$(document).off("click","a.approver-remove").on("click", "a.approver-remove", function(e) {
	  e.preventDefault();
	  
	  if(typeof $(this).data("id") !== "undefined")
	    if($.inArray($(this).data("id"),approver_del_arr) == -1)
	      approver_del_arr.push($(this).data("id"));
	  
	  $("#approver option[value='"+$(this).data("id")+"']","div#apprv-grps-modal").removeAttr("disabled");
	  
	  $(this).parent().parent().remove();
	  
	  if($("#approver_tbl").html() == "")
	    $("#approver_tbl").html("<tr id='no_assignment'><td colspan='4' class='center'>No Record Found</td></tr>");
		
	  $("#approver",'div#apprv-grps-modal').trigger("chosen:updated");
	});

	$(document).off("click","a.add-approver-remove").on("click", "a.add-approver-remove", function(e) {
	  e.preventDefault();
	  
	  $("#add-approver option[value='"+$(this).data("id")+"']","div#add-apprv-grps-modal").removeAttr("disabled");
	  
	  $(this).parent().parent().remove();
	  
	  if($("#add-approver_tbl").html() == "")
	    $("#add-approver_tbl").html("<tr id='no_assignment'><td colspan='4' class='center'>No Record Found</td></tr>");
		
	  $("#add-approver",'div#add-apprv-grps-modal').trigger("chosen:updated");
	});
	
	$("#uploader-add",'div#apprv-grps-modal').off("click").click(function(e) {
	  e.preventDefault();
	  
	  if($("#uploader",'div#apprv-grps-modal').val() == "") {
	    $("#uploader",'div#apprv-grps-modal').parent().addClass("has-error");
	    return false;
	  }
	  
	  $("#uploader",'div#apprv-grps-modal').parent().removeClass("has-error");
	  
	  var table_dtl = "<tr>\
						  <td>\
						    <input type='hidden' name='uploader[]' value='"+$("#uploader").val()+"' \>\
						    "+$("#uploader option:selected").text()+"\
						  </td>\
						  <td class='center'>\
							<span class='label label-warning'>\
							  <i class='fa fa-flag-o'></i>  Pending for Saving\
							</span>\
						  </td>\
						  <td class='center'>\
							<a class='red uploader-remove' href='#'>\
							  <i class='ace-icon fa fa-trash-o bigger-130'></i>\
							</a>\
						  </td>\
						</tr>";
	  
	  if($("#no_assignment","#uploader_tbl").html() == undefined)
	    $("#uploader_tbl",'div#apprv-grps-modal').append(table_dtl);
	  else
	    $("#uploader_tbl",'div#apprv-grps-modal').html(table_dtl);
	  
	  $("#uploader option[value='"+$("#uploader").val()+"']","div#apprv-grps-modal").attr("disabled",true);
	  $("#uploader",'div#apprv-grps-modal').val("");
	  $("#uploader",'div#apprv-grps-modal').trigger("chosen:updated");
	});
	
	$("#add-uploader-add",'div#add-apprv-grps-modal').off("click").click(function(e) {
	  e.preventDefault();
	  
	  if($("#add-uploader",'div#add-apprv-grps-modal').val() == "") {
	    $("#add-uploader",'div#add-apprv-grps-modal').parent().addClass("has-error");
	    return false;
	  }
	  
	  $("#add-uploader",'div#add-apprv-grps-modal').parent().removeClass("has-error");
	  
	  var table_dtl = "<tr>\
						  <td>\
						    <input type='hidden' name='add-uploader[]' value='"+$("#add-uploader").val()+"' \>\
						    "+$("#add-uploader option:selected").text()+"\
						  </td>\
						  <td class='center'>\
							<span class='label label-warning'>\
							  <i class='fa fa-flag-o'></i>  Pending for Saving\
							</span>\
						  </td>\
						  <td class='center'>\
							<a class='red add-uploader-remove' href='#' data-id='"+$("#add-uploader").val()+"'>\
							  <i class='ace-icon fa fa-trash-o bigger-130'></i>\
							</a>\
						  </td>\
						</tr>";
	  
	  if($("#no_assignment","#add-uploader_tbl").html() == undefined)
	    $("#add-uploader_tbl",'div#add-apprv-grps-modal').append(table_dtl);
	  else
	    $("#add-uploader_tbl",'div#add-apprv-grps-modal').html(table_dtl);
	  
	  $("#add-uploader option[value='"+$("#add-uploader").val()+"']","div#add-apprv-grps-modal").attr("disabled",true);
	  $("#add-uploader",'div#add-apprv-grps-modal').val("");
	  $("#add-uploader",'div#add-apprv-grps-modal').trigger("chosen:updated");
	});
	
	$("#approver-add",'div#apprv-grps-modal').off("click").click(function(e) {
	  e.preventDefault();
	  
	  if($("#approver",'div#apprv-grps-modal').val() == "") {
	    $("#approver",'div#apprv-grps-modal').parent().addClass("has-error");
	    return false;
	  }
	  
	  $("#approver",'div#apprv-grps-modal').parent().removeClass("has-error");
	  
	  var table_dtl = "<tr>\
						  <td>\
						    <input type='hidden' name='approver[]' value='"+$("#approver").val()+"' \>\
							<input type='hidden' name='approver_lvl[]' value='"+$("#approver_lvl").val()+"' \>\
						    "+$("#approver option:selected").text()+"\
						  </td>\
						  <td>"+$("#approver_lvl option:selected").val()+"</td>\
						  <td class='center'>\
							<span class='label label-warning'>\
							  <i class='fa fa-flag-o'></i>  Pending for Saving\
							</span>\
						  </td>\
						  <td class='center'>\
							<a class='red approver-remove' href='#'>\
							  <i class='ace-icon fa fa-trash-o bigger-130'></i>\
							</a>\
						  </td>\
						</tr>";
	  
	  if($("#no_assignment","#approver_tbl").html() == undefined)
	    $("#approver_tbl",'div#apprv-grps-modal').append(table_dtl);
	  else
	    $("#approver_tbl",'div#apprv-grps-modal').html(table_dtl);
	  
	  $("#approver option[value='"+$("#approver").val()+"']","div#apprv-grps-modal").attr("disabled",true);
	  $("#approver",'div#apprv-grps-modal').val("");
	  $("#approver_lvl",'div#apprv-grps-modal').val("1");
	  $("#approver",'div#apprv-grps-modal').trigger("chosen:updated");
	});
	
	$("#add-approver-add",'div#add-apprv-grps-modal').off("click").click(function(e) {
	  e.preventDefault();
	  
	  if($("#add-approver",'div#add-apprv-grps-modal').val() == "") {
	    $("#add-approver",'div#add-apprv-grps-modal').parent().addClass("has-error");
	    return false;
	  }
	  
	$("#add-approver",'div#add-apprv-grps-modal').parent().removeClass("has-error");
	  
	  var table_dtl = "<tr>\
						  <td>\
						    <input type='hidden' name='add-approver[]' value='"+$("#add-approver").val()+"' \>\
							<input type='hidden' name='add-approver_lvl[]' value='"+$("#add-approver_lvl").val()+"' \>\
						    "+$("#add-approver option:selected").text()+"\
						  </td>\
						  <td>"+$("#add-approver_lvl option:selected").val()+"</td>\
						  <td class='center'>\
							<span class='label label-warning'>\
							  <i class='fa fa-flag-o'></i>  Pending for Saving\
							</span>\
						  </td>\
						  <td class='center'>\
							<a class='red add-approver-remove' href='#' data-id='"+$("#add-approver").val()+"'>\
							  <i class='ace-icon fa fa-trash-o bigger-130'></i>\
							</a>\
						  </td>\
						</tr>";
	  
	  if($("#no_assignment","#add-approver_tbl").html() == undefined)
	    $("#add-approver_tbl",'div#add-apprv-grps-modal').append(table_dtl);
	  else
	    $("#add-approver_tbl",'div#add-apprv-grps-modal').html(table_dtl);
	  
	  $("#add-approver option[value='"+$("#add-approver").val()+"']","div#add-apprv-grps-modal").attr("disabled",true);
	  $("#add-approver",'div#add-apprv-grps-modal').val("");
	  $("#add-approver_lvl",'div#add-apprv-grps-modal').val("1");
	  $("#add-approver",'div#add-apprv-grps-modal').trigger("chosen:updated");
	});
	
	$("#add-apprv-btn").off("click").click(function(e) {
	  $('div#add-apprv-grps-modal').modal('show');
	});
	
	$(window)
	.off('resize.chosen')
	.on('resize.chosen', function() {
		$('.chosen-select').each(function() {
			 var $this = $(this);
			 $this.next().css({'width': $this.parent().width()});
		})
	}).trigger('resize.chosen');
	
	$("#apprv-grp-save", "#apprv-grps-modal").off("click").click(function(e) {
	  e.preventDefault();
	  var valid = true;
	  
	  if($("#apprv-grp-code","#apprv-grps-modal").val() == ""){
	    $("#apprv-grp-code","#apprv-grps-modal").parent().addClass("has-error");
		$('ul#apprv-grp-dtl a:first',"#apprv-grps-modal").tab('show');
		valid = false;
	  }
	  
	  if(!valid)
	    return false;
	  
	  $("#apprv-grp-code","#apprv-grps-modal").parent().removeClass("has-error");
	  
	  $(this).html('<i class="ace-icon fa fa-spinner fa-spin bigger-110"></i> Saving&hellip;');
	  $(this).attr("disabled",true);
	  
	  $(".success-msg","#apprv-grps-modal").html("");
	  $("div.alert-success","#apprv-grps-modal").addClass("hidden");
	  $(".err-msg","#apprv-grps-modal").html("");
	  $("div.alert-danger","#apprv-grps-modal").addClass("hidden");
	  
	  var uploader_del_str = "";
	  $.each(uploader_del_arr, function(i,v) {
	    uploader_del_str += "&uploader_del_arr[]="+v;
	  });
	  
	  var approver_del_str = "";
	  $.each(approver_del_arr, function(i,v) {
	    approver_del_str += "&approver_del_arr[]="+v;
	  });

	  $.post(
	    base_url + "timekeeping/updateApprovalGroup",
		$("#approval_group").serialize()+uploader_del_str+approver_del_str,
		function(response) {
		  if(response.success) {
		    $(".success-msg","#apprv-grps-modal").html(response.msg);
			$("div.alert-success","#apprv-grps-modal").removeClass("hidden");
			
			setTimeout(function(){
			  $("div.alert-success","#apprv-grps-modal").addClass("hidden"); 
			  $('div#apprv-grps-modal').modal('hide'); 
			  group_table_api.ajax.url(base_url + 'timekeeping/getAllApprovalGroups/' + ($('input#app-grps-display-inactive').prop('checked') ? '1' : '0')).load();
			},3000);
			
			uploader_del_arr = new Array();
			approver_del_arr = new Array();
			
		  }
		  else {
		    $(".err-msg","#apprv-grps-modal").html(response.msg);
			$("div.alert-danger","#apprv-grps-modal").removeClass("hidden");
			
			setTimeout(function(){ $("div.alert-danger","#apprv-grps-modal").addClass("hidden"); },3000);
		  }
		  
		  $("#apprv-grp-save").html('<i class="ace-icon fa fa-save bigger-110"></i> Update');
		  $("#apprv-grp-save").removeAttr("disabled");
		},
		"json"
	  );
	});
	
	$("#apprv-grp-save-add", "#add-apprv-grps-modal").off("click").click(function(e) {
	  e.preventDefault();
	  var valid = true;
	  
	  if($("#add-apprv-grp-code", "#add-apprv-grps-modal").val() == "") {
	    $("#add-apprv-grp-code", "#add-apprv-grps-modal").parent().addClass("has-error");
		$('ul#add-apprv-grp-dtl a:first', "#add-apprv-grps-modal").tab('show');
		valid = false;
	  }
	  
	  if(!valid)
	    return false;
	  
	  $("#add-apprv-grp-code", "#add-apprv-grps-modal").parent().removeClass("has-error");
	  
	  $(this).html('<i class="ace-icon fa fa-spinner fa-spin bigger-110"></i> Saving&hellip;');
	  $(this).attr("disabled",true);
	  
	  $(".success-msg","#add-apprv-grps-modal").html("");
	  $("div.alert-success","#add-apprv-grps-modal").addClass("hidden");
	  $(".err-msg","#add-apprv-grps-modal").html("");
	  $("div.alert-danger","#add-apprv-grps-modal").addClass("hidden");
	  
	  $.post(
	    base_url + "timekeeping/insertApprovalGroup",
		$("#add_approval_group").serialize(),
		function(response) {
		  if(response.success) {
		    $(".success-msg","#add-apprv-grps-modal").html(response.msg);
			$("div.alert-success","#add-apprv-grps-modal").removeClass("hidden");
			
			setTimeout(function(){
			  $("div.alert-success","#add-apprv-grps-modal").addClass("hidden"); 
			  $('div#add-apprv-grps-modal').modal('hide'); 
			  group_table_api.ajax.url(base_url + 'timekeeping/getAllApprovalGroups/' + ($('input#app-grps-display-inactive').prop('checked') ? '1' : '0')).load();
			},3000);
		  }
		  else {
		    $(".err-msg","#add-apprv-grps-modal").html(response.msg);
			$("div.alert-danger","#add-apprv-grps-modal").removeClass("hidden");
			
			setTimeout(function(){ $("div.alert-danger","#add-apprv-grps-modal").addClass("hidden"); },3000);
		  }
		  
		  $("#apprv-grp-save-add").html('<i class="ace-icon fa fa-save bigger-110"></i> Save');
		  $("#apprv-grp-save-add").removeAttr("disabled");
		},
		"json"
	  );
	});
	
	$(".delete-apprv-btn", "#delete-modal").off("click").click(function(e) {
	  e.preventDefault();
	  $.post(
	    base_url + "timekeeping/deleteApprovalGroup",
		{apprv_id: $('button.delete-apprv-btn',"div#delete-modal").data("id")},
		function(response) {
		  $('div#delete-modal').modal('hide'); 
		  group_table_api.ajax.url(base_url + 'timekeeping/getAllApprovalGroups/' + ($('input#app-grps-display-inactive').prop('checked') ? '1' : '0')).load();
		},
		"json"
	  );
	});
	
	/* CWS */
	
	var cws_group_table = $('table#cws-group-codes-table').dataTable({
			serverSide: true,
			ajax: {
				url: base_url + 'timekeeping/getAllCWSApprovalGroups/' + ($('input#app-cws-grps-display-inactive').prop('checked') ? '1' : '0'),
				type: "POST"
			},
			deferRender: true,
			autoWidth: false,
			method: "post",
			columns: [
					{
						orderable: false,
						data: "cws_apprv_grp_id",
						render: function (d) {
								return '<label class="position-relative"><input type="checkbox" class="ace" value="' + d + '" /><span class="lbl"></span></label>';
							},
						className: 'center'
					},
					{ data: "group_code" },
					{ data: "updated_datetime" },
					{ data: "enabled_lbl" },
					{ 
						orderable: false,
						data: "cws_apprv_grp_id",
						render: function (d,t,r,m) {
						  return '\
										<div class="hidden-sm hidden-xs action-buttons">\
											<a class="green cws-group-edit" href="#">\
												<i class="ace-icon fa fa-pencil-square-o bigger-130"></i>\
											</a>\
											<a class="red cws-group-remove" href="#">\
												<i class="ace-icon fa fa-trash-o bigger-130"></i>\
											</a>\
										</div>\
										<div class="hidden-md hidden-lg">\
											<div class="inline position-relative">\
												<button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown" data-position="auto">\
													<i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>\
												</button>\
												<ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">\
													<li>\
														<a href="#" class="tooltip-success cws-group-edit" data-rel="tooltip" title="Edit">\
															<span class="green">\
																<i class="ace-icon fa fa-pencil-square-o bigger-120"></i>\
															</span>\
														</a>\
													</li>\
													<li>\
														<a href="#" class="tooltip-error cws-group-remove" data-rel="tooltip" title="Delete">\
															<span class="red">\
																<i class="ace-icon fa fa-trash-o bigger-120"></i>\
															</span>\
														</a>\
													</li>\
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
			  $('a.cws-group-edit', r).click(function (e) {
				e.preventDefault();
				$('button#cws-apprv-grp-save',"div#cws-apprv-grps-modal").data({id: d["cws_apprv_grp_id"]});
				$('div#cws-apprv-grps-modal').modal('show');
			  });
			  $('a.cws-group-remove', r).click(function (e) {
				e.preventDefault();
				$('button.delete-cws-apprv-btn',"div#cws-delete-modal").data({id: d["cws_apprv_grp_id"], label: d["group_code"]});
				$('div#cws-delete-modal').modal('show');
			  });
			}
	      }),
	cws_group_table_api = cws_group_table.api();;

	$("input#app-cws-grps-display-inactive").off("change").change(function(){
	  cws_group_table_api.ajax.url(base_url + 'timekeeping/getAllCWSApprovalGroups/' + ($(this).prop('checked') ? '1' : '0')).load();
	});
	
	$('div#cws-apprv-grps-modal')
	.modal({
		backdrop: 'static',
		show: false
	})
	.off('show.bs.modal')
	.on('show.bs.modal', function () {
	  $(".success-msg",this).html("");
	  $('div.alert-success', this).addClass('hidden');
	  $(".err-msg",this).html("");
	  $('div.alert-danger', this).addClass('hidden');
	  
	  $.getJSON(
		base_url + 'timekeeping/getCWSApprovalGroup/' + $('button#cws-apprv-grp-save').data('id'),
		function (response) {
		  $('input#cws-apprv-grp-id'	,'div#cws-apprv-grps-modal').val(response.data[0].cws_apprv_grp_id);
		  $('input#cws-apprv-grp-code'	,'div#cws-apprv-grps-modal').val(response.data[0].group_code);
		  $('input#cws-apprv-grp-status','div#cws-apprv-grps-modal').val(response.data[0].enabled);
		  
		  var approver_list = uploader_list = "<option value=''></option>";
		  $.each(response.emp, function(i,v) {
		    approver_list += "<option value='"+v.mb_no+"'>"+(v.mb_3=="Expat"?v.mb_nick:v.mb_fname)+" "+v.mb_lname+"</option>";
		  });
		  $("#cws_approver",'div#cws-apprv-grps-modal').html(approver_list);
		  		  
		  if(response.data[0].approvers.length) {
		    var table_dtl = "";
		    $.each(response.data[0].approvers, function(i,v) {
		      $("#cws-approver option[value='"+v.mb_id+"']","div#cws-apprv-grps-modal").attr("disabled",true);
			  table_dtl += "<tr>\
							  <td>"+(v.mb_3=="Expat"?v.mb_nick:v.mb_fname)+" "+v.mb_lname+"</td>\
							  <td class='center'>"+v.level+"</td>\
							  <td class='center'>\
							    <span class='label label-success arrowed'>\
								  <i class='fa fa-check'></i>  Active\
								</span>\
							  </td>\
							  <td class='center'>\
							    <a class='red cws-approver-remove' href='#' data-id='"+v.mb_id+"'>\
								  <i class='ace-icon fa fa-trash-o bigger-130'></i>\
								</a>\
							  </td>\
							</tr>";
		    });
			$("#cws_approver_tbl",'div#cws-apprv-grps-modal').html(table_dtl);
		  }
		  else {
		    $("#cws_approver_tbl",'div#cws-apprv-grps-modal').html("<tr id='cws_no_assignment'><td colspan='4' class='center'>No Record Found</td></tr>");
		  }
		  $('.chosen-select','div#cws-apprv-grps-modal').chosen({allow_single_deselect:true, width: "100%"}); 
		  $('.chosen-select','div#cws-apprv-grps-modal').trigger("chosen:updated");
		  $("#cws_approver_chosen",'div#cws-apprv-grps-modal').css("width","100%");
		}
	  );
	});
	
	$('div#add-cws-apprv-grps-modal')
	.modal({
		backdrop: 'static',
		show: false
	})
	.off('show.bs.modal')
	.on('show.bs.modal', function () {
	  $(".success-msg",this).html("");
	  $('div.alert-success', this).addClass('hidden');
	  $(".err-msg",this).html("");
	  $('div.alert-danger', this).addClass('hidden');
	  
	  $.getJSON(
		base_url + 'timekeeping/getCWSApprovalGroupFields',
		function (response) {
		  $('input#add-cws-apprv-grp-code'	,'div#add-cws-apprv-grps-modal').val("");
		  $('input#add-cws-apprv-grp-status','div#add-cws-apprv-grps-modal').val("");
		  var approver_list = "<option value=''></option>";
		  $.each(response.emp, function(i,v) {
		    approver_list += "<option value='"+v.mb_no+"'>"+(v.mb_3=="Expat"?v.mb_nick:v.mb_fname)+" "+v.mb_lname+"</option>";
		  });
		  $("#add_cws_approver",'div#add-cws-apprv-grps-modal').html(approver_list);
		  $("#add_cws_approver_tbl",'div#add-cws-apprv-grps-modal').html("<tr id='add_cws_no_approver'><td colspan='4' class='center'>No Record Found</td></tr>");
		  $('.chosen-select','div#add-cws-apprv-grps-modal').chosen({allow_single_deselect:true, width: "100%"}); 
		  $('.chosen-select','div#add-cws-apprv-grps-modal').trigger("chosen:updated");
		  $("#add_cws_approver_chosen",'div#add-cws-apprv-grps-modal').css("width","100%");
		}
	  );
	});
	
	$('div#cws-delete-modal')
	.modal({
		backdrop: 'static',
		show: false
	})
	.off('show.bs.modal')
	.on('show.bs.modal', function () {
	  $("#cws_apprv_lbl").html($('button.delete-cws-apprv-btn',"div#cws-delete-modal").data("label"));
	});
	
    $(document).off("click","a.cws-approver-remove").on("click", "a.cws-approver-remove", function(e) {
	  e.preventDefault();
	  
	  if(typeof $(this).data("id") !== "undefined")
	    if($.inArray($(this).data("id"),cws_approver_del_arr) == -1)
	      cws_approver_del_arr.push($(this).data("id"));
	  
	  $("#cws-approver option[value='"+$(this).data("id")+"']","div#cws-apprv-grps-modal").removeAttr("disabled");
	  
	  $(this).parent().parent().remove();
	  
	  if($("#cws_approver_tbl").html() == "")
	    $("#cws_approver_tbl").html("<tr id='cws_no_assignment'><td colspan='4' class='center'>No Record Found</td></tr>");
		
	  $("#cws_approver",'div#cws-apprv-grps-modal').trigger("chosen:updated");
	});

	$(document).off("click","a.add-cws-approver-remove").on("click", "a.add-cws-approver-remove", function(e) {
	  e.preventDefault();
	  
	  $("#add_cws_approver option[value='"+$(this).data("id")+"']","div#add-cws-apprv-grps-modal").removeAttr("disabled");
	  
	  $(this).parent().parent().remove();
	  
	  if($("#add_cws_approver_tbl").html() == "")
	    $("#add_cws_approver_tbl").html("<tr id='add_cws_no_approver'><td colspan='4' class='center'>No Record Found</td></tr>");
		
	  $("#add_cws_approver",'div#add-cws-apprv-grps-modal').trigger("chosen:updated");
	});
	
	$("#cws-approver-add",'div#cws-apprv-grps-modal').off("click").click(function(e) {
	  e.preventDefault();
	  
	  if($("#cws_approver",'div#cws-apprv-grps-modal').val() == "") {
	    $("#cws_approver",'div#cws-apprv-grps-modal').parent().addClass("has-error");
	    return false;
	  }
	  
	  $("#cws_approver",'div#cws-apprv-grps-modal').parent().removeClass("has-error");
	  
	  var table_dtl = "<tr>\
						  <td>\
						    <input type='hidden' name='cws_approver[]' value='"+$("#cws_approver").val()+"' \>\
							<input type='hidden' name='cws_approver_lvl[]' value='"+$("#cws_approver_lvl").val()+"' \>\
						    "+$("#cws_approver option:selected").text()+"\
						  </td>\
						  <td>"+$("#cws_approver_lvl option:selected").val()+"</td>\
						  <td class='center'>\
							<span class='label label-warning'>\
							  <i class='fa fa-flag-o'></i>  Pending for Saving\
							</span>\
						  </td>\
						  <td class='center'>\
							<a class='red approver-remove' href='#'>\
							  <i class='ace-icon fa fa-trash-o bigger-130'></i>\
							</a>\
						  </td>\
						</tr>";
	  
	  if($("#cws_no_assignment","#cws_approver_tbl").html() == undefined)
	    $("#cws_approver_tbl",'div#cws-apprv-grps-modal').append(table_dtl);
	  else
	    $("#cws_approver_tbl",'div#cws-apprv-grps-modal').html(table_dtl);
	  
	  $("#cws_approver option[value='"+$("#cws_approver").val()+"']","div#cws-apprv-grps-modal").attr("disabled",true);
	  $("#cws_approver",'div#cws-apprv-grps-modal').val("");
	  $("#cws_approver_lvl",'div#cws-apprv-grps-modal').val("1");
	  $("#cws_approver",'div#cws-apprv-grps-modal').trigger("chosen:updated");
	});
	
	$("#add-cws-approver-add",'div#add-cws-apprv-grps-modal').off("click").click(function(e) {
	  e.preventDefault();
	  
	  if($("#add_cws_approver",'div#add-cws-apprv-grps-modal').val() == "") {
	    $("#add_cws_approver",'div#add-cws-apprv-grps-modal').parent().addClass("has-error");
	    return false;
	  }
	  
	$("#add_cws_approver",'div#add-cws-apprv-grps-modal').parent().removeClass("has-error");
	  
	  var table_dtl = "<tr>\
						  <td>\
						    <input type='hidden' name='add_cws_approver[]' value='"+$("#add_cws_approver").val()+"' \>\
							<input type='hidden' name='add_cws_approver_lvl[]' value='"+$("#add_cws_approver_lvl").val()+"' \>\
						    "+$("#add_cws_approver option:selected").text()+"\
						  </td>\
						  <td>"+$("#add_cws_approver_lvl option:selected").val()+"</td>\
						  <td class='center'>\
							<span class='label label-warning'>\
							  <i class='fa fa-flag-o'></i>  Pending for Saving\
							</span>\
						  </td>\
						  <td class='center'>\
							<a class='red add-cws-approver-remove' href='#' data-id='"+$("#add_cws_approver").val()+"'>\
							  <i class='ace-icon fa fa-trash-o bigger-130'></i>\
							</a>\
						  </td>\
						</tr>";
	  
	  if($("#add_cws_no_approver","#add_cws_approver_tbl").html() == undefined)
	    $("#add_cws_approver_tbl",'div#add-cws-apprv-grps-modal').append(table_dtl);
	  else
	    $("#add_cws_approver_tbl",'div#add-cws-apprv-grps-modal').html(table_dtl);
	  
	  $("#add_cws_approver option[value='"+$("#add_cws_approver").val()+"']","div#add-cws-apprv-grps-modal").attr("disabled",true);
	  $("#add_cws_approver",'div#add-cws-apprv-grps-modal').val("");
	  $("#add_cws_approver_lvl",'div#add-cws-apprv-grps-modal').val("1");
	  $("#add_cws_approver",'div#add-cws-apprv-grps-modal').trigger("chosen:updated");
	});

	$("#add-cws-apprv-btn").off("click").click(function(e) {
	  $('div#add-cws-apprv-grps-modal').modal('show');
	});
	
	$("#cws-apprv-grp-save", "#cws-apprv-grps-modal").off("click").click(function(e) {
	  e.preventDefault();
	  var valid = true;
	  
	  if($("#cws-apprv-grp-code","#cws-apprv-grps-modal").val() == "") {
	    $("#cws-apprv-grp-code","#cws-apprv-grps-modal").parent().addClass("has-error");
		valid = false;
	  }
	  
	  if(!valid)
	    return false;
	  
	  $("#cws-apprv-grp-code","#cws-apprv-grps-modal").parent().removeClass("has-error");
	  
	  $(this).html('<i class="ace-icon fa fa-spinner fa-spin bigger-110"></i> Saving&hellip;');
	  $(this).attr("disabled",true);
	  
	  $(".success-msg","#cws-apprv-grps-modal").html("");
	  $("div.alert-success","#cws-apprv-grps-modal").addClass("hidden");
	  $(".err-msg","#cws-apprv-grps-modal").html("");
	  $("div.alert-danger","#cws-apprv-grps-modal").addClass("hidden");
	  
	  var cws_approver_del_str = "";
	  $.each(cws_approver_del_arr, function(i,v) {
	    cws_approver_del_str += "&cws_approver_del_arr[]="+v;
	  });

	  $.post(
	    base_url + "timekeeping/updateCWSApprovalGroup",
		$("#cws_approval_group").serialize()+cws_approver_del_str,
		function(response) {
		  if(response.success) {
		    $(".success-msg","#cws-apprv-grps-modal").html(response.msg);
			$("div.alert-success","#cws-apprv-grps-modal").removeClass("hidden");
			
			setTimeout(function(){
			  $("div.alert-success","#cws-apprv-grps-modal").addClass("hidden"); 
			  $('div#cws-apprv-grps-modal').modal('hide'); 
			  cws_group_table_api.ajax.url(base_url + 'timekeeping/getAllCWSApprovalGroups/' + ($('input#app-cws-grps-display-inactive').prop('checked') ? '1' : '0')).load();
			},3000);
			
			cws_approver_del_arr = new Array();
		  }
		  else {
		    $(".err-msg","#cws-apprv-grps-modal").html(response.msg);
			$("div.alert-danger","#cws-apprv-grps-modal").removeClass("hidden");
			
			setTimeout(function(){ $("div.alert-danger","#cws-apprv-grps-modal").addClass("hidden"); },3000);
		  }
		  
		  $("#cws-apprv-grp-save").html('<i class="ace-icon fa fa-save bigger-110"></i> Update');
		  $("#cws-apprv-grp-save").removeAttr("disabled");
		},
		"json"
	  );
	});
	
	$("#add-cws-apprv-grp-save", "#add-cws-apprv-grps-modal").off("click").click(function(e) {
	  e.preventDefault();
	  var valid = true;
	  
	  if($("#add-cws-apprv-grp-code", "#add-cws-apprv-grps-modal").val() == "") {
	    $("#add-cws-apprv-grp-code", "#add-cws-apprv-grps-modal").parent().addClass("has-error");
		valid = false;
	  }
	  
	  if(!valid)
	    return false;
	  
	  $("#add-cws-apprv-grp-code", "#add-cws-apprv-grps-modal").parent().removeClass("has-error");
	  
	  $(this).html('<i class="ace-icon fa fa-spinner fa-spin bigger-110"></i> Saving&hellip;');
	  $(this).attr("disabled",true);
	  
	  $(".success-msg","#add-cws-apprv-grps-modal").html("");
	  $("div.alert-success","#add-cws-apprv-grps-modal").addClass("hidden");
	  $(".err-msg","#add-cws-apprv-grps-modal").html("");
	  $("div.alert-danger","#add-cws-apprv-grps-modal").addClass("hidden");
	  
	  $.post(
	    base_url + "timekeeping/insertCWSApprovalGroup",
		$("#add_cws_approval_group").serialize(),
		function(response) {
		  if(response.success) {
		    $(".success-msg","#add-cws-apprv-grps-modal").html(response.msg);
			$("div.alert-success","#add-cws-apprv-grps-modal").removeClass("hidden");
			
			setTimeout(function(){
			  $("div.alert-success","#add-cws-apprv-grps-modal").addClass("hidden"); 
			  $('div#add-cws-apprv-grps-modal').modal('hide'); 
			  cws_group_table_api.ajax.url(base_url + 'timekeeping/getAllCWSApprovalGroups/' + ($('input#app-cws-grps-display-inactive').prop('checked') ? '1' : '0')).load();
			},3000);
		  }
		  else {
		    $(".err-msg","#add-cws-apprv-grps-modal").html(response.msg);
			$("div.alert-danger","#add-cws-apprv-grps-modal").removeClass("hidden");
			
			setTimeout(function(){ $("div.alert-danger","#add-cws-apprv-grps-modal").addClass("hidden"); },3000);
		  }
		  
		  $("#add-cws-apprv-grp-save").html('<i class="ace-icon fa fa-save bigger-110"></i> Save');
		  $("#add-cws-apprv-grp-save").removeAttr("disabled");
		},
		"json"
	  );
	});
	
	$(".delete-cws-apprv-btn", "#cws-delete-modal").off("click").click(function(e) {
	  e.preventDefault();
	  $.post(
	    base_url + "timekeeping/deleteCWSApprovalGroup",
		{apprv_id: $('button.delete-cws-apprv-btn',"div#cws-delete-modal").data("id")},
		function(response) {
		  $('div#cws-delete-modal').modal('hide'); 
		  cws_group_table_api.ajax.url(base_url + 'timekeeping/getAllCWSApprovalGroups/' + ($('input#app-cws-grps-display-inactive').prop('checked') ? '1' : '0')).load();
		},
		"json"
	  );
	});
	
	
	/* End of CWS */
});