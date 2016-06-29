<style>
.widget-box[class*=widget-color-]>.widget-header div.chosen-drop {
  color: #000000 !important;
}
</style>
<div class="widget-box widget-color-green2">
	<div class="widget-header">
		<h5 class="widget-title smaller">Use the filters for specific records</h5>
		<div class="widget-toolbar no-border">
		    <button class="btn btn-xs btn-success" id="grp-search-btn">
				<i class="ace-icon fa fa-search"></i> Search
			</button>
		</div>
		<div class="widget-toolbar no-border">
			<div class="input-daterange input-group" style="margin: 4px 0 0 0;">
				<input type="text" class="input-sm form-control" id="date-from" name="start" style="background-color: #D1FFCA; width: 90px;" value="<?=$cur_period->start?>">
				<span class="input-group-addon btn-success">
					<i class="fa fa-exchange"></i>
				</span>
				<input type="text" class="input-sm form-control" id="date-to" name="end" style="background-color: #D1FFCA; width: 90px;" value="<?=$cur_period->end?>">
			</div>
		</div>
		<div class="widget-toolbar no-border">
		  <select id="grp-filter-emp" class="chosen-select" data-placeholder="Specific Employee">
		    <option value=""></option>
		  <?php
			foreach($emp_list as $emp)
				echo '<option value="' . $emp->mb_no . '" >' . ($emp->mb_3=="Expat"?$emp->mb_nick:$emp->mb_fname)." ".$emp->mb_lname . '</option>';
		  ?>
		  </select>
		</div>
		<div class="widget-toolbar no-border">
		<? if($allow_search) { ?>
			<button class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown" id="grp-filter-dept-btn" data-id="0">
				<i class="ace-icon fa fa-filter"></i> Department: All
				<i class="ace-icon fa fa-chevron-down icon-on-right"></i>
                        </button>
                                <ul class="dropdown-menu dropdown-menu-right scrollable-menu" role="menu" id="grp-filter-dept">
				<?php
					
					foreach($depts as $dept)
						echo '<li><a href="#" data-id="' . $dept->dept_no . '">' . $dept->dept_name . '</a></li>';
					
				?>
				<li class="divider"></li>
				<li class="active"><a href="#">All</a></li>
			</ul>
			
		<? } else { ?>
		  <button class="btn btn-xs btn-success" id="grp-filter-dept-btn" data-id="<?=$emp_dept?>">
			<? foreach($depts as $dept) { ?>
			   <? if($dept->dept_no == $emp_dept) { ?>
			   Department: <?=$dept->dept_name?>
			   <? } ?>
			<? } ?>
		   </button>
		<? } ?>
		</div>
		<? //if(in_array($this->session->userdata("mb_deptno"),array(23))) { ?>
		<div class="widget-toolbar no-border">
		  <select id="grp-filter-group" class="chosen-select form-control" data-placeholder="Specific Group" multiple>
		    <option value=""></option>
		  <?php
			foreach($groups as $group)
				echo '<option value="' . $group->id . '" >' . $group->group_name . '</option>';
		  ?>
		  </select>
		</div>
		<? //} else { ?>
                <!--
		   <select id="grp-filter-group" class="hidden" data-placeholder="Specific Group">
		    <option value=""></option>
		  </select>
                -->
	    <? //} ?>
		<div class="widget-toolbar no-border">
		  <select id="grp-filter-shift" class="chosen-select" data-placeholder="Specific Shift">
		    <option value=""></option>
		  <?php
			foreach($shifts as $id => $shift)
				echo '<option value="' . $id . '" >' . $shift->scode . ' - ' . $shift->stime . '</option>';
		  ?>
			<option value="null">RD</option>
		  </select>
		</div>
	</div>


	<div class="widget-body">
		<div class="widget-main no-padding" >
		    <div style="overflow: auto; height: 400px; position: relative; background-color: rgb(216, 216, 216);">
			  <div id="grp-table-loader" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-refresh fa-spin"></i> Loading...</div>
			  <table id="grp-schedules-table" class="table table-striped table-bordered table-hover" width="100%"></table>
			</div>
			<div id="grp-schedules-pager"></div>
			<div class="widget-toolbar no-border">
			<form id="exportForm" action="<?=base_url("groups/schedules_export_detail")?>" method="POST">
			  <input type="hidden" name="export-dept" value=""/>
			  <input type="hidden" name="export-from" value=""/>
			  <input type="hidden" name="export-to" value=""/>
			  <input type="hidden" name="export-emp" value=""/>
			  <input type="hidden" name="export-group" value=""/>
			  <input type="hidden" name="export-shift" value=""/>
			  <button class="btn btn-xs btn-success">Export</button>
			</form>
			</div>
		</div>
	</div>
</div>