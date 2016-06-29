
$(function() {
    
    Init_Condo();

});

function Init_Condo()
{
    Init_Condo_List();
    
    ajax_getAllExpats();
    

    $('#btn_add').on('click', function()
    {
        $('#modalTitle').html('New Condo');
        $('#modalTitle').attr('condo_id', 0);
        $('#btn_save').removeClass('hidden');

        ajax_Condo_Info(0, 0);
    });

    $('#btn_save').on('click', function()
    {
        Save_Condo();
    });
    
    $('#btn_cancel').on('click', function()
    {
        $('.alert-success').addClass('hidden');
        $('.alert-error').addClass('hidden');
        $('.alert-duplicate').addClass('hidden');
    });
     
    $('.close-modal').on('click', function()
    {
        $('.alert-success').addClass('hidden');
        $('.alert-error').addClass('hidden');
        $('.alert-duplicate').addClass('hidden');
    });
}


function ajax_getAllExpats()
{
    $.ajax
	(
            {
                type	: 'POST',
                data	: { },
                url     : base_url + 'condo/getAllExpats',
                cache	: false,
                dataType: 'JSON',
                success	: function(data)
                            {
                                window.expatData = data.expatData;
                            }
            }
	);
}


function Init_Condo_List()
{
    $('#tbl_list').dataTable({
        "iDisplayLength": 15,
        "aLengthMenu": [10, 15, 25, 50, 100],
        "aaSorting": [[0, 'asc']],
        "aoColumnDefs": [
            {
                bSortable: false,
                aTargets: [3]
            }
        ],
        "bAutoWidth": false,
        aoColumns: [
            {sClass: "left", "sWidth": "100px"},
            {sClass: "left", "sWidth": "150px"},
            {sClass: "center", "sWidth": "80px"},
            {sClass: "center", "sWidth": "75px"}
        ],
        "fnRowCallback": function() {
        },
        "fnDrawCallback": function() {
        }
    });


    //Action Button in List
    $('#tbl_list').on('click', '[id^=btn_view_], [id^=btn_edit_], [id^=btn_delete_]', function()
    {
        var thisElemID  = $(this).attr('id');
        var modal_title   = $(this).attr('modal_title');
        
        if (thisElemID.indexOf('btn_view_') == 0) {
            var condo_id = parseInt(thisElemID.replace('btn_view_', ''));

            $('#modalTitle').html('View Condo - ' + modal_title);
            $('#modalTitle').attr('condo_id', condo_id);
            $('#btn_save').addClass('hidden');
            
            ajax_Condo_Info(condo_id, 1);
        }
        else if (thisElemID.indexOf('btn_edit_') == 0) {
            var condo_id = parseInt(thisElemID.replace('btn_edit_', ''));
            
            $('#modalTitle').html('Update Condo - ' + modal_title);
            $('#modalTitle').attr('condo_id', condo_id);
            $('#btn_save').removeClass('hidden');
            
            ajax_Condo_Info(condo_id, 2);
        }
        else if (thisElemID.indexOf('btn_delete_') == 0) {
            var condo_id = parseInt(thisElemID.replace('btn_delete_', ''));
            
            Delete_Condo(condo_id, modal_title);
        }

    });
}

//action:  0-Add  ||  1-View  ||  2-Edit
function ajax_Condo_Info(condo_id, action)
{
    //showLoader();
    $.ajax(
            {
                type: 'POST',
                data: { condo_id: condo_id,
                        action : action},
                url: base_url + 'condo/Condo_Info',
                cache: false,
                dataType: 'JSON',
                success: function(data)
                {
                    $('#modal_info_container').html(data.condo_info_HTML);

                    Init_Condo_Elem();
                    //hideLoader();
                }
            }
    );
}


function Init_Condo_Elem()
{
    $('table[id^=tbl_modal_list]').on('click', '[id^=btn_add_list]', function() {

        $('#tbl_modal_list tr:last').after('<tr mb_no="0">\
                                                <td style="text-align:center; vertical-align:middle"><span id="btn_minus_list" class="glyphicon glyphicon-minus-sign" style="cursor:pointer; font-size:16px"></span></td>\
                                                <td class="form-group">\
                                                    <div class="col-sm-12">\
                                                        <select class="form-control" id="sel_expat" data-placeholder="...">\
                                                            <option value="0">&nbsp;</option>' +
                                                            $.map(window.expatData.getAllExpats, function (e, i) {
                                                                return '<option value="' + e.mb_no + '">' + e.mb_nick + ' (' + e.mb_name + ')</option>'
                                                            }).join('') +
                                                '       </select>\
                                                   </div>\
                                                </td>\
                                            </tr>');
    });
    
    
    $('table[id^=tbl_modal_list]').on('click', '[id^=btn_minus_list]', function() {
            $(this).closest('tr').remove()
    });
}



function Required_Field(fieldElem)
{
    $(fieldElem).closest('.form-group').removeClass('has-info').addClass('has-error');
}


function Save_Condo()
{
    var condo_id        = $('#modalTitle').attr('condo_id');
    var condo_name      = $('#txt_condo_name').val();
    var condo_address   = $('#txt_condo_address').val();
    var status          = $('#sel_status').val();
    
    $('.form-group').removeClass('has-error');
    
    var err = 0;
    if (condo_name == "") {
        err += 1;
        Required_Field('#txt_condo_name');
    }
    if (condo_address == "") {
        err += 1;
        Required_Field('#txt_condo_address');
    }
    
    
    if ($('select[id^=sel_expat]').length == 0) {
        err += 1;
        $('.alert-error').removeClass('hidden');
    }
    
    
    $('select[id^=sel_expat]').each(function ()
    {
        var thisElemID      = this;
        var curSel_mb_no    = $(this).val();
        
        if(curSel_mb_no == 0)
        {
            err += 1;
            Required_Field(this);
        }
        
        $('select[id^=sel_expat]').each(function () {
            
            if(thisElemID != this)
            {
                if(curSel_mb_no == $(this).val() && curSel_mb_no > 0)
                {
                    err += 1;
                    Required_Field(thisElemID);
                    Required_Field(this);
                    $('.alert-error').removeClass('hidden');
                }
            }
        });
    });
    
    
    if (err == 0) {
        $('#hiddenNote').addClass('hidden');
        
        var arr_expats = [];
	
        $('select[id^=sel_expat]').each(function () {
                var sel_mb_no = parseInt($(this).val());

                arr_expats[arr_expats.length] = sel_mb_no;
        });
        
        
        var save_data = [condo_id, condo_name, condo_address, status, arr_expats];
        //alert(save_data);
        
//        $('.alert-error').addClass('hidden');
//        $('.alert-success').removeClass('hidden');
        
        Condo_Exist(condo_id, condo_name, save_data);
        //ajax_Save_Condo(save_data);
    }
    else {
        $('.alert-error').removeClass('hidden');
    }
}


function Condo_Exist(condo_id, condo_name, save_data)
{
    $.ajax(
            {
                type: 'POST',
                data:   { 
                            condo_id    : condo_id,
                            condo_name  : condo_name
                        },
                url: base_url + 'condo/Condo_Exist',
                cache: false,
                dataType: 'JSON',
                success: function(data)
                {
                    if (data.iExist > 0)
                    {
                        Required_Field('#txt_condo_name');
                        
                        $('.alert-success').addClass('hidden');
                        $('.alert-error').addClass('hidden');
                        $('.alert-duplicate').removeClass('hidden');
                    }
                    else
                    {
                        $('.alert-success').removeClass('hidden');
                        $('.alert-error').addClass('hidden');
                        $('.alert-duplicate').addClass('hidden');
                        
                        ajax_Save_Condo(save_data)
                    }
                }
            }
    );
}




function ajax_Save_Condo(save_data)
{
    //showLoader();
    $.ajax(
            {
                type: 'POST',
                data: {save_data: save_data},
                url: base_url + 'condo/Save_Condo',
                cache: false,
                dataType: 'JSON',
                success: function(data)
                {
                    if (data.iRecordSave > 0)
                    {
                        setTimeout(function () {
                            
                            $('.alert-success').addClass('hidden');
                            $('.alert-error').addClass('hidden');
                            $('.alert-duplicate').addClass('hidden');
                            
                            $('#Content_Modal').modal('hide');
                        }, 2000);
                        
                        //hideLoader();
                        refresh_List();
                    }
                }
            }
    );
}



function Delete_Condo(condo_id, modal_title)
{
    bootbox.confirm('Are you sure you want to delete <b>"' + modal_title + '"</b> record?', function(result) {
        if (result) {
            ajax_Delete_Condo(condo_id);
        }
    });
}


function ajax_Delete_Condo(condo_id)
{
    //showLoader();
    $.ajax(
            {
                type: 'POST',
                data: { condo_id: condo_id },
                url: base_url + 'condo/Delete_Condo',
                cache: false,
                dataType: 'JSON',
                success: function(data)
                {
                    if (data.iRecordDelete > 0)
                    {
                        $('#Content_Modal').modal('hide');

                        //hideLoader();
                        refresh_List();
                    }
                }
            }
    );
}








function refresh_List()
{
    ajax_Build_Condo_List();
}


function ajax_Build_Condo_List()
{
    $.ajax(
            {
                type: 'POST',
                data: { },
                url: base_url + 'condo/Build_Condo_List',
                cache: false,
                dataType: 'JSON',
                success: function(data)
                {
                    $('#list_container').html(data.condo_list_HTML);
                    $('#list_container').show();

                    Init_Condo_List();
                }
            }
    );
}


//
//function Gritter_Show(type, title, text)
//{
//    $.gritter.add({
//        title: title,
//        text: text,
//        class_name: 'gritter-' + type + ' gritter-center',
//        time: 2000
//	
//    });
//    //return false;
//}
//
//function showLoader()
//{
//    $.blockUI({
//        message: '<h3><img height="30" width="30" src="' + base_url + 'assets/images/ajax-loaders/ajax-loader-13.gif">&nbsp;&nbsp;Loading ...</h3>',
//        baseZ: 99999
//    });
//}
//
//function hideLoader()
//{
//    //$('#loader_Modal').modal('hide');
//    setTimeout($.unblockUI, 150);
//}


