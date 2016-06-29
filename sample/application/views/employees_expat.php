<div class="widget-box widget-color-blue">
<div class="widget-header">
	<h5 class="widget-title smaller">Use the search box on the right to look for specific records</h5>
	<div class="widget-toolbar no-border">
		<button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#off-pen-modal">
			<i class="ace-icon fa fa-plus"></i>
			Add
		</button>
	</div>
	<div class="widget-toolbar no-border">
		<label class="small">Show inactive
			<input id="emp-display-inactive" class="ace ace-switch ace-switch-3" type="checkbox" />
			<span class="lbl middle"></span>
		</label>
	</div>
</div>

<div class="widget-body">
	<div class="widget-main no-padding">
	<table id="emp-table" class="table table-striped table-bordered table-hover">
	<thead>
	<tr>
		<th class="center">
			<label class="position-relative">
				<input type="checkbox" class="ace" />
				<span class="lbl"></span>
			</label>
		</th>
		<th>ID</th>
		<th>Last name</th>
		<th>First name</th>

		<th>
			Department
		</th>
		<th>Status</th>

		<th></th>
	</tr>
	</thead>
	</table>
	</div>
</div>
</div>

<div id="expat-modal" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Update employee</h4>
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

							The record has been saved.
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

							<span id="expat-err-msg"></span>
							<br />
						</div>
					</div>
				</div>
				<div class="row expat-update">
					<div class="col-sm-6">
						<table class="table table-striped table-bordered table-hover">
							<thead>
								<tr>
									<th colspan="2">Summary</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Name</td>
									<td>asdasdasdasdasd</td>
								</tr>
								<tr>
									<td>Nickname</td>
									<td>asd</td>
								</tr>
								<tr>
									<td>Position</td>
									<td>asdasdasdasdasd</td>
								</tr>
								<tr>
									<td>Department</td>
									<td>asd</td>
								</tr>
								<tr>
									<td>Date of commission</td>
									<td>asd</td>
								</tr>
							</tbody>
						</table>
						<form class="form-horizontal" role="form">
							<h3 class="header smaller"><small>TIN Application</small></h3>
							
							<div class="form-group">
								<label class="col-sm-6 control-label no-padding-right" for="expat-tin-application">Application date</label>

								<div class="col-sm-6">
									<input class="form-control" type="text" id="expat-tin-application" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-6 control-label no-padding-right" for="expat-tin-release">Date released</label>

								<div class="col-sm-6">
									<input class="form-control" type="text" id="expat-tin-release" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-6 control-label no-padding-right" for="expat-tin-no">TIN no.</label>

								<div class="col-sm-6">
									<input class="form-control" type="text" id="expat-tin-no" />
								</div>
							</div>
						</form>
					</div>
					<div class="col-sm-6">
						<form class="form-horizontal" role="form">
							
							<h3 class="header smaller no-margin-top"><small>AEP Application</small></h3>
							
							<div class="form-group">
								<label class="col-sm-6 control-label no-padding-right" for="expat-aep-application">Application date</label>

								<div class="col-sm-6">
									<input class="form-control" type="text" id="expat-aep-application" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-6 control-label no-padding-right" for="expat-aep-approval">Tentative approval date</label>

								<div class="col-sm-6">
									<input class="form-control" type="text" id="expat-aep-approval" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-6 control-label no-padding-right" for="expat-aep-issue">Date of issue</label>

								<div class="col-sm-6">
									<input class="form-control" type="text" id="expat-aep-issue" />
								</div>
							</div>
							
							<h3 class="header smaller"><small>CWV Application</small></h3>
							
							<div class="form-group">
								<label class="col-sm-6 control-label no-padding-right" for="expat-cwv-application">Application date</label>

								<div class="col-sm-6">
									<input class="form-control" type="text" id="expat-cwv-application" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-6 control-label no-padding-right" for="expat-cwv-approval">Tentative approval date</label>

								<div class="col-sm-6">
									<input class="form-control" type="text" id="expat-cwv-approval" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-6 control-label no-padding-right" for="expat-cwv-received">PP w/ CWV date received</label>

								<div class="col-sm-6">
									<input class="form-control" type="text" id="expat-cwv-received" />
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="row expat-update">
					<div class="col-sm-12">
						<label for="expat-remarks">Remarks</label>
						<input type="text" class="form-control" id="expat-remarks" />
					</div>
				</div>
				<!-- <div class="hr hr32 hr-dotted expat-update"></div>
				<div class="row">
					<div class="col-sm-12">
						<div class="alert alert-warning">
							<strong>Security check!</strong>
							Input current password to continue.
						</div>
						<form class="form-horizontal" role="form" id="expat-password">
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="vio-description">Password</label>

								<div class="col-sm-10">
									<input class="form-control" type="password" />
								</div>
								
							</div>
						</form>
					</div>
				</div> -->
			</div>

			<div class="modal-footer">
				<button class="btn btn-sm" data-dismiss="modal">
					<i class="ace-icon fa fa-times"></i>
					Cancel
				</button>
				<button class="btn btn-sm btn-primary" id="expat-save">
					<i class="ace-icon fa fa-lock"></i>
					Confirm
				</button>
			</div>
		</div>
	</div>
</div>