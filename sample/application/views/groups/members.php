<div class="col-xs-12 col-sm-12">
	<div class="widget-box widget-color-blue2">
		<div class="widget-header">
			<h4 class="widget-title">Group Members</h4>
			<div class="widget-toolbar no-border">
			  <button class="btn btn-xs btn-info" id="grp-search-btn">
			    <i class="ace-icon fa fa-search"></i> Search
			  </button>
			</div>
			<div class="widget-toolbar no-border">
			  <button class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown" id="grp-filter-group-btn" data-id="">
				<i class="ace-icon fa fa-filter"></i> Group: All
				<i class="ace-icon fa fa-chevron-down icon-on-right"></i>
			  </button>
			  <ul class="dropdown-menu dropdown-menu-right" role="menu" id="grp-filter-group">
				<? foreach($groups as $group) { ?>
					<li><a href="#" data-id="<?=$group->id?>"><?=$group->group_name?></a></li>
				<? } ?>
				<li class="divider"></li>
				<li class="active"><a href="#">All</a></li>
			  </ul>
			</div>
		</div>

		<div class="widget-body">
			<div class="widget-main no-padding">
				<div class="">
					<table id="group-members-table" class="table table-striped table-bordered table-hover" width="100%">
						<thead>
							<tr>
                                                                <th>Group Name</th>
								<th>Employee ID</th>
								<th>Nickname</th>
								<th>First Name</th>
								<th>Last Name</th>
								<th class="hidden-480"></th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="add-group-member-modal" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Group Member Information</h4>
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
						<form class="form-horizontal" role="form" id="add-group-member">
							<input type="hidden" name="hr_users_id" id="hr_users_id" />
							<h3 class="header smaller no-margin-top"><small>Group Information</small></h3>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label class="col-sm-6 control-label no-padding-right" >Employee</label>
										<label id="emp-name" class="col-sm-6 no-padding-right" ></label>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label class="col-sm-6 control-label no-padding-right" for="add-group-member-code">Group Code</label>
										<div class="col-sm-6">
											<select id ="add-group-member-code" name="add-group-member-code" class="form-control">
											<option value="">N/A</option>
											<? foreach($groups as $group) { ?>
												<option value="<?=$group->id?>"><?=$group->group_name?></option>
											<? } ?>
											</select>
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
				<button class="btn btn-sm btn-primary modal-action-btn" id="add-group-member-save">
					<i class="ace-icon fa fa-save"></i>
					Confirm
				</button>
			</div>
		</div>
	</div>
</div>
