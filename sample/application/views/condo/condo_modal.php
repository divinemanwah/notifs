
<div id="Content_Modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close close-modal" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="modalTitle"></h4>
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
                        <div class="alert alert-danger alert-error hidden">
                            
                            <strong>
                                    <i class="ace-icon fa fa-times"></i>
                                    Error!
                            </strong>
                            
                            Something went wrong while saving your data.
                            <br />
                        </div>
                        <div class="alert alert-danger alert-duplicate hidden">
                            
                            <strong>
                                    <i class="ace-icon fa fa-times"></i>
                                    Error!
                            </strong>

                            Condo name already exist.
                            <br />
                        </div>
                    </div>
                </div>

                <div class="widget-body">
                    <div class="widget-main">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="content" id="modal_info_container">

                                    <!-- details for Task Code Here... (New/Update/View) -->

                                </div>
                            </div>
                        </div>
                    </div>                
                </div>
            </div>

            <div class="modal-footer">
                <button id="btn_cancel" class="btn btn-sm" data-dismiss="modal">
                    <i class="ace-icon fa fa-times"></i>
                    Cancel
                </button>

                <button id="btn_save" class="btn btn-sm btn-primary">
                    <i class="ace-icon fa fa-check"></i>
                    Save
                </button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>