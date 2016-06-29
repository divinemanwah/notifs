$(function() {
    var schedules_tbl = $("#tk-schedules-table");
    var limit_default = 25;
    var loading = false;
    var current_page = 1;

    $('.chosen-select').chosen({allow_single_deselect: true, width: "100%"});
    $('.chosen-select').trigger("chosen:updated");

    $('ul#tk-filter-dept a').click(function(e) {
        e.preventDefault();
        $('ul#tk-filter-dept li').removeClass('active');
        $(this).parent().addClass('active');

        $('button#tk-filter-dept-btn').html('\
	  <i class="ace-icon fa fa-filter"></i> Department: ' + $(this).text() + '\
	  <i class="ace-icon fa fa-chevron-down icon-on-right"></i>\
	');

        if ($(this).data('id'))
            $('button#tk-filter-dept-btn').data('id', $(this).data('id'));
        else
            $('button#tk-filter-dept-btn').removeData('id');
        // getSchedules(1);
    });

    $('.input-daterange').datepicker({autoclose: true, format: "yyyy-mm-dd"});

    // $('#date-from').change(function(e){
    // e.preventDefault();
    // if($('#date-to').val() != ""){
    // getSchedules(1);
    // }
    // });

    // $('#date-to').change(function(e){
    // e.preventDefault();
    // if($('#date-from').val() != "") {
    // getSchedules(1);
    // }
    // });

    $('#tk-search-btn').click(function(e) {
        if ($('#date-to').val() != "" && $('#date-from').val() != "") {
            getSchedules(1);
        }
    });

    $(document).on("click", "div#tk-schedules-pager ul.pagination li a", function(e) {
        e.preventDefault();
        getSchedules($(this).data("page"));
    });

    getSchedules(1);

    function getSchedules(page_num) {
        if (loading) {
            return false;
        }
        $("#tk-table-loader").removeClass("hidden");
        schedules_tbl.addClass("hidden");
        loading = true;
        current_page = page_num;
        $.ajax({
            url: base_url + "timekeeping/getAllSchedules",
            data: {limit: limit_default, page: page_num, department: $('button#tk-filter-dept-btn').data('id'), emp: $('select#tk-filter-emp').val(), dateFrom: $('#date-from').val(), dateTo: $('#date-to').val()},
            cache: false,
            dataType: "json",
            type: "post",
            success: function(response) {
                $("#tk-table-loader").addClass("hidden");
                schedules_tbl.removeClass("hidden");
                schedules_tbl.handsontable({
                    colHeaders: response.header,
                    fixedColumnsLeft: 3,
                    data: response.data,
                    cells: function(row, col, prop) {
                        var cellProperties = {};
                        cellProperties.readOnly = true;
                        cellProperties.renderer = shiftCellRenderer;
                        if (col > 1)
                            cellProperties.className = "htCenter";
                        return cellProperties;
                    },
                    afterRender: function() {
                        $('[data-toggle="tooltip"]').removeAttr('title').tooltip({container: 'body', delay: {show: 500, hide: 100}, trigger: "hover"});
                        $("[data-original-title]:empty")
                                .removeAttr('data-toggle')
                                .removeAttr('data-placement')
                                .removeAttr('data-original-title')
                                .removeAttr('title');
                    }
                });



                var total_count = response.total_count;
                var total_pages = Math.ceil(total_count / limit_default);
                var current_page = response.page;
                var pagination_limit = 9;
                var max_page = pagination_limit;
                if (pagination_limit > total_pages)
                    max_page = total_pages;

                var pagination_lbl = '<ul class="pagination">';
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
                pagination_lbl += '</ul>';
                $("#tk-schedules-pager").html(pagination_lbl);
                loading = false;
            }
        });

    }

    function shiftCellRenderer(instance, td, row, col, prop, value, cellProperties) {
        var celldata = arguments[5].split("#");
        if (celldata.length == 3 && celldata[3] !== "") {
            value = celldata[0];
            td.style.background = "#" + celldata[1];
            td.style.color = "#000";
            td.setAttribute('data-toggle', 'tooltip');
            td.setAttribute('data-placement', 'top');
            td.setAttribute('data-original-title', celldata[2]);
            td.title = celldata[2];
            td.className = "shift";
        }
        Handsontable.renderers.TextRenderer.apply(this, arguments);
    }

    $(document).on("submit", "#exportForm", function(e) {
        $("input[name='export-dept']").val($('button#tk-filter-dept-btn').data('id'));
        $("input[name='export-from']").val($('#date-from').val());
        $("input[name='export-to']").val($('#date-to').val());
        $("input[name='export-emp']").val($('select#tk-filter-emp').val());
    });

});