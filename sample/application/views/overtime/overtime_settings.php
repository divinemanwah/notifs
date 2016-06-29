<div class="col-xs-12 col-sm-3">
	<div class="widget-box widget-color-red">
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
			  <form id="gen-form" role="form" class="form-horizontal">
				<div class="form-group">
					<label class="col-sm-7 control-label no-padding-right" for="min_ot">Min overtime minutes </label>
					<div class="col-sm-4 pull-right">
					  <input type="text" name="min_ot" class="input-mini" value="<?=$data[0]->min_overtime_min?>"/>
					</div>
				</div>
				<br/>
				<div class="center">
					<button type="button" class="btn btn-sm btn-danger" id="gen-set-save">
						<i class="ace-icon fa fa-save bigger-110"></i>
						Save
					</button>
				</div>
			  </form>
			</div>
		</div>
	</div>
</div>
