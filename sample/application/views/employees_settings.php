<div class="row">
	<div class="col-sm-6 widget-container-col">
	 
		 <div class="widget-box widget-color-blue">
			<div class="widget-header">
				<h5 class="widget-title smaller">Department Heads</h5>

				<!-- <div class="widget-toolbar no-border">
					<button class="btn btn-xs btn-primary off-pen-add-btn" data-toggle="modal" data-target="#off-pen-modal" data-backdrop="static" data-type="offense">
						<i class="ace-icon fa fa-save"></i>
						Save
					</button>
				</div>
				<div class="widget-toolbar no-border">
					<label class="small">Show disabled
						<input id="off-display-disabled" class="ace ace-switch ace-switch-3" type="checkbox" />
						<span class="lbl middle"></span>
					</label>
				</div> -->
			</div>

			<div class="widget-body">
				<div class="widget-main no-padding">
					
					<table id="off-table" class="table table-striped table-bordered table-hover" style="width: 100%; table-layout: fixed;">
						<thead>
							<tr>
								<th class="center" width="10%">
									<label class="position-relative">
										<input type="checkbox" class="ace" />
										<span class="lbl"></span>
									</label>
								</th>
								<th width="40%">Department</th>
								<th width="50%">Head</th>
							</tr>
						</thead>
						<tbody>
							<?php

								foreach($depts as $dept)
									echo
										"<tr>
											<td class=\"center\">
												<label class=\"position-relative\">
													<input type=\"checkbox\" class=\"ace\" />
													<span class=\"lbl\"></span>
												</label>
											</td>
											<td width=\"40%\">{$dept->dept_name}</td>
											<td width=\"50%\"><a href=\"#\" class=\"head-name dept-" . $dept->dept_no . ($dept->id ? '' : ' text-muted') . "\" data-id=\"" . intval($dept->hr_users_id, 10) . "\" data-dept=\"" . $dept->dept_no . "\" data-toggle=\"modal\" data-target=\"#emp-settings-modal\" data-backdrop=\"static\">" . ($dept->id ? $dept->first_name . ' ' . $dept->last_name : 'Not set') . "</a></td>
										</tr>";
								
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

	</div>
	
	<!-- <div class="col-sm-6 widget-container-col">
	 
		 <div class="widget-box widget-color-blue">
			<div class="widget-header">
				<h5 class="widget-title smaller">Penalties</h5>

				<div class="widget-toolbar no-border">
					<button class="btn btn-xs btn-primary off-pen-add-btn" data-toggle="modal" data-target="#off-pen-modal" data-backdrop="static" data-type="penalty">
						<i class="ace-icon fa fa-plus"></i>
						Add
					</button>
				</div>
				<div class="widget-toolbar no-border">
					<label class="small">Show disabled
						<input id="pen-display-disabled" class="ace ace-switch ace-switch-3" type="checkbox" />
						<span class="lbl middle"></span>
					</label>
				</div>
			</div>

			<div class="widget-body">
				<div class="widget-main no-padding">
					<table id="pen-table" class="table table-striped table-bordered table-hover" style="width: 100%; table-layout: fixed;">
						<thead>
							<tr>
								<th class="center" width="10%">
									<label class="position-relative">
										<input type="checkbox" class="ace" />
										<span class="lbl"></span>
									</label>
								</th>
								<th width="50%">Description</th>
								<th width="20%">Status</th>
								<th width="20%"></th>
								<th></th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>

	</div> -->
</div>

<div id="emp-settings-modal" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Select an employee</h4>
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

							Something went wrong while saving your data.
							<br />
						</div>
					</div>
				</div>
				<div class="row">.
					<div class="col-xs-12">
						<div class="form-group">
							<span id="emp-settings-loading">Loading&hellip;</span>
							<select id="emp-settings-select" class="chosen-select" data-placeholder="Employee ID - Name" style="width: 90%;"></select>
						</div>
					</div>
				</div>
				
			</div>

			<div class="modal-footer">
				<button class="btn btn-sm" data-dismiss="modal">
					<i class="ace-icon fa fa-times"></i>
					Cancel
				</button>
				<button class="btn btn-sm btn-primary" id="emp-settings-save">
					<i class="ace-icon fa fa-check"></i>
					Save
				</button>
			</div>
		</div>
	</div>
</div>