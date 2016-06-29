<div class="col-md-12">
    <div class="widget-box widget-color-green2">
        <div class="widget-header">
            <h5 class="widget-title ">Breaktime Records</h5>
            <div class="widget-toolbar no-border">
                <button class="btn btn-xs btn-success" id="search-logs">
                    Search
                </button>
            </div>
            <div class="widget-toolbar no-border">
                <div class="input-daterange input-group" style="margin: 4px 0 0 0;">
                    <input type="text" class="input-sm form-control" id="date-from" name="start" style="background-color: #D1FFCA; width: 90px;" value="<?= $cur_period->start ?>">
                    <span class="input-group-addon btn-success">
                        <i class="fa fa-exchange"></i>
                    </span>
                    <input type="text" class="input-sm form-control" id="date-to" name="end" style="background-color: #D1FFCA; width: 90px;" value="<?= $cur_period->end ?>">
                </div>
            </div>
            <div class="widget-toolbar no-border">
                <select id="brk-emp" class="chosen-select" data-placeholder="Specific Employee">
                    <option value=""></option>
                    <?php
                    // inedit ko to mack
                    foreach ($emp_list as $emp)
                        echo '<option value="' . $emp->mb_no . '" ' . (( $emp_id == $emp->mb_no) ? 'selected="selected"' : '') . ' >' . $emp->mb_nick . " " . $emp->mb_lname . '</option>';
                    ?>
                </select>
            </div>

        </div>
        <div class="widget-body">
            <div class="widget-main no-padding" >              
                <div style="overflow: auto; height: 400px; position: relative; background-color: rgb(216, 216, 216);">
                    <div id="brk-table-loader" class="hidden" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-refresh fa-spin"></i> Loading...</div>
                    <div id="brk-table-no-record" class="hidden" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-warning"></i> No Record Found</div>
                    <div id="brk-logs" ></div>
                </div>
                <div id="brk-logs-pager"><ul class="pagination"><li class="active"><a href="javascript:void(0)" data-page="1">1</a></li></ul></div>
            </div>
        </div>

    </div>
</div>