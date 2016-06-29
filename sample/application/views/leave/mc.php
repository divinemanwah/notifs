<div class="col-md-10 col-sm-12">
	<div class="widget-box widget-color-green">
		<div class="widget-header">
			<h4 class="widget-title">Medical Certificates</h4>
			<div class="widget-toolbar no-border">
			  <button class="btn btn-xs btn-success" id="add-mc-btn">
				<i class="ace-icon fa fa-plus"></i>
				Add
			  </button>
		    </div>
			<div class="widget-toolbar no-border">
				<select id="tk-filter-emp" class="chosen-select" data-placeholder="Specific Employee">
				  <option value=""></option>
				  <?php
					foreach($emp_list as $emp)
						echo '<option value="' . $emp->mb_no . '">' . ($emp->mb_3=="Expat"?$emp->mb_nick:$emp->mb_fname)." ".$emp->mb_lname . '</option>';
				  ?>
				</select>
			</div>
		</div>

		<div class="widget-body">
			<div class="widget-main no-padding">
			    <div style="overflow: auto; height: 400px; position: relative; background-color: rgb(216, 216, 216);">
				  <div id="tk-mc-loader" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-refresh fa-spin"></i> Loading...</div>
				  <div id="tk-mc-no-record" class="hidden" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-warning"></i> No Record Found</div>
				  <table id="tk-mc-table" class="table table-striped table-bordered table-hover" width="100%"></table>
				</div>
				<div id="tk-mc-pager"></div>
				
			</div>
		</div>
	</div>
</div>

<div id="request-modal" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Medical Certificate</h4>
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
						<form class="form-horizontal" role="form" id="requestForm" novalidate="novalidate">
							<input class="form-control" type="hidden" id="request-id" name="request-id" />
							<h3 class="header smaller no-margin-top"><small>Medical Certificate Information</small></h3>
							<div class="row">
								<div class="form-group col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="control-no">Control No : </label>
									<div class="col-sm-8">
										<input class="form-control" readonly type="text" id="control-no" name="control-no" />
										<span class="help-inline col-12">
											<span class="middle text-muted">This is system generated</span>
										</span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="emp-no">Employee : </label>
									<div class="col-sm-8">
										<select class="form-control input-sm chosen-select" type="text" id="emp-no" name="emp-no" >
										<option value=""></option>
										<? foreach($emp_list as $emp) { ?>
										<option value="<?=$emp->mb_no?>"><?=($emp->mb_3=="Expat"?$emp->mb_nick:$emp->mb_fname)." ".$emp->mb_lname?></option>
										<? } ?>
										</select>
									</div>
								</div>
								<div class="form-group col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="date-submitted">Date Submitted: </label>
									<div class="col-sm-4">
										<input class="form-control input-sm input-date" type="text" id="date-submitted" name="date-submitted" />
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-12">
									<label class="col-sm-2 control-label no-padding-right" for="notes">Remarks :</label>
									<div class="col-sm-8 control-label no-padding-right">
									  <textarea id="notes" style="height: 112px; resize: none;" class="form-control limited" name="notes" maxlength="250" ></textarea>
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
					Close
				</button>
				<button class="btn btn-sm btn-primary modal-action-btn" id="request-save">
					<i class="ace-icon fa fa-save"></i>
					Confirm
				</button>
			</div>
		</div>
	</div>
</div>

<div id="action-modal" class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
	  <div class="modal-header">
	    <button type="button" data-dismiss="modal" class="close">&times;</button>
		<h4 class="blue bigger">Medical Certificate</h4>
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
		  <div class="col-xs-12">
		    <div class="well">
			  <h4 class="red smaller lighter">Are you sure you want to <label id='request-action'>delete</label> Medical Certificate <strong id="request_lbl">&nbsp;</strong>?</h4>
			  <button class="btn btn-sm btn-success modal-action-btn">Yes</button>
			  <button class="btn btn-sm btn-danger" data-dismiss="modal">No</button>
			</div>
			<strong>Note:</strong> This cannot be undone
		  </div>
		</div>
	  </div>
	</div>
  </div>
</div>

<div id="messsage-modal" class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
	  <div class="modal-header">
	    <button type="button" data-dismiss="modal" class="close">&times;</button>
		<h4 class="blue bigger">Alert</h4>
	  </div>
	  <div class="modal-body">
	    <div class="row">
		  <div class="col-xs-12">
		    <div class="well">
			  <h4 class="red smaller lighter" id="message"></h4>
			  <button class="btn btn-sm btn-danger" data-dismiss="modal">Ok</button>
			</div>
		  </div>
		</div>
	  </div>
	</div>
  </div>
</div>
