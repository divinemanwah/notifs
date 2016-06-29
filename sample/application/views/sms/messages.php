<style>
.widget-box[class*=widget-color-]>.widget-header div.chosen-drop {
  color: #000000 !important;
}
</style>
<div class="col-md-12">
  <div class="widget-box widget-color-green approval-list-widget">
	<div class="widget-header">
	  <h4 class="widget-title">Received SMS Messages</h4>
	  <div class="widget-toolbar no-border">
		<button class="btn btn-xs btn-success" id="sms-search-btn">
		  <i class="ace-icon fa fa-search"></i> Search
		</button>
	  </div>
	  <div class="widget-toolbar no-border">
			<div class="input-daterange input-group" style="margin: 4px 0 0 0;">
				<input type="text" class="input-sm form-control" id="sms-date-from" name="start" style="background-color: #D1FFCA; width: 90px;" value="<?=$today?>">
				<span class="input-group-addon btn-success">
					<i class="fa fa-exchange"></i>
				</span>
				<input type="text" class="input-sm form-control" id="sms-date-to" name="end" style="background-color: #D1FFCA; width: 90px;" value="<?=$today?>">
			</div>
		</div>
		<div class="widget-toolbar no-border">
		  <select id="sms-filter-emp" class="chosen-select" data-placeholder="Specific Employee">
		    <option value=""></option>
		  <?php
			foreach($emp_list as $emp)
				echo '<option value="' . $emp->mb_no . '" '.($emp_id==$emp->mb_no?"selected='selected'":"").'>' . ($emp->mb_3=="Expat"?$emp->mb_nick:$emp->mb_fname)." ".$emp->mb_lname . '</option>';
		  ?>
		  </select>
		</div>
		<div class="widget-toolbar no-border">
		<? if($allow_search) { ?>
			<button class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown" id="sms-filter-dept-btn" data-id="0">
				<i class="ace-icon fa fa-filter"></i> Department: All
				<i class="ace-icon fa fa-chevron-down icon-on-right"></i>
                        </button>
                                <ul class="dropdown-menu dropdown-menu-right scrollable-menu" role="menu" id="sms-filter-dept">
				<?php
					foreach($depts as $dept)
						echo '<li><a href="#" data-id="' . $dept->dept_no . '">' . $dept->dept_name . '</a></li>';
					
				?>
				<li class="divider"></li>
				<li class="active"><a href="#">All</a></li>
			</ul>
		<? } else { ?>
		  <button class="btn btn-xs btn-success" id="sms-filter-dept-btn" data-id="<?=$emp_dept?>">
			<? foreach($depts as $dept) { ?>
			   <? if($dept->dept_no == $emp_dept) { ?>
			   Department: <?=$dept->dept_name?>
			   <? } ?>
			<? } ?>
		   </button>
		<? } ?>
		</div>
	</div>
	<div class="widget-body">
	  <div class="widget-main no-padding">
        <div class="">
		  <table id="sms-table" class="table table-striped table-bordered table-hover" width="100%">
			<thead>
			  <tr>
				<th class="center">
				  <label class="position-relative">
					<input type="checkbox" class="ace" />
					<span class="lbl"></span>
				  </label>
				</th>
				<th>Employee ID</th>
				<th>Employee Name</th>
                                <th>Department</th>
				<th>Time</th>
				<th>Phone Number</th>
				<th>Code</th>
				<th>Status</th>
			  </tr>
			</thead>
		  </table>
	    </div>
	  </div>
	</div>
  </div>
</div>