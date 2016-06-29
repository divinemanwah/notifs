/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var limit = 16;
$(function() {

    $('.chosen-select').chosen({allow_single_deselect: true, width: "100%"});
    $('.chosen-select').trigger("chosen:updated");

    $('.input-daterange').datepicker({autoclose: true, format: "yyyy-mm-dd"});

    var brk_table_api = $('div#brk-logs').handsontable('getInstance');

    window.setForm = function() {
        $.post(
                base_url + 'breaktime/datalogs',
                {
                    "emp": $("select#brk-emp").val(),
                    "datefrom": $("input#date-from").val(),
                    "dateto": $("input#date-to").val(),
                    "page": $("div#brk-logs-pager ul.pagination li.active a").data("page") ? $("div#brk-logs-pager ul.pagination li.active a").data("page") : 1,
                    "limit": limit
                }, function(data) {
            setLogs(data);
        },
                'json'
                );
    }
    setForm();

    var brk_table = $('div#brk-logs')
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
                    'Render',
					'Tagged',
					'Untagged'
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
                        data: function(d) {
                            return d["out"] ? d["out"].substring(0, 16) : "";
                        }//empid
                    },
                    {
                        data: function(d) {
                            return d["in"] ? d["in"].substring(0, 16) : "";
                        }//empid
                    },
                    {
                        data: "render" //empid
                    },                    {
                        data: "tagged" //empid
                    },                    {
                        data: "untagged" //empid
                    },
                ],
                cells: function(row, col, prop) {
                    var cellProperties = {};
                    var s = this.instance.getSourceDataAtRow(row);
                    if (col == 6 && parseInt(s["render"]) > 60)
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

    $(document).off("click", "button#search-logs, div#brk-logs-pager ul.pagination li a")
            .on("click", "button#search-logs, div#brk-logs-pager ul.pagination li a ", function() {
                if ($(this).data("page")) {
                    $("div#brk-logs-pager ul.pagination li").removeClass("active");
                    $(this).parent("li").addClass("active");
                }
                setForm();
            });



    window.setLogs = function(data) {
        $("#brk-table-loader").removeClass("hidden");
        $("#brk-table-no-record").removeClass("hidden");
        $('div#brk-logs table').removeClass("hidden");

        $("#brk-table-no-record").addClass("hidden");
        $('div#brk-logs table').addClass("hidden");


        if (data.count > 0) {
            $("#brk-table-loader").addClass("hidden");
            $('div#brk-logs table').removeClass("hidden");

            var brk_table_api = $('div#brk-logs').handsontable('getInstance');
            brk_table_api.loadData(data.data);


        } else {
            $("#brk-table-no-record").removeClass("hidden");
            $("#brk-table-loader").addClass("hidden");
            $("div#brk-logs-pager ul.pagination").html("");
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

        $("div#brk-logs-pager ul.pagination").html(pagination_lbl)

    }

});
