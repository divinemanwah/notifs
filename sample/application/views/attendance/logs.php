<style>
.widget-box[class*=widget-color-]>.widget-header div.chosen-drop {
  color: #000000 !important;
}
</style>
<div class="col-md-8">
<div class="widget-box widget-color-blue3">
	<div class="widget-header">
		<h5 class="widget-title smaller col-md-4">In Out Records</h5>
		<div class="widget-toolbar no-border">
		    <button class="btn btn-xs btn-purple" id="tk-search-btn">
				<i class="ace-icon fa fa-search"></i> Search
			</button>
		</div>
		<div class="widget-toolbar no-border">
			<div class="input-daterange input-group" style="margin: 4px 0 0 0;">
				<input type="text" class="input-sm form-control" id="date-from" name="start" style="background-color: #D7D4E8; width: 90px;" value="<?=$cur_period->start?>">
				<span class="input-group-addon btn-purple">
					<i class="fa fa-exchange"></i>
				</span>
				<input type="text" class="input-sm form-control" id="date-to" name="end" style="background-color: #D7D4E8; width: 90px;" value="<?=$cur_period->end?>">
			</div>
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
	</div>
	<div class="widget-body">
		<div class="widget-main no-padding" >
			<div style="overflow: auto; height: 400px; position: relative; background-color: rgb(216, 216, 216);">
			  <div id="tk-table-loader" class="hidden" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-refresh fa-spin"></i> Loading...</div>
			  <div id="tk-table-no-record" class="hidden" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-warning"></i> No Record Found</div>
			  <table id="tk-logs-table" class="table table-striped table-bordered table-hover" width="100%"></table>
			</div>
			<div id="tk-logs-pager"></div>
			<div class="widget-toolbar no-border">
			<form id="exportForm" action="<?=base_url("attendance/exportLogs")?>" method="POST">
			  <input type="hidden" name="export-emp" value=""/>
			  <input type="hidden" name="export-from" value=""/>
			  <input type="hidden" name="export-to" value=""/>
			  <button class="btn btn-xs btn-purple">Export</button>
			</form>
			</div>
		</div>
	</div>
</div>
</div>
<div class="col-md-4"></div>