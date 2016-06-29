$(function(){
   
   var holiday_table = $('table#holiday-table').dataTable({
                ajax:{
                    url: base_url + 'timekeeping/getholidaydata',
                    type: "POST"
                },
                deferRender: true,
                autoWidth: false,
		method: "post",
                columns: [
                        { data: "h_name" },
                        { data: "h_date" },
                        { data: "h_status" },
                        { data: "h_type" },
                        { width: "15%",
			  orderable: false,
			  data: "id",
			  render: function (d,t,r,m) {
							return '\
										<div class="hidden-sm hidden-xs action-buttons">\
											<a class="green holiday-edit" href="#">\
												<i class="ace-icon fa fa-pencil-square-o bigger-130"></i>\
											</a>\
										</div>\
									';
						},
						className: 'center no-highlight'
					}
				],
			order: [[1, 'desc']],
			rowCallback: function (r, d) {
			  $('a.holiday-edit', r).click(function (e) {
				e.preventDefault();
				$('button#add-holiday-save',"div#add-holiday-modal").data({id: d["id"]});
				$('div#add-holiday-modal').modal('show');
			  });
                        }
   }),
   holiday_table_api = holiday_table.api();;
   
 
   
   $("#holiday-set-save").off("click").click(function(e) {
       if($('#holidaydate').val() == "" || $('#holidayname').val() == ""){
           $('.alert-danger').removeClass('hidden');
           $('.alert-success').addClass('hidden');
           return false;
       }
       
        $.post(
	    base_url + "timekeeping/h_insert",
            { holidayname:$('#holidayname').val(),
                   holidaydate:$('#holidaydate').val(),
                   holidaytype:$('#holidaytype').val(),
                   holidaystatus:$('#holidaystatus').val()
                 },
             function(feedback){
                 if(feedback.success == 1){
                    $('.alert-success').find('.success-msg').html(feedback.msg);
                    $('.alert-success').removeClass('hidden'); 
                    setTimeout(function(){ 
				  $('.alert-success').addClass('hidden'); 
				  holiday_table_api.ajax.url(base_url + 'timekeeping/getholidaydata/').load();
				},3000);
                    $('.alert-danger').addClass('hidden');
                 }else{
                    $('.alert-danger').removeClass('hidden');
                    $('.alert-danger').find('.err-msg').html(feedback.msg);
                    setTimeout(function(){ $('.alert-danger').addClass('hidden'); },3000);
                    $('.alert-success').addClass('hidden');
                }
            },
            "json"
            );
   });
    
    	$('div#add-holiday-modal')
	.modal({
		backdrop: 'static',
		show: false
	})
	.on('show.bs.modal', function () {
	  $(".success-msg",this).html("");
	  $('div.alert-success', this).addClass('hidden');
	  $(".err-msg",this).html("");
	  $('div.alert-danger', this).addClass('hidden');
	  
	  if($('button#add-holiday-save').data('id')) {
	    $.post(
		  base_url + 'timekeeping/getholidaydata/',
                 {id:$('button#add-holiday-save').data('id'),getform:'history'},
                 
		  function (response) {
		    $("#holiday_id").val(response.data[0].id);
                    $("#add-holiday-code").val(response.data[0].h_name);
                    $("#add-holiday-date").val(response.data[0].h_date);
                    $("#add-holiday-type").val(response.data[0].h_status);
                    $("#add-holiday-status").val((response.data[0].h_type == 'Fixed')?1:0);
                    $("#add-holiday-create").html(response.data[0].created_by ? response.data[0].created_by : "Admin");
                    
                    if(response.history.length > 0){
                        $("#history-panel").removeClass("hidden");
                        var history_info = "";
                        $.each(response.history,function(a,b){
                            history_info += 
                            '<div class="well well-sm alert-success text-left"><b><i>'+b.h_created+'</i></b><br>'+b.created_by+' : '+b.h_name+'/'+b.h_date+'</div>';
                            $('#holiday-history').find('span').html(history_info);
                        });
                        $("#holiday-history").find("span").css({"overflow":"auto","height":"150px"});
                        $("#holiday-history").find("span").find('.well-sm').css({'padding':'0.5em','margin-bottom':'10px'});
                    }else{
                        $("#history-panel").addClass("hidden");
                    }

		  },
                  'json'
            );
	  }
	  else {
	    $("#holiday_id").val("");
		$("#add-holiday-code").val("");
	  }
          
	});
        
        $("button#add-holiday-save").off("click").click(function(e) {
            if($("#add-holiday-code").val() == "" ||
               $("#add-holiday-date").val() == "" ||
               $("#add-holiday-type").val() == "" ||
               $("#add-holiday-status").val() == ""){
                $('.alert-danger').removeClass('hidden');
                    $('.modal-body .alert-danger').find('.err-msg').html('Please Fill up all blank inputs');
                    setTimeout(function(){ $('.modal-body .alert-danger').removeClass('hidden'); },3000);
                    $('.modal-body .alert-success').addClass('hidden');
            }else{
                $.post(
			base_url + "timekeeping/updateholiday",
                        {hname:$("#add-holiday-code").val(),
                         hdate:$("#add-holiday-date").val(),
                         htype:$("#add-holiday-type").val(),
                         hstatus:$("#add-holiday-status").val(),
                         hid:$("#holiday_id").val()
                        },
			function(response) {
			  if(response.success) {
				$(".success-msg","#add-holiday-modal").html(response.msg);
				$("div.alert-success","#add-holiday-modal").removeClass("hidden");
				
				setTimeout(function(){
				  $("div.alert-success","#add-holiday-modal").addClass("hidden"); 
				  $('div#add-holiday-modal').modal('hide'); 
				  holiday_table_api.ajax.url(base_url + 'timekeeping/getholidaydata/').load();
				},1500);
			  }
			  else {
				$(".err-msg","#add-holiday-modal").html(response.msg);
				$("div.alert-danger","#add-holiday-modal").removeClass("hidden");
				setTimeout(function(){ $("div.alert-danger","#add-holiday-modal").addClass("hidden"); },1500);
			  }
			  
			  $("input, select, button, textarea",'div#add-holiday-modal').attr("disabled",false);
			  $("button#add-holiday-save","div#add-holiday-modal").html('<i class="ace-icon fa fa-save bigger-110"></i> Save');
			},
			"json"
                        
		);
            }
               
                
                    
        });
    var now = new Date();
    var start = new Date(now.getFullYear(), 0, 0);
    var diff = now - start;
    var oneDay = 1000 * 60 * 60 * 24;
    var day = ((Math.floor(diff / oneDay)-1)*-1);
    
    $('.input-daterange').datepicker({startDate: '0d' ,startView:1,autoclose:true, format: "yyyy-mm-dd"});
});