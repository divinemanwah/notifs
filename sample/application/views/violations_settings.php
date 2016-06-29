<div class="row">
	<div class="col-sm-6">
		<div class="widget-box widget-color-blue">
			<div class="widget-header">
				<h5 class="widget-title smaller">Categories</h5>

				<div class="widget-toolbar no-border">
					<button class="btn btn-xs btn-primary vio-cat-add-btn" data-toggle="modal" data-target="#vio-cat-modal" data-backdrop="static" data-type="offense" disabled="disabled">
						<i class="ace-icon fa fa-plus"></i>
						Add
					</button>
				</div>
				<div class="widget-toolbar no-border">
					<label class="small">Show disabled
						<input id="vio-cat-display-disabled" class="ace ace-switch ace-switch-3" type="checkbox" />
						<span class="lbl middle"></span>
					</label>
				</div>
			</div>

			<div class="widget-body">
				<div class="widget-main no-padding">
					
					<table id="vio-cat-table" class="table table-striped table-bordered table-hover" style="width: 100%; table-layout: fixed;">
						<thead>
							<tr>
								<th class="center" width="6%">
									<label class="position-relative">
										<input type="checkbox" class="ace" />
										<span class="lbl"></span>
									</label>
								</th>
								<th width="54%">Title</th>
								<th width="20%">Status</th>
								<th width="20%"></th>
								<th></th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="widget-box widget-color-blue">
			<div class="widget-header">
				<h5 class="widget-title smaller">Violations</h5>

				<div class="widget-toolbar no-border">
					<button class="btn btn-xs btn-primary vio-add-btn" data-toggle="modal" data-target="#vio-modal" data-backdrop="static" data-type="offense">
						<i class="ace-icon fa fa-plus"></i>
						Add
					</button>
				</div>
				<div class="widget-toolbar no-border">
					<label class="small">Show disabled
						<input id="vio-display-disabled" class="ace ace-switch ace-switch-3" type="checkbox" />
						<span class="lbl middle"></span>
					</label>
				</div>
			</div>

			<div class="widget-body">
				<div class="widget-main no-padding">
					
					<table id="vio-table" class="table table-striped table-bordered table-hover" style="width: 100%; table-layout: fixed;">
						<thead>
							<tr>
								<th class="center" width="6%">
									<label class="position-relative">
										<input type="checkbox" class="ace" />
										<span class="lbl"></span>
									</label>
								</th>
								<th width="54%">Description</th>
								<th width="20%">Status</th>
								<th width="20%"></th>
								<th></th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="vio-modal" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Add new violation</h4>
			</div>

			<div class="modal-body">
				<form autocomplete="off">
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
					<div class="row">
						<div class="form-group">
							<label class="col-sm-2 control-label no-padding-right text-right" for="vio-description">Description</label>

							<div class="col-sm-4">
								<input class="form-control add" type="text" />
								<div class="input-group edit hidden">
									<input class="form-control" type="text" />
									<div class="input-group-btn">
										<button data-toggle="dropdown" class="btn btn-success btn-sm dropdown-toggle">
											Enabled
											<span class="ace-icon fa fa-caret-down icon-on-right"></span>
										</button>

										<ul class="dropdown-menu">
											<li>
												<a href="#" data-color="success">Enabled</a>
											</li>
											<li>
												<a href="#" data-color="warning">Disabled</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
							<label class="col-sm-1 control-label no-padding-right text-right" for="vio-category-2">Category</label>

							<div class="col-sm-5">
								<select id="vio-category-2" class="form-control" disabled="disabled">
									<option>Loading&hellip;</option>
								</select>
							</div>
							
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="space-12"></div>
							<div class="widget-box widget-color-blue">
								<div class="widget-header">
									<h5 class="widget-title smaller">KPI Infraction Matrix</h5>
								</div>
								<div class="widget-body">
									<div class="widget-main no-padding">
										<table class="table table-bordered infraction-table">
											<thead>
												<tr>
													<th>1st</th>
													<th>2nd</th>
													<th>3rd</th>
													<th>4th</th>
													<th>5th</th>
													<th>6th</th>
													<th>Prescriptive Period</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>
														<label class="middle dismissed" title="Dismissal">
															<input class="ace" name="infractions[]" type="checkbox" value="D" />
															<span class="lbl"> D</span>
														</label>
														<div class="space-6"></div>
														<input type="text" class="infraction-score" name="infractions[]" id="infraction-1" />
													</td>
													<td>
														<label class="middle dismissed" title="Dismissal">
															<input class="ace" name="infractions[]" type="checkbox" value="D" />
															<span class="lbl"> D</span>
														</label>
														<div class="space-6"></div>
														<input type="text" class="infraction-score" name="infractions[]" id="infraction-2" />
													</td>
													<td>
														<label class="middle dismissed" title="Dismissal">
															<input class="ace" name="infractions[]" type="checkbox" value="D" />
															<span class="lbl"> D</span>
														</label>
														<div class="space-6"></div>
														<input type="text" class="infraction-score" name="infractions[]" id="infraction-3" />
													</td>
													<td>
														<label class="middle dismissed" title="Dismissal">
															<input class="ace" name="infractions[]" type="checkbox" value="D" />
															<span class="lbl"> D</span>
														</label>
														<div class="space-6"></div>
														<input type="text" class="infraction-score" name="infractions[]" id="infraction-4" />
													</td>
													<td>
														<label class="middle dismissed" title="Dismissal">
															<input class="ace" name="infractions[]" type="checkbox" value="D" />
															<span class="lbl"> D</span>
														</label>
														<div class="space-6"></div>
														<input type="text" class="infraction-score" name="infractions[]" id="infraction-5" />
													</td>
													<td>
														<label class="middle dismissed" title="Dismissal">
															<input class="ace" name="infractions[]" type="checkbox" value="D" />
															<span class="lbl"> D</span>
														</label>
														<div class="space-6"></div>
														<input type="text" class="infraction-score" name="infractions[]" id="infraction-6" />
													</td>
													<td>
														<select name="infra-years" id="infra-years">
															<option value="1">1 year</option>
															<option value="2">2 years</option>
														</select>
														<select name="infra-condition" id="infra-condition">
															<option value="1">from commission</option>
															<option value="2">every end of</option>
														</select>
														<select name="infra-month" id="infra-month" disabled="disabled">
															<?php
															
																$cal_info = cal_info(0);
																
																foreach($cal_info['months'] as $i => $k)
																	echo "<option value=\"$i\">$k</option>";
															
															?>
														</select>
													</td>
												</tr>
												<tr>
													<td colspan="7">
														<label class="middle">
															<input class="ace" name="no-dismissal-switch" type="checkbox" id="no-dismissal-switch" />
															<span class="lbl"> No dismissal</span>
														</label>
														<input type="text" class="infraction-score" name="infractions[]" id="no-dismissal-score" />
														<label class="middle per-incident text-muted">
															<span class="lbl">&nbsp;per incident</span>
														</label>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="space-12"></div>
							<div class="widget-box widget-color-blue">
								<div class="widget-header">
									<h5 class="widget-title smaller">Cite Form Rules</h5>
								</div>
								<div class="widget-body">
									<div class="widget-main no-padding">
										<table class="table table-bordered infraction-table">
											<thead>
												<tr>
													<th></th>
													<th>Frequency</th>
													<th>Offense</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td width="12%">
														<label>
															<input name="vio-rule-switch[]" value="1" class="ace ace-switch ace-switch-3" type="checkbox" />
															<span class="lbl middle"></span>
														</label>
													</td>
													<td>
														Upon commission
													</td>
													<td>
														<select name="vio-rules-offenses[]" disabled="disabled"><option selected="selected">Loading&hellip;</option></select>
													</td>
												</tr>
												<tr>
													<td width="12%">
														<label>
															<input name="vio-rule-switch[]" value="2" class="ace ace-switch ace-switch-3" type="checkbox" />
															<span class="lbl middle"></span>
														</label>
													</td>
													<td>
														Two (2) times in a week (Mon-Sun)
													</td>
													<td>
														<select name="vio-rules-offenses[]" disabled="disabled"><option selected="selected">Loading&hellip;</option></select>
													</td>
												</tr>
												<tr>
													<td width="12%">
														<label>
															<input name="vio-rule-switch[]" value="3" class="ace ace-switch ace-switch-3" type="checkbox" />
															<span class="lbl middle"></span>
														</label>
													</td>
													<td>
														Four (4) times or more in a month
													</td>
													<td>
														<select name="vio-rules-offenses[]" disabled="disabled"><option selected="selected">Loading&hellip;</option></select>
													</td>
												</tr>
												<tr>
													<td width="12%">
														<label>
															<input name="vio-rule-switch[]" value="4" class="ace ace-switch ace-switch-3" type="checkbox" />
															<span class="lbl middle"></span>
														</label>
													</td>
													<td>
														One (1) hour or more within one (1) month
													</td>
													<td>
														<select name="vio-rules-offenses[]" disabled="disabled"><option selected="selected">Loading&hellip;</option></select>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>

			<div class="modal-footer">
				<button class="btn btn-sm" data-dismiss="modal">
					<i class="ace-icon fa fa-times"></i>
					Cancel
				</button>
				<button class="btn btn-sm btn-primary" id="vio-save">
					<i class="ace-icon fa fa-check"></i>
					Save
				</button>
			</div>
		</div>
	</div>
</div>

<div id="vio-cat-modal" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Add new category</h4>
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
				<div class="row">
					<div class="form-group">
						<label class="col-sm-2 control-label no-padding-right" for="vio-cat-description">Title</label>

						<div class="col-sm-10">
							<input class="form-control add" type="text" />
							<div class="input-group edit hidden">
								<input class="form-control" type="text" />
								<div class="input-group-btn">
									<button data-toggle="dropdown" class="btn btn-success btn-sm dropdown-toggle">
										Enabled
										<span class="ace-icon fa fa-caret-down icon-on-right"></span>
									</button>

									<ul class="dropdown-menu">
										<li>
											<a href="#" data-color="success">Enabled</a>
										</li>
										<li>
											<a href="#" data-color="warning">Disabled</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="space-6"></div>
				<div class="row">
					<div class="form-group">
						<label class="col-sm-2 control-label no-padding-right" for="vio-category-1">Parent</label>

						<div class="col-sm-10">
							<select id="vio-category-1" class="form-control" disabled="disabled">
								<option>Loading&hellip;</option>
							</select>
						</div>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button class="btn btn-sm" data-dismiss="modal">
					<i class="ace-icon fa fa-times"></i>
					Cancel
				</button>
				<button class="btn btn-sm btn-primary" id="vio-cat-save">
					<i class="ace-icon fa fa-check"></i>
					Save
				</button>
			</div>
		</div>
	</div>
</div>

<div id="vio-rules-modal" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Rules</h4>
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
				<table class="table table-bordered" style="width: 100%; table-layout: fixed;">
					<tr>
						<td id="vio-rules-description" class="ellipsis"></td>
					</tr>
				</table>
				<div class="space-6"></div>
				<div class="row">
					<div class="col-xs-12">
						<div class="tabbable">
							<ul class="nav nav-tabs padding-12 tab-color-blue background-blue" id="vio-rules-tab">
								<li class="active">
									<a data-toggle="tab" href="#vio-rules-all">All</a>
								</li>

								<li class="hidden">
									<a data-toggle="tab" href="#vio-rules-current">Rule 1</a>
								</li>
							</ul>

							<div class="tab-content">
								<div id="vio-rules-all" class="tab-pane in active">
									<div id="vio-rules-all-list"></div>
									<div>
										<a href="#" id="vio-rules-all-add"><i class="ace-icon fa fa-plus"></i> Add new rule</a>
									</div>
								</div>

								<div id="vio-rules-current" class="tab-pane">
									<table class="table center" style="table-layout: fixed;">
										<thead>
											<tr>
												<th>Cite form</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td style="border-top: none;">
													<div class="clearfix" style="display: inline-block;">
														<div style="float: right; margin-left: 5px;">time<span id="vio-rules-times-plural" class="hidden">s</span> in a <select id="vio-rules-week-month"><option value="1">week</option><option value="2">month</option></select>&nbsp;, then</div>
														<input type="text" class="input-mini" id="vio-rules-times" value="1" />
														<div style="float: right; margin-right: 5px; margin-top: 6px;">If this was repeated for</div>
													</div>
												</td>
											</tr>
											<tr>
												<td style="border-top: none;">
													issue the&nbsp;"&nbsp;<select id="vio-rules-offenses" disabled="disabled"><option selected="selected">Loading&hellip;</option></select>&nbsp;"&nbsp;offense
												</td>
											</tr>
										</tbody>
									</table>
									<table class="table center" style="table-layout: fixed;">
										<thead>
											<tr>
												<th>KPI</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>
													<div class="clearfix" style="display: inline-block;">
														<div style="float: right; margin-left: 5px; margin-top: 6px;">for <span id="sub-comm" title="Per-month scheme">subsequent commissions</span></div>
														<input type="text" class="input-mini" id="vio-rules-minus2" value="0" />
														<div style="float: right; margin-left: 5px; margin-top: 6px;">point<span id="vio-rules-minus-plural" class="hidden">s</span> , then add </div>
														<input type="text" class="input-mini" id="vio-rules-minus" value="0" />
														<div style="float: right; margin-right: 5px; margin-top: 6px;">Subtract</div>
													</div>
												</td>
											</tr>
											<tr>
												<td style="border-top: none;"><span class="text-muted lighter">Note: Point subtractions reset to 0 every 6 months.</span></td>
											</tr>
											<tr id="vio-rules-remove">
												<td>
													<a href="#" class="text-danger"><i class="ace-icon fa fa-trash-o"></i>&nbsp;Remove this rule</a>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button class="btn btn-sm" data-dismiss="modal">
					<i class="ace-icon fa fa-times"></i>
					Cancel
				</button>
				<button class="btn btn-sm btn-primary" id="vio-rules-save" disabled="disabled">
					<i class="ace-icon fa fa-check"></i>
					Save
				</button>
			</div>
		</div>
	</div>
</div>