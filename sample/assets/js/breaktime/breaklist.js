/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var limit = 16;
$(function() {

    $('.chosen-select').chosen({allow_single_deselect: true, width: "100%"});
    $('.chosen-select').trigger("chosen:updated");

    $('ul#brk-dept-btn a').off("click").click(function(e) {
        e.preventDefault();
        $('ul#brk-dept-btn li').removeClass('active');
        $(this).parent().addClass('active');

        $('button#brk-dept').html('\
	  <i class="ace-icon fa fa-filter"></i> Department: ' + $(this).text() + '\
	  <i class="ace-icon fa fa-chevron-down icon-on-right"></i>\
	');

        if ($(this).data('id'))
            $('button#brk-dept').data('id', $(this).data('id'));
        else
            $('button#brk-dept').removeData('id');
    });

    $('.input-daterange').datepicker({autoclose: true, format: "yyyy-mm-dd"});






    var brk_table_api = $('div#brk-list').handsontable('getInstance');

    window.setForm = function() {
        $.post(
                base_url + 'breaktime/getlogs',
                {
                    "onbrk": $("input#display-onbrk").prop("checked") ? 1 : 0,
                    "overbrk": $("input#display-overbrk").prop("checked") ? 1 : 0,
                    "dept": $("button#brk-dept").data("id"),
                    "emp": $("select#brk-emp").val(),
                    "datefrom": $("input#date-from").val(),
                    "dateto": $("input#date-to").val(),
                    "page": $("div#brk-list-pager ul.pagination li.active a").data("page") ? $("div#brk-list-pager ul.pagination li.active a").data("page") : 1,
                    "limit": limit
                }, function(data) {
            setList(data);
        },
                'json'
                );
    }
    setForm();

    $(document).off("click", "button#search-brklist, div#brk-list-pager ul.pagination li a, input#display-overbrk, input#display-onbrk")
            .on("click", "button#search-brklist, div#brk-list-pager ul.pagination li a, input#display-overbrk, input#display-onbrk", function() {
                if ($(this).data("page")) {
                    $("div#brk-list-pager ul.pagination li").removeClass("active");
                    $(this).parent("li").addClass("active");
                }
                setForm();
            });

    var brk_table = $('div#brk-list')
            .handsontable({
                readOnly: true,
                stretchH: 'all',
                currentRowClassName: 'currentRow',
                //currentColClassName: 'currentCol',
                colHeaders: [
                    'Employee ID',
                    'Name',
                    'Department',
                    'Date',
                    'Last Out',
                    'Last In',
                    'Minute',
                    'Total Break',
                    'Status'
                ],
                columns: [
                    {
                        data: "mb_id" //empid
                    },
                    {
                        data: function(d) {
                            return d["mb_nick"] + " " + d["mb_lname"];
                        }
                    },
                    {
                        data: "dept_name" //empid
                    },
                    {
                        data: "shift" //empid
                    },
                    {
                        data: "last_out" //empid
                    },
                    {
                        data: "last_in" //empid
                    },
                    {
                        data: "min" //empid
                    },
                    {
                        data: "total_break" //empid
                    },
                    {
                        data: function(d) {
                            return (d["break_status"] == 1) ? "ON BREAK" : "";
                        },
                        render: "html"
                                //empid
                    },
                ],
                cells: function(row, col, prop) {
                    var cellProperties = {};
                    var s = this.instance.getSourceDataAtRow(row);
                    if (col == 8 && s["break_status"] == "1")
                        this.renderer = function(instance, td, row, col, prop, value, cellProperties) {

                            Handsontable.renderers.TextRenderer.apply(this, arguments);

                            $(td).css({'background': '#87b78f', 'color': '#fff', 'font-weight': '700'});
                        };
                    if (col == 6 && parseInt(s["min"]) > 60)
                        this.renderer = function(instance, td, row, col, prop, value, cellProperties) {

                            Handsontable.renderers.TextRenderer.apply(this, arguments);

                            $(td).css({'font-weight': '700', 'color': '#E93E3E'});
                        };

                    cellProperties.readOnly = true;
                    if (col >= 3)
                        cellProperties.className = "htCenter";
                    return cellProperties;
                }
            }).find('table').addClass('hidden');





    window.setList = function(data) {
        $("#brk-table-loader").removeClass("hidden");
        $("#brk-table-no-record").removeClass("hidden");
        $('div#brk-list table').removeClass("hidden");

        $("#brk-table-no-record").addClass("hidden");
        $('div#brk-list table').addClass("hidden");


        if (data.count > 0) {
            $("#brk-table-loader").addClass("hidden");
            $('div#brk-list table').removeClass("hidden");

            var brk_table_api = $('div#brk-list').handsontable('getInstance');
            brk_table_api.loadData(data.data);


        } else {
            $("#brk-table-no-record").removeClass("hidden");
            $("#brk-table-loader").addClass("hidden");
            $("div#brk-list-pager ul.pagination").html("");
        }

        var total_count = data.count;
        var total_pages = Math.ceil(total_count / limit);
        var current_page = data.page;
        var pagination_limit = 9;
        var max_page = pagination_limit;
        if (pagination_limit > total_pages)
            max_page = total_pages;

        var pagination_lbl = '';
        if (total_pages > 1)
            pagination_lbl += '<li ' + (current_page == 1 ? 'class="active"' : '') + '><a href="javascript:void(0)" data-page="1">1</a></li>';
        if (current_page > 5) {
            if (total_pages > pagination_limit)
                pagination_lbl += '<li class="disabled"><a href="javascript:void(0)"><i class="ace-icon fa fa-ellipsis-h"></i></a></li>';
            var initial = ctr = (current_page - 3 > total_pages - pagination_limit + 2 ? total_pages - pagination_limit + 2 : current_page - 3);
            var last = initial + pagination_limit - 2;
            if (last > total_pages)
                last = total_pages;
            for (; ctr < last; ctr++) {
                pagination_lbl += '<li ' + (current_page == ctr ? 'class="active"' : '') + '><a href="javascript:void(0)" data-page="' + ctr + '">' + ctr + '</a></li>';
            }
        }
        else {
            for (var ctr = 2; ctr < max_page; ctr++) {
                pagination_lbl += '<li ' + (current_page == ctr ? 'class="active"' : '') + '><a href="javascript:void(0)" data-page="' + ctr + '">' + ctr + '</a></li>';
            }
        }
        if (current_page < total_pages - 4 && total_pages > pagination_limit)
            pagination_lbl += '<li class="disabled"><a href="javascript:void(0)"><i class="ace-icon fa fa-ellipsis-h"></i></a></li>';
        if (total_pages > 1)
            pagination_lbl += '<li ' + (current_page == total_pages ? 'class="active"' : '') + '><a href="javascript:void(0)"  data-page="' + total_pages + '">' + total_pages + '</a></li>';

        $("div#brk-list-pager ul.pagination").html(pagination_lbl)

    }



});