$(function () {
        
            
	var group_table = $('table#group-codes-table').dataTable({
			serverSide: true,
			ajax: {
				url: base_url + 'groups/getAllGroups/',
				type: "POST",
                                data: function (d) {
                                    return $.extend({}, d, {showactive:($('input#group-display-inactive').prop('checked') ? '1' : '0')});
                                }
			},
			deferRender: true,
			autoWidth: false,
			method: "post",
			columns: [
					{ data: "group_name" },
                                        { 
                                          width: "10%",
                                          orderable:false,  
                                          data: "dept_status",
                                          searchable: false , 
                                          render:function(d){
                                              return d == 1 ? '<span class="label label-success">active</span>' : '<span class="label label-danger">deleted</span>';
                                          },
                                          className: 'center no-highlight',
                                        },
					{ 
                                                width: "15%",
						orderable: false,
						data: "id",
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
			order: [[0, 'asc']],
			rowCallback: function (r, d) {
			  $('a.group-edit', r).click(function (e) {
				e.preventDefault();
				$('button#add-group-save',"div#add-groups-modal").data({id: d["id"]});
				$('div#add-groups-modal').modal('show');
			  });
			  $('a.group-remove', r).click(function (e) {
				e.preventDefault();
				$('button.delete-group-btn',"div#delete-modal").data({id: d["id"], label: d["group_name"]});
				$('div#delete-modal').modal('show');
			  });
			}
	      }),
	group_table_api = group_table.api();;
        
        

	$("input#group-display-inactive").change(function(){
                group_table_api.ajax.url(base_url + 'groups/getAllGroups/').load();
                change_deleted();
	});
	
	$('div#delete-modal')
	.modal({
		backdrop: 'static',
		show: false
	})
	.on('show.bs.modal', function () {
	  $("#group_lbl").html($('button.delete-group-btn',"div#delete-modal").data("label"));
	});
	
	$('div#add-groups-modal')
	.modal({
		backdrop: 'static',
		show: false
	})
	.on('show.bs.modal', function () {
	  $(".success-msg",this).html("");
	  $('div.alert-success', this).addClass('hidden');
	  $(".err-msg",this).html("");
	  $('div.alert-danger', this).addClass('hidden');
	  
	  if($('button#add-group-save').data('id')) {
	    $.getJSON(
		  base_url + 'groups/getGroup/' + $('button#add-group-save').data('id'),
		  function (response) {
		    $("#group_id").val(response.data.id);
			$("#add-group-code").val(response.data.group_name);
                        change_deleted();
		  }
	    );
	  }
	  else {
	    $("#group_id").val("");
		$("#add-group-code").val("");
	  }
          
	});
	
	$("#add-group-btn").click(function(e) {
	  $('button#add-group-save',"div#add-groups-modal").data("id","");
	  $('div#add-groups-modal').modal('show');
          
	});
	
	$("#add-group-save",'div#add-groups-modal').click(function(e) {
	  e.preventDefault();
	  var form_data = $("#add-group").serialize();
	  $("input, select, button, textarea",'div#add-groups-modal').attr("disabled",true);
	  $("button#add-group-save","div#add-groups-modal").html('<i class="ace-icon fa fa-spinner fa-spin bigger-110"></i> Saving&hellip;');
	  if($.trim($("#add-group-code").val()) == "" || $.trim($("#add-group-code").val()) == "") {
	    $(".err-msg","#add-groups-modal").html("Invalid Record");
		$("div.alert-danger","#add-groups-modal").removeClass("hidden");
		
		setTimeout(function(){ $("div.alert-danger","#add-groups-modal").addClass("hidden"); },1500);
		
		$("input, select, button, textarea",'div#add-groups-modal').attr("disabled",false);
		$("button#add-group-save","div#add-groups-modal").html('<i class="ace-icon fa fa-save bigger-110"></i> Save');
		return false;
	  }
	  
	  if($("#group_id").val() == "") {
		  $.post(
			base_url + "groups/insertGroup",
			form_data,
			function(response) {
			  if(response.success) {
				$(".success-msg","#add-groups-modal").html(response.msg);
				$("div.alert-success","#add-groups-modal").removeClass("hidden");
				
				setTimeout(function(){
				  $("div.alert-success","#add-groups-modal").addClass("hidden"); 
				  $('div#add-groups-modal').modal('hide'); 
				  group_table_api.ajax.url(base_url + 'groups/getAllGroups/').load();
				},1500);
			  }
			  else {
				$(".err-msg","#add-groups-modal").html(response.msg);
				$("div.alert-danger","#add-groups-modal").removeClass("hidden");
				
				setTimeout(function(){ $("div.alert-danger","#add-groups-modal").addClass("hidden"); },1500);
			  }
			  
			  $("input, select, button, textarea",'div#add-groups-modal').attr("disabled",false);
			  $("button#add-group-save","div#add-groups-modal").html('<i class="ace-icon fa fa-save bigger-110"></i> Save');
			},
			"json"
		  );
	  }
	  else {
		  $.post(
			base_url + "groups/updateGroup",
			form_data,
			function(response) {
			  if(response.success) {
				$(".success-msg","#add-groups-modal").html(response.msg);
				$("div.alert-success","#add-groups-modal").removeClass("hidden");
				
				setTimeout(function(){
				  $("div.alert-success","#add-groups-modal").addClass("hidden"); 
				  $('div#add-groups-modal').modal('hide'); 
				  group_table_api.ajax.url(base_url + 'groups/getAllGroups/').load();
                                  change_deleted();
				},1500);
			  }
			  else {
				$(".err-msg","#add-groups-modal").html(response.msg);
				$("div.alert-danger","#add-groups-modal").removeClass("hidden");
				setTimeout(function(){ $("div.alert-danger","#add-groups-modal").addClass("hidden"); },1500);
			  }
			  
			  $("input, select, button, textarea",'div#add-groups-modal').attr("disabled",false);
			  $("button#add-group-save","div#add-groups-modal").html('<i class="ace-icon fa fa-save bigger-110"></i> Save');
			},
			"json"
                        
		  );
	  }
          
        });
	
	$(".delete-group-btn", "#delete-modal").click(function(e) {
	  e.preventDefault();
	  $.post(
	    base_url + "groups/deleteGroup",
		{group_id: $('button.delete-group-btn',"div#delete-modal").data("id")},
		function(response) {
		  $('div#delete-modal').modal('hide'); 
		  group_table_api.ajax.url(base_url + 'groups/getAllGroups/').load();
                  change_deleted();
		},
		"json"
	  );
	});
        function change_deleted(){
            
        
        group_table_api.ajax.reload(function(){
            $('table#group-codes-table tr td span.label.label-danger').parent().parent().find('td:last').find('a.group-remove').each(function(){
                 $(this).addClass('hide');
            });
                    
        });
        
        }change_deleted();
});