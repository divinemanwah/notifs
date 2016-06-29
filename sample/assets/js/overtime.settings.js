$(function () {
	$("#gen-set-save").click(function(e) {
	  e.preventDefault();
	  
	  $(this).html('<i class="ace-icon fa fa-spinner fa-spin bigger-110"></i> Saving&hellip;');
	  $(this).attr("disabled",true);
	  
	  $(".success-msg","#gen-setting").html("");
	  $("div.alert-success","#gen-setting").addClass("hidden");
	  $(".err-msg","#gen-setting").html("");
	  $("div.alert-danger","#gen-setting").addClass("hidden");
	  
	  $.post(
	    base_url + "overtime/updateGeneralSettings",
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