$(function () {

    var displayTable = $("table#tk-hrmis-table");
    var scoringTable = $('div#scoring-table');
    var yearSelector = $('#emp-display-inclusive-year');

    var color = ['286090', 'F8F8F8', 'D8D8D8'];
    var yearValue = yearSelector.val();
    var currYear = new Date().getFullYear();
    var limit_default = 25;
    var loading = false;
    var base_score = 100;
    
    // Month array for setting dates on scoring mode
    var monthNames = [ "January",   "February", "March",    "April",
                       "May",       "June",     "July",     "August",
                       "September", "October",  "November", "December" ]

    // Begin Function
    yearSelector.val(currYear).css('cursor', 'pointer');
    scoringTable.hide();
    setDisplayView(1);

    $("#base_score").html(base_score);

    $("#tk-search-btn").off("click").click(function (e) {
        e.preventDefault();

        if ($('input#emp-display-expat').is(":checked")) {
            setScoringView(1);
        } else {
            setDisplayView(1);
        }

    });

    $("#hrmis-table_length").change(function () {
        if ($('input#emp-display-expat').is(":checked")) {
            setScoringView(1);
        } else {
            setDisplayView(1);
        }
    });

    yearSelector.change(function () {
        if (yearValue !== yearSelector.val()) {
            if ($('input#emp-display-expat').is(":checked")) {
                setScoringView(1);
            } else {
                setDisplayView(1);
            }
            yearValue = yearSelector.val();
        }
    });

    $('input#emp-display-expat').change(function () {

        if ($(this).is(':checked')) {
            $("#inclusive_year").hide();
            $("#tk-hrmis-pager-bot").addClass("hidden");
            setScoringView(1);
        } else {
            scoringTable.hide();
            $("#tk-scoring-pager-bot").addClass("hidden");
            displayTable.show();
            setDisplayView(1);
            $("#inclusive_year").show();
        }

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

    // Function for selecting departments
    $('ul#tk-filter-dept a').off("click").click(function (e) {
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
    });

    $('ul#emp-filter-nation a').off("click").click(function (e) {
        e.preventDefault();
        $('ul#emp-filter-nation li').removeClass('active');
        $(this).parent().addClass('active');

        $('button#emp-filter-nation-btn').html('\
            <i class="ace-icon fa fa-filter"></i> Nationality: ' + $(this).text() + '\
            <i class="ace-icon fa fa-chevron-down icon-on-right"></i>\
          ');

        if ($(this).data('id'))
            $('button#emp-filter-nation-btn').data('id', $(this).data('id'));
        else
            $('button#emp-filter-nation-btn').removeData('id');
    });

    $('ul#emp-filter-status a').off("click").click(function (e) {
        e.preventDefault();
        $('ul#emp-filter-status li').removeClass('active');
        $(this).parent().addClass('active');

        $('button#emp-filter-status-btn').html('\
            <i class="ace-icon fa fa-filter"></i> Status: ' + $(this).text() + '\
            <i class="ace-icon fa fa-chevron-down icon-on-right"></i>\
          ');

        if ($(this).data('id'))
            $('button#emp-filter-status-btn').data('id', $(this).data('id'));
        else
            $('button#emp-filter-status-btn').removeData('id');
    });

    $(document).off("click", "div#tk-hrmis-pager-bot ul.pagination li a").on("click", "div#tk-hrmis-pager-bot ul.pagination li a", function (e) {
        e.preventDefault();
        if ($(this).data("page") > 0)
            setDisplayView($(this).data("page"));
    });

    $(document).off("click", "div#tk-scoring-pager-bot ul.pagination li a").on("click", "div#tk-scoring-pager-bot ul.pagination li a", function (e) {
        e.preventDefault();
        if ($(this).data("page") > 0)
            setScoringView($(this).data("page"));
    });

    var scoring_table = scoringTable.handsontable({
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
            'Status',
            'Started Date',
            'Scoring Month',
            'Month Range',
            'Score',
            'Status'
        ],
        colWidths: [60, 220, 100, 100, 100, 115, 120, 50],
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
                data: 'mb_employment_status',
                renderer: "html"
            },
            {
                data: 'commence_date'
            },
            {
                data: 'scoring_month'
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
            // cellProperties.className = "htCenter";
            cellProperties.className = "htCenter";
            var dt = new Date();

            // Displaying cell data
            var hot = scoringTable.handsontable('getInstance');
            var mData = hot.getDataAtCell(row, 5);
            
            var date = mData.split(' ');
            var getMonth = dt.getMonth() + 1;
            var mm = (getMonth < 10) ? '0' + getMonth : getMonth;

            // Comparing date via YYYY && MM only.
            // Getting the value
            var sMonth = ('0' + parseInt(monthNames.indexOf(date[0]), 10) + 1).slice(-2);

            var scoring_date = new Date(date[1] + '-' + sMonth  + '-01');
            var current_month = new Date(dt.getFullYear() + "-" + mm + "-01");
            
            //Disable modifying score when it is not the right month point.
            if (col === 7) {
            	

                if (scoring_date > current_month) {
                    cellProperties.readOnly = true;
                }
                else
                {
                    cellProperties.readOnly = false;
                }

            }

            if (col < 7 || col > 7)
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

                    // Add required data on _val array object
                    _val.unshift(s.incrementer);//Month Incrementer
                    _val.unshift(s.mb_id);	//Employee's ID
                    _val.unshift(s.month_raw);	//Commence Date
                    _data.changes[_data.changes.length] = _val;
                    console.log(_val)
                });

                $.ajaxq('scoringqueue', {
                    url: base_url + 'employees/updateScores',
                    dataType: 'json',
                    type: 'POST',
                    data: _data
                }).done(function (data) {
                    //Add notification
                });
            }
        },
        beforeChange: function (changes, source) {

            var errTitle = "Invalid score input";
            var score = changes[0][3];
            if (score > base_score) {
                alertNotif(errTitle, 'Score should be max of ' + base_score);
                return false;
            } else if (score <= 0) {     
            	// Will not update score if value is null or zero
                return false;
            } else {
                return true;
            }
            
        }
    });

    function alertNotif(title, text) {
        $.gritter.add({
            title: title,
            text: text,
            sticky: false,
            time: '2000',
            class_name: 'gritter-error'
        });
    }

    /**
     * Returning showing values for pagination
     * @param {type} page_num
     * @param {type} limit_default
     * @param {type} current_page
     * @param {type} base_score_pages
     * @param {type} base_score_count
     * @returns {employees.hrmis_L1.tblInfo.retVal}
     * @author Gerald Tecson
     */
    function tblInfo(page_num, limit_default, current_page, base_score_pages, base_score_count) {

        page_num = typeof page_num !== 'undefined' ? page_num : 0;
        limit_default = typeof limit_default !== 'undefined' ? limit_default : 0;
        current_page = typeof current_page !== 'undefined' ? current_page : 0;
        base_score_pages = typeof base_score_pages !== 'undefined' ? base_score_pages : 0;
        base_score_count = typeof base_score_count !== 'undefined' ? base_score_count : 0;
        var retVal = "Showing " + (((page_num - 1) * limit_default) + 1) + " to " + ((current_page == base_score_pages) ? base_score_count : (((page_num - 1) * limit_default) + parseInt(limit_default))) + " of " + base_score_count + " entries";

        if (page_num === 0) {
            retVal = "No data available";
        }

        return retVal;
    }

    /**
     *
     * @param {type} page_num
     * @returns {Boolean}
     * @author Gerald Tecson
     */
    function setScoringView(page_num) {

        if (loading) {
            return false;
        }

        loading = true;
        $("div#tk-table-loader").removeClass("hidden");
        $("#tk-table-no-record").addClass("hidden");
        $("#tk-hrmis-pager-bot").addClass("hidden");

        limit_default = $('select#hrmis-table_length option:selected').text();
        var scoring_table_api = scoring_table.handsontable('getInstance');
        $.getJSON(
                base_url + 'employees/getScoreData/' + $('button#tk-filter-dept-btn').data('id') + '/' + $('button#emp-filter-nation-btn').data('id') + '/' + $('button#emp-filter-status-btn').data('id') + '/' + page_num + '/' + limit_default,
                function (data) {

                    if (data.total_count === 0) {
                        $("#tk-table-no-record").removeClass("hidden");
                        $("#tk-table-loader").addClass("hidden");
                        scoringTable.hide();
                        loading = false;
                        $("#tk-scoring-pager-bot").addClass("hidden");
                        $("div#hrmis-scoring-table-info").text(tblInfo());
                        return;
                    }
                    $("#tk-table-no-record").addClass("hidden");
                    $("#tk-table-loader").addClass("hidden");
                    scoring_table_api.loadData(data.data);

                    var base_score_count = data.total_count;
                    var base_score_pages = Math.ceil(base_score_count / limit_default);
                    var current_page = data.page;
                    var pagination_limit = 9;
                    var max_page = pagination_limit;
                    if (pagination_limit > base_score_pages)
                        max_page = base_score_pages;

                    console.log(base_score_count)
                    //var tbl_info = "Showing "+ (((page_num - 1) * limit_default) + 1) +" to "+ ((current_page == base_score_pages)? base_score_count : (((page_num - 1) * limit_default) + parseInt(limit_default))) +" of "+ base_score_count +" entries";
                    $("div#hrmis-scoring-table-info").text(tblInfo(page_num, limit_default, current_page, base_score_pages, base_score_count));
                    var pagination_lbl = '<ul class="pagination">';

                    pagination_lbl += '<li ' + (current_page == 1 ? ' class="disabled" disabled' : ' ') + '><a href="javascript:void(0)" data-page="' + (current_page == 1 ? ' 0' : current_page - 1) + '"><i class="ace-icon fa fa-angle-double-left"></i></a></li>';

                    if (base_score_pages > 1)
                        pagination_lbl += '<li ' + (current_page == 1 ? 'class="active"' : '') + '><a href="javascript:void(0)" data-page="1">1</a></li>';

                    if (current_page > 5) {
                        if (base_score_pages > pagination_limit)
                            pagination_lbl += '<li class="disabled"><a href="javascript:void(0)"><i class="ace-icon fa fa-ellipsis-h"></i></a></li>';
                        var initial = ctr = (current_page - 3 > base_score_pages - pagination_limit + 2 ? base_score_pages - pagination_limit + 2 : current_page - 3);
                        var last = initial + pagination_limit - 2;
                        if (last > base_score_pages)
                            last = base_score_pages;
                        for (; ctr < last; ctr++) {
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
                    if (current_page < base_score_pages - 4 && base_score_pages > pagination_limit)
                        pagination_lbl += '<li class="disabled"><a href="javascript:void(0)"><i class="ace-icon fa fa-ellipsis-h"></i></a></li>';
                    if (base_score_pages > 1)
                        pagination_lbl += '<li ' + (current_page == base_score_pages ? 'class="active"' : '') + '><a href="javascript:void(0)"  data-page="' + base_score_pages + '">' + base_score_pages + '</a></li>';

                    pagination_lbl += '<li ' + (current_page == base_score_pages ? ' class="disabled" disabled' : ' ') + '><a href="javascript:void(0)" data-page="' + (current_page == base_score_pages ? ' 0' : parseInt(current_page) + 1) + '"><i class="ace-icon fa fa-angle-double-right"></i></a></li>';

                    pagination_lbl += '</ul>';

                    $("#tk-scoring-pager-bot").html(pagination_lbl);

                    loading = false;

                }
        );
        scoring_table.show();
        displayTable.hide();
        $("#tk-scoring-pager-bot").removeClass("hidden");

    }

    /**
     *
     * @param {type} page_num
     * @returns {Boolean}
     * @author Gerald Tecson
     *
     */
    function setDisplayView(page_num) {

        if (loading) {
            $("div#hrmis-scoring-table-info").text("Data not available");
            return false;
        }

        $("#tk-table-loader").removeClass("hidden");
        $("#tk-table-no-record").addClass("hidden");
        displayTable.addClass("hidden");
//        $("#tk-hrmis-pager-bot").addClass("hidden");
        loading = true;
        limit_default = $('select#hrmis-table_length option:selected').text();
        $.ajax({
            url: base_url + "employees/getEmpHRMIS",
            data: {
                limit: limit_default,
                page: page_num,
                department: $('button#tk-filter-dept-btn').data('id'),
                inclusive_year: $('input#emp-display-inclusive-year').val(),
                nationality: $('button#emp-filter-nation-btn').data('id'),
                emp_status: $('button#emp-filter-status-btn').data('id')
            },
            cache: false,
            dataType: "json",
            type: "post",
            success: function (response) {
                console.log(response)
                if (response.base_score_count === 0) {
                    $("#tk-table-no-record").removeClass("hidden");
                    $("#tk-table-loader").addClass("hidden");
                    $("#tk-hrmis-pager-bot").addClass("hidden");
                    $("div#hrmis-scoring-table-info").text(tblInfo());
                    loading = false;
                    return;
                }
                $("#tk-table-no-record").addClass("hidden");
                $("#tk-table-loader").addClass("hidden");
                displayTable.removeClass("hidden");
                $("#tk-hrmis-pager-bot").removeClass("hidden");

                displayTable.handsontable({
                    colHeaders: response.header,
                    fixedColumnsLeft: 5,
                    data: response.data,
                    cells: function (row, col, prop) {

                        // Employees detail not editable
                        this.editor = false;

                        // Start if cell property setting
                        var cellProperties = {};
                        cellProperties.readOnly = true;
                        cellProperties.renderer = scoreCellRenderer;
                        if (col > 3)
                            cellProperties.className = "htCenter";

                        return cellProperties;

                    }
                });

                var base_score_count = response.total_count;
                var base_score_pages = Math.ceil(base_score_count / limit_default);

                var current_page = response.page;
                var pagination_limit = 9;
                var max_page = pagination_limit;
                if (pagination_limit > base_score_pages)
                    max_page = base_score_pages;

                console.log(base_score_count)
                $("div#hrmis-scoring-table-info").text(tblInfo(page_num, limit_default, current_page, base_score_pages, base_score_count));

                var pagination_lbl = '<ul class="pagination">';

                pagination_lbl += '<li ' + (current_page == 1 ? ' class="disabled" disabled' : ' ') + '><a href="javascript:void(0)" data-page="' + (current_page == 1 ? ' 0' : current_page - 1) + '"><i class="ace-icon fa fa-angle-double-left"></i></a></li>';

                if (base_score_pages > 1)
                    pagination_lbl += '<li ' + (current_page == 1 ? 'class="active"' : '') + '><a href="javascript:void(0)" data-page="1">1</a></li>';


                if (current_page > 5) {
                    if (base_score_pages > pagination_limit)
                        pagination_lbl += '<li class="disabled"><a href="javascript:void(0)"><i class="ace-icon fa fa-ellipsis-h"></i></a></li>';


                    var ctr = (current_page - 3 > base_score_pages - pagination_limit + 2) ? base_score_pages - pagination_limit + 2 : current_page - 3;

                    var initial = ctr;

                    var last = initial + pagination_limit - 2;
                    if (last > base_score_pages)
                        last = base_score_pages;

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

                if (current_page < base_score_pages - 4 && base_score_pages > pagination_limit)
                    pagination_lbl += '<li class="disabled"><a href="javascript:void(0)"><i class="ace-icon fa fa-ellipsis-h"></i></a></li>';
                if (base_score_pages > 1)
                    pagination_lbl += '<li ' + (current_page == base_score_pages ? 'class="active"' : '') + '><a href="javascript:void(0)"  data-page="' + base_score_pages + '">' + base_score_pages + '</a></li>';

                pagination_lbl += '<li ' + (current_page == base_score_pages ? ' class="disabled" disabled' : ' ') + '><a href="javascript:void(0)" data-page="' + (current_page == base_score_pages ? ' 0' : parseInt(current_page) + 1) + '"><i class="ace-icon fa fa-angle-double-right"></i></a></li>';

                pagination_lbl += '</ul>';

//                $("#tk-hrmis-pager-top").html(pagination_lbl);
                $("#tk-hrmis-pager-bot").html(pagination_lbl);

                loading = false;
            }
        })

    }

    function scoreCellRenderer(instance, td, row, col, prop, value, cellProperties) {

        var celldata = arguments[5].split("#");
        var bg = celldata[1];
        td.style.color = "3f3f3f";
        td.style.background = bg;

        // Column 5 onwards only
        if (col > 4) {
            value = "";
            if (bg !== color[3] && celldata[0] !== "") {

                value = celldata[0];
                cellProperties.editor = false;
                cellProperties.readOnly = true;

                if (bg === color[0]) {
                    td.style.color = "#FFF";
                }

                td.style.background = "#" + celldata[1];

            }

        }

        if (celldata.length === 2) {
            td.style.background = "#" + celldata[1];
            td.style.color = "000";
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
