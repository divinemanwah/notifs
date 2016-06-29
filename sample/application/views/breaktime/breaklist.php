<div class="col-md-11">
    <div class="widget-box widget-color-green2">
        <div class="widget-header">
            <h5 class="widget-title "><span class="color-gray fa fa-users"></span> Breaktime List</h5>
            <div class="widget-toolbar no-border">
                <button class="btn btn-xs btn-success" id="search-brklist">
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
                        echo '<option value="' . $emp->mb_no . '" ' . (( $emp_id == $emp->mb_no && count($emp_list) == 1 ) ? 'selected="selected"' : '') . ' >' . $emp->mb_nick . " " . $emp->mb_lname . '</option>';
                    ?>
                </select>
            </div>
            <div class="widget-toolbar no-border">
                <button class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown" id="brk-dept" data-id="<?= $emp_dept ?>">
                    <? if ($emp_dept == 0) { ?>
                        <i class="ace-icon fa fa-filter"></i> Department: All
                    <? } else { ?>
                        <? foreach ($depts as $dept) { ?>
                            <? if ($dept->dept_no == $emp_dept) { ?>
                                <i class="ace-icon fa fa-filter"></i> Department: <?= $dept->dept_name ?>
                            <? } ?>
                        <? } ?>
                    <? } ?>
                    <i class="ace-icon fa fa-chevron-down icon-on-right"></i>
                </button>
                <? if (count($depts) > 1) { ?>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu" id="brk-dept-btn">

                        <?php
                        if (count($depts) > 1) {
                            foreach ($depts as $dept) {
                                echo '<li ' . (($dept->dept_no == $emp_dept) ? 'class="active"' : '') . ' ><a href="#" data-id="' . $dept->dept_no . '">' . $dept->dept_name . '</a></li>';
                            }
                        }
                        ?>

                        <li class="divider"></li>
                        <li <? echo ($dept->dept_no == $emp_dept) ? 'class="active"' : ''; ?>class="active"><a href="#">All</a></li>

                    </ul>
                <? } ?>
            </div>
            <div class="widget-toolbar no-border">
                <label class="small">Over Break
                    <input id="display-overbrk" class="ace ace-switch ace-switch-1 btn-empty" type="checkbox">
                    <span class="lbl middle"></span>
                </label>
            </div>
            <div class="widget-toolbar no-border">
                <label class="small">On Break
                    <input id="display-onbrk" class="ace ace-switch ace-switch-1 btn-empty" type="checkbox">
                    <span class="lbl middle"></span>
                </label>
            </div>
        </div>
        <div class="widget-body">
            <div class="widget-main no-padding" >              
                <div style="overflow: auto; height: 400px; position: relative; background-color: rgb(216, 216, 216);">
                    <div id="brk-table-loader" class="hidden" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-refresh fa-spin"></i> Loading...</div>
                    <div id="brk-table-no-record" class="hidden" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-warning"></i> No Record Found</div>
                    <div id="brk-list" ></div>
                </div>
                <div id="brk-list-pager"><ul class="pagination"><li class="active"><a href="javascript:void(0)" data-page="1">1</a></li></ul></div>
            </div>
        </div>

    </div>
</div>