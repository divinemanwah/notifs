<div class="col-xs-12 col-sm-3">
	<div class="widget-box widget-color-green2">
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
			  <form id="gen-form">
				<div>
					<label for="default_period">Default First Day of the Schedule</label>
					<select class="form-control" id="default_period" name="default_period">
					<? for ($day=1;$day<31;$day++) { $day=($day==30?31:$day);?>
					  <option value="<?=$day?>" <?=$data[0]->default_period==$day?"selected":""?>><?=($day==31?"End-of-the-month":$day)?></option>
					<? } ?>
					</select>
				</div>
				<p class="text-muted" id="schedule-pattern">e.g. March <span id="sched-day-from">1</span> to <span id="sched-month-to">March</span> <span id="sched-day-to">31</span></p>
				<div>
					<label for="default_sub_day">Deadline for Submission of Schedule</label>
					<select class="form-control" id="default_sched_day" name="default_sched_day">
					<? for ($day=1;$day<31;$day++) { ?>
					  <option value="<?=$day?>" <?=$data[0]->default_sched_day==$day?"selected":""?>><?=($day==30?"End-of-the-month":$day)?></option>
					<? } ?>
					</select>
				</div>
				<br/>
				<div class="center">
					<button type="button" class="btn btn-sm btn-success" id="gen-set-save">
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
	<div class="widget-box widget-color-blue3">
		<div class="widget-header">
			<h4 class="widget-title">Shift Codes</h4>
			<div class="widget-toolbar no-border">
			  <button class="btn btn-xs btn-purple" id="add-shift-btn">
				<i class="ace-icon fa fa-plus"></i>
				Add
			  </button>
		    </div>
			<div class="widget-toolbar no-border">
			  <label class="small">Show inactive
				<input id="shift-display-inactive" class="ace ace-switch ace-switch-3" type="checkbox" />
				<span class="lbl middle"></span>
			  </label>
		    </div>
		</div>

		<div class="widget-body">
			<div class="widget-main no-padding">
				<div class="">
					<table id="shift-codes-table" class="table table-striped table-bordered table-hover" width="100%">
						<thead>
							<tr>
								<th class="center">
									<label class="position-relative">
										<input type="checkbox" class="ace" />
										<span class="lbl"></span>
									</label>
								</th>
								<th>Shift Code</th>
								<th>
									<i class="ace-icon fa fa-clock-o bigger-110 hidden-480"></i>
									Start
								</th>
								<th>
									<i class="ace-icon fa fa-clock-o bigger-110 hidden-480"></i>
									End
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

<div id="shifts-modal" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Update Shift</h4>
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
						<form class="form-horizontal" role="form" id="shift">
							<input class="form-control" type="hidden" id="shift-id" name="shift-id" />
							<h3 class="header smaller no-margin-top"><small>Shift Information</small></h3>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label class="col-sm-5 control-label no-padding-right" for="shift-code">Shift Code</label>
										<div class="col-sm-7">
											<input class="form-control" type="text" id="shift-code" name="shift-code" />
										</div>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label class="col-sm-5 control-label no-padding-right" for="shift-status">Enabled</label>
										<div class="col-sm-7">
											<select class="form-control" id="shift-status" name="shift-status">
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
										<label class="col-sm-5 control-label no-padding-right" for="shift-start">Start Shift</label>
										<div class="col-sm-7">
										  <div class="input-group">
											<input class="form-control timepicker" type="text" id="shift-start" name="shift-start" />
											<span class="input-group-addon">
												<i class="fa fa-clock-o bigger-110"></i>
											</span>
										  </div>
										</div>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label class="col-sm-5 control-label no-padding-right" for="shift-end">End Shift</label>
										<div class="col-sm-7">
										  <div class="input-group">
											<input class="form-control timepicker" type="text" id="shift-end" name="shift-end" />
											<span class="input-group-addon">
												<i class="fa fa-clock-o bigger-110"></i>
											</span>
										  </div>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label class="col-sm-5 control-label no-padding-right" for="shift-color">Color</label>
										<div class="col-sm-7">
										  <div class="input-group">
											<select class="hide colorpicker" type="text" id="shift-color" name="shift-color">
											  <option value="#ac725e">#ac725e</option>
											  <option value="#d06b64">#d06b64</option>
											  <option value="#f83a22">#f83a22</option>
											  <option value="#fa573c">#fa573c</option>
											  <option value="#ff7537">#ff7537</option>
											  <option value="#ffad46">#ffad46</option>
											  <option value="#42d692">#42d692</option>
											  <option value="#16a765">#16a765</option>
											  <option value="#7bd148">#7bd148</option>
											  <option value="#b3dc6c">#b3dc6c</option>
											  <option value="#fbe983">#fbe983</option>
											  <option value="#fad165">#fad165</option>
											  <option value="#92e1c0">#92e1c0</option>
											  <option value="#9fe1e7">#9fe1e7</option>
											  <option value="#9fc6e7">#9fc6e7</option>
											  <option value="#4986e7">#4986e7</option>
											  <option value="#9a9cff">#9a9cff</option>
											  <option value="#b99aff">#b99aff</option>
											  <option value="#c2c2c2">#c2c2c2</option>
											  <option value="#cabdbf">#cabdbf</option>
											  <option value="#cca6ac">#cca6ac</option>
											  <option value="#f691b2">#f691b2</option>
											  <option value="#cd74e6">#cd74e6</option>
											  <option value="#555555">#555555</option>
											  <option value="#dddddd">#dddddd</option>
											</select>
										  </div>
										</div>
									</div>
								</div>
							</div>
						    <div class="row">
							  <div class="col-sm-6">
							    <h3 class="header smaller no-margin-top"><small>Department - Schedule</small></h3>
							    <div class="row">
							      <div class="col-sm-12">
							        <label>
								      <input id="sched-dept-all" class="ace ace-checkbox" type="checkbox">
								      <span class="lbl"> All</span>
							        </label>
							      </div>
							    </div>
								<div class="row">
								  <? foreach($depts as $dept) { ?>
								  <div class="col-sm-4">
									<label>
									  <input name="sched-dept[]" value="<?=$dept->dept_no?>" class="ace ace-checkbox sched-depts" type="checkbox">
									  <span class="lbl"> <?=$dept->dept_name?></span>
									</label>
								  </div>
								  <? } ?>
								</div>
							  </div>
							  <div class="col-sm-6">
							    <h3 class="header smaller no-margin-top"><small>Department - CWS</small></h3>
							    <div class="row">
							      <div class="col-sm-12">
							        <label>
								      <input id="cws-dept-all" class="ace ace-checkbox" type="checkbox">
								      <span class="lbl"> All</span>
							        </label>
							      </div>
							    </div>
								<div class="row">
								  <? foreach($depts as $dept) { ?>
								  <div class="col-sm-4">
									<label>
									  <input name="cws-dept[]" value="<?=$dept->dept_no?>" class="ace ace-checkbox cws-depts" type="checkbox">
									  <span class="lbl"> <?=$dept->dept_name?></span>
									</label>
								  </div>
								  <? } ?>
								</div>
							  </div>
						    </div>
							<div class="row">
							  <div class="col-sm-12">
							    <h3 class="header smaller no-margin-top"><small>Fixed for Employee - Schedule</small></h3>
							  </div>
							  <div class="col-sm-12">
							    <select id="sched-user" name="sched-user[]" class="chosen-select" multiple data-placeholder="Set Employee...">
								<? foreach($emp_list as $emp) { ?>
								<option value="<?=$emp->mb_no?>"><?=$emp->mb_nick." ".$emp->mb_lname?></option>
								<? } ?>
								</select>
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
				<button class="btn btn-sm btn-primary modal-action-btn" id="shift-save">
					<i class="ace-icon fa fa-save"></i>
					Confirm
				</button>
			</div>
		</div>
	</div>
</div>

<div id="add-shifts-modal" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Add Shift</h4>
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
						<form class="form-horizontal" role="form" id="add-shift">
							<h3 class="header smaller no-margin-top"><small>Shift Information</small></h3>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label class="col-sm-5 control-label no-padding-right" for="add-shift-code">Shift Code</label>
										<div class="col-sm-7">
											<input class="form-control" type="text" id="add-shift-code" name="add-shift-code" />
										</div>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label class="col-sm-5 control-label no-padding-right" for="add-shift-status">Enabled</label>
										<div class="col-sm-7">
											<select class="form-control" id="add-shift-status" name="add-shift-status">
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
										<label class="col-sm-5 control-label no-padding-right" for="add-shift-start">Start Shift</label>
										<div class="col-sm-7">
										  <div class="input-group">
											<input class="form-control timepicker" type="text" id="add-shift-start" name="add-shift-start" />
											<span class="input-group-addon">
												<i class="fa fa-clock-o bigger-110"></i>
											</span>
										  </div>
										</div>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label class="col-sm-5 control-label no-padding-right" for="add-shift-end">End Shift</label>
										<div class="col-sm-7">
										  <div class="input-group">
											<input class="form-control timepicker" type="text" id="add-shift-end" name="add-shift-end" />
											<span class="input-group-addon">
												<i class="fa fa-clock-o bigger-110"></i>
											</span>
										  </div>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label class="col-sm-5 control-label no-padding-right" for="shift-color">Color</label>
										<div class="col-sm-7">
										  <div class="input-group">
											<select class="hide colorpicker" type="text" id="add-shift-color" name="add-shift-color">
											  <option value="#ac725e">#ac725e</option>
											  <option value="#d06b64">#d06b64</option>
											  <option value="#f83a22">#f83a22</option>
											  <option value="#fa573c">#fa573c</option>
											  <option value="#ff7537">#ff7537</option>
											  <option value="#ffad46">#ffad46</option>
											  <option value="#42d692">#42d692</option>
											  <option value="#16a765">#16a765</option>
											  <option value="#7bd148">#7bd148</option>
											  <option value="#b3dc6c">#b3dc6c</option>
											  <option value="#fbe983">#fbe983</option>
											  <option value="#fad165">#fad165</option>
											  <option value="#92e1c0">#92e1c0</option>
											  <option value="#9fe1e7">#9fe1e7</option>
											  <option value="#9fc6e7">#9fc6e7</option>
											  <option value="#4986e7">#4986e7</option>
											  <option value="#9a9cff">#9a9cff</option>
											  <option value="#b99aff">#b99aff</option>
											  <option value="#c2c2c2">#c2c2c2</option>
											  <option value="#cabdbf">#cabdbf</option>
											  <option value="#cca6ac">#cca6ac</option>
											  <option value="#f691b2">#f691b2</option>
											  <option value="#cd74e6">#cd74e6</option>
											  <option value="#555555">#555555</option>
											  <option value="#dddddd">#dddddd</option>
											</select>
										  </div>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
							  <div class="col-sm-6">
							    <h3 class="header smaller no-margin-top"><small>Department - Schedule</small></h3>
							    <div class="row">
							      <div class="col-sm-12">
							        <label>
								      <input id="add-sched-dept-all" class="ace ace-checkbox" type="checkbox">
								      <span class="lbl"> All</span>
							        </label>
							      </div>
							    </div>
								<div class="row">
								  <? foreach($depts as $dept) { ?>
								  <div class="col-sm-4">
									<label>
									  <input name="add-sched-dept[]" value="<?=$dept->dept_no?>" class="ace ace-checkbox add-sched-depts" type="checkbox">
									  <span class="lbl"> <?=$dept->dept_name?></span>
									</label>
								  </div>
								  <? } ?>
								</div>
							  </div>
							  <div class="col-sm-6">
							    <h3 class="header smaller no-margin-top"><small>Department - CWS</small></h3>
							    <div class="row">
							      <div class="col-sm-12">
							        <label>
								      <input id="add-cws-dept-all" class="ace ace-checkbox" type="checkbox">
								      <span class="lbl"> All</span>
							        </label>
							      </div>
							    </div>
								<div class="row">
								  <? foreach($depts as $dept) { ?>
								  <div class="col-sm-4">
									<label>
									  <input name="add-cws-dept[]" value="<?=$dept->dept_no?>" class="ace ace-checkbox add-cws-depts" type="checkbox">
									  <span class="lbl"> <?=$dept->dept_name?></span>
									</label>
								  </div>
								  <? } ?>
								</div>
							  </div>
						    </div>
							<div class="row">
							  <div class="col-sm-12">
							    <h3 class="header smaller no-margin-top"><small>Fixed for Employee - Schedule</small></h3>
							  </div>
							  <div class="col-sm-12">
							    <select id="add-sched-user" name="add-sched-user[]" class="chosen-select" multiple data-placeholder="Set Employee...">
								<? foreach($emp_list as $emp) { ?>
								<option value="<?=$emp->mb_no?>"><?=$emp->mb_nick." ".$emp->mb_lname?></option>
								<? } ?>
								</select>
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
				<button class="btn btn-sm btn-primary modal-action-btn" id="add-shift-save">
					<i class="ace-icon fa fa-save"></i>
					Confirm
				</button>
			</div>
		</div>
	</div>
</div>
