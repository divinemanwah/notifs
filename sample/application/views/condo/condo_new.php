<?
$Condo_expat = $Condo_result[1];
?>

<div class="row">
    <div class="col-xs-12">
        <!-- PAGE CONTENT BEGINS -->
        
        <form id="validate_form" class="form-horizontal" role="form">
            
            <div style="height:10px">&nbsp;</div>
            
            <div class="form-group">
                <label class="col-sm-2 control-label no-padding-right" for="txt_condo_name"> * Condo Name &nbsp;</label>
                
                <div class="col-sm-7">
                    <input class="form-control" type="text" id="txt_condo_name">
                </div>
            </div>
            
            <div style="height:10px">&nbsp;</div>
            
            <div class="form-group">
                <label class="col-sm-2 control-label no-padding-right" for="txt_condo_address"> * Address &nbsp;</label>
                
                <div class="col-sm-9">
                    <input class="form-control" type="text" id="txt_condo_address">
                </div>
            </div>
            
            <div style="height:10px">&nbsp;</div>
            
            <div class="form-group">
                <label class="col-sm-2 control-label no-padding-right" for="sel_status"> Status &nbsp;</label>

                <div class="col-sm-3">
                    <select class="form-control" id="sel_status" data-placeholder="Choose a Status...">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            
            
            <div style="height:10px">&nbsp;</div>
            <div style="height:10px">&nbsp;</div>
            
            <div class="form-group">
                <div class="col-sm-1">&nbsp;</div>
                <div class="col-sm-10">
                    <div class="table-header">Expat List</div>
                    <div style="overflow:auto;">
                        <table id="tbl_modal_list" class="table table-striped table-bordered table-hover" style="table-layout: fixed;">
                            <colgroup>
                                <col width="12px"/>
                                <col width="150px"/>
                            </colgroup>
                            <thead>
                                <tr>
                                    <th nowrap style="text-align:center; vertical-align:middle"><span id="btn_add_list" class="glyphicon glyphicon-plus-sign" style="cursor:pointer; font-size:16px"></span></th>
                                    <th nowrap style="text-align:center;">Name</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?
                                echo    '<tr mb_no="0">';
                                echo    '   <td style="text-align:center; vertical-align:middle"><span id="btn_minus_list" class="glyphicon glyphicon-minus-sign" style="cursor:pointer; font-size:16px"></span></td>';
                                echo    '   <td class="form-group">
                                                <div class="col-sm-12">
                                                    <select class="form-control" id="sel_expat" data-placeholder="...">
                                                        <option value="0">&nbsp;</option>';
                                                        ForEach ($Expat_result as $expat) {
                                echo    '                   <option value="'.$expat->mb_no.'">'.$expat->mb_nick. ' ('.$expat->mb_name.')</option>';
                                                        }

                                echo    '           </select>
                                                </div>
                                            </td>
                                        </tr>';
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            
            <div id="hiddenNote" class="form-group">
                <label class="col-sm-12 control-label" style="color: #A94442"></label>
            </div>
            
        </form>
        
        <!-- PAGE CONTENT ENDS -->
    </div><!-- /.col -->
</div><!-- /.row -->