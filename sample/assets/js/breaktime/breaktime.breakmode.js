/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var page = 10;

$(function() {

    $('.chosen-select').chosen({allow_single_deselect: true, width: "100%"});
    $('.chosen-select').trigger("chosen:updated");



    var brk_table = $('table#brk-list-table').dataTable({
        ajax: base_url + 'breaktime/brklist/',
        "columnDefs": [
            {
                "targets": [0],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [3],
                "visible": false,
                "searchable": false
            }

        ],
        columns: [
            {
                data: null
            },
            {
                searchable: false,
                orderable: false,
                data: "mb_no",
                render: function(d) {
                    return '<label class="position-relative"><input type="checkbox" class="ace multi-check" value="' + d + '" /><span class="lbl"></span></label>';
                },
                className: ' center'
            },
            {data: "fname", className: ' label-h2'},
            {data: "dept_name", searchable: false, orderable: false},
            {data: "last_out",
                render: function(d) {
                    return d ? d.substring(0, 16) : "";
                }},
            {data: "last_in",
                render: function(d) {
                    return d ? d.substring(0, 16) : "";
                }},
            {data: "min", searchable: false},
            {data: "total_break"},
            {data: "mb_no", searchable: false, orderable: false,
                className: 'center',
                render: function(d) {
                    return '<button class="btn btn-success btn-xs">BREAK</button>';
                }
            }
        ],
        "bLengthChange": false,
        "iDisplayLength": page,
        "bAutoWidth": false,
        initComplete: function(settings, json) {
            $("div#brk-table-loader, div#brk-table-no-record, div#brk-list-table_wrapper").removeClass("hidden");

            $("div#brk-list-table_wrapper, div#brk-table-no-record").addClass("hidden");

            if (json.data.length > 0) {
                $("div#brk-table-loader").addClass("hidden");

                $("div#brk-list-table_wrapper").removeClass("hidden");

                if (json.data.length > page) {
                    $("div#brk-list-table_info").parent().attr('class', 'col-xs-5');
                    $("div#brk-list-table_paginate").parent().attr('class', 'col-xs-7');
                }
                $.post(base_url + 'breaktime/shiftlist', {},
                        function(feeds) {
                            var shift = '<select id="shift-id" class="chosen-select" data-placeholder="Select Shift">';
                            $.each(feeds, function(key, tblshift) {
                                shift += '<option value=' + tblshift["shift_id"] + '>' + tblshift["shift_code"] + '&nbsp;(' + tblshift["time_from"] + ' - ' + tblshift["time_to"] + ')</option>';
                            });
                            shift += '<option value=0 selected="selected" >ON DUTY</option>';
                            shift += '</select>';
                            $("table#brk-list-table").prev().find("div:eq(0)").html(shift);
                            $('.chosen-select').chosen({allow_single_deselect: true, width: "100%"});
                            $('.chosen-select').trigger("chosen:updated");
                        },
                        'json'
                        );

            } else {
                $("div#brk-table-loader").addClass("hidden");

                $("div#brk-table-no-record").removeClass("hidden");
            }


        }
    })
    brk_table_api = brk_table.api();


    var onbrk_table = $('table#brk-onbreak-table').dataTable({
        ajax: base_url + 'breaktime/onbreak/',
        "columnDefs": [
            {
                "targets": [0],
                "visible": false,
                "searchable": false
            }
        ],
        columns: [
            {
                data: null
            },
            {
                searchable: false,
                orderable: false,
                data: "id",
                render: function(d) {
                    return '<label class="position-relative"><input type="checkbox" class="ace multi-check" value="' + d + '" /><span class="lbl"></span></label>';
                },
                className: ' center'
            },
            {data: "fname", className: ' label-h2'},
            {data: "out",
                render: function(d) {
                    return d ? d.substring(0, 16) : "";
                }},
            {data: "mb_no", searchable: false, orderable: false,
                className: 'center',
                render: function(d) {
                    return '<button class="btn btn-warning btn-xs">IN</button>';
                }
            }
        ],
        "bLengthChange": false,
        "iDisplayLength": page,
        initComplete: function(settings, json) {
            $("div#onbrk-table-loader, div#onbrk-table-no-record, div#brk-onbreak-table_wrapper").removeClass("hidden");

            $("div#onbrk-table-no-record, div#brk-onbreak-table_wrapper").addClass("hidden");

            if (json.data.length > 0) {
                $("div#onbrk-table-loader").addClass("hidden");
                $("div#brk-onbreak-table_wrapper").removeClass("hidden");
            } else {
                $("div#onbrk-table-loader").addClass("hidden");
                $("div#onbrk-table-no-record").removeClass("hidden");
            }


        }
    })
    onbrk_table_api = onbrk_table.api();

    $(document).on('change', 'select#shift-id', function() {
        brk_table_api.ajax.url(base_url + 'breaktime/brklist/' + $(this).val()).load(function() {


            $("div#brk-table-loader, div#brk-table-no-record, div#brk-list-table_wrapper div.row:eq(1), div#brk-list-table_wrapper table").removeClass("hidden");

            $("div#brk-list-table_wrapper div.row:eq(1), div#brk-list-table_wrapper table, div#brk-table-no-record").addClass("hidden");

            if (brk_table_api.rows().nodes().length > 0) {
                $("div#brk-table-loader").addClass("hidden");

                $("div#brk-list-table_wrapper div.row:eq(1), div#brk-list-table_wrapper table").removeClass("hidden");

                if (brk_table_api.rows().nodes().length > page) {
                    $("div#brk-list-table_info").parent().attr('class', 'col-xs-5');
                    $("div#brk-list-table_paginate").parent().attr('class', 'col-xs-7');
                }
            } else {
                $("div#brk-table-loader").addClass("hidden");

                $("div#brk-table-no-record").removeClass("hidden");
            }

            $('input#on-main-check')
                    .prop('checked', false)
                    .next()
                    .removeClass('indeterminate');
        });
    });

    $(document).on('change', 'table#brk-list-table td input.multi-check', function() {
        $("button#multi-brk").removeClass('hidden');

        var n = brk_table_api.rows().nodes(),
                s = $(':checked', n).length;

        $('input#main-check').next().toggleClass('indeterminate', s && brk_table_api.rows()[0].length != s);

        if (s < 1)
            $("button#multi-brk").addClass('hidden');
    });

    $(document).on('change', 'table#brk-onbreak-table td input.multi-check', function() {
        $("button#multi-brk-in").removeClass('hidden');

        var n = onbrk_table_api.rows().nodes(),
                s = $(':checked', n).length;

        $('input#on-main-check').next().toggleClass('indeterminate', s && onbrk_table_api.rows()[0].length != s);

        if (s < 1)
            $("button#multi-brk-in").addClass('hidden');
    });

    $('input#main-check')
            .prop('checked', false)
            .change(function() {

                $(this).next().removeClass('indeterminate');

                $('input.multi-check', brk_table_api.rows().nodes())
                        .prop('checked', $(this).prop('checked'))
                        .change()
                        .parents('tr')
                        .toggleClass('tr-selected', $(this).prop('checked'));
            });

    $('input#on-main-check')
            .prop('checked', false)
            .change(function() {

                $(this).next().removeClass('indeterminate');

                $('input.multi-check', onbrk_table_api.rows().nodes())
                        .prop('checked', $(this).prop('checked'))
                        .change()
                        .parents('tr')
                        .toggleClass('tr-selected', $(this).prop('checked'));
            });


    $("table#brk-list-table tbody").on('click', 'button', function() {
        var tblrow = $(this).parents('tr').get(0)._DT_RowIndex;
        var data = brk_table_api.rows(tblrow).data()[0];

        $('button#brk-save').data('breakmode', 'OUT');
        $('button#brk-save').data('multi', 0);
        $('button#brk-save').data('data', [data]);
        $('button#brk-save').data('rows', [tblrow]);
        $('button#brk-save').removeAttr("disabled").html('<i class="ace-icon fa fa-save"></i> Confirm');
        $('div#brk-modal').modal('show').data('triggered', false);
        /**
         $.ajax(base_url + 'breaktime/breakout/' + data["mb_no"] + '/' + data["shift_date"]).done(function(proc) {
         brk_table_api.rows(tblrow).remove().draw(); //delete rows selected
         
         onbrk_table_api.ajax.url(base_url + 'breaktime/onbreak/').load(function() {
         
         $("div#onbrk-table-no-record").removeClass("hidden");
         $("div#brk-onbreak-table_wrapper").addClass("hidden");
         if (onbrk_table_api.rows().nodes().length > 0) {
         $("div#onbrk-table-no-record").addClass("hidden");
         $("div#brk-onbreak-table_wrapper").removeClass("hidden");
         }
         $('input#main-check')
         .prop('checked', false)
         .next()
         .removeClass('indeterminate');
         });
         });
         **/
    });

    $("table#brk-onbreak-table tbody").on('click', 'button', function() {
        var tblrow = $(this).parents('tr').get(0)._DT_RowIndex;
        var data = onbrk_table_api.rows(tblrow).data()[0];

        $('button#brk-save').data('breakmode', 'IN');
        $('button#brk-save').data('multi', 0);
        $('button#brk-save').data('data', [data]);
        $('button#brk-save').data('rows', [tblrow]);
        $('button#brk-save').removeAttr("disabled").html('<i class="ace-icon fa fa-save"></i> Confirm');
        $('div#brk-modal').modal('show').data('triggered', false);

        /**
         $.ajax(base_url + 'breaktime/breakin/' + data["id"]).done(function(proc) {
         onbrk_table_api.rows(tblrow).remove().draw(); //delete rows selected
         
         brk_table_api.ajax.url(base_url + 'breaktime/brklist/').load(function() {
         $("div#brk-table-no-record").removeClass("hidden");
         if (brk_table_api.rows().nodes().length > 0)
         $("div#brk-table-no-record").addClass("hidden");
         
         $('input#on-main-check')
         .prop('checked', false)
         .next()
         .removeClass('indeterminate');
         });
         });
         **/

    });

    $("button#multi-brk").on('click', function() {
        var n = brk_table_api.rows().nodes(),
                c = $(':checked', n),
                s = c.length, rowcount = [];
        var _data = [];
        $.each(c, function(a) {
            _data.push(brk_table_api.rows($(this).parents('tr').get(0)._DT_RowIndex).data()[0]);
            rowcount.push($(this).parents('tr').get(0)._DT_RowIndex);
        });

        $('button#brk-save').data('breakmode', 'OUT');
        $('button#brk-save').data('multi', 1);
        $('button#brk-save').data('data', _data);
        $('button#brk-save').data('rows', rowcount);
        $('button#brk-save').removeAttr("disabled").html('<i class="ace-icon fa fa-save"></i> Confirm');
        $('div#brk-modal').modal('show').data('triggered', false);
    });

    $('div#brk-modal')
            .on('show.bs.modal', function() {
                $("h4#modal-confirmation").html("Set as Break " + ($('button#brk-save').data('breakmode') == "IN" ? "IN?" : "OUT?"));

            });


    $("button#multi-brk-in").on('click', function() {
        var n = onbrk_table_api.rows().nodes(),
                c = $(':checked', n),
                s = c.length, rowcount = [];
        var _data = [];
        $.each(c, function(a) {
            _data.push(onbrk_table_api.rows($(this).parents('tr').get(0)._DT_RowIndex).data()[0]);
            rowcount.push($(this).parents('tr').get(0)._DT_RowIndex);
        });

        $('button#brk-save').data('breakmode', 'IN');
        $('button#brk-save').data('multi', 1);
        $('button#brk-save').data('data', _data);
        $('button#brk-save').data('rows', rowcount);
        $('button#brk-save').removeAttr("disabled").html('<i class="ace-icon fa fa-save"></i> Confirm');
        $('div#brk-modal').modal('show').data('triggered', false);
    });

    $('button#brk-save').on('click', function() {
        $(this).attr("disabled","disabled").html('<i class="ace-icon fa fa-save"></i> Please Wait&hellip;');
        var proc = $(this).data();
        /***
         console.log(brk_table_api.rows().data());
         console.log(proc);
         return false;
         **/
        if (proc["breakmode"] == "OUT") {

            $.post(base_url + 'breaktime/chgbreakout/',
                    {data: proc["data"]},
            'json'
                    ).done(function(success) {
                $.each(proc["rows"], function(key, tblrow) {
                    brk_table_api.rows(tblrow - key).remove(); //delete rows selected    
                });
                brk_table_api.rows().draw();

                if (proc["rows"].length > 0)
                    $("button#multi-brk").addClass('hidden');

                $("div#onbrk-table-loader, div#onbrk-table-no-record, div#brk-onbreak-table_wrapper").removeClass("hidden");

                $("div#onbrk-table-no-record, div#brk-onbreak-table_wrapper").addClass("hidden");



                $('div#brk-modal').modal('hide');
                $('div#brk-modal').removeData();
                $(this).removeAttr("disabled").html('<i class="ace-icon fa fa-save"></i> Confirm');


                onbrk_table_api.ajax.reload(function(feedback) {

                    if (onbrk_table_api.rows().data().length > 0) {
                        $("div#onbrk-table-loader").addClass("hidden");
                        $("div#brk-onbreak-table_wrapper").removeClass("hidden");
                    } else {
                        $("div#onbrk-table-loader").addClass("hidden");
                        $("div#onbrk-table-no-record").removeClass("hidden");
                    }

                }, false);

                $('input#main-check')
                        .prop('checked', false)
                        .next()
                        .removeClass('indeterminate');
                /**
                 .url(base_url + 'breaktime/onbreak/').load(function() {
                 
                 if (onbrk_table_api.rows().data().length > 0) {
                 $("div#onbrk-table-loader").addClass("hidden");
                 $("div#brk-onbreak-table_wrapper").removeClass("hidden");
                 } else {
                 $("div#onbrk-table-loader").addClass("hidden");
                 $("div#onbrk-table-no-record").removeClass("hidden");
                 }
                 
                 $('div#brk-modal').modal('hide');
                 $('div#brk-modal').removeData();
                 
                 $('input#main-check')
                 .prop('checked', false)
                 .next()
                 .removeClass('indeterminate');
                 
                 
                 });
                 **/
            });

        } else if (proc["breakmode"] == "IN") {

            $.post(base_url + 'breaktime/chgbreakin/',
                    {data: proc["data"]},
            'json'
                    ).done(function(success) {
                $.each(proc["rows"], function(key, tblrow) {
                    onbrk_table_api.rows(tblrow - key).remove(); //delete rows selected    
                });

                onbrk_table_api.rows().draw();


                if (proc["rows"].length > 0)
                    $("button#multi-brk-in").addClass('hidden');



                $('div#brk-modal').modal('hide');
                $('div#brk-modal').removeData();
                $(this).removeAttr("disabled").html('<i class="ace-icon fa fa-save"></i> Confirm');

                $('input#on-main-check')
                        .prop('checked', false)
                        .next()
                        .removeClass('indeterminate');


                $("div#brk-table-loader, div#brk-table-no-record, div#brk-list-table_wrapper").removeClass("hidden");

                $("div#brk-list-table_wrapper, div#brk-table-no-record").addClass("hidden");

                brk_table_api.ajax.reload(function(feeds) {
                    if (brk_table_api.rows().data().length > 0) {
                        $("div#brk-table-loader").addClass("hidden");

                        $("div#brk-list-table_wrapper").removeClass("hidden");
                    } else {
                        $("div#brk-table-loader").addClass("hidden");

                        $("div#brk-table-no-record").removeClass("hidden");
                    }
                }, false);

                /**
                 brk_table_api.ajax.url(base_url + 'breaktime/brklist/').load(function() {
                 
                 if (brk_table_api.rows().data().length > 0) {
                 $("div#brk-table-loader").addClass("hidden");
                 
                 $("div#brk-list-table_wrapper").removeClass("hidden");
                 } else {
                 $("div#brk-table-loader").addClass("hidden");
                 
                 $("div#brk-table-no-record").removeClass("hidden");
                 }
                 
                 
                 $('div#brk-modal').modal('hide');
                 $('div#brk-modal').removeData();
                 
                 $('input#on-main-check')
                 .prop('checked', false)
                 .next()
                 .removeClass('indeterminate');
                 
                 });
                 **/

            });

        } else {
        }

    });


});