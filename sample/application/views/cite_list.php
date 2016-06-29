<div class="widget-box widget-color-blue">
	<div class="widget-header">
		<h5 class="widget-title smaller">Use the search box on the right to look for specific records</h5>
		<!-- <div class="widget-toolbar no-border">
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
		</div> -->
		<div class="widget-toolbar no-border">
			<button class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" id="cite-filter-btn">
				<i class="ace-icon fa fa-filter"></i> Show all
				<i class="ace-icon fa fa-chevron-down icon-on-right"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu" id="cite-filter-status">
				<?php if(!isset($_id)): ?>
				<li><a href="#" data-id="0">Pending</a></li>
				<?php endif; ?>
				<li><a href="#" data-id="1">For explanation</a></li>
				<li><a href="#" data-id="2">For investigation</a></li>
				<li><a href="#" data-id="3">Closed</a></li>
				<li><a href="#" data-id="4">Cancelled</a></li>
				<li class="divider"></li>
				<li class="active"><a href="#">Show all</a></li>
			</ul>
		</div>
	</div>

	<div class="widget-body">
		<div class="widget-main no-padding">
		<table id="rec-table" class="table table-striped table-bordered table-hover small" style="width: 100%; table-layout: fixed;"<?=($_id ? ' data-id="' . $_id . '"' : '')?>>
			<thead>
				<tr>
					<th class="center" width="4%">
						<label class="position-relative">
							<input type="checkbox" class="ace" />
							<span class="lbl"></span>
						</label>
					</th>
					<th width="5%">ID</th>
					<th width="6%"><span title="Last name">L. name</span></th>
					<th width="7%"><span title="First name">F. name</span></th>
					<th width="5%"><span title="Nickname">Nick.</span></th>

					<th width="7%">
						<span title="Department">Dept.</span>
					</th>
					<th width="5%">
						<span title="Cite code">Cite</span>
					</th>
					<th width="6%">
						<span title="Nationality">Nation.</span>
					</th>
					<th width="6%">
						<!-- <span title="Gender">
							<i class="ace-icon fa fa-male"></i>&nbsp;/&nbsp;<i class="ace-icon fa fa-female"></i>
						</span> -->
						Gender
					</th>
					<th width="6%">
						<span title="Supervisor">Super.</span>
					</th>
					<th width="11%">
						Offense
					</th>
					<th width="8%">
						<span title="Date of notice to explain">N.T.E.</span>
					</th>
					<th width="9%">
						Penalty
					</th>
					<th width="10%">
						Status
					</th>

					<th width="7%"></th>
				</tr>
			</thead>
		</table>
		</div>
	</div>
</div>

<div id="cite-modal" class="modal fade" data-backdrop="static" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Update Cite Record</h4>
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
					<div class="col-sm-12">
						<table class="table table-bordered" style="width: 100%; table-layout: fixed;">
							<tbody>
								<tr>
									<td width="18%">Name</td>
									<td width="82%"><span id="cite-modal-name"></span></td>
								</tr>
								<tr>
									<td width="18%">Offense</td>
									<td width="82%"><span id="cite-modal-offense"></span></td>
								</tr>
								<tr>
									<td width="100%" colspan="2" class="text-center"><a href="#" id="cite-details">View cite details</a></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="row">
					<?php if(isset($_id)): ?>
					<div class="col-sm-12">
						<label for="explanation" style="width: 100%;">Explanation&nbsp;<span class="label label-warning" id="explain-until">09-15-2015</span></label>
						<textarea class="form-control limited" id="explanation" maxlength="50"></textarea>					
					</div>
					<?php else: ?>
					<div class="col-sm-4">
						<div class="control-group">
							<label class="control-label">Status</label>
							<div class="radio">
								<label>
									<input name="cite-status" class="ace" type="radio" checked="checked" value="0" />
									<span class="lbl"> <span class="label label-danger">Pending</span></span>
								</label>
							</div>
							<div class="radio">
								<label>
									<input name="cite-status" class="ace" type="radio" value="1" />
									<span class="lbl"> <span class="label label-yellow">For explanation</span></span>
								</label>
							</div>
							<div class="radio">
								<label>
									<input name="cite-status" class="ace" type="radio" value="2" />
									<span class="lbl"> <span class="label label-info">For investigation</span></span>
								</label>
							</div>
							<div class="radio">
								<label>
									<input name="cite-status" class="ace" type="radio" value="3" />
									<span class="lbl"> <span class="label label-success">Closed</span></span>
								</label>
							</div>
							<div class="radio">
								<label>
									<input name="cite-status" class="ace" type="radio" value="4" />
									<span class="lbl"> <span class="label">Cancelled</span></span>
								</label>
							</div>
						</div>
					</div>
					<div class="col-sm-8">
						<form class="form-horizontal" role="form">
							<div class="form-group">
								<label class="col-sm-5 control-label" for="cite-code">Cite code</label>
								<div class="col-sm-7">
									<input id="cite-code" class="form-control" type="text" />
								</div>
							</div>
							<!-- <div class="form-group">
								<label class="col-sm-5 control-label" for="cite-doc">Date of commission</label>

								<div class="col-sm-7">
									<input id="cite-doc" type="text" />
								</div>
							</div> -->
							<div class="form-group">
								<label class="col-sm-5 control-label" for="cite-penalty">Penalty</label>
								<div class="col-sm-7">
									<select id="cite-penalty" class="form-control" disabled="disabled"></select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-5 control-label" for="cite-type">Type</label>
								<div class="col-sm-7">
									<select id="cite-type" class="form-control" disabled="disabled">
										<option value="1">Minor</option>
										<option value="2">Major</option>
										<option value="3">Zero tolerance</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-5 control-label" for="cite-nte">Notice to explain</label>

								<div class="col-sm-7">
									<div class="input-group">
										<input class="form-control" id="cite-nte" type="text" data-date-format="MM-DD-YYYY HH:mm" />
										<span class="input-group-addon">
											<i class="fa fa-clock-o bigger-110"></i>
										</span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-5 control-label" for="cite-nte-deadline">NTE deadline</label>

								<div class="col-sm-7">
									<div class="input-group">
										<input class="form-control" id="cite-nte-deadline" type="text" readonly="readonly" />
										<span class="input-group-addon">
											<i class="fa fa-clock-o bigger-110"></i>
										</span>
									</div>
								</div>
							</div>
						</form>
					</div>
					<?php endif; ?>
				</div>
				<?php if(!isset($_id)): ?>
				<div class="row">
					<div class="col-sm-12">
						<form class="form-horizontal" role="form">
							<div class="form-group">
								<label class="col-sm-2 control-label" for="cite-remarks">Remarks</label>
								<div class="col-sm-10">
									<input id="cite-remarks" class="form-control" type="text" placeholder="Optional" />
								</div>
							</div>
						</form>
					</div>
				</div>
				<?php endif; ?>
			</div>

			<div class="modal-footer">
				<button class="btn btn-sm" data-dismiss="modal">
					<i class="ace-icon fa fa-times"></i>
					Cancel
				</button>
				<button class="btn btn-sm btn-primary" id="cite-save">
					<i class="ace-icon fa fa-check"></i>
					<?=(isset($_id) ? 'Submit' : 'Save')?>
				</button>
			</div>
		</div>
	</div>
</div>