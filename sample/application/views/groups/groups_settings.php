<div class="col-xs-12 col-sm-6">
	<div class="widget-box widget-color-blue2">
		<div class="widget-header">
			<h4 class="widget-title">Group Codes</h4>
			<div class="widget-toolbar no-border">
			  <button class="btn btn-xs btn-info" id="add-group-btn">
				<i class="ace-icon fa fa-plus"></i>
				Add
			  </button>
		    </div>
			<div class="widget-toolbar no-border">
			  <label class="small">Show Deleted
				<input id="group-display-inactive" class="ace ace-switch ace-switch-3" type="checkbox" />
				<span class="lbl middle"></span>
			  </label>
		    </div>
		</div>

		<div class="widget-body">
			<div class="widget-main no-padding">
				<div class="">
					<table id="group-codes-table" class="table table-striped table-bordered table-hover" width="100%">
						<thead>
							<tr>
								<th>Group Name</th>
                                                                <th>Status</th>
								<th class="hidden-480"></th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="add-groups-modal" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-md ">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Group Information</h4>
			</div>

			<div class="modal-body">
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
							<br />
							<span class="success-msg"></span>
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
							<br />
							<span class="err-msg"></span>
							<br />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<form class="form-horizontal" role="form" id="add-group">
							<input type="hidden" name="group_id" id="group_id" />
							<h3 class="header smaller no-margin-top"><small>Group Information</small></h3>
							<div class="row">
								<div class="col-sm-6 col-md-10">
									<div class="form-group">
										<label class="col-sm-6 control-label no-padding-right" for="add-group-code">Department Group</label>
										<div class="col-sm-6">
											<input class="form-control" type="text" id="add-group-code" name="add-group-code" />
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button class="btn btn-sm btn-danger modal-action-btn" data-dismiss="modal">
					<i class="ace-icon fa fa-times"></i>
					Cancel
				</button>
				<button class="btn btn-sm btn-primary modal-action-btn" id="add-group-save">
					<i class="ace-icon fa fa-save"></i>
					Confirm
				</button>
			</div>
		</div>
	</div>
</div>

<div id="delete-modal" class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
	  <div class="modal-header">
	    <button type="button" data-dismiss="modal" class="close">&times;</button>
		<h4 class="blue bigger">Delete Group</h4>
	  </div>
	  <div class="modal-body">
	    <div class="row">
		  <div class="col-xs-12">
		    <div class="well">
			  <h4 class="red smaller lighter">Are you sure you want to delete <strong id="group_lbl">&nbsp;</strong>?</h4>
			  <button class="btn btn-sm btn-success delete-group-btn">Yes</button>
			  <button class="btn btn-sm btn-danger" data-dismiss="modal">No</button>
			</div>
			<strong>Note:</strong> This cannot be undone
		  </div>
		</div>
	  </div>
	</div>
  </div>
</div>
