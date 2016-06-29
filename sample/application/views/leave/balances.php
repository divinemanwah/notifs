<style>
.widget-box[class*=widget-color-]>.widget-header div.chosen-drop {
  color: #000000 !important;
}
</style>
<div class="col-xs-12">
	<div class="widget-box widget-color-green2">
		<div class="widget-header">
			<h4 class="widget-title">Leave Balances</h4>
			<div class="widget-toolbar no-border">
		      <button class="btn btn-xs btn-success" id="tk-search-btn">
				<i class="ace-icon fa fa-search"></i> Search
			  </button>
		    </div>
			<div class="widget-toolbar no-border">
				<select id="tk-filter-year" class="chosen-select" data-placeholder="Select Year">
					<?php
					for(;$start_year<=$end_year+1;$start_year++)
						echo '<option value="' . $start_year . '" ' . ($start_year==$end_year?"selected='selected'":"") . ' >' . $start_year . '</option>';
					?>
				</select>
			</div>
			<div class="widget-toolbar no-border">
			<? if($allow_search) { ?>
				<select id="tk-filter-emp" class="chosen-select" data-placeholder="Specific Employee">
				  <option value=""></option>
				  <?php
					foreach($emp_list as $emp)
						echo '<option value="' . $emp->mb_no . '" '.($emp_id==$emp->mb_no?"selected='selected'":"").'>' . ($emp->mb_3=="Expat"?$emp->mb_nick:$emp->mb_fname)." ".$emp->mb_lname . '</option>';
				  ?>
				</select>
			<? } else { ?>
			  <select id="tk-filter-emp" class="chosen-select">
				  <?php
					foreach($emp_list as $emp)
					  if($emp_id == $emp->mb_no)
						echo '<option value="' . $emp->mb_no . '">' . ($emp->mb_3=="Expat"?$emp->mb_nick:$emp->mb_fname)." ".$emp->mb_lname . '</option>';
				  ?>
				</select>
			<? } ?>
			</div>
			<div class="widget-toolbar no-border">
				<button class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown" id="tk-filter-le-btn" data-id="0">
					<i class="ace-icon fa fa-filter"></i> Type: All
					<i class="ace-icon fa fa-chevron-down icon-on-right"></i>
				</button>
				<ul class="dropdown-menu dropdown-menu-right" role="menu" id="tk-filter-le">
					<li><a href="#" data-id="local">Local</a></li>
					<li><a href="#" data-id="expat">Expat</a></li>
					<li class="divider"></li>
					<li class="active"><a href="#">All</a></li>
				</ul>
			</div>
			<div class="widget-toolbar no-border">
			<? if($allow_search) { ?>
				<button class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown" id="tk-filter-dept-btn" data-id="0">
					<i class="ace-icon fa fa-filter"></i> Department: All
					<i class="ace-icon fa fa-chevron-down icon-on-right"></i>
				</button>
				<ul class="dropdown-menu dropdown-menu-right" role="menu" id="tk-filter-dept">
					<?php
						foreach($depts as $dept) {
							echo '<li><a href="#" data-id="' . $dept->dept_no . '">' . $dept->dept_name . '</a></li>';
						}
					?>
					<li class="divider"></li>
					<li class="active"><a href="#">All</a></li>
				</ul>
			<? } else { ?>
			  <button class="btn btn-xs btn-success" id="tk-filter-dept-btn" data-id="<?=$emp_dept?>">
				<? foreach($depts as $dept) { ?>
				   <? if($dept->dept_no == $emp_dept) { ?>
				   Department: <?=$dept->dept_name?>
				   <? } ?>
				<? } ?>
			   </button>
			<? } ?>
			</div>
			<div class="widget-toolbar no-border">
				<button class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown" id="tk-filter-emp-stat-btn" data-id="0">
					<i class="ace-icon fa fa-filter"></i> Employment: All
					<i class="ace-icon fa fa-chevron-down icon-on-right"></i>
				</button>
				<ul class="dropdown-menu dropdown-menu-right" role="menu" id="tk-filter-emp-stat">
					<li><a href="#" data-id="1">Probationary</a></li>
					<li><a href="#" data-id="2">Confirmed</a></li>
					<li class="divider"></li>
					<li class="active"><a href="#">All</a></li>
				</ul>
			</div>
		</div>
		
		<div class="widget-body" id="gen-setting">
			<div class="widget-main no-padding">
			  <div style="overflow: auto; height: 400px; position: relative; background-color: rgb(216, 216, 216);">
			    <div id="tk-table-loader" class="hidden" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-refresh fa-spin"></i> Loading...</div>
			    <div id="tk-table-no-record" class="hidden" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-warning"></i> No Record Found</div>
			    <table id="tk-leave-table" class="table table-striped table-bordered table-hover" width="100%"></table>
			  </div>
			  <div id="tk-leave-pager"></div>
			  <div class="widget-toolbar no-border">
				<form id="exportForm" action="<?=base_url("leave/exportBalances")?>" method="POST">
				  <input type="hidden" name="export-dept" value=""/>
				  <input type="hidden" name="export-emp" value=""/>
				  <input type="hidden" name="export-type" value=""/>
				  <input type="hidden" name="export-emp-stat" value=""/>
				  <input type="hidden" name="export-year" value=""/>
				  <button class="btn btn-xs btn-success">Export</button>
				</form>
			  </div>
			</div>
		</div>
	</div>
</div>

<div id="request-modal" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Modify Leave Balance</h4>
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
							<input class="form-control" type="hidden" id="mb-no" name="mb-no" />
							<input class="form-control" type="hidden" id="lv-year" name="lv-year" />
							<h4 id="emp-header"></h4>
							<table class="table">
							  <thead>
							    <th>Leave Code</th>
								<th>Leave Name</th>
								<th>Balance</th>
								<th>Pending</th>
								<th>Allocated</th>
								<th>Used</th>
								<th>Paid</th>
								<th>Forfeited</th>
							  </thead>
							  <tbody id="leave-table">
							  </tbody>
							</table>
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


