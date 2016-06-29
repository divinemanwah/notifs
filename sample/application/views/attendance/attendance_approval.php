
    <div class="col-md-12">
        <div class="widget-box widget-color-green2 change-att-list-widget">
            <div class="widget-header">
                <h4 class="widget-title">Change of Attendance for Approval</h4>
                <div class="widget-toolbar no-border">
                    <button class="btn btn-xs btn-success" id="tk-att-search-btn">
                        <i class="ace-icon fa fa-search"></i> Search
                    </button>
                </div>
                <div class="widget-toolbar no-border">
                    <button class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown" id="tk-att-filter-status-btn" data-id="1">
                        <i class="ace-icon fa fa-filter"></i> Status: Submitted
                        <i class="ace-icon fa fa-chevron-down icon-on-right"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu" id="tk-att-filter-status">
                        <li class="active"><a href="#" data-id="1">Submitted</a></li>
                        <li><a href="#" data-id="2">Approved</a></li>
                        <li><a href="#" data-id="0">Rejected</a></li>
                        <li class="divider"></li>
                        <li><a href="#" data-id="">All</a></li>
                    </ul>
                </div>
            </div>
            <div class="widget-body"> </div>
            <div class="widget-main no-padding">
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
                            <span id="change-att-list-success-msg"></span>
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
                            <span id="change-att-list-err-msg"></span>
                            <br />
                        </div>
                    </div>
                </div>
                <div class="">
                    <table id="change-att-table" class="table table-striped table-bordered table-hover" width="100%">
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
                                <th>Date</th>
                                <th>Shift</th>
                                <th>Actual In</th>
                                <th>Actual Out</th>
                                <th>New In</th>
                                <th>New Out</th>
                                <th>Submitted By</th>
                                <th>Approved By</th>
                                <th class="hidden-480">Status</th>
                                <th class="hidden-480"></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div id="change-att-approval-modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="blue bigger">Change of Attendance for Approval</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="well">
                                <h4 class="red smaller lighter">Are you sure you want to <label id='change-modal-action'>&nbsp;</label> <strong id="change-modal-target-lbl">&nbsp;</strong>?</h4>
                                <p id="remarks_lbl">Remarks: (Optional)</p>
                                <textarea id="change-remarks" name="change-remarks" style="height: 112px; resize: none;" class="form-control limited" maxlength="250" ></textarea>
                                <br/>
                                <button class="btn btn-sm btn-success change-modal-action-btn">Yes</button>
                                <button class="btn btn-sm btn-danger" data-dismiss="modal">No</button>
                            </div>
                            <strong>Note:</strong> This cannot be undone
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="req-modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="blue bigger">Change Attendance</h4>
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
                                <br />
                                <span class="success-msg"></span>
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
                    <div class="row">
                        <div class="col-sm-12">
                            <form class="form-horizontal" role="form" id="add-att">
                                <input type="hidden" name="att_id" id="att_id" />
                                <h3 class="header smaller no-margin-top"><small><span id="att_name">Name</span></small></h3>
                                <div class="row">
                                    <input type="hidden" id="add-file" value="" name="add-file">
                                    <div class="col-sm-6 col-md-12">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label class="col-sm-2 control-label no-padding-right" for="add-actual-in">Actual In</label>
                                                <div class="col-sm-4">
                                                    <input class="form-control text-center" type="text" id="add-actual-in" name="add-actual-in" readonly/>
                                                </div>



                                                <label class="col-sm-2 control-label no-padding-right" for="add-actual-out">Actual Out</label>
                                                <div class="col-sm-4">
                                                    <input class="form-control text-center" type="text" id="add-actual-out" name="add-actual-out" readonly/>
                                                </div>
                                            </div>
                                            <br><br>
                                            <div class="col-sm-12">
                                                <label class="col-sm-2 control-label no-padding-right" for="add-new-in">New In</label>
                                                <div class="col-sm-4">
                                                    <input class="form-control text-center" type="text" id="add-new-in" name="add-new-in" />
                                                </div>



                                                <label class="col-sm-2 control-label no-padding-right" for="add-new-out">New Out</label>
                                                <div class="col-sm-4">
                                                    <input class="form-control text-center" type="text" id="add-new-out" name="add-new-out" />
                                                </div>
                                            </div>

                                            <br><br>
                                            <div class="col-sm-12">    
                                                <label class="col-sm-2 control-label no-padding-right" for="add-remarks">Remarks</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" id="add-remarks" name="add-remarks" style="height: 112px; resize: none;">
                                                    </textarea>
                                                </div>
                                            </div>    
                                            <br><br>
                                            <br><br>
                                            <div class="col-sm-12">

                                                <br>
                                                <ul class="media-list" id="imgtbl"></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-sm btn-success modal-action-btn" id="att-approve">
                        <i class="ace-icon fa fa-check"></i>
                        Approve
                    </button>
                    <button class="btn btn-sm btn-danger modal-action-btn" id="att-reject">
                        <i class="ace-icon fa fa-times"></i>
                        Reject
                    </button>
                    <button class="btn btn-sm btn-cancel modal-action-btn" data-dismiss="modal">
                        <i class="ace-icon fa fa-share-square-o"></i>
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>


