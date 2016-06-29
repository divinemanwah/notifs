$(function () {

    var loadingScreen = $('div#kpi-table-loader');
    var noRecordScreen = $('div#kpi-table-no-record');
    var kpiHrDisplayView = $('table#kpi-department-table');
    var kpiHrScoringView = $('div#kpi-scoring-table');
    var yearSelector = $('#kpi-display-inclusive-year');

    var yearValue = yearSelector.val();
    var monthSelector = $('#kpi-display-inclusive-month');
    var monthValue = monthSelector.val();

    var kpiScoringPager = $('#kpi-display-pager-bot');
    var tableInfo = $("div#kpi-scoring-table-info");

    var currYear = new Date().getFullYear();
    var currMonth = moment().format('YYYY-MM');

    var loading = false;
    var limit_default = 25;
    var base_score = 20;

    Date.prototype.monthNames = [
        "January", "February", "March",
        "April", "May", "June",
        "July", "August", "September",
        "October", "November", "December"
    ];


    yearSelector.val(currYear).css('cursor', 'pointer');
    monthSelector.val(currMonth).css('cursor', 'pointer');

    $("#base_score").html(base_score);
    setLoadScores(1);

    var dateStart = new Date();
    dateStart.setDate(dateStart.getDate() - 1);
    var dp = document.getElementById("kpi-display-inclusive-month");

    monthSelector.datepicker({
        format: "yyyy-mm",
        viewMode: "months",
        minViewMode: "months",
        endDate: dateStart
    });

    yearSelector.datepicker({
        autoclose: true,
        format: "yyyy",
        viewMode: "years",
        minViewMode: "years",
        onRender: function (date) {
            return date.valueOf() < now.valueOf() ? 'disabled' : '';
        }
    });

    yearSelector.change(function () {
        if (yearValue !== yearSelector.val()) {
            setLoadScores(1);
            yearValue = yearSelector.val();
        }
    });

    monthSelector.change(function () {
        if (monthValue !== monthSelector.val()) {
            setScoringView(1);
            monthValue = monthSelector.val();
        }
    });

    $("#kpi-search-btn").off("click").click(function (e) {
        e.preventDefault();

        if ($('input#kpi-scoring-mode').is(":checked")) {
            setScoringView(1);
        } else {
            setLoadScores(1);
        }
    });

    $("#kpi-table-length").change(function () {
        if ($('input#kpi-scoring-mode').is(":checked")) {
            setScoringView(1);
        } else {
            setLoadScores(1);
        }
    });

//    $('input#kpi-scoring-mode').change(function () {
//
//        if ($(this).is(':checked')) {
//            setScoringView(1);
//        } else {
//            setLoadScores(1);
//        }
//
//    });

    // Function for selecting departments
    $('ul#kpi-filter-dept a').off("click").click(function (e) {
        e.preventDefault();
        $('ul#kpi-filter-dept li').removeClass('active');
        $(this).parent().addClass('active');

        $('button#kpi-filter-dept-btn').html('\
            <i class="ace-icon fa fa-filter"></i> Department: ' + $(this).text() + '\
            <i class="ace-icon fa fa-chevron-down icon-on-right"></i>\
          ');

        if ($(this).data('id'))
            $('button#kpi-filter-dept-btn').data('id', $(this).data('id'));
        else
            $('button#kpi-filter-dept-btn').removeData('id');
    });

    $('ul#kpi-filter-nation a').off("click").click(function (e) {
        e.preventDefault();
        $('ul#kpi-filter-nation li').removeClass('active');
        $(this).parent().addClass('active');

        $('button#kpi-filter-nation-btn').html('\
            <i class="ace-icon fa fa-filter"></i> Nationality: ' + $(this).text() + '\
            <i class="ace-icon fa fa-chevron-down icon-on-right"></i>\
          ');

        if ($(this).data('id'))
            $('button#kpi-filter-nation-btn').data('id', $(this).data('id'));
        else
            $('button#kpi-filter-nation-btn').removeData('id');
    });

    $(document).off("click", "div#kpi-display-pager-bot ul.pagination li a").on("click", "div#kpi-display-pager-bot ul.pagination li a", function (e) {
        e.preventDefault();
        var page = $(this).data("page");
        if ($(this).data("page") > 0)
            if ($('input#kpi-scoring-mode').is(":checked")) {
                setScoringView(page);
            } else {
                setLoadScores(page);
            }

    });

    var kpiHrScoringTbl = kpiHrScoringView.handsontable({
        currentRowClassName: 'currentRow',
        currentColClassName: 'currentCol',
        fixedColumnsLeft: 4,
        ColumnSorting: {
            column: 1,
            sortOrder: true
        },
        colHeaders: [
            'ID',
            'Name',
            'Department',
            'Month',
            'Score'
        ],
        colWidths: [60, 220, 100, 100],
        columns: [
            {
                data: 'mb_id'
            },
            {
                data: 'mb_nick'
            },
            {
                data: 'dept_name'
            },
            {
                data: 'month'
            },
            {
                data: 'score',
                type: 'numeric',
                format: '0.00'
            }
        ],
        cells: function (row, col, prop) {

            var cellProperties = {};
            cellProperties.className = "htCenter";

            if (col < 4)
                cellProperties.readOnly = true;


            return cellProperties;

        },
        afterChange: function (changes, source) {

            if (changes && source !== 'loadData') {
                var that = this, _data = {changes: new Array()};

                $.each(changes, function (i, val) {
                    var _val = val.slice();
                    var s = that.getSourceDataAtRow(_val[0]);
                    _val[1] = that.propToCol(_val[1]);

                    _val.unshift(s.month_raw);
                    _val.unshift(s.month);
                    _val.unshift(s.head_id);
                    _val.unshift(s.dept_id);
                    _val.unshift(s.mb_no);
                    _data.changes[_data.changes.length] = _val;

                });

                $.ajaxq('kpideptscorequeue', {
                    url: base_url + 'employees/updateHrKpiScore',
                    dataType: 'json',
                    type: 'POST',
                    data: _data
                }).done();
            }
        },
        beforeChange: function (changes, source) {

            if (changes[0][3] > base_score) {
                alert('Score should be max of ' + base_score);
                return false;
            } else if (changes[0][3] <= 0) {
                alert("score should not be zero or null");
                return false;
            } else {
                return true;
            }
        }
    });

    /**
     * Returning showing values for pagination
     * @param {type} page_num
     * @param {type} limit_default
     * @param {type} current_page
     * @param {type} total_pages
     * @param {type} total_count
     * @returns {employees.hrmis_L1.tblInfo.retVal}
     * @author Gerald Tecson
     */
    function tblInfo(page_num, limit_default, current_page, total_pages, total_count) {

        page_num = typeof page_num !== 'undefined' ? page_num : 0;
        limit_default = typeof limit_default !== 'undefined' ? limit_default : 0;
        current_page = typeof current_page !== 'undefined' ? current_page : 0;
        total_pages = typeof total_pages !== 'undefined' ? total_pages : 0;
        total_count = typeof total_count !== 'undefined' ? total_count : 0;
        var retVal = "Showing " + (((page_num - 1) * limit_default) + 1) + " to " + ((current_page == total_pages) ? total_count : (((page_num - 1) * limit_default) + parseInt(limit_default))) + " of " + total_count + " entries";

        if (page_num === 0) {
            retVal = "No data available";
        }

        return retVal;
    }

    /**
     * Load Pagination Function
     * @param {type} total_count
     * @param {type} limit_default
     * @param {type} current_page
     * @param {type} pagination_limit
     * @returns {undefined}
     */
    function loadPagination(total_count, limit_default, current_page, pagination_limit) {

        var total_pages = Math.ceil(total_count / limit_default);
        var max_page = pagination_limit;
        if (pagination_limit > total_pages)
            max_page = total_pages;

        $("div#kpi-scoring-table-info").text(tblInfo(current_page, limit_default, current_page, total_pages, total_count));

        var pagination_lbl = '<ul class="pagination page-grey">';

        pagination_lbl += '<li ' + (current_page == 1 ? ' class="disabled" disabled' : ' ') + '><a href="javascript:void(0)" data-page="' + (current_page == 1 ? ' 0' : current_page - 1) + '"><i class="ace-icon fa fa-angle-double-left"></i></a></li>';

        if (total_pages > 1)
            pagination_lbl += '<li ' + (current_page == 1 ? 'class="active"' : '') + '><a href="javascript:void(0)" data-page="1">1</a></li>';

        if (current_page > 5) {
            if (total_pages > pagination_limit)
                pagination_lbl += '<li class="disabled"><a href="javascript:void(0)"><i class="ace-icon fa fa-ellipsis-h"></i></a></li>';

            var ctr = (current_page - 3 > total_pages - pagination_limit + 2) ? total_pages - pagination_limit + 2 : current_page - 3;

            var initial = ctr;

            var last = initial + pagination_limit - 2;
            if (last > total_pages)
                last = total_pages;

            for (ctr; ctr < last; ctr++) {
                // Pagination Fix
                if (ctr < 2)
                    continue;
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

        pagination_lbl += '<li ' + (current_page == total_pages ? ' class="disabled" disabled' : ' ') + '><a href="javascript:void(0)" data-page="' + (current_page == total_pages ? ' 0' : parseInt(current_page) + 1) + '"><i class="ace-icon fa fa-angle-double-right"></i></a></li>';

        pagination_lbl += '</ul>';

//        $("#tk-hrmis-pager-top").html(pagination_lbl);
        $("#kpi-display-pager-bot").html(pagination_lbl);

    }

    function setScoringView(page_num) {

        if (loading) {
            return false;
        }

        loadingScreen.removeClass('hidden');
        noRecordScreen.addClass('hidden');
        kpiHrScoringView.addClass('hidden');
        kpiHrDisplayView.addClass('hidden');
        $("#inclusive_year").addClass('hidden');
        $("#inclusive_month").removeClass('hidden');

        limit_default = $('select#kpi-table-length option:selected').text();

        $.getJSON(
                base_url + 'employees/getHrScoreData'
                + "/" + $('button#kpi-filter-nation-btn').data('id')
                + "/" + $('button#kpi-filter-dept-btn').data('id')
                + "/" + page_num
                + "/" + limit_default
                + "/" + $('input#kpi-display-inclusive-month').val(),
                function (data) {

                    if (data.total_count === 0) {
                        noRecordScreen.removeClass("hidden");
                        kpiScoringPager.addClass("hidden");
                        tableInfo.text(tblInfo());
                        loading = false;
                        return;
                    }

                    noRecordScreen.addClass('hidden');
                    loadingScreen.addClass('hidden');
                    kpiHrScoringView.removeClass('hidden');


                    kpiHrScoringTbl.handsontable('getInstance').loadData(data.data);
                    loadPagination(data.total_count, limit_default, page_num, 9);
                }
        );

        kpiHrScoringView.show();
        loadingScreen.addClass('hidden');
        kpiScoringPager.removeClass("hidden");
    }

    /**
     * 
     * @param {type} page_num
     */
    function setLoadScores(page_num) {

        if (loading) {
            return false;
        }

        loadingScreen.removeClass('hidden');
        kpiHrDisplayView.addClass('hidden');
        kpiHrScoringView.addClass('hidden');
        $("#inclusive_year").removeClass('hidden');
        $("#inclusive_month").addClass('hidden');
        loading = true;
        limit_default = $('select#kpi-table-length option:selected').text();

        $.ajax({
            url: base_url + "employees/getHrScores",
            data: {
                limit: limit_default,
                department: $('button#kpi-filter-dept-btn').data('id'),
                nationality: $('button#kpi-filter-nation-btn').data('id'),
                inclusive_year: $('input#kpi-display-inclusive-year').val(),
                page: page_num
            },
            cache: false,
            dataType: "json",
            type: "post",
            success: function (data) {

                console.log(data.data)
                if (data.total_count === 0) {
                    noRecordScreen.removeClass('hidden');
                    loadingScreen.addClass('hidden');
                    kpiScoringPager.addClass('hidden');
                    tableInfo.text(tblInfo());
                    loading = false;
                    return;
                }

                noRecordScreen.addClass('hidden');
                loadingScreen.addClass('hidden');
                kpiHrDisplayView.removeClass('hidden');
                kpiScoringPager.removeClass('hidden');

                kpiHrDisplayView.handsontable({
                    colHeaders: data.header,
                    fixedColumnsLeft: 5,
                    data: data.data,
                    cells: function (row, col, prop) {
                        this.editor = false;
                        var cellProperties = {};
                        cellProperties.readOnly = true;
                        cellProperties.renderer = scoreCellRenderer;
                        cellProperties.className = "htCenter";
                        return cellProperties;
                    }
                });
                loadPagination(data.total_count, limit_default, page_num, 9);

                loading = false;
            }
        })
    }

    function scoreCellRenderer(instance, td, row, col, prop, value, cellProperties) {
        td.style.color = "3f3f3f";
        //Added if else to fix issue on pagination page #3
        if (arguments[5]) {
            var data = arguments[5].split("#");

            if (col > 4) {

                value = data[0];
                cellProperties.editor = false;
                cellProperties.readOnly = true;
                if (data[1] === "7c7c7c") {
                    td.style.color = "#d6d6d6";
                }
                td.style.background = "#" + data[1];

            }
        }

        Handsontable.renderers.TextRenderer.apply(this, arguments);
    }

    $(document).off("submit", "#exportForm").on("submit", "#exportForm", function (e) {
        $("input[name='export-dept']").val($('button#tk-filter-dept-btn').data('id'));
        $("input[name='export-emp']").val($('select#tk-filter-emp').val());
        $("input[name='export-from']").val($('#date-from').val());
        $("input[name='export-to']").val($('#date-to').val());
        $("input[name='export-type']").val($('button#tk-filter-le-btn').data('id'));
    });

});