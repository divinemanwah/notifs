<div class="col-md-4">
  <div class="widget-box widget-color-orange upload-file-widget">
    <div class="widget-header">
	  <h4 class="widget-title">Upload File</h4>
	</div>
	<div class="widget-body">
	  <div class="widget-main">
		<div class="row">
		  <div class="col-xs-12">
			 <div class="alert alert-success hidden">
				<button type="button" class="close" data-dismiss="alert">
				  <i class="ace-icon fa fa-times"></i>
				</button>
				<strong>
				  <i class="ace-icon fa fa-check"></i>
				  Success!
				</strong>
				<span id="upload-file-success-msg"></span>
			    <br />
			  </div>
			  <div class="alert alert-danger hidden">
				<button type="button" class="close" data-dismiss="alert">
				<i class="ace-icon fa fa-times"></i>
			  </button>
			  <strong>
				<i class="ace-icon fa fa-times"></i>
				Error!
			  </strong>
			  <span id="upload-file-err-msg"></span>
			  <br />
			</div>
		  </div>
		</div>
	    <form id="upload-form" enctype="multipart/form-data">
	      <div class="row">
		    <div class="col-md-12 form-group input-daterange">
		      <label class="col-md-4 control-label no-padding-right" for="add-shift-code">Current Period</label>
			  <input class="col-md-3 input-date" id="period-start" name="period-start" type="text" readonly value="<?=$cur_period->start?>" />
			  <span class="col-md-1">~</span>
			  <input class="col-md-3 input-date" id="period-end" name="period-end" type="text" readonly value="<?=$cur_period->end?>" />
		    </div>
		    <div class="col-md-12 form-group">
		      <input type="file" id="schedule-file" name="schedule-file"/>
		    </div>
		    <div class="center">
			  <p id="note-upload" class="text-muted"><strong>Note:</strong> Only .xls files are allowed</p>
			  <p id="note-upload-extn" class="text-muted"></p>
			  <button type="button" class="btn btn-sm btn-yellow" id="upload-file-btn">
				<i class="ace-icon fa fa-upload bigger-110"></i>
				Upload
			  </button>
			</div>
		  </div>
		</form>
	  </div>
    </div>
  </div>
  <div class="widget-box widget-color-green">
    <div class="widget-header">
	  <h4 class="widget-title">Download Template</h4>
	</div>
	<div class="widget-body">
	  <div class="widget-main">
	    <div class="row">
		  <form id="download-template" action="<?=base_url("timekeeping/download_sp_template")?>" method="POST">
		    <div class="col-md-12 form-group input-daterange">
		      <label class="col-md-4 control-label no-padding-right" for="add-shift-code">Current Period</label>
			  <input class="col-md-3 input-date" id="period-start" name="period-start" type="text" readonly value="<?=$cur_period->start?>" />
			  <span class="col-md-1">~</span>
			  <input class="col-md-3 input-date" id="period-end" name="period-end" type="text" readonly value="<?=$cur_period->end?>" />
		    </div>
		    <div class="center">
			  <button type="submit" class="btn btn-sm btn-success" id="download-template-btn" target="_blank">
				<i class="ace-icon fa fa-save bigger-110"></i>
				Export
			  </button>
			</div>
		  </form>
		</div>
	  </div>
    </div>
  </div>
</div>

<div class="col-md-8">
  <div class="widget-box widget-color-dark upload-list-widget">
	<div class="widget-header">
	  <h4 class="widget-title">Uploaded File Listing</h4>
	</div>
	<div class="widget-body">
	  <div class="widget-main">
	    <div class="row">
		  <div class="col-xs-12">
			 <div class="alert alert-success hidden">
				<button type="button" class="close" data-dismiss="alert">
				  <i class="ace-icon fa fa-times"></i>
				</button>
				<strong>
				  <i class="ace-icon fa fa-check"></i>
				  Success!
				</strong>
				<span id="upload-list-success-msg"></span>
			    <br />
			  </div>
			  <div class="alert alert-danger hidden">
				<button type="button" class="close" data-dismiss="alert">
				<i class="ace-icon fa fa-times"></i>
			  </button>
			  <strong>
				<i class="ace-icon fa fa-times"></i>
				Error!
			  </strong>
			  <span id="upload-list-err-msg"></span>
			  <br />
			</div>
		  </div>
		</div>
        <div class="row">
		  <table id="uploads-table" class="table table-striped table-bordered table-hover" width="100%">
			<thead>
			  <tr>
				<th class="center">
				  <label class="position-relative">
					<input type="checkbox" class="ace" />
					<span class="lbl"></span>
				  </label>
				</th>
				<th>Period</th>
				<th>Uploaded By</th>
				<th>Uploaded File</th>
				<th>
				  <i class="ace-icon fa fa-clock-o bigger-110 hidden-480"></i>
				  Uploaded
				</th>
				<th class="hidden-480">Status</th>
				<th class="hidden-480"></th>
			  </tr>
			</thead>
		  </table>
	    </div>
	  </div>
	</div>
  </div>
</div>

<div id="delete-modal" class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
	  <div class="modal-header">
	    <button type="button" data-dismiss="modal" class="close">&times;</button>
		<h4 class="blue bigger">Uploaded File</h4>
	  </div>
	  <div class="modal-body">
	    <div class="row">
		  <div class="col-xs-12">
		    <div class="well">
			  <h4 class="red smaller lighter">Are you sure you want to <label id='modal-action'>delete</label> <strong id="upload_lbl">&nbsp;</strong>?</h4>
			  <button class="btn btn-sm btn-success modal-upload-btn">Yes</button>
			  <button class="btn btn-sm btn-danger" data-dismiss="modal">No</button>
			</div>
			<strong>Note:</strong> This cannot be undone
		  </div>
		</div>
	  </div>
	</div>
  </div>
</div>

<div id="history-modal" class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
	  <div class="modal-header">
	    <button type="button" data-dismiss="modal" class="close">&times;</button>
		<h4 class="blue bigger">Schedule Approval History</h4>
	  </div>
	  <div class="modal-body">
	    <div id="approval_header">
		  <h3 class="header smaller no-margin-top"><small>Approval Remarks</small></h3>
		</div>
		<div style="max-height: 150px; overflow: auto;" id="approval_list">None</div>
	  </div>
	  <div class="modal-footer">
		  <button class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
	  </div>
	</div>
  </div>
</div>