$(function () {
	var group_table = $('table#group-members-table').dataTable({
			serverSide: true,
			ajax: {
				url: base_url + 'groups/getAllGroupMembers/',
				type: "POST",
				data: function(d) {
				  d.group = $('button#grp-filter-group-btn').data('id');
			    }
			},
			deferRender: true,
			autoWidth: false,
			method: "post",
			columns: [
					{ data: "group_name"},
					{ data: "mb_id",
                                          searchable:false},
					{ data: "mb_nick" },
					{ data: "mb_fname" },
					{ data: "mb_lname" },
					{ 
						orderable: false,
						data: "mb_no",
						render: function (d,t,r,m) {
							return '\
										<div class="hidden-sm hidden-xs action-buttons">\
											<a class="green member-group-edit" href="#">\
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
														<a href="#" class="tooltip-success member-group-edit" data-rel="tooltip" title="Edit">\
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
			order: [[0, 'asc']],
			rowCallback: function (r, d) {
			  $('a.member-group-edit', r).click(function (e) {
				e.preventDefault();
				$('button#add-group-member-save',"div#add-group-member-modal").data({id: d["mb_no"], group: d["id"], name: d["mb_nick"]+" "+d["mb_lname"]});
				$('div#add-group-member-modal').modal('show');
			  });
			}
	      }),
	group_table_api = group_table.api();;
	
	$('div#add-group-member-modal')
	.modal({
		backdrop: 'static',
		show: false
	})
	.on('show.bs.modal', function () {
	  $(".success-msg",this).html("");
	  $('div.alert-success', this).addClass('hidden');
	  $(".err-msg",this).html("");
	  $('div.alert-danger', this).addClass('hidden');
	  
	  $("#add-group-member-code").val($('button#add-group-member-save',"div#add-group-member-modal").data("group"));
	  $("#emp-name").html($('button#add-group-member-save',"div#add-group-member-modal").data("name"));
	  $("#hr_users_id").val($('button#add-group-member-save',"div#add-group-member-modal").data("id"));
	});
	
	$("#add-group-member-save",'div#add-group-member-modal').click(function(e) {
	  e.preventDefault();
	  var form_data = $("#add-group-member").serialize();
	  $("input, select, button, textarea",'div#add-group-member-modal').attr("disabled",true);
	  $("button#add-group-member-save","div#add-groups-modal").html('<i class="ace-icon fa fa-spinner fa-spin bigger-110"></i> Saving&hellip;');
	  
	  $.post(
		base_url + "groups/setMemberGroup",
		form_data,
		function(response) {
		  if(response.success) {
			$(".success-msg","#add-group-member-modal").html(response.msg);
			$("div.alert-success","#add-group-member-modal").removeClass("hidden");
			
			setTimeout(function(){
			  $("div.alert-success","#add-group-member-modal").addClass("hidden"); 
			  $('div#add-group-member-modal').modal('hide'); 
			  group_table_api.ajax.url(base_url + 'groups/getAllGroupMembers/').load();
			},1500);
		  }
		  else {
			$(".err-msg","#add-group-member-modal").html(response.msg);
			$("div.alert-danger","#add-group-member-modal").removeClass("hidden");
			
			setTimeout(function(){ $("div.alert-danger","#add-group-member-modal").addClass("hidden"); },1500);
		  }
		  
		  $("input, select, button, textarea",'div#add-group-member-modal').attr("disabled",false);
		  $("button#add-group-member-save","div#add-group-member-modal").html('<i class="ace-icon fa fa-save bigger-110"></i> Save');
		},
		"json"
	  );
	});
	
	$('ul#grp-filter-group a').unbind("click").click(function (e) {
	  e.preventDefault();
	  $('ul#grp-filter-group li').removeClass('active');
	  $(this).parent().addClass('active');
	
	  $('button#grp-filter-group-btn').html('\
	    <i class="ace-icon fa fa-filter"></i> Group: ' + $(this).text() + '\
	    <i class="ace-icon fa fa-chevron-down icon-on-right"></i>\
	  ');
		
	  if($(this).data('id'))
	    $('button#grp-filter-group-btn').data('id', $(this).data('id'));
	  else
	    $('button#grp-filter-group-btn').data('id', 0);
    });
	
	$('button#grp-search-btn').unbind("click").click(function (e) {
	  group_table_api.ajax.reload(null,true);
	});
	
});