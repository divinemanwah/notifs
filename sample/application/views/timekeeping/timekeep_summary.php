<div class="widget-box widget-color-blue">
	<div class="widget-header">
		<h5 class="widget-title smaller">Use the search box on the right to look for specific records</h5>
		<div class="widget-toolbar no-border">
			<button class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" id="tk-filter-sched-btn" data-id="2014-1">
				<i class="ace-icon fa fa-filter"></i> Schedule Period: 2013-12-26 ~ 2014-01-25
				<i class="ace-icon fa fa-chevron-down icon-on-right"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu" id="tk-filter-sched">
				<li class="active"><a href="#" data-id="2014-1">2013-12-26 ~ 2014-01-25</a></li>
				<li><a href="#" data-id="2014-2">2013-01-26 ~ 2014-02-25</a></li>
			</ul>
		</div>
		<div class="widget-toolbar no-border">
			<button class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" id="tk-filter-dept-btn" data-id="0">
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
			<table id="tk-summary-table" class="table table-striped table-bordered table-hover" width="100%">
				<thead>
					<tr>
						<th>Department</th>
						<th>Date Submitted</th>
						<th>Status</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
</div>