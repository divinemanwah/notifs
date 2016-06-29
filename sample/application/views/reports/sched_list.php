<style>
.widget-box[class*=widget-color-]>.widget-header div.chosen-drop {
  color: #000000 !important;
}
</style>
<div class="col-12">
	<div class="widget-box widget-color-pink">
		<div class="widget-header">
			<h4 class="widget-title">Schedule Uploads</h4>
			<div class="widget-toolbar no-border">
			  <button class="btn btn-xs btn-pink" id="tk-search-btn">
				<i class="ace-icon fa fa-search"></i>
				Search
			  </button>
		    </div>
			<div class="widget-toolbar no-border">
				<div class="input-daterange input-group" style="margin: 4px 0 0 0;">
					<input type="text" class="input-sm form-control" id="date-from" name="start" style="background-color: #F7F7F7; width: 90px;" value="<?=$cur_period->start?>">
					<span class="input-group-addon btn-pink">
						<i class="fa fa-exchange"></i>
					</span>
					<input type="text" class="input-sm form-control" id="date-to" name="end" style="background-color: #F7F7F7; width: 90px;" value="<?=$cur_period->end?>">
				</div>
			</div>
			<div class="widget-toolbar no-border">
				<button class="btn btn-xs btn-pink dropdown-toggle" data-toggle="dropdown" id="tk-filter-status-btn" data-id="0">
					<i class="ace-icon fa fa-filter"></i> Status: All
					<i class="ace-icon fa fa-chevron-down icon-on-right"></i>
				</button>
				<ul class="dropdown-menu dropdown-menu-right" role="menu" id="tk-filter-status">
				    <li><a href="#" data-id="-1">Pending</a></li>
					<li><a href="#" data-id="1">Submitted</a></li>
					<li><a href="#" data-id="2">Rejected</a></li>
					<li><a href="#" data-id="3">Approved</a></li>
					<li><a href="#" data-id="4">Cancelled</a></li>
					<li class="divider"></li>
					<li class="active"><a href="#">All</a></li>
				</ul>
			</div>
			<div class="widget-toolbar no-border hidden">
				<button class="btn btn-xs btn-pink dropdown-toggle" data-toggle="dropdown" id="tk-filter-dept-btn" data-id="0">
					<i class="ace-icon fa fa-filter"></i> Department: All
					<i class="ace-icon fa fa-chevron-down icon-on-right"></i>
				</button>
				<ul class="dropdown-menu dropdown-menu-right" role="menu" id="tk-filter-dept">
					<?php
						foreach($depts as $dept)
							echo '<li><a href="#" data-id="' . $dept->dept_no . '">' . $dept->dept_name . '</a></li>';
					?>
					<li class="divider"></li>
					<li class="active"><a href="#">All</a></li>
				</ul>
			</div>
		</div>
		
		<div class="widget-body">
			<div class="widget-main no-padding">
			    <div style="overflow: auto; height: 400px; position: relative; background-color: rgb(216, 216, 216);">
				  <div id="tk-sched-loader" class="hidden" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-refresh fa-spin"></i> Loading...</div>
				  <div id="tk-sched-no-record" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-warning"></i> No Record Found</div>
				  <table id="tk-sched-table" class="table table-striped table-bordered table-hover" width="100%"></table>
				</div>
				<div id="tk-sched-pager"></div>
			</div>
		</div>
	</div>
</div>

<div id="request-modal" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-lg">
    <div class="modal-content">
	  <div class="modal-header">
	    <button type="button" data-dismiss="modal" class="close">&times;</button>
		<h4 class="blue bigger">Schedule Approval History</h4>
	  </div>
	  <div class="modal-body">
	    <div id="approval_header">
		  <h3 class="header smaller no-margin-top"><small>Approval Remarks</small></h3>
		</div>
		<div style="max-height: 150px; overflow: auto;" id="approval_list">None</div>
	  </div>
	  <div class="modal-footer">
		  <button class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
	  </div>
	</div>
  </div>
</div>
