
<div style="overflow-x: auto; overflow-y: hidden;">
    <table id="tbl_list" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th width="25%">Condo name</th>
                <th width="45%">Address</th>
                <th width="15%">Status</th>
                <th width="15%"></th>
            </tr>
        </thead>

        <tbody> 
            <?
            If (Count($record_List) > 0) {
                ForEach ($record_List as $row) {

                    echo '  <tr id="id_' . $row->condo_id . '">';
                    echo '      <td style="font-weight:bold">' . stripslashes($row->condo_name) . '</td>';
                    echo '      <td>' . stripslashes($row->condo_address) . '</td>';
                    echo '      <td>' . ($row->status == 1 ? '<span class="label label-sm label-success">Active</span>' : '<span class="label label-sm label-warning">Inactive</span>') . '</td>';
                    echo '      <td>
                                    <div class="action-buttons">
                                        <a id="btn_view_' . $row->condo_id . '" modal_title="' . stripslashes($row->condo_name) . '" class="blue tooltip-info" style="cursor:pointer" data-rel="tooltip" title="View Record" data-target="#Content_Modal" data-toggle="modal" data-backdrop="static" data-keyboard="false">
                                            <i class="ace-icon fa fa-search-plus bigger-130"></i>
                                        </a>
                                        <a id="btn_edit_' . $row->condo_id . '" modal_title="' . stripslashes($row->condo_name) . '" class="green tooltip-info" style="cursor:pointer" data-rel="tooltip" title="Edit Record" data-target="#Content_Modal" data-toggle="modal" data-backdrop="static" data-keyboard="false">
                                            <i class="ace-icon fa fa-pencil bigger-130"></i>
                                        </a>
                                        <a id="btn_delete_' . $row->condo_id . '" modal_title="' . stripslashes($row->condo_name) . '" class="red tooltip-info" style="cursor:pointer" data-rel="tooltip" title="Delete Record">
                                            <i class="ace-icon fa fa-trash-o bigger-130"></i>
                                        </a>'
                            
                                    .'</div>
                                </td>';
                    echo '  </tr>';
                }
            }
            ?>
        </tbody>

    </table>
</div>