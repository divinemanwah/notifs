<div class="col-md-7">
    <div class="widget-box widget-color-green2">
        <div class="widget-header">
            <h5 class="widget-title "><span class="color-gray fa fa-users"></span> Breaktime List</h5>
            <div class="widget-toolbar no-border">
                <button class="btn btn-xs btn-success hidden" id="multi-brk">
                    Break &Gt;
                </button>
            </div>
        </div>
        <div class="widget-body">
            <div class="widget-main no-padding" >

                <div style="overflow: auto; min-height:400px; height: auto;  position: relative; background-color: rgb(216, 216, 216);">
                    <table id="brk-list-table" class="table table-striped table-bordered table-hover" width="100%">
                        <thead>
                            <tr>
                                <th>
                                </th>
                                <th class="center">
                                    <label class="position-relative">
                                        <input type="checkbox" class="ace" id="main-check" />
                                        <span class="lbl"></span>
                                    </label>
                                </th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Last Out</th>
                                <th>Last In</th>
                                <th>Minutes</th>
                                <th>Break</th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                    <div id="brk-table-loader" class="hidden" style="height: 400px; width: 20%; position: relative; padding-top: 20%; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-refresh fa-spin"></i> Loading...</div>
                    <div id="brk-table-no-record" class="hidden" style="height: 400px; width: 20%; position: relative; padding-top: 20%; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-warning"></i> No Record Found</div>
                </div>
            </div>
        </div>

    </div>
</div>

<!--<div class="col-md-1"></div>-->

<div class="col-md-5">
    <div class="widget-box widget-color-green2">
        <div class="widget-header">
            <h5 class="widget-title "><span class="color-gray fa fa-clock-o"></span> On Break</h5>
            <div class="widget-toolbar no-border">
                <button class="btn btn-xs btn-warning hidden" id="multi-brk-in">
                    IN &Lt;
                </button>
            </div>
        </div>

        <div class="widget-body">
            <div class="widget-main no-padding" >
                <div style="overflow: auto; height: auto; position: relative; background-color: rgb(216, 216, 216);">
                    <div id="onbrk-table-loader" class="hidden" style="height: 400px; width: 20%; position: relative; padding-top: 20%; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-refresh fa-spin"></i> Loading...</div>
                    <div id="onbrk-table-no-record" class="hidden" style="height: 400px; width: 20%; position: relative; padding-top: 20%; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-warning"></i> No Record Found</div>
                    <table id="brk-onbreak-table" class="table table-striped table-bordered table-hover" width="100%">
                        <thead>
                            <tr>
                                <th>
                                </th>
                                <th class="center">
                                    <label class="position-relative">
                                        <input type="checkbox" class="ace" id="on-main-check" />
                                        <span class="lbl"></span>
                                    </label>
                                </th>
                                <th>Name</th>
                                <th>OUT</th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
<!--- MODAL CONTENT -->

<div id="brk-modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-md ">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 id="modal-confirmation" class="blue bigger">Set as Break </h4>
            </div>

            <div class="modal-body hidden">
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
                        <div class="form-group">
                            <div class="col-sm-12">
                                <h4 class="green smaller lighter">Please Confirm?</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-danger modal-action-btn" data-dismiss="modal">
                    <i class="ace-icon fa fa-times"></i>
                    Cancel
                </button>
                <button class="btn btn-sm btn-primary modal-action-btn" id="brk-save">
                    <i class="ace-icon fa fa-save"></i>
                    Confirm
                </button>

            </div>
        </div>
    </div>
</div>
