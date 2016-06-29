<style>
    .widget-box[class*=widget-color-]>.widget-header div.chosen-drop {
        color: #000000 !important;
    }
</style>
<div class="widget-box widget-color-green2">
    <div class="widget-header">
        <h5 class="widget-title smaller">Use the search box on the right to look for specific records</h5>
        <div class="widget-toolbar no-border">
            <button class="btn btn-xs btn-success" id="tk-search-btn">
                <i class="ace-icon fa fa-search"></i> Search
            </button>
        </div>
        <div class="widget-toolbar no-border">
            <div class="input-daterange input-group" style="margin: 4px 0 0 0;">
                <input type="text" class="input-sm form-control" id="date-from" name="start" style="background-color: #B9EABA; width: 90px;" value="<?= $cur_period->start ?>">
                <span class="input-group-addon btn-success">
                    <i class="fa fa-exchange"></i>
                </span>
                <input type="text" class="input-sm form-control" id="date-to" name="end" style="background-color: #B9EABA; width: 90px;" value="<?= $cur_period->end ?>">
            </div>
        </div>
        <div class="widget-toolbar no-border">
            <select id="tk-filter-emp" class="chosen-select" data-placeholder="Specific Employee">
                <option value=""></option>
                <?php
                foreach ($emp_list as $emp)
                    echo '<option value="' . $emp->mb_no . '" ' . ($emp_id == $emp->mb_no ? "selected='selected'" : "") . '>' . ($emp->mb_3 == "Expat" ? $emp->mb_nick : $emp->mb_fname) . " " . $emp->mb_lname . '</option>';
                ?>
            </select>
        </div>
        <? if ($allow_search) { ?>
            <div class="widget-toolbar no-border">
                <button class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown" id="tk-filter-le-btn" data-id="0">
                    <i class="ace-icon fa fa-filter"></i> Type: All
                    <i class="ace-icon fa fa-chevron-down icon-on-right"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-right" role="menu" id="tk-filter-le">
                    <li><a href="#" data-id="local">Local</a></li>
                    <li><a href="#" data-id="expat">Expat</a></li>
                    <li class="divider"></li>
                    <li class="active"><a href="#">All</a></li>
                </ul>
            </div>
        <? } ?>
        <div class="widget-toolbar no-border">
            <button class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown" id="tk-filter-dept-btn" data-id="0">
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
        <div class="widget-main no-padding" >
            <div style="overflow: auto; height: 400px; position: relative; background-color: rgb(216, 216, 216);">
                <div id="tk-table-loader" class="hidden" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-refresh fa-spin"></i> Loading...</div>
                <div id="tk-table-no-record" class="hidden" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-warning"></i> No Record Found</div>
                <table id="tk-attendance-table" class="table table-striped table-bordered table-hover" width="100%"></table>
            </div>
            <div id="tk-attendance-pager"></div>
            <div class="widget-toolbar no-border">
                <form id="exportForm" action="<?= base_url("attendance/export") ?>" method="POST">
                    <input type="hidden" name="export-dept" value=""/>
                    <input type="hidden" name="export-emp" value=""/>
                    <input type="hidden" name="export-from" value=""/>
                    <input type="hidden" name="export-to" value=""/>
                    <input type="hidden" name="export-type" value=""/>
                    <button class="btn btn-xs btn-success">Export</button>
                </form>
            </div>
        </div>
    </div>
</div>




<div id="add-att-modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-md ">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="blue bigger">Attendance Information</h4>
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
                            <input type="hidden" name="att_date" id="att_date" />
                            <input type="hidden" name="mb_id" id="mb_id" />
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
                                                <textarea class="form-control" id="add-remarks" name="add-remarks" style=" resize: none;">
                                                </textarea>
                                            </div>
                                        </div>    
                                        <br><br>
                                        <br><br>
                                        <div class="col-sm-12 hidden" id="reject-field">
                                            <label class="col-sm-2 control-label no-padding-right" for="reject-remarks">Reject Remarks</label>
                                            <div class="col-sm-10">
                                                <textarea class="form-control" id="reject-remarks" name="reject-remarks" style=" resize: none;">
                                                </textarea>
                                            </div>
                                        </div>
                                        <br><br>
                                        <div class="col-sm-12">

                                            <script type="text/template" id="qq-template-validation">
                                                <div class="qq-upload-button-selector qq-uploader-selector qq-uploader" qq-drop-area-text="Drop image here">
                                                <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
                                                <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
                                                </div>
                                                <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
                                                <span class="qq-upload-drop-area-text-selector"></span>
                                                </div>
                                                <span class="qq-drop-processing-selector qq-drop-processing">
                                                <span>Processing dropped files...</span>
                                                <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
                                                </span>
                                                <span class="glyphicon glyphicon-open-file"></span>
                                                <ul class="qq-upload-list-selector qq-upload-list" aria-live="polite" aria-relevant="additions removals">
                                                <li>
                                                <div class="qq-progress-bar-container-selector">
                                                <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
                                                </div>
                                                <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
                                                <img class="qq-thumbnail-selector" qq-max-size="100" qq-server-scale>
                                                <span class="qq-upload-file-selector qq-upload-file"></span>
                                                <button type="button" class="qq-btn qq-upload-cancel-selector qq-upload-cancel">Cancel</button>
                                                <button type="button" class="qq-btn qq-upload-retry-selector qq-upload-retry">Retry</button>
                                                <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">Delete</button>
                                                <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
                                                </li>
                                                </ul>

                                                <dialog class="qq-alert-dialog-selector">
                                                <div class="qq-dialog-message-selector"></div>
                                                <div class="qq-dialog-buttons">
                                                <button type="button" class="qq-cancel-button-selector">Close</button>
                                                </div>
                                                </dialog>

                                                <dialog class="qq-confirm-dialog-selector">
                                                <div class="qq-dialog-message-selector"></div>
                                                <div class="qq-dialog-buttons">
                                                <button type="button" class="qq-cancel-button-selector">No</button>
                                                <button type="button" class="qq-ok-button-selector">Yes</button>
                                                </div>
                                                </dialog>

                                                <dialog class="qq-prompt-dialog-selector">
                                                <div class="qq-dialog-message-selector"></div>
                                                <input type="text">
                                                <div class="qq-dialog-buttons">
                                                <button type="button" class="qq-cancel-button-selector">Cancel</button>
                                                <button type="button" class="qq-ok-button-selector">Ok</button>
                                                </div>
                                                </dialog>
                                                </div>
                                            </script>

                                            <div id="fine-uploader-validation"></div>
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
                <button class="btn btn-sm btn-danger modal-action-btn" data-dismiss="modal">
                    <i class="ace-icon fa fa-times"></i>
                    Cancel
                </button>
                <button class="btn btn-sm btn-primary modal-action-btn" id="add-att-save">
                    <i class="ace-icon fa fa-save"></i>
                    Confirm
                </button>
                <button class="btn btn-sm btn-primary modal-action-btn hidden" id="cancel-req">
                    <i class="ace-icon fa fa-save"></i>
                    Cancel Request
                </button>
            </div>
        </div>
    </div>
</div>