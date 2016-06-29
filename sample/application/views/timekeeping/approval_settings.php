<div class="col-xs-12 col-md-6">
	<div class="widget-box widget-color-green2">
		<div class="widget-header">
			<h4 class="widget-title">Approval Groups - Schedule</h4>
			<div class="widget-toolbar no-border">
			  <button class="btn btn-xs btn-success" id="add-apprv-btn">
				<i class="ace-icon fa fa-plus"></i>
				Add
			  </button>
		    </div>
			<div class="widget-toolbar no-border">
			  <label class="small">Show inactive
				<input id="app-grps-display-inactive" class="ace ace-switch ace-switch-3" type="checkbox" />
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
								<th class="center">
									<label class="position-relative">
										<input type="checkbox" class="ace" />
										<span class="lbl"></span>
									</label>
								</th>
								<th>Group Code</th>
								<th>
									<i class="ace-icon fa fa-clock-o bigger-110 hidden-480"></i>
									Update
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
<div class="col-xs-12 col-md-6">
	<div class="widget-box widget-color-red2">
		<div class="widget-header">
			<h4 class="widget-title">Approval Groups - CWS</h4>
			<div class="widget-toolbar no-border">
			  <button class="btn btn-xs btn-danger" id="add-cws-apprv-btn">
				<i class="ace-icon fa fa-plus"></i>
				Add
			  </button>
		    </div>
			<div class="widget-toolbar no-border">
			  <label class="small">Show inactive
				<input id="app-cws-grps-display-inactive" class="ace ace-switch ace-switch-3" type="checkbox" />
				<span class="lbl middle"></span>
			  </label>
		    </div>
		</div>

		<div class="widget-body">
			<div class="widget-main no-padding">
				<div class="">
					<table id="cws-group-codes-table" class="table table-striped table-bordered table-hover" width="100%">
						<thead>
							<tr>
								<th class="center">
									<label class="position-relative">
										<input type="checkbox" class="ace" />
										<span class="lbl"></span>
									</label>
								</th>
								<th>Group Code</th>
								<th>
									<i class="ace-icon fa fa-clock-o bigger-110 hidden-480"></i>
									Update
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
		<h4 class="blue bigger">Delete Approval Group</h4>
	  </div>
	  <div class="modal-body">
	    <div class="row">
		  <div class="col-xs-12">
		    <div class="well">
			  <h4 class="red smaller lighter">Are you sure you want to delete <strong id="apprv_lbl">&nbsp;</strong>?</h4>
			  <button class="btn btn-sm btn-success delete-apprv-btn">Yes</button>
			  <button class="btn btn-sm btn-danger" data-dismiss="modal">No</button>
			</div>
			<strong>Note:</strong> This cannot be undone
		  </div>
		</div>
	  </div>
	</div>
  </div>
</div>

<div id="apprv-grps-modal" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Update Approval Group - Schedule</h4>
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
				<div class="row apprv-grp-update">
					<div class="col-sm-12">
						<form class="form-horizontal" role="form" id="approval_group">
						<input class="form-control" type="hidden" id="apprv-grp-id" name="apprv-grp-id" />
							<div id="apprv-grp-tab-general">
								<h3 class="header smaller no-margin-top"><small>Group Information</small></h3>
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label class="col-sm-5 control-label no-padding-right" for="apprv-grp-code">Group Code</label>
											<div class="col-sm-7">
												<input class="form-control" type="text" id="apprv-grp-code" name="apprv-grp-code" />
											</div>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label class="col-sm-5 control-label no-padding-right" for="apprv-grp-status">Enabled</label>
											<div class="col-sm-7">
												<select class="form-control" id="apprv-grp-status" name="apprv-grp-status">
													<option value="1">Enabled</option>
													<option value="0">Disabled</option>
												</select>
											</div>
										</div>
									</div>
								</div>
							</div>
							
							<div id="assignment">
							  <div class="row">
								<div class="col-sm-12">
									<h3 class="header smaller no-margin-top"><small>Uploaders</small></h3>
										<table class="table table-striped table-bordered table-hover">
											<thead class="thin-border-bottom">
												<tr>
													<th class="col-xs-7">
														<i class="ace-icon fa fa-user"></i>
														Full Name
													</th>
													<th class="col-xs-3 center">
														Status
													</th>
													<th class="hidden-480 col-xs-2">&nbsp;</th>
												</tr>
											</thead>
											<tbody id="uploader_tbl">
												<tr id="no_assignment"><td colspan=3 class="center">No Record Found</td></tr>
											</tbody>
											<tfoot class="alert alert-info">
												<tr>
													<th class="col-sm-7 form-group">
														<select id="uploader" class="chosen-select" data-placeholder="Choose a user..."></select>
													</th>
													<th class="col-xs-3">&nbsp;</th>
													<th class="hidden-480 col-xs-2">
														<button class="btn btn-sm btn-primary" id="uploader-add">
															<i class="ace-icon fa fa-plus"></i>
															Add
														</button>
													</th>
												</tr>
											</tfoot>
										</table>
								</div>
								<div class="col-sm-12">
									<h3 class="header smaller no-margin-top"><small>Approvers</small></h3>
									<table class="table table-striped table-bordered table-hover">
										<thead class="thin-border-bottom">
											<tr>
												<th class="col-sm-6">
													<i class="ace-icon fa fa-user"></i>
													Full Name
												</th>
												<th class="col-sm-1 center">
													Level
												</th>
												<th class="col-sm-3 center">
													Status
												</th>
												<th class="hidden-480 col-sm-2">&nbsp;</th>
											</tr>
										</thead>
										<tbody id="approver_tbl">
											<tr id="no_approver"><td colspan=4 class="center">No Record Found</td></tr>
										</tbody>
										<tfoot class="alert alert-success">
											<tr>
												<th class="col-sm-6 form-group">
													<select id="approver" class="chosen-select" data-placeholder="Choose a user..."></select>
												</th>
												<th class="col-sm-1 form-group">
													<select id="approver_lvl">
													  <? for($ctr=1;$ctr<=10;$ctr++) { ?>
													  <option value="<?=$ctr?>"><?=$ctr?></option>
													  <? } ?>
													</select>
													<!-- <input id="approver_lvl" type="text" class="col-xs-12"/> -->
												</th>
												<td class="col-sm-3">&nbsp;</td>
												<th class="hidden-480 col-sm-2">
													<button class="btn btn-sm btn-primary" id="approver-add">
														<i class="ace-icon fa fa-plus"></i>
														Add
													</button>
												</th>
											</tr>
										</tfoot>
									</table>
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
				<button class="btn btn-sm btn-primary modal-action-btn" id="apprv-grp-save">
					<i class="ace-icon fa fa-save"></i>
					Update
				</button>
			</div>
		</div>
	</div>
</div>

<div id="add-apprv-grps-modal" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Add Approval Group</h4>
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
				<div class="row apprv-grp-update">
					<div class="col-sm-12">
						<form class="form-horizontal" role="form" id="add_approval_group">
							<div id="add-apprv-grp-tab-general">
								<h3 class="header smaller no-margin-top"><small>Group Information</small></h3>
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label class="col-sm-5 control-label no-padding-right" for="add-apprv-grp-code">Group Code</label>
											<div class="col-sm-7">
												<input class="form-control" type="text" id="add-apprv-grp-code" name="add-apprv-grp-code" />
											</div>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label class="col-sm-5 control-label no-padding-right" for="add-apprv-grp-status">Enabled</label>
											<div class="col-sm-7">
												<select class="form-control" id="add-apprv-grp-status" name="add-apprv-grp-status">
													<option value="1">Enabled</option>
													<option value="0">Disabled</option>
												</select>
											</div>
										</div>
									</div>
								</div> 
							</div>
							
							<div id="add-assignment">
							  <div class="row">
								<div class="col-sm-12">
									<h3 class="header smaller no-margin-top"><small>Uploaders</small></h3>
										<table class="table table-striped table-bordered table-hover">
											<thead class="thin-border-bottom">
												<tr>
													<th class="col-xs-7">
														<i class="ace-icon fa fa-user"></i>
														Full Name
													</th>
													<th class="col-xs-3">
														Status
													</th>
													<th class="hidden-480 col-xs-2">&nbsp;</th>
												</tr>
											</thead>
											<tbody id="add-uploader_tbl">
												<tr id="add-no_assignment"><td colspan=3 class="center">No Record Found</td></tr>
											</tbody>
											<tfoot class="alert alert-info">
												<tr>
													<th class="col-sm-7 form-group">
														<select id="add-uploader" class="chosen-select" data-placeholder="Choose a user..."></select>
													</th>
													<th class="col-xs-3">&nbsp;</th>
													<th class="hidden-480 col-xs-2">
														<button class="btn btn-sm btn-primary" id="add-uploader-add">
															<i class="ace-icon fa fa-plus"></i>
															Add
														</button>
													</th>
												</tr>
											</tfoot>
										</table>
								</div>
								<div class="col-sm-12">
									<h3 class="header smaller no-margin-top"><small>Approvers</small></h3>
									<table class="table table-striped table-bordered table-hover">
										<thead class="thin-border-bottom">
											<tr>
												<th class="col-sm-6">
													<i class="ace-icon fa fa-user"></i>
													Full Name
												</th>
												<th class="col-sm-1">
													Level
												</th>
												<th class="col-sm-3">
													Status
												</th>
												<th class="hidden-480 col-sm-2">&nbsp;</th>
											</tr>
										</thead>
										<tbody id="add-approver_tbl">
											<tr id="add-no_approver"><td colspan=4 class="center">No Record Found</td></tr>
										</tbody>
										<tfoot class="alert alert-success">
											<tr>
												<th class="col-sm-6 form-group">
													<select id="add-approver" class="chosen-select" data-placeholder="Choose a user..."></select>
												</th>
												<th class="col-sm-1 form-group">
												    <select id="add-approver_lvl">
													  <? for($ctr=1;$ctr<=10;$ctr++) { ?>
													  <option value="<?=$ctr?>"><?=$ctr?></option>
													  <? } ?>
													</select>
												</th>
												<td class="col-sm-3">&nbsp;</td>
												<th class="hidden-480 col-sm-2">
													<button class="btn btn-sm btn-primary" id="add-approver-add">
														<i class="ace-icon fa fa-plus"></i>
														Add
													</button>
												</th>
											</tr>
										</tfoot>
									</table>
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
				<button class="btn btn-sm btn-primary modal-action-btn" id="apprv-grp-save-add">
					<i class="ace-icon fa fa-save"></i>
					Save
				</button>
			</div>
		</div>
	</div>
</div>

<!-- CWS -->
<div id="cws-apprv-grps-modal" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Update Approval Group - CWS</h4>
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
				<div class="row cws-apprv-grp-update">
					<div class="col-sm-12">
						<form class="form-horizontal" role="form" id="cws_approval_group">
						<input class="form-control" type="hidden" id="cws-apprv-grp-id" name="cws-apprv-grp-id" />
						<div id="cws-apprv-grp-tab-general">
							<h3 class="header smaller no-margin-top"><small>Group Information</small></h3>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label class="col-sm-5 control-label no-padding-right" for="cws-apprv-grp-code">Group Code</label>
										<div class="col-sm-7">
											<input class="form-control" type="text" id="cws-apprv-grp-code" name="cws-apprv-grp-code" />
										</div>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label class="col-sm-5 control-label no-padding-right" for="cws-apprv-grp-status">Enabled</label>
										<div class="col-sm-7">
											<select class="form-control" id="cws-apprv-grp-status" name="cws-apprv-grp-status">
												<option value="1">Enabled</option>
												<option value="0">Disabled</option>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div id="cws-assignment">
							  <div class="row">
								<div class="col-sm-12">
									<h3 class="header smaller no-margin-top"><small>Approvers</small></h3>
									<table class="table table-striped table-bordered table-hover">
										<thead class="thin-border-bottom">
											<tr>
												<th class="col-sm-6">
													<i class="ace-icon fa fa-user"></i>
													Full Name
												</th>
												<th class="col-sm-1 center">
													Level
												</th>
												<th class="col-sm-3 center">
													Status
												</th>
												<th class="hidden-480 col-sm-2">&nbsp;</th>
											</tr>
										</thead>
										<tbody id="cws_approver_tbl">
											<tr id="cws_no_approver"><td colspan=4 class="center">No Record Found</td></tr>
										</tbody>
										<tfoot class="alert alert-success">
											<tr>
												<th class="col-sm-6 form-group">
													<select id="cws_approver" class="chosen-select" data-placeholder="Choose a user..."></select>
												</th>
												<th class="col-sm-1 form-group">
													<select id="cws_approver_lvl">
													  <? for($ctr=1;$ctr<=10;$ctr++) { ?>
													  <option value="<?=$ctr?>"><?=$ctr?></option>
													  <? } ?>
													</select>
													<!-- <input id="approver_lvl" type="text" class="col-xs-12"/> -->
												</th>
												<td class="col-sm-3">&nbsp;</td>
												<th class="hidden-480 col-sm-2">
													<button class="btn btn-sm btn-primary" id="cws-approver-add">
														<i class="ace-icon fa fa-plus"></i>
														Add
													</button>
												</th>
											</tr>
										</tfoot>
									</table>
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
				<button class="btn btn-sm btn-primary modal-action-btn" id="cws-apprv-grp-save">
					<i class="ace-icon fa fa-save"></i>
					Update
				</button>
			</div>
		</div>
	</div>
</div>

<div id="add-cws-apprv-grps-modal" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Add Approval Group - CWS</h4>
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
				<div class="row cws-apprv-grp-add">
					<div class="col-sm-12">
						<form class="form-horizontal" role="form" id="add_cws_approval_group">
						<input class="form-control" type="hidden" id="add-cws-apprv-grp-id" name="add-cws-apprv-grp-id" />
						<div id="add-cws-apprv-grp-tab-general">
							<h3 class="header smaller no-margin-top"><small>Group Information</small></h3>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label class="col-sm-5 control-label no-padding-right" for="add-cws-apprv-grp-code">Group Code</label>
										<div class="col-sm-7">
											<input class="form-control" type="text" id="add-cws-apprv-grp-code" name="add-cws-apprv-grp-code" />
										</div>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label class="col-sm-5 control-label no-padding-right" for="add-cws-apprv-grp-status">Enabled</label>
										<div class="col-sm-7">
											<select class="form-control" id="add-cws-apprv-grp-status" name="add-cws-apprv-grp-status">
												<option value="1">Enabled</option>
												<option value="0">Disabled</option>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div id="add-cws-assignment">
							  <div class="row">
								<div class="col-sm-12">
									<h3 class="header smaller no-margin-top"><small>Approvers</small></h3>
									<table class="table table-striped table-bordered table-hover">
										<thead class="thin-border-bottom">
											<tr>
												<th class="col-sm-6">
													<i class="ace-icon fa fa-user"></i>
													Full Name
												</th>
												<th class="col-sm-1 center">
													Level
												</th>
												<th class="col-sm-3 center">
													Status
												</th>
												<th class="hidden-480 col-sm-2">&nbsp;</th>
											</tr>
										</thead>
										<tbody id="add_cws_approver_tbl">
											<tr id="add_cws_no_approver"><td colspan=4 class="center">No Record Found</td></tr>
										</tbody>
										<tfoot class="alert alert-success">
											<tr>
												<th class="col-sm-6 form-group">
													<select id="add_cws_approver" class="chosen-select" data-placeholder="Choose a user..."></select>
												</th>
												<th class="col-sm-1 form-group">
													<select id="add_cws_approver_lvl">
													  <? for($ctr=1;$ctr<=10;$ctr++) { ?>
													  <option value="<?=$ctr?>"><?=$ctr?></option>
													  <? } ?>
													</select>
													<!-- <input id="approver_lvl" type="text" class="col-xs-12"/> -->
												</th>
												<td class="col-sm-3">&nbsp;</td>
												<th class="hidden-480 col-sm-2">
													<button class="btn btn-sm btn-primary" id="add-cws-approver-add">
														<i class="ace-icon fa fa-plus"></i>
														Add
													</button>
												</th>
											</tr>
										</tfoot>
									</table>
								</div>
							  </div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button class="btn btn-sm btn-danger add-modal-action-btn" data-dismiss="modal">
					<i class="ace-icon fa fa-times"></i>
					Cancel
				</button>
				<button class="btn btn-sm btn-primary add-modal-action-btn" id="add-cws-apprv-grp-save">
					<i class="ace-icon fa fa-save"></i>
					Add
				</button>
			</div>
		</div>
	</div>
</div>


<div id="cws-delete-modal" class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
	  <div class="modal-header">
	    <button type="button" data-dismiss="modal" class="close">&times;</button>
		<h4 class="blue bigger">Delete Approval Group - CWS</h4>
	  </div>
	  <div class="modal-body">
	    <div class="row">
		  <div class="col-xs-12">
		    <div class="well">
			  <h4 class="red smaller lighter">Are you sure you want to delete <strong id="cws_apprv_lbl">&nbsp;</strong>?</h4>
			  <button class="btn btn-sm btn-success delete-cws-apprv-btn">Yes</button>
			  <button class="btn btn-sm btn-danger" data-dismiss="modal">No</button>
			</div>
			<strong>Note:</strong> This cannot be undone
		  </div>
		</div>
	  </div>
	</div>
  </div>
</div>

<!-- END of CWS -->
