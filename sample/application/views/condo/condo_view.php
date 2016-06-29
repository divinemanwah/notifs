<?
$Condo_Info     = $Condo_result[0];
$Condo_expat    = $Condo_result[1];

ForEach ($Condo_Info as $row) {
    $condo_id       = $row->condo_id;
    $condo_name     = stripslashes($row->condo_name);
    $condo_address  = stripslashes($row->condo_address);
    $status         = $row->status;
}
?>

<div class="row">
    <div class="col-xs-12">
        <!-- PAGE CONTENT BEGINS -->
        
        <form id="validate_form" class="form-horizontal" role="form">
            
            <div class="form-group">
                <label class="col-sm-2 control-label no-padding-right"> Condo Name &nbsp;</label>

                <label class="col-sm-9" style="font-size:15px; font-weight: bold"> <?= $condo_name; ?></label>
            </div>
            
            <div class="form-group">
                <label class="col-sm-2 control-label no-padding-right"> Address &nbsp;</label>

                <label class="col-sm-9" style="font-size:15px; font-weight: bold"> <?= $condo_address; ?></label>
            </div>
            
            <div class="form-group">
                <label class="col-sm-2 control-label no-padding-right"> Status &nbsp;</label>

                <label class="col-sm-9" style="font-size:15px; font-weight: bold"> <?= ($status == 1 ? "Active" : "Inactive"); ?></label>
            </div>
            
            <div style="height:10px">&nbsp;</div>
            <div class="form-group">
                <div class="col-sm-1">&nbsp;</div>
                <div class="col-sm-10">
                    <div class="table-header">Items</div>
                    <div style="overflow:auto;">
                        <table id="tbl_modal_list" class="table table-striped table-bordered table-hover" style="table-layout: fixed;">
                            <colgroup>
                            <col width="12px"/>
                            <col width="150px"/>
                            </colgroup>
                            <thead>
                                <tr>
                                    <th nowrap style="text-align:center; vertical-align:middle">#</th>
                                    <th nowrap style="text-align:center;">Name</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?
                                If (Count($Condo_expat) > 0) {
                                    $cnt = 0;
                                    ForEach ($Condo_expat as $row) {
                                        $cnt = $cnt + 1;
                                        echo    '<tr>';
                                        echo    '    <td style="text-align:center; vertical-align:middle">'.$cnt.'</td>';
                                        echo    '    <td style="text-align:center; vertical-align:middle;">'.$row->mb_nick. ' ('.$row->mb_name.')</td>';
                                        echo    '</tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            
        </form>
        
        <!-- PAGE CONTENT ENDS -->
    </div><!-- /.col -->
</div><!-- /.row -->