<style type="text/css" id="expat-style"></style>

<div class="widget-box widget-color-blue">
	<div class="widget-header">
		<h5 class="widget-title smaller">Employee Records</h5>
		<div class="widget-toolbar no-border">
			<button class="btn btn-xs btn-primary" id="emp-add-user">
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
		<div class="widget-toolbar no-border">
			<button class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" id="emp-filter-nation-btn" data-id="0">
				<i class="ace-icon fa fa-filter"></i> Nationality: All
				<i class="ace-icon fa fa-chevron-down icon-on-right"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu" id="emp-filter-nation">
				<li><a href="#" data-id="2">Local</a></li>
				<li><a href="#" data-id="1">Expat</a></li>
				<li class="divider"></li>
				<li class="active"><a href="#" data-id="0">All</a></li>
			</ul>
		</div>
		<div class="widget-toolbar no-border">
			<button class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" id="emp-filter-dept-btn" data-id="0">
				<i class="ace-icon fa fa-filter"></i> Department: All
				<i class="ace-icon fa fa-chevron-down icon-on-right"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right scrollable-menu" role="menu" id="emp-filter-dept">
				<?php

					foreach($depts as $dept)
						echo '<li><a href="#" data-id="' . $dept->dept_no . '">' . $dept->dept_name . '</a></li>';

				?>
				<li class="divider"></li>
				<li class="active"><a href="#">All</a></li>
			</ul>
		</div>
		<div class="widget-toolbar no-border hidden" id="expat-switch">
			<label class="small">Show extended fields
				<input id="emp-display-expat" class="ace ace-switch ace-switch-3" type="checkbox" />
				<span class="lbl middle"></span>
			</label>
		</div>
		<div class="widget-toolbar no-border hidden" id="expat-page">
			<label class="small">Page
				<select id="emp-display-expat-page">
					<option>1</option>
				</select>
			</label>
		</div>
		<div class="widget-toolbar no-border hidden" id="expat-auto-save-container">
			<label class="small"><span class="auto-save-message">Auto save</span>
				<input id="expat-auto-save" class="ace ace-switch ace-switch-3" type="checkbox" checked="checked" />
				<span class="lbl middle"></span>
			</label>
		</div>
		<div class="widget-toolbar no-border hidden" id="expat-save">
			<button class="btn btn-xs btn-primary">
				<i class="ace-icon fa fa-save"></i>
				Save
			</button>
		</div>
	</div>


	<div class="widget-body">
		<div class="widget-main no-padding">
			<div class="row form-inline expat-extended-table-header" style="display: none;">
				<div class="col-sm-6">
					<div class="dataTables_length">
						<label>
							Show <select class="form-control input-sm" aria-controls="emp-table" name="emp-table_length">
								<!-- <option value="10">10</option> -->
								<option value="25">25</option>
								<option value="50">50</option>
								<option value="100">100</option>
							</select> entries
						</label>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="dataTables_filter">
						<label>Search:<input id="expat-search" aria-controls="emp-table" placeholder="" class="form-control input-sm" type="search" /></label>
					</div>
				</div>
			</div>
			<div id="expat-extended-table"></div>
			<table id="emp-table" class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th class="center">
							<label class="position-relative">
								<input type="checkbox" class="ace" id="main-check" />
								<span class="lbl"></span>
							</label>
						</th>
						<th>ID</th>
						<th>Last name</th>
						<th>First name</th>
						<th>Nickname</th>

						<th>
							Department
						</th>
						<th>Nationality</th>
						<th>Status</th>

						<th></th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
</div>

<div id="emp-modal" class="modal fade" tabindex="-1">
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

							<span id="emp-err-msg"></span>
							<br />
						</div>
					</div>
				</div>
                            <div class="row emp-resign hidden">
                                <div class="col-sm-10">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label no-padding-right" for="emp-effective-date">Effective Date:</label>

                                        <div class="col-sm-8">
                                            <div class="input-daterange input-group">
                                                <input class="input-sm form-control" name="emp-effective-date" id="emp-effective-date" placeholder="Present" type="text">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
				<div class="row emp-update">
					<div class="col-sm-12">
						<ul class="nav nav-tabs" role="tablist" id="emp-tab">
							<li class="active"><a href="#emp-tab-general" role="tab" data-toggle="tab">General</a></li>
							<li><a href="#emp-tab-account" role="tab" data-toggle="tab">Account</a></li>
							<li class="hidables"><a href="#emp-tab-photo" role="tab" data-toggle="tab">Photo</a></li>
							<li><a href="#emp-tab-approvers" role="tab" data-toggle="tab">Approvers</a></li>
							<li class="hidden"><a href="#emp-tab-expat" role="tab" data-toggle="tab">Expat Records</a></li>
                            <li ><a href="#emp-tab-access-report" role="tab" data-toggle="tab">Access Report</a></li>
                            <li ><a href="#emp-tab-others" role="tab" data-toggle="tab">Others</a></li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane active" id="emp-tab-general">
								<h3 class="header smaller no-margin-top"><small>Employee Information</small></h3>
								<div class="row">
									<div class="col-sm-6">
										<form class="form-horizontal" role="form">
											
											<div class="form-group hidables">
												<label class="col-sm-4 control-label no-padding-right" for="emp-id">Employee ID</label>

												<div class="col-sm-8">
													<input class="form-control" type="text" id="emp-id" />
												</div>
											</div>
											<div class="form-group hidables">
												<label class="col-sm-4 control-label no-padding-right" for="emp-lname">Last name</label>

												<div class="col-sm-8">
													<input class="form-control" type="text" id="emp-lname" />
												</div>
											</div>
											<div class="form-group hidables">
												<label class="col-sm-4 control-label no-padding-right" for="emp-fname">First name</label>

												<div class="col-sm-8">
													<input class="form-control" type="text" id="emp-fname" />
												</div>
											</div>
											<div class="form-group hidables">
												<label class="col-sm-4 control-label no-padding-right" for="emp-nick">Nickname</label>

												<div class="col-sm-8">
													<input class="form-control" type="text" id="emp-nick" />
												</div>
											</div>
											<div class="form-group hidables">
												<label class="col-sm-4 control-label no-padding-right" for="emp-mname">Middle name</label>

												<div class="col-sm-8">
													<input class="form-control" type="text" id="emp-mname" />
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-4 control-label no-padding-right" for="emp-civil">Civil status</label>

												<div class="col-sm-8">
													<select class="form-control" id="emp-civil">
														<option value="1">Single</option>
														<option value="2">Married</option>
														<option value="3">Widowed</option>
														<option value="4">Separated</option>
														<option value="5">Divorced</option>
													</select>
												</div>
											</div>
											<div class="form-group hidables">
												<label class="col-sm-4 control-label no-padding-right" for="emp-bday">Birthdate</label>

												<div class="col-sm-8">
													<div class="input-group">
														<input class="form-control" type="text" id="emp-bday" />
														<span class="input-group-addon">
															<i class="fa fa-calendar"></i>
														</span>
													</div>
												</div>
											</div>
										</form>
									</div>
									<div class="col-sm-6">
										<form class="form-horizontal" role="form">
											
											<div class="form-group">
												<label class="col-sm-4 control-label no-padding-right" for="emp-sex">Gender</label>

												<div class="col-sm-8">
													<select class="form-control" id="emp-sex">
														<option value="1">Male</option>
														<option value="2">Female</option>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-4 control-label no-padding-right" for="emp-dept">Department</label>

												<div class="col-sm-8">
													<select class="form-control" id="emp-dept">
														<?php

															foreach($depts as $dept)
																echo '<option value="' . $dept->dept_no . '">' . $dept->dept_name . '</option>';

														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-4 control-label no-padding-right" for="emp-job-title">Job title</label>

												<div class="col-sm-8">
													<input class="form-control" type="text" id="emp-job-title" />
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-4 control-label no-padding-right" for="emp-ethnicity">Ethnicity</label>

												<div class="col-sm-8">
													<select class="form-control" id="emp-ethnicity">
														<option value="1">Local</option>
														<option value="2">Expat</option>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-4 control-label no-padding-right" for="emp-doc-start">D.O.C.</label>
												
												<div class="col-sm-8">
													<div class="input-daterange input-group">
														<input class="input-sm form-control" name="emp-doc-start" id="emp-doc-start" type="text" />
														<span class="input-group-addon">
															<i class="fa fa-long-arrow-right"></i>
														</span>

														<input class="input-sm form-control" name="emp-doc-start" id="emp-doc-end" type="text" placeholder="Present" />
													</div>
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-4 control-label no-padding-right" for="emp-status">Status</label>

												<div class="col-sm-8">
													<select class="form-control" id="emp-status">
														<option value="1">Probational</option>
														<option value="2">Regular</option>
													</select>
												</div>
											</div>
                                            <div class="form-group hidden" id="reg-status">
                                                <label class="col-sm-4 control-label no-padding-right" for="reg-status-txt">Confirmation date</label>

                                                <div class="col-sm-8">
                                                    <div class="input-daterange">
                                                        <input class="form-control" name="reg-status-txt" id="reg-status-txt" type="text" readonly>
                                                    </div>
                                                </div>
                                            </div>
											<div class="form-group">
												<label class="col-sm-4 control-label no-padding-right" for="emp-sched-group">Approval Group</label>

												<div class="col-sm-8">
													<select class="form-control" id="emp-sched-group">
														<?php
															foreach($approval_groups as $approval_group)
																if($approval_group->enabled)
																	echo "<option value=\"{$approval_group->apprv_grp_id}\">{$approval_group->group_code}</option>";

														?>
													</select>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
							<div class="tab-pane" id="emp-tab-account">
								<h3 class="header smaller no-margin-top"><small>Account Details</small></h3>
								<div class="row">
									<div class="col-sm-6">
										<form class="form-horizontal" role="form">
											
											<div class="form-group hidables">
												<label class="col-sm-5 control-label no-padding-right" for="emp-username">Username</label>

												<div class="col-sm-7">
													<input class="form-control" type="text" id="emp-username" />
												</div>
											</div>
											<div class="form-group hidables">
												<label class="col-sm-5 control-label no-padding-right" for="emp-email">E-mail</label>

												<div class="col-sm-7">
													<input class="form-control" type="text" id="emp-email" />
												</div>
											</div>
										</form>
									</div>
									<div class="col-sm-6">
										<form class="form-horizontal" role="form">
											
											<div class="form-group">
												<label class="col-sm-5 control-label no-padding-right" for="emp-password">New password</label>

												<div class="col-sm-7">
													<input class="form-control" type="password" id="emp-password" placeholder="Welcome1" />
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-5 control-label no-padding-right" for="emp-password-confirm">Confirm password</label>

												<div class="col-sm-7">
													<input class="form-control" type="password" id="emp-password-confirm" placeholder="Welcome1" />
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
							<div class="tab-pane" id="emp-tab-photo">
								<h3 class="header smaller no-margin-top"><small>Employee Photo</small></h3>
								<!-- <div class="alert alert-warning">
									<button type="button" class="close" data-dismiss="alert">
										<i class="ace-icon fa fa-times"></i>
									</button>
									<strong>Warning!</strong>

									Please do not use. Still under development.
									<br>
								</div> -->
								<div class="row">
									<div class="col-sm-5">
										<div class="pull-right">
											<span class="profile-picture">
												<img style="display: block; width: 150px !important; height: 150px !important;" id="avatar" class="editable img-responsive editable-click editable-empty" src="<?=base_url()?>assets/avatars/default-avatar-male.jpg">
											</span>
										</div>
									</div>
									<div class="col-sm-5">
										<div class="well">
											<h4 class="green smaller lighter">Tips</h4>
											<ul class="fa-ul">
												<li><i class="fa-li fa fa-lightbulb-o"></i>Click on the image to make changes</li>
												<li><i class="fa-li fa fa-lightbulb-o"></i>Ideal image resolution is 150&times;150</li>
												<li><i class="fa-li fa fa-lightbulb-o"></i>Any photo that exceeds the recommended dimensions will be automatically resized</li>
											</ul>
										</div>
									</div>
									<div class="col-sm-2"></div>
								</div>
							</div>
							<div class="tab-pane" id="emp-tab-approvers">
								<h3 class="header smaller no-margin-top"><small>Approver Groups</small></h3>
								<div class="row">
									<div class="col-sm-6">
										<form class="form-horizontal" role="form">
											
											<div class="form-group">
												<label class="col-sm-5 control-label no-padding-right" for="emp-group-ot">O.T. Approver</label>

												<div class="col-sm-7">
													<select class="form-control" id="emp-group-ot">
														<option value="0">None</option>
														<?php
															foreach($approver_ot as $group)
																echo "<option value=\"{$group->ot_apprv_grp_id}\">{$group->ot_group_code}</option>";
														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-5 control-label no-padding-right" for="emp-group-leave">Leave Approver</label>

												<div class="col-sm-7">
													<select class="form-control" id="emp-group-leave">
														<option value="0">None</option>
														<?php
															foreach($approver_leave as $group)
																echo "<option value=\"{$group->lv_apprv_grp_id}\">{$group->lv_group_code}</option>";

														?>
													</select>
												</div>
											</div>
										</form>
									</div>
									<div class="col-sm-6">
										<form class="form-horizontal" role="form">
											
											<div class="form-group">
												<label class="col-sm-5 control-label no-padding-right" for="emp-group-obt">O.B.T. Approver</label>

												<div class="col-sm-7">
													<select class="form-control" id="emp-group-obt">
														<option value="0">None</option>
														<?php
															foreach($approver_obt as $group)
																echo "<option value=\"{$group->obt_apprv_grp_id}\">{$group->obt_group_code}</option>";

														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-5 control-label no-padding-right" for="emp-group-cws">C.W.S. Approver</label>

												<div class="col-sm-7">
													<select class="form-control" id="emp-group-cws">
														<option value="0">None</option>
														<?php
															foreach($approver_cws as $group)
																echo "<option value=\"{$group->cws_apprv_grp_id}\">{$group->group_code}</option>";															
														?>
													</select>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
							<div class="tab-pane hidden" id="emp-tab-expat">
								
								<div class="row">
									<div class="col-sm-6">
										<form class="form-horizontal" role="form">
											<h3 class="header smaller no-margin-top"><small>TIN Application</small></h3>
											
											<div class="form-group">
												<label class="col-sm-6 control-label no-padding-right" for="expat-tin-application">Application date</label>

												<div class="col-sm-6">
													<div class="input-group">
														<input type="text" class="form-control date-picker" id="expat-tin-application" placeholder="mm-dd-yyyy" data-date-format="mm-dd-yyyy" />
														<span class="input-group-addon">
															<i class="fa fa-calendar bigger-110"></i>
														</span>
													</div>
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-6 control-label no-padding-right" for="expat-tin-release">Date released</label>

												<div class="col-sm-6">
													<div class="input-group">
														<input type="text" class="form-control date-picker" id="expat-tin-release" placeholder="mm-dd-yyyy" data-date-format="mm-dd-yyyy" />
														<span class="input-group-addon">
															<i class="fa fa-calendar bigger-110"></i>
														</span>
													</div>
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-6 control-label no-padding-right" for="expat-tin-no">TIN no.</label>

												<div class="col-sm-6">
													<input class="form-control" type="text" id="expat-tin-no" />
												</div>
											</div>
											
											<h3 class="header smaller"><small>AEP Application</small></h3>
											
											<div class="form-group">
												<label class="col-sm-6 control-label no-padding-right" for="expat-aep-application">Application date</label>

												<div class="col-sm-6">
													<div class="input-group">
														<input type="text" class="form-control date-picker" id="expat-aep-application" placeholder="mm-dd-yyyy" data-date-format="mm-dd-yyyy" />
														<span class="input-group-addon">
															<i class="fa fa-calendar bigger-110"></i>
														</span>
													</div>
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-6 control-label no-padding-right" for="expat-aep-approval">Tentative approval date</label>

												<div class="col-sm-6">
													<div class="input-group">
														<input type="text" class="form-control date-picker" id="expat-aep-approval" placeholder="mm-dd-yyyy" data-date-format="mm-dd-yyyy" />
														<span class="input-group-addon">
															<i class="fa fa-calendar bigger-110"></i>
														</span>
													</div>
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-6 control-label no-padding-right" for="expat-aep-issue">Date of issue</label>

												<div class="col-sm-6">
													<div class="input-group">
														<input type="text" class="form-control date-picker" id="expat-aep-issue" placeholder="mm-dd-yyyy" data-date-format="mm-dd-yyyy" />
														<span class="input-group-addon">
															<i class="fa fa-calendar bigger-110"></i>
														</span>
													</div>
												</div>
											</div>
										</form>
									</div>
									<div class="col-sm-6">
										<form class="form-horizontal" role="form">
											
											<h3 class="header smaller no-margin-top"><small>CWV Application</small></h3>
											
											<div class="form-group">
												<label class="col-sm-6 control-label no-padding-right" for="expat-cwv-application">Application date</label>

												<div class="col-sm-6">
													<div class="input-group">
														<input type="text" class="form-control date-picker" id="expat-cwv-application" placeholder="mm-dd-yyyy" data-date-format="mm-dd-yyyy" />
														<span class="input-group-addon">
															<i class="fa fa-calendar bigger-110"></i>
														</span>
													</div>
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-6 control-label no-padding-right" for="expat-cwv-approval">Tentative approval date</label>

												<div class="col-sm-6">
													<div class="input-group">
														<input type="text" class="form-control date-picker" id="expat-cwv-approval" placeholder="mm-dd-yyyy" data-date-format="mm-dd-yyyy" />
														<span class="input-group-addon">
															<i class="fa fa-calendar bigger-110"></i>
														</span>
													</div>
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-6 control-label no-padding-right" for="expat-cwv-received">PP w/ CWV date received</label>

												<div class="col-sm-6">
													<div class="input-group">
														<input type="text" class="form-control date-picker" id="expat-cwv-received" placeholder="mm-dd-yyyy" data-date-format="mm-dd-yyyy" />
														<span class="input-group-addon">
															<i class="fa fa-calendar bigger-110"></i>
														</span>
													</div>
												</div>
											</div>
											
											<label for="expat-remarks">Remarks</label>
											<input type="text" class="form-control" id="expat-remarks" />
										</form>
									</div>
								</div>
							         
							</div>  

                            <div class="tab-pane" id="emp-tab-others">
                                <h3 class="header smaller no-margin-top"><small>Other Details</small></h3>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <form class="form-horizontal" role="form">
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label no-padding-right" for="emp-condo">Condo</label>

                                                <div class="col-sm-8">
                                                    <select class="form-control" id="emp-condo">
                                                        <option value="0">&nbsp;</option>
                                                        <?php
                                                        foreach ($condos as $condo)
                                                            echo '<option value="' . $condo->condo_id . '">' . $condo->condo_name . '</option>';
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                    <div class="col-sm-6">
                                        <form class="form-horizontal" role="form">
                                            <div class="form-group">
                                                <label class="col-sm-10 control-label no-padding-right" for="allowed-group">Access Group
                                                    <input id="allowed-group" class="ace ace-switch ace-switch-3" type="checkbox"> <span class="lbl middle"></span>
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-10 control-label no-padding-right" for="allowed-holiday">Allowed Holiday
                                                    <input id="allowed-holiday" class="ace ace-switch ace-switch-3" type="checkbox"> <span class="lbl middle"></span>
                                                </label>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- access report tab content -->
                            <div class="tab-pane" id="emp-tab-access-report">
                            	
                                <div class="row" >
                                
                                    <div class="col-sm-12">
                                    	
                                        <h3 class="header smaller no-margin-top"><small>Reports</small></h3>
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead class="thin-border-bottom">
                                                <tr>
                                                    <th class="col-sm-3">
                                                        <i class="ace-icon fa fa-files-o"></i>
                                                        Report
                                                    </th>
                                                    <th class="hidden-480 col-sm-6 center">
                                                        Department
                                                    </th>
                                                    <th class="col-sm-3 center">
                                                        Status
                                                    </th>
                                                    <th class="hidden-480 col-sm-1">&nbsp;</th>
                                                </tr>
                                            </thead>
                                            <tbody id="reports_module_tbl">
                                                <tr id="no_report_module"><td colspan=4 class="center">No Record Found</td></tr>
                                            </tbody>
                                            <tfoot class="alert alert-success">
                                                <tr>
                                                    <th class="col-sm-3 form-group">
                                                        <select id="emp_report" name="emp_report" class="chosen-select" placeholder="Choose a report..." >
                                                        	<option value="" >&nbsp; </option> 
                                                             <?php
															 foreach($report_status as $row => $rstatus){ 
															 ?>
															 <option  value="<?=$rstatus['value'];?>" ><?=ucwords($rstatus['label']);?></option>
															 <?php
															 }
															 ?>
                                                        </select>
                                                    </th>
                                                    <th class="hidden-480 col-sm-6 form-group">
                                                        <select id="emp_access_dept" name="emp_access_dept" class="chosen-select" data-placeholder="Choose a department..."  > 
                                                        	<option value="" >&nbsp; </option>
                                                            <?php
															foreach($depts as $dept){
															?>
                                                            <option value="<?=$dept->dept_no?>" ><?=$dept->dept_name?></option>
                                                            <?php
															}
															?> 
                                                        </select>
                                                        <!-- <input id="approver_lvl" type="text" class="col-xs-12"/> -->
                                                    </th>
                                                    <td class="col-sm-1">&nbsp;</td>
                                                    <th class="hidden-480 col-sm-2">
                                                        <button class="btn btn-sm btn-primary" id="report-access-add">
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
                            <!-- access report tab content --> 
                            
						</div>
					</div>
				</div>
				<!-- <div class="hr hr32 hr-dotted emp-update"></div>
				<div class="row">
					<div class="col-sm-12">
						<div class="alert alert-warning">
							<strong><i class="ace-icon fa fa-lock"></i> Security check!</strong>
							Please input your current password to continue.
						</div>
						<form class="form-horizontal" role="form" id="emp-password">
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
				<button class="btn btn-sm btn-primary" id="emp-save">
					<i class="ace-icon fa fa-check"></i>
					Save
				</button>
			</div>
		</div>
	</div>
</div>