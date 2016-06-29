<div class="widget-box widget-color-blue">
    <div class="widget-header">
        <h5 class="widget-title smaller">HRMIS</h5>
        <div class="widget-toolbar no-border">
            <button class="btn btn-xs btn-primary" id="tk-search-btn">
                <i class="ace-icon fa fa-search"></i> Search
            </button>
        </div>

        <div class="widget-toolbar no-border" id="mode_switch">
            <label class="small">Scoring Mode
                <input id="emp-display-expat" class="ace ace-switch ace-switch-4" type="checkbox" />
                <span class="lbl middle"></span>
            </label>
        </div>

        <div class="widget-toolbar no-border" id="inclusive_year">

            <label class="small">View Year
                <input class="form-control date-picker" id="emp-display-inclusive-year" type="text" data-date-format="yyyy" style="width: 50px; height: 30px; text-align: center; margin: 0 5px" readonly />
            </label>
        </div>
        
<!--         Development on process.. -->

        <div class="widget-toolbar s-border">
            <button class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" id="emp-filter-nation-btn" data-id="0">
                <i class="ace-icon fa fa-filter"></i> Nationality: All
                <i class="ace-icon fa fa-chevron-down icon-on-right"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-right" role="menu" id="emp-filter-nation">
                <li><a href="#" data-id="local">Local</a></li>
                <li><a href="#" data-id="expat">Expat</a></li>
                <li class="divider"></li>
                <li class="active"><a href="#">All</a></li>
            </ul>
        </div>

        <div class="widget-toolbar no-border">
            <button class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" id="emp-filter-status-btn" data-id="0">
                <i class="ace-icon fa fa-filter"></i> Status: All
                <i class="ace-icon fa fa-chevron-down icon-on-right"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-right" role="menu" id="emp-filter-status">
                <li><a href="#" data-id="1">Probationary</a></li>
                <li><a href="#" data-id="2">Regular</a></li>
                <li class="divider"></li>
                <li class="active"><a href="#" data-id="0">All</a></li>
            </ul>
        </div>

        <div class="widget-toolbar no-border">
            <button class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" id="tk-filter-dept-btn" data-id="0">
                <i class="ace-icon fa fa-filter"></i> Department: All
                <i class="ace-icon fa fa-chevron-down icon-on-right"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-right" role="menu" id="tk-filter-dept">
                <?php
                foreach ($depts as $dept) {
                    echo '<li><a href="#" data-id="' . $dept->dept_no . '">' . $dept->dept_name . '</a></li>';
                }
                ?>
                <li class="divider"></li>
                <li class="active"><a href="#">All</a></li>
            </ul>
        </div>
    </div>

    <div class="widget-body">
        <div class="widget-main no-padding">
            <div class="row form-inline hrmis-table-header">
                <div class="col-sm-6">
                    <div class="dataTables_length">
                        <label>
                            Show <select class="form-control input-sm" aria-controls="emp-table" id="hrmis-table_length">
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select> entries
                        </label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <span class="label label-lg label-primary arrowed-in-right arrowed-in pull-right">Maximum HRMIS Score : <b id="base_score"></b></span>
                </div>
            </div>
            <div style="overflow: auto; height: 400px; position: relative; background-color: rgb(216, 216, 216);">

                <div id="tk-table-loader" class="hidden" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center; padding: 20px;">
                    <i class="ace-icon fa fa-refresh fa-spin"></i> Loading...
                </div>

                <div id="tk-table-no-record" class="hidden" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-warning"></i> No Record Found</div>

                <div id="scoring-table">                
                </div>

                <table id="tk-hrmis-table" class="table table-striped table-bordered table-hover" width="100%"></table>
            </div>

            <div class="row">
                <div class="col-xs-6">
                    <div id="hrmis-scoring-table-info" class="dataTables_info" role="status" aria-live="polite">

                    </div>
                </div>
                <div class="col-xs-6 dataTables_paginate">
                    <div id="tk-hrmis-pager-bot" style="padding: 15px;" class="hidden"></div>
                    <div id="tk-scoring-pager-bot" style="padding: 15px; align: right;" class="hidden"></div>
                </div>
            </div>
        </div>

        <div class="widget-toolbar no-border">
            <form id="exportForm" action="<?=base_url("employees/exportHRMISKpi")?>" method="POST">
                <input type="hidden" name="export-dept" value=""/>
                <input type="hidden" name="export-emp" value=""/>
                <input type="hidden" name="export-from" value=""/>
                <input type="hidden" name="export-to" value=""/>
                <input type="hidden" name="export-type" value=""/>
                <!-- <button class="btn btn-xs btn-primary">Export</button> -->
            </form>
        </div>
    </div>
</div>
