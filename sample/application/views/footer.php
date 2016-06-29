					<!-- PAGE CONTENT ENDS -->
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.page-content-area -->
	</div><!-- /.page-content -->
</div><!-- /.main-content -->

<div class="footer">
	<div class="footer-inner">
		<div class="footer-content">
			<span class="bigger-120">
				<a href="<?=base_url()?>"><span class="blue bolder">HRIS</span></a><?=($rev ? " <small class=\"text-muted\">v1.$rev</small>" : '')?>
				Programmer Team &copy; 2014-<?=date('Y')?> <a class="bolder" href="http://pspbpo.com/" target="_blank">PSP</a>
			</span>
		</div>
	</div>
</div>

<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
	<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
</a>
</div><!-- /.main-container -->

<div id="violations-quick-add" class="modal fade" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Quick add</h4>
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
						<div class="alert alert-warning hidden">
							<button type="button" class="close" data-dismiss="alert">
								<i class="ace-icon fa fa-times"></i>
							</button>

							<strong>
								<i class="ace-icon fa fa-exclamation-triangle"></i>
								Warning!
							</strong>

							Record already exists.
							<br />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-6">
						<div class="form-group">
							<span id="loading-employees">Loading&hellip;</span>
							<select id="vio-employees-select" class="chosen-select" data-placeholder="Employee ID - Name" style="width: 92%;"></select>
						</div>
						
						<div class="space-4"></div>
						
						<div class="form-group">
							<label for="vio-id">Violation</label>
							<select class="form-control" id="vio-id"></select>
						</div>

						<div class="space-4"></div>

						<div class="form-group">
							<label for="vio-doc">Date of commission</label>
							<div class="input-group">
								<input type="text" class="form-control" id="vio-doc" data-date-format="MM-DD-YYYY HH:mm" />
								<span class="input-group-addon">
									<i class="fa fa-clock-o bigger-110"></i>
								</span>
							</div>
						</div>
						
						<div class="space-4"></div>
						
						<div class="form-group">
							<label for="vio-remarks">Remarks</label>
							<input class="form-control" type="text" id="vio-remarks" placeholder="Optional" />
						</div>
					</div>
					
					<div class="col-xs-6">
						
						<div class="info">
							Select an Employee ID on the left to display information here.
						</div>
						
						
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button class="btn btn-sm" data-dismiss="modal">
					<i class="ace-icon fa fa-times"></i>
					Cancel
				</button>

				<button class="btn btn-sm btn-primary" disabled="disabled" data-emploaded="false" data-violoaded="false">
					<i class="ace-icon fa fa-check"></i>
					Save
				</button>
			</div>
		</div>
	</div>
</div>

<div id="cite-quick-add" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Quick add</h4>
			</div>

			<div class="modal-body">
				<div class="row">
					<div class="col-xs-5">
						<span id="loading-employees">Loading&hellip;</span>
						<select id="employees-select" class="chosen-select" data-placeholder="Employee ID" style="width: 200px;"></select>
					</div>

					<div class="col-xs-7">
						<div class="form-group">
							<label for="cite-code">Cite code</label>
							<input class="form-control" type="text" id="cite-code" />
						</div>

						<div class="space-4"></div>

						<div class="form-group">
							<div class="row">
								<div class="col-xs-6">
									<label for="cite-doc">Date of commission</label>
									<input class="form-control" type="text" id="cite-doc" placeholder="mm-dd-yy" />
								</div>
								<div class="col-xs-6">
									<label for="cite-nte">Date of N.T.E.</label>
									<input class="form-control" type="text" id="cite-nte" placeholder="mm-dd-yy" />
								</div>
							</div>
						</div>
						
						<div class="space-4"></div>
						
						<div class="form-group">
							<label for="cite-remarks">Remarks</label>
							<input class="form-control" type="text" id="cite-remarks" placeholder="Optional" />
						</div>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button class="btn btn-sm" data-dismiss="modal">
					<i class="ace-icon fa fa-times"></i>
					Cancel
				</button>

				<button class="btn btn-sm btn-primary" disabled="disabled" data-emploaded="false">
					<i class="ace-icon fa fa-check"></i>
					Save
				</button>
			</div>
		</div>
	</div>
</div>

<div id="notifs-modal" class="modal fade" tabindex="-1" data-page="1" data-count="0">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Notifications</h4>
			</div>

			<div class="modal-body">
				<!-- <div class="list-group">
					<a class="list-group-item" href="#"><i class="fa fa-home fa-fw"></i>&nbsp; Home</a>
					<a class="list-group-item" href="#"><i class="fa fa-book fa-fw"></i>&nbsp; Library</a>
					<a class="list-group-item" href="#"><i class="fa fa-pencil fa-fw"></i>&nbsp; Applications</a>
					<a class="list-group-item" href="#"><i class="fa fa-cog fa-fw"></i>&nbsp; Settings</a>
				</div> -->
				
				<div class="alert alert-info">
					<i class="ace-icon fa fa-info-circle"></i>
					Nothing to display
				</div>

				<nav>
					<ul class="pager">
						<li class="previous disabled"><a href="#"><span aria-hidden="true">&larr;</span> Older</a></li>
						<li class="next disabled"><a href="#">Newer <span aria-hidden="true">&rarr;</span></a></li>
					</ul>
				</nav>
			</div>

			<div class="modal-footer">
				<button class="btn btn-sm btn-primary" id="mark-all-read" disabled="disabled">
					<i class="ace-icon fa fa-eye"></i>
					Mark all as read
				</button>
			</div>
		</div>
	</div>
</div>

<?php if(count($subordinates)): ?>
<div id="subords-modal" class="modal fade" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Department KPI</h4>
			</div>

			<div class="modal-body">
				<div id="kpi-success-alert" class="alert alert-block alert-success">
					<i class="ace-icon fa fa-check green"></i>
					Scores successfully updated!
				</div>
				<div class="widget-box widget-color-blue">
					<div class="widget-header">
						<h5 class="widget-title smaller">Scores for <?=$from_to?></h5>
					</div>
					<div class="widget-body">
						<div class="widget-main no-padding">
							<table id="dept-kpi-scores" class="table table-bordered table-hover">
								<thead>
									<tr>
										<th>Employee name</th>
										<th>Nickname</th>
										<th class="text-center">Score <small>(min = 0, max = 60)</small></th>
									</tr>
								</thead>
								<tbody>
									<?php
									
										$cal_info = cal_info(0);
										
										foreach($subordinates as $i => $subordinate)
											echo
												"<tr id={$subordinate->hr_users_id}>
													<td>{$subordinate->last_name}, {$subordinate->first_name}</td>
													<td>{$subordinate->nick_name}</td>
													<td class=\"text-center\"><input id={$subordinate->hr_users_id} type=\"text\" size=\"2\" class=\"score-input\" value=\"{$subordinate->score}\" placeholder=\"\" /></td>
												</tr>";
										
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				
			</div>

			<div class="modal-footer">
				<button class="btn btn-sm" data-dismiss="modal">
					<i class="ace-icon fa fa-times"></i>
					Cancel
				</button>

				<button id="saveDeptKpi" class="btn btn-sm btn-primary">
					<i class="ace-icon fa fa-check"></i>
					Save
				</button>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<!-- basic scripts -->

<script src="<?=base_url()?>assets/js/json2.min.js"></script>

<!--[if !IE]> -->
<script src="<?=base_url()?>assets/js/jquery.2.1.1.min.js"></script>
<!-- <![endif]-->

<!--[if IE]>
<script src="<?=base_url()?>assets/js/jquery.1.11.1.min.js"></script>
<![endif]-->

<!--[if IE]>
<script type="text/javascript">
	window.jQuery || document.write("<script src='<?=base_url()?>assets/js/jquery1x.min.js'>"+"<"+"/script>");
</script>
<![endif]-->
<script type="text/javascript">
	if('ontouchstart' in document.documentElement) document.write("<script src='<?=base_url()?>assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
</script>
<script src="<?=base_url()?>assets/js/bootstrap.min.js"></script>

<!-- <script src="<?=base_url()?>assets/js/date-time/bootstrap-timepicker.min.js"></script> -->

<!-- page specific plugin scripts -->

<!--[if lte IE 8]>
<script src="<?=base_url()?>assets/js/excanvas.min.js"></script>
<![endif]-->

<script type="text/javascript" src="<?=base_url()?>assets/js/jquery-ui.custom.min.js"></script>
<script type="text/javascript">
	$.fn._datepicker = $.fn.datepicker;
	
	var notif_icons = <?=$notif_icons?>;
</script>
<script src="<?=base_url()?>assets/js/handsontable.full.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/js/moment.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/js/date-time/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/js/date-time/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/js/date-time/daterangepicker.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/js/date-time/bootstrap-datetimepicker.min.js"></script>

<!-- ace scripts -->
<script src="<?=base_url()?>assets/js/ace-elements.min.js"></script>
<script src="<?=base_url()?>assets/js/ace.min.js"></script>

<!-- KPI -->
<script type="text/javascript">
	var user_id = <?=$this->session->userdata('mb_no')?>;
	var dept_id = <?=$this->session->userdata('mb_deptno')?>;
	var base_url = '<?=base_url()?>';
	var SOCKET_LOCAL = '<?=base_url()?>assets/js/socket.io-1.3.6.js';
	var _doc = moment('<?=$_doc?>', 'YYYY-MM-DD').unix();
	
	var base_dept_score = <?=$base_dept_score?>,
		base_hr_score = <?=$base_hr_score?>,
		base_hrmis_score = <?=$base_hrmis_score?>;
		
	var NOTICE_USER = '<?=strtolower($this->session->userdata('username'))?>',
		NOTICE_DEPT = '<?=$this->session->userdata('mb_deptno')?>',
		NOTICE_KEY = '<?=$this->encrypt->encode($this->session->userdata('username'))?>';
</script>
<script id="ws-loader" type="text/javascript" src="http://10.120.10.138/ws/loader.js?module=notifs"></script>
<script type="text/javascript" src="<?=base_url()?>assets/js/jquery.history.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/js/spin.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/js/jquery.spin.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/js/chosen.jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/js/highlight.js"></script>
<!-- <script type="text/javascript" src="<?=base_url()?>assets/js/blockies.min.js"></script>
<script src="<?=base_url()?>assets/js/date-time/bootstrap-datetimepicker.min.js"></script>
<!-- <script src="<?=base_url()?>assets/js/jquery.handsontable.full.min.js"></script> -->
<script type="text/javascript" src="<?=base_url()?>assets/js/jquery.ba-throttle-debounce.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/js/jstorage.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/js/jquery.jgrowl.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/js/fuelux/fuelux.spinner.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/js/common.js?v=<?=$rev?>"></script>

<div id="dynamic-files">
	<?php foreach($css as $style): ?>
	<link type="text/css" rel="stylesheet" href="<?=base_url()?>assets/css/<?=$style?>" />
	<?php endforeach; ?>

	<?php foreach($js as $script): ?>
	<script type="text/javascript" src="<?=base_url()?>assets/js/<?=$script?>"></script>
	<?php endforeach; ?>
</div>

<!-- inline scripts related to this page -->
<script type="text/javascript">
	jQuery(function($) {
		var $sidebar = $('.sidebar').eq(0);
		if( !$sidebar.hasClass('h-sidebar') ) return;

		$(document).on('settings.ace.top_menu' , function(ev, event_name, fixed) {
			if( event_name !== 'sidebar_fixed' ) return;

			var sidebar = $sidebar.get(0);
			var $window = $(window);

			//return if sidebar is not fixed or in mobile view mode
			if( !fixed || ( ace.helper.mobile_view() || ace.helper.collapsible() ) ) {
				$sidebar.removeClass('hide-before');
				//restore original, default marginTop
				ace.helper.removeStyle(sidebar , 'margin-top')

				$window.off('scroll.ace.top_menu')
				return;
			}


			var done = false;
			$window.on('scroll.ace.top_menu', function(e) {

				var scroll = $window.scrollTop();
				scroll = parseInt(scroll / 4);//move the menu up 1px for every 4px of document scrolling
				if (scroll > 17) scroll = 17;


				if (scroll > 16) {
					if(!done) {
						$sidebar.addClass('hide-before');
						done = true;
					}
				}
				else {
					if(done) {
						$sidebar.removeClass('hide-before');
						done = false;
					}
				}

				sidebar.style['marginTop'] = (17-scroll)+'px';
			}).triggerHandler('scroll.ace.top_menu');

		}).triggerHandler('settings.ace.top_menu', ['sidebar_fixed' , $sidebar.hasClass('sidebar-fixed')]);

		$(window).on('resize.ace.top_menu', function() {
			$(document).triggerHandler('settings.ace.top_menu', ['sidebar_fixed' , $sidebar.hasClass('sidebar-fixed')]);
		});


	});
</script>
</body>
</html>
