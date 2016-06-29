$(function () {
	
	var sub_categs_del_arr = new Array();
	var dependents_del_arr = new Array();
	var rules_del_arr = new Array();

	var leave_table = $('table#leave-codes-table').dataTable({
			serverSide: true,
			ajax: {
				url: base_url + 'leave/getAllLeaves/' + ($('input#leave-display-inactive').prop('checked') ? '1' : '0'),
				type: "POST"
			},
			deferRender: true,
			autoWidth: false,
			method: "post",
			columns: [
					{
						orderable: false,
						data: "leave_id",
						render: function (d) {
								return '<label class="position-relative"><input type="checkbox" class="ace" value="' + d + '" /><span class="lbl"></span></label>';
							},
						className: 'center'
					},
					{ data: "leave_code" },
					{ data: "leave_name" },
					{ data: "enabled_lbl" },
					{ 
						orderable: false,
						data: "leave_id",
						render: function (d,t,r,m) {
							return '\
										<div class="hidden-sm hidden-xs action-buttons">\
											<a class="green leave-edit" href="#">\
												<i class="ace-icon fa fa-pencil-square-o bigger-130"></i>\
											</a>\
										</div>\
										<div class="hidden-md hidden-lg">\
											<div class="inline position-relative">\
												<button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown" data-position="auto">\
													<i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>\
												</button>\
												<ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">\
													<li>\
														<a href="#" class="tooltip-success leave-edit" data-rel="tooltip" title="Edit">\
															<span class="green">\
																<i class="ace-icon fa fa-pencil-square-o bigger-120"></i>\
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
			  $('a.leave-edit', r).click(function (e) {
				e.preventDefault();
				$('button#add-leave-save',"div#add-leaves-modal").data({id: d["leave_id"]});
				$('div#add-leaves-modal').modal('show');
			  });
			}
	      }),
	leave_table_api = leave_table.api();;

	$("input#leave-display-inactive").change(function(){
	  leave_table_api.ajax.url(base_url + 'leave/getAllLeaves/' + ($('input#leave-display-inactive').prop('checked') ? '1' : '0')).load();
	});
	
	$('div#add-leaves-modal')
	.modal({
		backdrop: 'static',
		show: false
	})
	.on('show.bs.modal', function () {
	  $(".success-msg",this).html("");
	  $('div.alert-success', this).addClass('hidden');
	  $(".err-msg",this).html("");
	  $('div.alert-danger', this).addClass('hidden');
	  $('input[type!="radio"], select, textarea', this).val("");
	  $("#leave-entitlement-enabled", this).prop("checked",true);
	  $("#leave-entitlement-enabled", this).trigger("click");
	  $("#leave-dependencies-enabled", this).prop("checked",true);
	  $("#leave-dependencies-enabled", this).trigger("click");
	  $("#leave-sub-category-enabled", this).prop("checked",true);
	  $("#leave-sub-category-enabled", this).trigger("click");
	  $("#leave-rules-enabled", this).prop("checked",true);
	  $("#leave-rules-enabled", this).trigger("click");
	  $("#add-subcateg-tbl", this).html("<tr id='no-subcateg'><td colspan='4' class='center'>No Record Found</td></tr>");
	  $("#add-dependencies-tbl",this).html("<tr id='no-dependencies'><td colspan='4' class='center'>No Record Found</td></tr>");
	  $("#add-rules-tbl",this).html("<tr id='no-rules'><td colspan='6' class='center'>No Record Found</td></tr>");
	  if($('button#add-leave-save', this).data("id")) {
	    $.getJSON(
		  base_url + 'leave/getLeave/' + $('button#add-leave-save',"div#add-leaves-modal").data('id'),
		  function (response) {
			$("#leave_id").val(response.data[0].leave_id);
			$("#add-leave-code").val(response.data[0].leave_code);
			$("#add-leave-status").val(response.data[0].status);
			$("#add-leave-name").val(response.data[0].leave_name);
			$("#add-leave-desc").val(response.data[0].leave_desc);
			$("#add-leave-le").val(response.data[0].local_expat);
			$("#add-leave-gender").val(response.data[0].gender);
			$("#add-leave-staggered").val(response.data[0].staggered);
			$("#add-leave-full-consume").val(response.data[0].staggered);
			$("#add-leave-forfeit").val(response.data[0].forfeit_excess);
			$("#add-emp-type").val(response.data[0].emp_type);
			$("#add-leave-req-mc").val(response.data[0].req_mc);
			$("#add-max-advanced-days").val(response.data[0].max_advanced_days);
			$("#leave-sub-category-enabled").prop("checked",!(response.data[0].has_sub_category*1));
			$("#leave-sub-category-enabled").trigger("click");
			$("#leave-dependencies-enabled").prop("checked",!(response.data[0].has_leave_dependency*1));
			$("#leave-dependencies-enabled").trigger("click");
			$("#leave-entitlement-enabled").prop("checked",!(response.data[0].has_entitlement*1));
			$("#leave-entitlement-enabled").trigger("click");
			$("#manual-entitlement").prop("checked",(response.data[0].is_manual_entitlement*1));
			if(response.data[0].is_manual_entitlement*1)
			  $("#manual-entitlement").trigger("click");
			$("#fixed-entitlement").prop("checked",(response.data[0].is_fixed_entitlement*1));
			if(response.data[0].is_fixed_entitlement*1)
			  $("#fixed-entitlement").trigger("click");
			$("#add-leave-entitlement-credit").val(response.data[0].fixed_entitlement*1);
			$("#computed-entitlement").prop("checked",(response.data[0].is_computed_entitlement*1));
			if(response.data[0].is_computed_entitlement*1)
			  $("#computed-entitlement").trigger("click");
			$("#add-leave-entitlement-date").val(response.data[0].start_date);
			$("#add-leave-entitlement-add-date").val(response.data[0].adjustment_day);
			$("#add-leave-entitlement-divisor").val(response.data[0].total_days);
			$("#add-leave-max-entitlement").val(response.data[0].max_entitlement);
			$("#leave-rules-enabled").prop("checked",!(response.data[0].has_rules*1));
			$("#leave-rules-enabled").trigger("click");
			
			$.each(response.subleave,function(i,v){
			  var table_dtl = "<tr>\
						  <td class='center'>\
						  "+v.sub_categ_code+"\
						  </td>\
						  <td class='center'>"+v.sub_categ_name+"</td>\
						  <td class='center'>"+(v.req_mc==1?"Yes":"No")+"</td>\
						  <td class='center'>\
							<span class='label label-success'>\
							  <i class='fa fa-check'></i>  Available\
							</span>\
						  </td>\
						  <td class='center'>\
							<a class='red add-subcateg-remove' href='#' data-id='"+v.sub_categ_id+"'>\
							  <i class='ace-icon fa fa-trash-o bigger-130'></i>\
							</a>\
						  </td>\
						</tr>";
	  
	          if($("#no-subcateg","#add-subcateg-tbl").html() == undefined)
	            $("#add-subcateg-tbl",'div#add-leaves-modal').append(table_dtl);
	          else
	            $("#add-subcateg-tbl",'div#add-leaves-modal').html(table_dtl);
			});
			
			$.each(response.dependents,function(i,v){
			  var table_dtl = "<tr>\
						  <td class='center'>\
						  "+v.leave_code+"-"+v.leave_name+"\
						  </td>\
						  <td class='center'>\
							<span class='label label-success'>\
							  <i class='fa fa-check'></i>  Available\
							</span>\
						  </td>\
						  <td class='center'>\
							<a class='red add-dependencies-remove' href='#' data-id='"+v.leave_dep_id+"'>\
							  <i class='ace-icon fa fa-trash-o bigger-130'></i>\
							</a>\
						  </td>\
						</tr>";
	  
	          if($("#no-dependencies","#add-dependencies-tbl").html() == undefined)
	            $("#add-dependencies-tbl",'div#add-leaves-modal').append(table_dtl);
	          else
	            $("#add-dependencies-tbl",'div#add-leaves-modal').html(table_dtl);
			});
			
			$.each(response.rules,function(i,v){
			  var table_dtl = "<tr>\
						  <td class='center'>\
						  "+(v.sub_categ_id == 0?"Default":v.sub_categ_code+" - "+v.sub_categ_name)+"\
						  </td>\
						  <td class='center'>\
						  "+v.max_days+"\
						  </td>\
						  <td class='center'>\
						  "+v.days_prior+"\
						  </td>\
						  <td class='center'>\
						  "+v.days_after+"\
						  </td>\
						  <td class='center'>\
							<span class='label label-success'>\
							  <i class='fa fa-check'></i>  Available\
							</span>\
						  </td>\
						  <td class='center'>\
							<a class='red add-rules-remove' href='#' data-id='"+v.rule_id+"'>\
							  <i class='ace-icon fa fa-trash-o bigger-130'></i>\
							</a>\
						  </td>\
						</tr>";
	  
	          if($("#no-rules","#add-rules-tbl").html() == undefined)
	            $("#add-rules-tbl",'div#add-leaves-modal').append(table_dtl);
	          else
	            $("#add-rules-tbl",'div#add-leaves-modal').html(table_dtl);
			});
			
		    $.post(
	          base_url + "leave/getLeaves",
		      function(response) {
		        var add_dependency_list = "";
		        $.each(response,function(i,v){
		          add_dependency_list += "<option value='"+v.leave_id+"'>"+v.leave_code+"-"+v.leave_name+"</option>";
		        });
		        $("#add-dependencies-leave").html(add_dependency_list);
		      },
		      "json"
	        );
			
			$.post(
	          base_url + "leave/getSubLeaves",
			  { lv_id: response.data[0].leave_id},
		      function(response) {
		        var add_subleave_rules_list = "<option value='0' data-label='Default'>Default</option>";
		        $.each(response,function(i,v){
		          add_subleave_rules_list += "<option value='"+v.sub_categ_id+"' data-label='"+v.sub_categ_code+" - "+v.sub_categ_name+"'>"+v.sub_categ_code+" - "+v.sub_categ_name+"</option>";
		        });
		        $("#day-sub-categ").html(add_subleave_rules_list);
		      },
		      "json"
	        );
		  }
	    );
	  }
	  else {
	    $.post(
	      base_url + "leave/getLeaves",
		  function(response) {
		    var add_dependency_list = "";
		    $.each(response,function(i,v){
		      add_dependency_list += "<option value='"+v.leave_id+"'>"+v.leave_code+"-"+v.leave_name+"</option>";
		    });
		    $("#add-dependencies-leave").html(add_dependency_list);
		  },
		  "json"
	    );
		
		$.post(
		  base_url + "leave/getSubLeaves",
		  { lv_id: 0},
		  function(response) {
			var add_subleave_rules_list = "<option value='0' data-label='Default'>Default</option>";
			$.each(response,function(i,v){
			  add_subleave_rules_list += "<option value='"+v.sub_categ_id+"' data-label='"+v.sub_categ_code+" - "+v.sub_categ_name+"'>"+v.sub_categ_code+" - "+v.sub_categ_name+"</option>";
			});
			$("#day-sub-categ").html(add_subleave_rules_list);
		  },
		  "json"
		);
	  }
	});
	
	$("#add-leave-btn").click(function(e) {
	  $('button#add-leave-save',"div#add-leaves-modal").data("id","");
	  $('div#add-leaves-modal').modal('show');
	});
	
	$("#add-leave-save",'div#add-leaves-modal').click(function(e) {
	  var form_data = $("#add-leave").serialize();
	  $("input, select, button, textarea",'div#add-leaves-modal').attr("disabled",true);
	  $("button#add-leave-save","div#add-leaves-modal").html('<i class="ace-icon fa fa-spinner fa-spin bigger-110"></i> Saving&hellip;');
	  if($.trim($("#add-leave-code").val()) == "" || $.trim($("#add-leave-name").val()) == "") {
	    $(".err-msg","#add-leaves-modal").html("Invalid Record");
		$("div.alert-danger","#add-leaves-modal").removeClass("hidden");
		
		setTimeout(function(){ $("div.alert-danger","#add-leaves-modal").addClass("hidden"); },1500);
		
		$("input, select, button, textarea",'div#add-leaves-modal').attr("disabled",false);
		$("button#add-leave-save","div#add-leaves-modal").html('<i class="ace-icon fa fa-save bigger-110"></i> Save');
		return false;
	  }
	  if($("#leave_id").val() == "") {
	  $.post(
	    base_url + "leave/insertLeave",
		form_data,
		function(response) {
		  if(response.success) {
		    $(".success-msg","#add-leaves-modal").html(response.msg);
			$("div.alert-success","#add-leaves-modal").removeClass("hidden");
			
			setTimeout(function(){
			  $("div.alert-success","#add-leaves-modal").addClass("hidden"); 
			  $('div#add-leaves-modal').modal('hide'); 
			  leave_table_api.ajax.url(base_url + 'leave/getAllLeaves/' + ($('input#leave-display-inactive').prop('checked') ? '1' : '0')).load();
			},1500);
		  }
		  else {
		    $(".err-msg","#add-leaves-modal").html(response.msg);
			$("div.alert-danger","#add-leaves-modal").removeClass("hidden");
			
			setTimeout(function(){ $("div.alert-danger","#add-leaves-modal").addClass("hidden"); },1500);
		  }
		  $("input, select, button, textarea",'div#add-leaves-modal').attr("disabled",false);
		  $("button#add-leave-save","div#add-leaves-modal").html('<i class="ace-icon fa fa-save bigger-110"></i> Save');
		},
		"json"
	  );
	  }
	  else {
	  var sub_categs_del_str = "";
	  $.each(sub_categs_del_arr, function(i,v) {
	    sub_categs_del_str += "&sub_categs_del_arr[]="+v;
	  });
	  
	  var dependents_del_str = "";
	  $.each(dependents_del_arr, function(i,v) {
	    dependents_del_str += "&dependents_del_arr[]="+v;
	  });
	  
	  var rules_del_str = "";
	  $.each(rules_del_arr, function(i,v) {
	    rules_del_str += "&rules_del_arr[]="+v;
	  });
	  
	  $.post(
	    base_url + "leave/updateLeave",
		form_data+sub_categs_del_str+dependents_del_str+rules_del_str,
		function(response) {
		  if(response.success) {
		    $(".success-msg","#add-leaves-modal").html(response.msg);
			$("div.alert-success","#add-leaves-modal").removeClass("hidden");
			
			setTimeout(function(){
			  $("div.alert-success","#add-leaves-modal").addClass("hidden"); 
			  $('div#add-leaves-modal').modal('hide'); 
			  leave_table_api.ajax.url(base_url + 'leave/getAllLeaves/' + ($('input#leave-display-inactive').prop('checked') ? '1' : '0')).load();
			  $("input, select, button, textarea",'div#add-leaves-modal').removeAttr("disabled");
			},1500);
		  }
		  else {
		    $(".err-msg","#add-leaves-modal").html(response.msg);
			$("div.alert-danger","#add-leaves-modal").removeClass("hidden");
			$("input, select, button, textarea",'div#add-leaves-modal').removeAttr("disabled");
			setTimeout(function(){ $("div.alert-danger","#add-leaves-modal").addClass("hidden"); },1500);
		  }
		  
		  $("button#add-leave-save","div#add-leaves-modal").html('<i class="ace-icon fa fa-save bigger-110"></i> Save');
		},
		"json"
	  );
	  }
	});
	
	$("#leave-entitlement-enabled").click(function(e) {
	  if($(this).prop("checked")) {
	    $("#leave-entitlement-type-div").removeClass("hidden");
	  }
	  else {
	    $("#leave-entitlement-type-div").addClass("hidden");
	  }
	});
	
	$("#leave-dependencies-enabled").click(function(e) {
	  if($(this).prop("checked")) {
	    $("#leave-dependency-div").removeClass("hidden");
	  }
	  else {
	    $("#leave-dependency-div").addClass("hidden");
	  }
	});
	
	$("#leave-sub-category-enabled").click(function(e) {
	  if($(this).prop("checked")) {
	    $("#leave-sub-categ-div").removeClass("hidden");
	  }
	  else {
	    $("#leave-sub-categ-div").addClass("hidden");
	  }
	});
	
	$("#leave-rules-enabled").click(function(e) {
	  if($(this).prop("checked")) {
	    $("#leave-rules-div").removeClass("hidden");
	  }
	  else {
	    $("#leave-rules-div").addClass("hidden");
	  }
	});
	
	$(".entitlement-type").click(function(e) {
	  switch($(this).val()) {
	    case "manual":	$("#leave-entitlement-fixed-type-div").addClass("hidden");
		                $("#leave-entitlement-formula-type-div").addClass("hidden");
						break;
	    case "fixed":	$("#leave-entitlement-fixed-type-div").removeClass("hidden");
		                $("#leave-entitlement-formula-type-div").addClass("hidden");
						break;
		case "formula":	$("#leave-entitlement-fixed-type-div").addClass("hidden");
		                $("#leave-entitlement-formula-type-div").removeClass("hidden");
						break;

	  }
	});
	
	$("#subcateg-add").click(function(e) {
	  e.preventDefault();
	  var valid = true;
	  $("#add-subcateg-code","#add-leaves-modal").parent().removeClass("has-error");
	  $("#add-subcateg-name","#add-leaves-modal").parent().removeClass("has-error");
	  
	  if($("#add-subcateg-code").val() == "") {
	    $("#add-subcateg-code","#add-leaves-modal").parent().addClass("has-error");
		valid = false;
	  } 
	  if($("#add-subcateg-name").val() == "") {
	    $("#add-subcateg-name","#add-leaves-modal").parent().addClass("has-error");
		valid = false;
	  }
	  if($("#add-subcateg-mc").val() == null || $("#add-subcateg-mc").val() == "") {
	    $("#add-subcateg-mc","#add-leaves-modal").parent().addClass("has-error");
		valid = false;
	  }
	  if(!valid)
	    return false;
	  
	  var table_dtl = "<tr>\
						  <td class='center'>\
						    <input type='hidden' name='add-subcateg-codes[]' value='"+$("#add-subcateg-code","#add-leaves-modal").val()+"' \>\
							<input type='hidden' name='add-subcateg-names[]' value='"+$("#add-subcateg-name","#add-leaves-modal").val()+"' \>\
							<input type='hidden' name='add-subcateg-mcs[]' value='"+$("#add-subcateg-mc","#add-leaves-modal").val()+"' \>\
						    "+$("#add-subcateg-code","#add-leaves-modal").val()+"\
						  </td>\
						  <td class='center'>"+$("#add-subcateg-name","#add-leaves-modal").val()+"</td>\
						  <td class='center'>"+($("#add-subcateg-mc","#add-leaves-modal").val()==1?"Yes":"No")+"</td>\
						  <td class='center'>\
							<span class='label label-warning'>\
							  <i class='fa fa-flag-o'></i>  Pending for Saving\
							</span>\
						  </td>\
						  <td class='center'>\
							<a class='red add-subcateg-remove' href='#'>\
							  <i class='ace-icon fa fa-trash-o bigger-130'></i>\
							</a>\
						  </td>\
						</tr>";
	  
	  if($("#no-subcateg","#add-subcateg-tbl").html() == undefined)
	    $("#add-subcateg-tbl",'div#add-leaves-modal').append(table_dtl);
	  else
	    $("#add-subcateg-tbl",'div#add-leaves-modal').html(table_dtl);
	  
	  $("#add-subcateg-code","#add-leaves-modal").val("");
	  $("#add-subcateg-name","#add-leaves-modal").val("");
	  $("#add-subcateg-mc","#add-leaves-modal").val("");
	});
	
	$("#dependencies-add").click(function(e) {
	  e.preventDefault();
	  var valid = true;
	  $("#add-dependencies-leave","#add-leaves-modal").parent().removeClass("has-error");

	  if($("#add-dependencies-leave").val() == "") {
	    $("#add-dependencies-leave","#add-leaves-modal").parent().addClass("has-error");
		valid = false;
	  } 
	  
	  if(!valid)
	    return false;
	  
	  var table_dtl = "<tr>\
						  <td class='center'>\
						    <input type='hidden' name='add-dependencies-leaves[]' value='"+$("#add-dependencies-leave","#add-leaves-modal").val()+"' \>\
						    "+$("#add-dependencies-leave option:selected","#add-leaves-modal").text()+"\
						  </td>\
						  <td class='center'>\
							<span class='label label-warning'>\
							  <i class='fa fa-flag-o'></i>  Pending for Saving\
							</span>\
						  </td>\
						  <td class='center'>\
							<a class='red add-dependencies-remove' href='#'>\
							  <i class='ace-icon fa fa-trash-o bigger-130'></i>\
							</a>\
						  </td>\
						</tr>";
	  
	  if($("#no-dependencies","#add-dependencies-tbl").html() == undefined)
	    $("#add-dependencies-tbl",'div#add-leaves-modal').append(table_dtl);
	  else
	    $("#add-dependencies-tbl",'div#add-leaves-modal').html(table_dtl);
	  
	  $("#add-dependencies-leave","#add-leaves-modal").val("");
	});
	
	$("#rules-add").click(function(e) {
	  e.preventDefault();
	  var valid = true;
	  $("#day-sub-categ","#add-leaves-modal").parent().removeClass("has-error");
	  $("#days-leave","#add-leaves-modal").parent().removeClass("has-error");
	  $("#days-prior","#add-leaves-modal").parent().removeClass("has-error");
	  $("#days-later","#add-leaves-modal").parent().removeClass("has-error");
	  
	  if($("#day-sub-categ").val() == "" || isNaN($("#day-sub-categ").val())) {
	    $("#day-sub-categ",'div#apprv-grps-modal').parent().addClass("has-error");
	    valid = false;
	  }
	  if($("#days-leave").val() == "" || isNaN($("#days-leave").val())) {
	    $("#days-leave","#add-leaves-modal").parent().addClass("has-error");
		valid = false;
	  } 
	  if($("#days-prior").val() == "" || isNaN($("#days-prior").val())) {
	    $("#days-prior","#add-leaves-modal").parent().addClass("has-error");
		valid = false;
	  }
	  if($("#days-later").val() == "" || isNaN($("#days-later").val())) {
	    $("#days-later","#add-leaves-modal").parent().addClass("has-error");
		valid = false;
	  }
	  
	  if(!valid)
	    return false;
	  
	  var table_dtl = "<tr>\
						  <td class='center'>\
						    <input type='hidden' name='add-sub-categs[]' value='"+$("#day-sub-categ","#add-leaves-modal").val()+"' \>\
						    <input type='hidden' name='add-days[]' value='"+$("#days-leave","#add-leaves-modal").val()+"' \>\
							<input type='hidden' name='add-prior[]' value='"+$("#days-prior","#add-leaves-modal").val()+"' \>\
							<input type='hidden' name='add-later[]' value='"+$("#days-later","#add-leaves-modal").val()+"' \>\
						    "+$("#day-sub-categ option:selected","#add-leaves-modal").data("label")+"\
						  </td>\
						  <td class='center'>"+$("#days-leave","#add-leaves-modal").val()+"</td>\
						  <td class='center'>"+$("#days-prior","#add-leaves-modal").val()+"</td>\
						  <td class='center'>"+$("#days-later","#add-leaves-modal").val()+"</td>\
						  <td class='center'>\
							<span class='label label-warning'>\
							  <i class='fa fa-flag-o'></i>  Pending for Saving\
							</span>\
						  </td>\
						  <td class='center'>\
							<a class='red add-rules-remove' href='#'>\
							  <i class='ace-icon fa fa-trash-o bigger-130'></i>\
							</a>\
						  </td>\
						</tr>";
	  
	  if($("#no-rules","#add-rules-tbl").html() == undefined)
	    $("#add-rules-tbl",'div#add-leaves-modal').append(table_dtl);
	  else
	    $("#add-rules-tbl",'div#add-leaves-modal').html(table_dtl);
	  
	  $("#days-leave","#add-leaves-modal").val("");
	  $("#days-prior","#add-leaves-modal").val("");
	  $("#days-later","#add-leaves-modal").val("");
	});
	
	$(document).on("click", "a.add-subcateg-remove", function(e) {
	  e.preventDefault();
	  
	  $(this).parent().parent().remove();
	  
	  if(typeof $(this).data("id") !== "undefined")
	    if($.inArray($(this).data("id"),sub_categs_del_arr) == -1)
	      sub_categs_del_arr.push($(this).data("id"));
	  
	  if($("#add-subcateg-tbl",'div#add-leaves-modal').html() == "")
	    $("#add-subcateg-tbl",'div#add-leaves-modal').html("<tr id='no-subcateg'><td colspan='4' class='center'>No Record Found</td></tr>");
	});

	$(document).on("click", "a.add-dependencies-remove", function(e) {
	  e.preventDefault();
	  
	  $(this).parent().parent().remove();
	  
	  if(typeof $(this).data("id") !== "undefined")
	    if($.inArray($(this).data("id"),dependents_del_arr) == -1)
	      dependents_del_arr.push($(this).data("id"));
	  
	  if($("#add-dependencies-tbl",'div#add-leaves-modal').html() == "")
	    $("#add-dependencies-tbl",'div#add-leaves-modal').html("<tr id='no-dependencies'><td colspan='4' class='center'>No Record Found</td></tr>");
	});
	
	$(document).on("click", "a.add-rules-remove", function(e) {
	  e.preventDefault();
	  
	  $(this).parent().parent().remove();
	  
	  if(typeof $(this).data("id") !== "undefined")
	    if($.inArray($(this).data("id"),rules_del_arr) == -1)
	      rules_del_arr.push($(this).data("id"));
	  
	  if($("#add-rules-tbl",'div#add-leaves-modal').html() == "")
	    $("#add-rules-tbl",'div#add-leaves-modal').html("<tr id='no-rules'><td colspan='6' class='center'>No Record Found</td></tr>");
	});
	
	$("#gen-set-save").click(function(e) {
	  e.preventDefault();
	  
	  $(this).html('<i class="ace-icon fa fa-spinner fa-spin bigger-110"></i> Saving&hellip;');
	  $(this).attr("disabled",true);
	  
	  $(".success-msg","#gen-setting").html("");
	  $("div.alert-success","#gen-setting").addClass("hidden");
	  $(".err-msg","#gen-setting").html("");
	  $("div.alert-danger","#gen-setting").addClass("hidden");
	  
	  $.post(
	    base_url + "leave/updateGeneralSettings",
		$("#gen-form").serialize(),
		function(response) {
		  if(response.success) {
		    $(".success-msg","#gen-setting").html(response.msg);
			$("div.alert-success","#gen-setting").removeClass("hidden");
			
			setTimeout(function(){ $("div.alert-success","#gen-setting").addClass("hidden"); },3000);
		  }
		  else {
		    $(".err-msg","#gen-setting").html(response.msg);
			$("div.alert-danger","#gen-setting").removeClass("hidden");
			
			setTimeout(function(){ $("div.alert-danger","#gen-setting").addClass("hidden"); },3000);
		  }
		  
		  $("#gen-set-save").html('<i class="ace-icon fa fa-save bigger-110"></i> Save');
		  $("#gen-set-save").removeAttr("disabled");
		},
		"json"
	  );
	});

	
});