<div class="row">
    <div class="col-xs-12">
        <div class="widget-box widget-color-red">
            <div class="widget-header">
                <h5 class="widget-title smaller">Score Percentage Management</h5>

            </div>
            <div class="widget-body">
                <div class="widget-main ">
                    <!--                    <div class="form-horizontal" role="form">
                                            <div class="form-group">
                                                <label class="col-sm-7 control-label no-padding-right">Max HRMIS KPI Score</label>
                                                <div class="col-sm-4 pull-right" style="margin-right: 5px;">
                                                    <input id="max_hrmis" type="text" value="20" name="max_hrmis" />
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-7 control-label no-padding-right">Max Department KPI Score</label>
                                                <div class="col-sm-4 pull-right" style="margin-right: 5px;">
                                                    <input id="max_department" name="max_department"  value="60" type="text" />
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-7 control-label no-padding-right">Max HR Records KPI Score</label>
                                                <div class="col-sm-4 pull-right" style="margin-right: 5px;">
                                                    <input id="max_hr_records" type="text" value="20" name="max_hr_records" />
                                                </div>
                                            </div>
                                            
                                            <br />
                                            
                                            <div class="center">
                                                <button class="btn btn-sm btn-info" type="button">Save</button>
                                            </div>
                                            
                                        </div>-->
                    <table id="simple-table" class="table table-striped table-bordered table-hover">

                        <thead>
                            <tr>
                                <th><i class="ace-icon fa fa-cogs bigger-10"></i>KPI Type</th>
                                <th class="hidden-480"><i class="ace-icon fa fa-clock-o bigger-10"></i>Update</th>
                                <th style="width: 150px;" class="center"><i class="ace-icon fa fa-pencil-square-o bigger-10"></i>Percentage</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            foreach ($base_scores as $key=>$kpi) {

                                $row = '<tr>';
                                $row .= '<td style="vertical-align: middle"><i class="menu-icon fa ' . $obj_kpi[$key][0] . '"></i>&nbsp;' . $obj_kpi[$key][1] . ' KPI</td>';
                                $row .= '<td style="vertical-align: middle" class="hidden-480">' . $kpi->updated_ymd . '</td>';
                                $row .= '<td class="center"><input id="' . $obj_kpi[$key][2] . '" type="text" value="' . $kpi->score . '" name="max_hrmis" /></td>';
                                $row .= '</tr>';

                                echo $row;
                            }
                            ?>
                        </tbody>
                    </table>

                    <div class="center">
                        <button id="saveBtn" class="btn btn-sm btn-danger" type="button"><i class="ace-icon fa fa-floppy-o"></i> Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>