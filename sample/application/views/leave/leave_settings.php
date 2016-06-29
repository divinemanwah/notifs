<div class="col-xs-12 col-sm-3">
	<div class="widget-box widget-color-red">
		<div class="widget-header">
			<h4 class="widget-title">General Settings</h4>
		</div>
		
		<div class="widget-body" id="gen-setting">
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
					<br />
					<span class="success-msg">The record has been saved.</span>
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
			  <form id="gen-form" role="form" class="form-horizontal">
				<div class="form-group">
					<label class="col-sm-7 control-label no-padding-right" for="max_pending">Max pending leave count </label>
					<div class="col-sm-4 pull-right">
					  <input type="text" name="max_pending" class="input-mini" value="<?=$data[0]->max_pending_leave?>"/>
					</div>
				</div>
				<br/>
				<div class="center">
					<button type="button" class="btn btn-sm btn-danger" id="gen-set-save">
						<i class="ace-icon fa fa-save bigger-110"></i>
						Save
					</button>
				</div>
			  </form>
			</div>
		</div>
	</div>
</div>

<div class="col-xs-12 col-sm-9">
	<div class="widget-box widget-color-blue2">
		<div class="widget-header">
			<h4 class="widget-title">Leave Codes</h4>
			<div class="widget-toolbar no-border">
			  <button class="btn btn-xs btn-info" id="add-leave-btn">
				<i class="ace-icon fa fa-plus"></i>
				Add
			  </button>
		    </div>
			<div class="widget-toolbar no-border">
			  <label class="small">Show inactive
				<input id="leave-display-inactive" class="ace ace-switch ace-switch-3" type="checkbox" />
				<span class="lbl middle"></span>
			  </label>
		    </div>
		</div>

		<div class="widget-body">
			<div class="widget-main no-padding">
				<div class="">
					<table id="leave-codes-table" class="table table-striped table-bordered table-hover" width="100%">
						<thead>
							<tr>
								<th class="center">
									<label class="position-relative">
										<input type="checkbox" class="ace" />
										<span class="lbl"></span>
									</label>
								</th>
								<th>Leave Code</th>
								<th>Leave Name</th>
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

<div id="add-leaves-modal" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Leave Information</h4>
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
						<ul class="nav nav-tabs" id="add-leave-dtl" role="tablist">
							<li class="active"><a href="#add-leave-tab-general" role="tab" data-toggle="tab">General</a></li>
							<li><a href="#add-leave-advanced" role="tab" data-toggle="tab">Advanced</a></li>
							<li><a href="#add-entitlement" role="tab" data-toggle="tab">Entitlement</a></li>
							<li><a href="#add-rules" role="tab" data-toggle="tab">Filing Rules</a></li>
						</ul>
						<form class="form-horizontal" role="form" id="add-leave">
						<input type="hidden" name="leave_id" id="leave_id" />
						<div class="tab-content">
							<div class="tab-pane active" id="add-leave-tab-general">
								<h3 class="header smaller no-margin-top"><small>Leave Information</small></h3>
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label class="col-sm-6 control-label no-padding-right" for="add-leave-code">Leave Code</label>
											<div class="col-sm-6">
												<input class="form-control" type="text" id="add-leave-code" name="add-leave-code" />
											</div>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label class="col-sm-4 control-label no-padding-right" for="add-leave-status">Status</label>
											<div class="col-sm-6">
												<select class="form-control" id="add-leave-status" name="add-leave-status">
													<option value="1">Enabled</option>
													<option value="0">Disabled</option>
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label class="col-sm-6 control-label no-padding-right" for="add-leave-name">Leave Name</label>
											<div class="col-sm-6">
												<input class="form-control" type="text" id="add-leave-name" name="add-leave-name" />
											</div>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
										    <label class="col-sm-4 control-label no-padding-right" for="add-leave-req-mc">Require Medical Certificate</label>
											<div class="col-sm-6">
											  <select class="form-control" id="add-leave-req-mc" name="add-leave-req-mc">
											    <option value="1">Yes</option>
												<option value="0">No</option>
											  </select>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<label class="col-sm-3 control-label no-padding-right" for="add-leave-desc">Leave Description</label>
											<div class="col-sm-9">
												<textarea id="add-leave-desc" name="add-leave-desc" class="autosize-transition form-control" rows="3" maxlength="200"></textarea>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label class="col-sm-6 control-label no-padding-right" for="add-leave-le">Local/Expat</label>
											<div class="col-sm-6">
											  <select class="form-control" id="add-leave-le" name="add-leave-le">
											    <option value="b">Both</option>
											    <option value="l">Local</option>
												<option value="e">Expat</option>
											  </select>
											</div>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label class="col-sm-4 control-label no-padding-right" for="add-leave-gender">Gender</label>
											<div class="col-sm-6">
											  <select class="form-control" id="add-leave-gender" name="add-leave-gender">
											    <option value="b">Both</option>
											    <option value="m">Male</option>
												<option value="f">Female</option>
											  </select>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label class="col-sm-6 control-label no-padding-right" for="add-leave-staggered">Staggered Filing</label>
											<div class="col-sm-6">
											  <select class="form-control" id="add-leave-staggered" name="add-leave-staggered">
												<option value="1">Yes</option>
												<option value="0">No</option>
											  </select>
											</div>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label class="col-sm-4 control-label no-padding-right" for="add-leave-full-consume">Full Consume</label>
											<div class="col-sm-6">
											  <select class="form-control" id="add-leave-full-consume" name="add-leave-full-consume">
												<option value="1">Yes</option>
												<option value="0" selected='selected'>No</option>
											  </select>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label class="col-sm-6 control-label no-padding-right" for="add-leave-forfeit">Forfeit excess upon filing</label>
											<div class="col-sm-6">
											  <select class="form-control" id="add-leave-forfeit" name="add-leave-forfeit">
												<option value="1">Yes</option>
												<option value="0" selected='selected'>No</option>
											  </select>
											</div>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label class="col-sm-4 control-label no-padding-right" for="add-emp-type">Employment Type</label>
											<div class="col-sm-6">
											  <select class="form-control" id="add-emp-type" name="add-emp-type">
												<option value="all">All</option>
											    <option value="probationary">Probationary</option>
												<option value="regular">Regular</option>
											  </select>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label class="col-sm-6 control-label no-padding-right" for="add-max-advanced-days">Max advanced Leave Days</label>
											<div class="col-sm-6">
											  <input id="add-max-advanced-days" name="add-max-advanced-days" type="text" class="form-control input input-mini" value="0"/>
											</div>
										</div>
									</div>
								</div>
							</div>
							
							<div class="tab-pane" id="add-leave-advanced">
							  <div class="row">
							    <div class="col-sm-12">
							      <label>Enable Sub Category
								    <input id="leave-sub-category-enabled" name="leave-sub-category-enabled" class="ace ace-switch ace-switch-3" type="checkbox" />
								  <span class="lbl middle"></span>
							      </label>
								</div>
							  </div>
							  <div class="row hidden" id="leave-sub-categ-div">
								<div class="col-sm-12">
								  <p>List of Leaves that would share the entitlement of parent leave type</p>
								  <table class="table table-striped table-bordered table-hover">
									<thead class="thin-border-bottom">
										<tr>
											<th class="col-sm-2 center">
												Leave Code
											</th>
											<th class="col-sm-3 center">
												Leave Name
											</th>
											<th class="col-sm-2 center">
												Require MC
											</th>
											<th class="col-sm-3 center">
												Status
											</th>
											<th class="hidden-480 col-sm-2">&nbsp;</th>
										</tr>
									</thead>
									<tbody id="add-subcateg-tbl">
										<tr id="no-subcateg"><td colspan="4" class="center">No Record Found</td></tr>
									</tbody>
									<tfoot class="alert alert-info">
										<tr>
											<td class="col-sm-2 form-group center">
												<input type="text" id="add-subcateg-code" value=""/>
											</td>
											<td class="col-sm-3 form-group center">
												<input type="text" id="add-subcateg-name" style="width: 100%;" value=""/>
											</td>
											<td class="col-sm-2 form-group center">
												<select id="add-subcateg-mc" style="width: 100%;" class="ace">
												  <option value='1'>Yes</option>
												  <option value='0'>No</option>
												</select>
											</td>
											<td class="col-sm-3 center">&nbsp;</td>
											<td class="hidden-480 col-sm-2">
												<button class="btn btn-sm btn-primary" id="subcateg-add">
													<i class="ace-icon fa fa-plus"></i>
													Add
												</button>
											</td>
										</tr>
									</tfoot>
								  </table>
								</div>
							  </div>
							  <div class="row">
							    <div class="col-sm-12">
							      <label>Enable Leave Dependencies
								  <input id="leave-dependencies-enabled" name="leave-dependencies-enabled" class="ace ace-switch ace-switch-3" type="checkbox" />
								    <span class="lbl middle"></span>
							      </label>
								</div>
							  </div>
							  <div class="row hidden" id="leave-dependency-div">
								<div class="col-sm-12">
							      <p>List of Leaves that would automatically be used when credits of this leave have already been used</p>
							      <table class="table table-striped table-bordered table-hover">
								<thead class="thin-border-bottom">
									<tr>
										<th class="col-sm-5 center">
											Leave Code
										</th>
										<th class="col-sm-5 center">
											Status
										</th>
										<th class="hidden-480 col-sm-2">&nbsp;</th>
									</tr>
								</thead>
								<tbody id="add-dependencies-tbl">
									<tr id="no-dependencies"><td colspan="4" class="center">No Record Found</td></tr>
								</tbody>
								<tfoot class="alert alert-info">
									<tr>
										<td class="col-sm-5 form-group center">
											<select id="add-dependencies-leave" style="width: 100%;" ></select>
										</td>
										<td class="col-sm-5 center">&nbsp;</td>
										<td class="hidden-480 col-sm-2">
											<button class="btn btn-sm btn-primary" id="dependencies-add">
												<i class="ace-icon fa fa-plus"></i>
												Add
											</button>
										</td>
									</tr>
								</tfoot>
							  </table>
								</div>
							  </div>
							</div>
						
							<div class="tab-pane" id="add-entitlement">
							  <div class="row">
								<div class="col-sm-12">
								  <label>Enable Entitlement
									<input id="leave-entitlement-enabled" name="leave-entitlement-enabled" class="ace ace-switch ace-switch-3" type="checkbox" />
									<span class="lbl middle"></span>
								  </label>
								</div>
							  </div>
							  <div class="row hidden" id="leave-entitlement-type-div">
							    <div class="col-sm-12">
								  <div class="row">
								    <div class="col-sm-12">
									  <div class="form-group">
										<label class="col-sm-1 control-label no-padding-right" for="add-leave-entitlement-credit">Type :</label>
										<div class="col-sm-3">
										  <div class="radio">
											<label>
											  <input id="manual-entitlement" name="add-leave-entitlement-type" type="radio" class="ace entitlement-type" value="manual">
											  <span class="lbl"> Manual Entitlement</span>
											</label>
										  </div>
										</div>
										<div class="col-sm-3">
										  <div class="radio">
											<label>
											  <input id="fixed-entitlement" name="add-leave-entitlement-type" type="radio" class="ace entitlement-type" value="fixed">
											  <span class="lbl"> Fixed Entitlement</span>
											</label>
										  </div>
										</div>
										<div class="col-sm-3">
										  <div class="radio">
											<label>
											  <input id="computed-entitlement" name="add-leave-entitlement-type" type="radio" class="ace entitlement-type" value="formula">
											  <span class="lbl"> Computed via Formula</span>
											</label>
										  </div>
										</div>
									  </div>
									</div>
								  </div>
								  <div class="row hidden" id="leave-entitlement-fixed-type-div">
								    <div class="col-sm-12">
									  <div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="add-leave-entitlement-credit">Entitlement Fixed Credit :</label>
										<div class="col-sm-2">
										  <input class="form-control" type="text" id="add-leave-entitlement-credit" name="add-leave-entitlement-credit" value = "0"/>
										</div>
										<label class="col-sm-7 no-padding-left">per year</label>
									  </div>
									</div>
								  </div>
								  <div class="row hidden" id="leave-entitlement-formula-type-div">
								    <div class="col-sm-12">
									  <div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="add-leave-entitlement-date">Entitlement Formula :</label>
										<div class="col-sm-9">
										  <select class="input-large" id="add-leave-entitlement-date" name="add-leave-entitlement-date">
										    <option value="">N/A</option>
										    <option value="commencement">Commencement Date</option>
											<option value="confirmation">Confirmation Date</option>
										  </select>
										  +
										  <input class="input-mini" type="text" id="add-leave-entitlement-add-date" name="add-leave-entitlement-add-date" value="0" />
										  days /
										  <input class="input-mini" type="text" id="add-leave-entitlement-divisor" name="add-leave-entitlement-divisor" value="365" />
										  *
										  <input class="input-mini" type="text" id="add-leave-max-entitlement" name="add-leave-max-entitlement" value="0" />
										  per year
										</div>
									  </div>
									</div>
								  </div>
								</div>
							  </div>
							</div>
						
							<div class="tab-pane" id="add-rules">
							  <div class="row">
								<div class="col-sm-12">
								  <label>Enable Rules
									<input id="leave-rules-enabled" name="leave-rules-enabled" class="ace ace-switch ace-switch-3" type="checkbox" />
									<span class="lbl middle"></span>
								  </label>
								</div>
							  </div>
							  <div class="row hidden" id="leave-rules-div">
							    <div class=" col-sm-12">
							    <h3 class="header smaller no-margin-top"><small>Rules List</small></h3>
							    <table class="table table-striped table-bordered table-hover">
								  <thead class="thin-border-bottom">
									<tr>
									    <th class="col-sm-2 center">
											Leave Type
										</th>
										<th class="col-sm-2 center">
											Max Leave Days
										</th>
										<th class="col-sm-2 center">
											Filing Days Prior
										</th>
										<th class="col-sm-2 center">
											Filing Days After
										</th>
										<th class="col-sm-2 center">
											Status
										</th>
										<th class="hidden-480 col-sm-2">&nbsp;</th>
									</tr>
								  </thead>
								  <tbody id="add-rules-tbl">
									<tr id="no-rules"><td colspan="6" class="center">No Record Found</td></tr>
								  </tbody>
								  <tfoot class="alert alert-info">
									<tr>
									    <td class="col-sm-2 form-group center">
											<select id="day-sub-categ" class="ace" style="width: 100%"></select>
										</td>
										<td class="col-sm-2 form-group center">
											<input type="text" id="days-leave" value="" class="input input-mini"/>
										</td>
										<td class="col-sm-2 form-group center">
											<input type="text" id="days-prior" value="" class="input input-mini"/>
										</td>
										<td class="col-sm-2 form-group center">
											<input type="text" id="days-later" value="" class="input input-mini"/>
										</td>
										<td class="col-sm-2 center">&nbsp;</td>
										<td class="hidden-480 col-sm-2">
											<button class="btn btn-sm btn-primary" id="rules-add">
												<i class="ace-icon fa fa-plus"></i>
												Add
											</button>
										</td>
									</tr>
								  </tfoot>
							    </table>
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
				<button class="btn btn-sm btn-primary modal-action-btn" id="add-leave-save">
					<i class="ace-icon fa fa-save"></i>
					Confirm
				</button>
			</div>
		</div>
	</div>
</div>
