<div class="widget-box widget-color-blue">
	<div class="widget-header">
		<h5 class="widget-title smaller">Use the search box on the right to look for specific records</h5>
		<!-- <div class="widget-toolbar no-border">
			<button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#vio-import-modal">
				<i class="ace-icon fa fa-file-excel-o"></i>
				Import
			</button>
		</div> -->
		<div class="widget-toolbar no-border">
			<button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#violations-quick-add">
				<i class="ace-icon fa fa-plus"></i>
				Add
			</button>
		</div>
	</div>

	<div class="widget-body">
		<div class="widget-main no-padding">
			<table id="vio-rec-table" class="table table-striped table-bordered table-hover" style="width: 100%; table-layout: fixed;">
				<thead>
					<tr>
						<th class="center" width="5%">
							<label class="position-relative">
								<input type="checkbox" class="ace" />
								<span class="lbl"></span>
							</label>
						</th>
						<th width="8%">ID</th>
						<th width="13%">Last name</th>
						<th width="13%">First name</th>
						<th width="13%">Department</th>
						<th width="17%">Violation</th>
						<th width="9%"><span title="Date of commission">D.O.C.</span></th>
						<th width="13%">Remarks</th>
						<th width="9%"></th>
						<th></th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
</div>

<div id="vio-import-modal" class="modal fade" data-backdrop="static" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Import Violation Records</h4>
			</div>

			<div class="modal-body">
				<div class="row">
					<div class="form-group">
						<div class="col-xs-12">
							<input type="file" id="vio-import" name="vio-import" multiple="multiple" />
						</div>
					</div>
				</div>
				<div class="row">
					<form autocomplete="off">
						<div class="col-xs-12">
							<table class="table">
								<tr>
									<td style="border: none; vertical-align: middle; text-align: right;">
										<label>Generate Cite forms?
											<input id="vio-generate" class="ace ace-switch ace-switch-2" type="checkbox" />
											<span class="lbl middle"></span>
										</label>
									</td>
									<td style="border: none;">
										<button class="btn btn-light">
											<i class="ace-icon fa fa-download"></i>
											Download template
										</button>
									</td>
								</tr>
								<tr>
									<td style="border: none; vertical-align: middle; text-align: right;">
										<label>Overwrite old records?
											<input id="vio-overwrite" class="ace ace-switch ace-switch-2" type="checkbox" />
											<span class="lbl middle"></span>
										</label>
									</td>
									<td style="border: none;">
										<button class="btn btn-primary" id="vio-import-upload" disabled="disabled">
											<i class="ace-icon fa fa-upload"></i>
											Upload
										</button>
									</td>
								</tr>
							</table>
						</div>
					</form>
				</div>
			</div>

			<div class="modal-footer">
				<button class="btn btn-sm" data-dismiss="modal">
					<i class="ace-icon fa fa-times"></i>
					Cancel
				</button>
			</div>
		</div>
	</div>
</div>