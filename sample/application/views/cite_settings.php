<div class="row">
	<div class="col-sm-6 widget-container-col">
	 
		 <div class="widget-box widget-color-blue">
			<div class="widget-header">
				<h5 class="widget-title smaller">Offenses</h5>

				<div class="widget-toolbar no-border">
					<button class="btn btn-xs btn-primary off-pen-add-btn" data-toggle="modal" data-target="#off-pen-modal" data-backdrop="static" data-type="offense">
						<i class="ace-icon fa fa-plus"></i>
						Add
					</button>
				</div>
				<div class="widget-toolbar no-border">
					<label class="small">Show disabled
						<input id="off-display-disabled" class="ace ace-switch ace-switch-3" type="checkbox" />
						<span class="lbl middle"></span>
					</label>
				</div>
			</div>

			<div class="widget-body">
				<div class="widget-main no-padding">
					
					<table id="off-table" class="table table-striped table-bordered table-hover" style="width: 100%; table-layout: fixed;">
						<thead>
							<tr>
								<th class="center" width="10%">
									<label class="position-relative">
										<input type="checkbox" class="ace" />
										<span class="lbl"></span>
									</label>
								</th>
								<th width="50%">Description</th>
								<th width="20%">Status</th>
								<th width="20%"></th>
								<th></th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>

	</div>
	
	<div class="col-sm-6 widget-container-col">
	 
		 <div class="widget-box widget-color-blue">
			<div class="widget-header">
				<h5 class="widget-title smaller">Penalties</h5>

				<div class="widget-toolbar no-border">
					<button class="btn btn-xs btn-primary off-pen-add-btn" data-toggle="modal" data-target="#off-pen-modal" data-backdrop="static" data-type="penalty">
						<i class="ace-icon fa fa-plus"></i>
						Add
					</button>
				</div>
				<div class="widget-toolbar no-border">
					<label class="small">Show disabled
						<input id="pen-display-disabled" class="ace ace-switch ace-switch-3" type="checkbox" />
						<span class="lbl middle"></span>
					</label>
				</div>
			</div>

			<div class="widget-body">
				<div class="widget-main no-padding">
					<table id="pen-table" class="table table-striped table-bordered table-hover" style="width: 100%; table-layout: fixed;">
						<thead>
							<tr>
								<th class="center" width="10%">
									<label class="position-relative">
										<input type="checkbox" class="ace" />
										<span class="lbl"></span>
									</label>
								</th>
								<th width="50%">Description</th>
								<th width="20%">Status</th>
								<th width="20%"></th>
								<th></th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>

	</div>
</div>

<div id="off-pen-modal" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger"></h4>
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

							Something went wrong while saving your data.
							<br />
						</div>
					</div>
				</div>
				
				<form class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label no-padding-right" for="off-pen-description">Description</label>

						<div class="col-sm-10">
							<input class="form-control add" type="text" />
							<div class="input-group edit hidden">
								<input class="form-control" type="text" />
								<div class="input-group-btn">
									<button data-toggle="dropdown" class="btn btn-success btn-sm dropdown-toggle">
										Enabled
										<span class="ace-icon fa fa-caret-down icon-on-right"></span>
									</button>

									<ul class="dropdown-menu">
										<li>
											<a href="#" data-color="success">Enabled</a>
										</li>
										<li>
											<a href="#" data-color="warning">Disabled</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
						
						
					</div>
					<!--<div class="form-group off-type">
						<label class="col-sm-2 control-label no-padding-right" for="off-pen-type">Type</label>

						<div class="col-sm-10">
							<div class="input-group">
								<div class="input-group-btn">
									<button data-toggle="dropdown" class="btn btn-default btn-sm dropdown-toggle" id="off-pen-type">
										Minor
										<span class="ace-icon fa fa-caret-down icon-on-right"></span>
									</button>

									<ul class="dropdown-menu">
										<li>
											<a href="#" class="off-type-select" data-id="1">Minor</a>
										</li>
										<li>
											<a href="#" class="off-type-select" data-id="2">Major</a>
										</li>
										<li>
											<a href="#" class="off-type-select" data-id="3">Zero tolerance</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div> -->
				</form>
				
			</div>

			<div class="modal-footer">
				<button class="btn btn-sm" data-dismiss="modal">
					<i class="ace-icon fa fa-times"></i>
					Cancel
				</button>
				<button class="btn btn-sm btn-primary" id="off-pen-save">
					<i class="ace-icon fa fa-check"></i>
					Save
				</button>
			</div>
		</div>
	</div>
</div>