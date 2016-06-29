var updateexpat = new Array();
var test = new Array();
var expat_table_api = "";
var expat_table = "";
function getRegularables() {

    var emp_table_api = $('table#emp-table').DataTable();
    emp_table_api.ajax.url(base_url + 'employees/getRegularables').load();
}

$(function() {

    var emp_table = $('table#emp-table').dataTable({
        ajax: base_url + 'employees/getAll/' + ($('input#emp-display-inactive').prop('checked') ? '1' : '0') + '/' + $('button#emp-filter-nation-btn').data('id') + '/' + ($('button#emp-filter-dept-btn').data('id') ? $('button#emp-filter-dept-btn').data('id') : 0),
        // deferRender: true,
        autoWidth: false,
        columns: [
            {
                orderable: false,
                data: 5,
                render: function(d) {
                    return '<label class="position-relative"><input type="checkbox" class="ace multi-check" value="' + d + '" /><span class="lbl"></span></label>';
                },
                className: 'center'
            },
            {data: 0},
            {data: 1},
            {data: 2},
            {data: 7},
            {
                data: 3,
                searchable: false,
                className: 'no-highlight'
            },
            {
                data: 6,
                searchable: false,
                className: 'no-highlight'
            },
            {
                data: 4,
                render: function(d) {

                    return d == 1 ? '<span class="label label-sm label-success">Active</span>' : '<span class="label label-sm label-warning">Inactive</span>';
                },
                className: 'center no-highlight',
                searchable: false
            },
            {
                orderable: false,
                data: 5,
                render: function(d) {

                    return '\
										<div class="hidden-sm hidden-xs action-buttons">\
											<a class="blue emp-info" href="#">\
												<i class="ace-icon fa fa-search-plus bigger-130"></i>\
											</a>\
											<a class="green emp-edit" href="#">\
												<i class="ace-icon fa fa-pencil bigger-130"></i>\
											</a>\
											<a class="red emp-remove" href="#">\
												<i class="ace-icon fa fa-power-off bigger-130"></i>\
											</a>\
										</div>\
										<div class="hidden-md hidden-lg">\
											<div class="inline position-relative">\
												<button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown" data-position="auto">\
													<i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>\
												</button>\
												<ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">\
													<li>\
														<a href="#" class="tooltip-info emp-info" data-rel="tooltip" title="View">\
															<span class="blue">\
																<i class="ace-icon fa fa-search-plus bigger-120"></i>\
															</span>\
														</a>\
													</li>\
													<li>\
														<a href="#" class="tooltip-success emp-edit" data-rel="tooltip" title="Edit">\
															<span class="green">\
																<i class="ace-icon fa fa-pencil-square-o bigger-120"></i>\
															</span>\
														</a>\
													</li>\
													<li>\
														<a href="#" class="tooltip-error emp-remove" data-rel="tooltip" title="Enable/Disable">\
															<span class="red">\
																<i class="ace-icon fa fa-power-off bigger-120"></i>\
															</span>\
														</a>\
													</li>\
												</ul>\
											</div>\
										</div>\
									';
                },
                className: 'center no-highlight'
            }
        ],
        order: [[6, 'desc'], [2, 'asc']],
        // createdRow: function (row, data, index) {

        // $('td:eq(6) a', row).tooltip();

        // },
        createdRow: function(row, data) {

            var tr_selected = new Array();
            $(row).click(function(e) {

                // e.stopImmediatePropagation();

                // $('input.multi-check', this)
                // .prop('checked', !$('input.multi-check', this).prop('checked'))
                // .change();

                $('input.multi-check', this)
                        .prop('checked', !$('input.multi-check', this).prop('checked'))
                        .change();
                $(this).toggleClass('tr-selected', $('input.multi-check', this).prop('checked'));
            });
            $('td:eq(1)', row).html(function(i, html) {

                return '<a href="' + base_url + 'employees/profile/' + data[5] + '">' + html + '</a>';
            });
            $('td:eq(1) a', row).click(function(e) {

                e.stopImmediatePropagation();
            });
            $('td:eq(8) a', row).tooltip();
            $('td:eq(8) a.emp-edit', row).click(function(e) {

                e.preventDefault();
                e.stopImmediatePropagation();
                $('div#emp-modal').data('mode', 'update');
                $('button#emp-save').data({
                    id: data[5],
                    mode: 'edit'
                });
                // $('a[href=#emp-tab-expat]').parent().toggle(data[6] == 'Expat');

                $('div#emp-modal div.modal-dialog').addClass('modal-lg');
                $('div#emp-modal div.modal-header h4').text('Update account');
                $('div#emp-modal div.row.emp-resign').removeClass('hidden').addClass('hidden');
                $('div#emp-modal div.emp-update').show();
                $('div#emp-modal').modal('show');
            });
            $('td:eq(8) a.emp-remove', row).click(function(e) {

                e.preventDefault();
                e.stopImmediatePropagation();
                $('div#emp-modal').data('mode', 'update');
                $('button#emp-save').data({
                    id: data[5],
                    mode: data[4] == 1 ? 'disable' : 'enable'
                });
                $('div#emp-modal div.modal-dialog').removeClass('modal-lg');
                $('div#emp-modal div.modal-header h4').text((parseInt(data[4]) ? 'Disable' : 'Enable') + ' account');
                if (parseInt(data[4]))
                    $('div#emp-modal div.row.emp-resign').removeClass('hidden');
                $('div#emp-modal div.emp-update').hide();
                $('div#emp-modal').modal('show');
            });
            $('td:eq(8) a.emp-info', row)
                    .click(function(e) {

                        e.preventDefault();
                        e.stopImmediatePropagation();
                    })
                    .popover({
                        content: 'Please wait&hellip;',
                        placement: 'left',
                        html: true,
                        trigger: 'focus'
                    })
                    .on('hide.bs.popover', function() {

                        if (window.curr_emp_xhr)
                            window.curr_emp_xhr.abort();
                    })
                    .on('shown.bs.popover', function() {

                        var that = this;
                        window.curr_emp_xhr = $.getJSON(
                                base_url + 'employees/get/' + data[5],
                                function(_data) {

                                    delete window.curr_emp_xhr;
                                    var _kpi = moment().format('YYYY') in _data.kpi && moment().format('M') in _data.kpi[moment().format('YYYY')] ? _data.kpi[moment().format('YYYY')][moment().format('M')] : base_hr_score;
                                    var _badge = 'danger';
                                    if (_kpi <= base_hr_score && _kpi > (base_hr_score / 2))
                                        _badge = 'success';
                                    if (_kpi <= (base_hr_score / 2) && _kpi > (base_hr_score / 4))
                                        _badge = 'warning';
                                    $(that).popover('destroy');
                                    setTimeout(function() {

                                        $(that)
                                                .popover({
                                                    title: 'Information',
                                                    content: '\
															<table class="table" width="100%" style="table-layout: fixed;">\
																<tr><td width="40%">ID</td><td width="60%">' + _data.mb_id + '</td></tr>\
																<tr><td width="40%">Name</td><td width="60%" style="word-wrap: break-word;">' + _data.mb_name + '</td></tr>\
																<tr><td width="40%">Job Title</td><td width="60%">' + _data.mb_2 + '</td></tr>\
																<tr><td width="40%">Created Date</td><td width="60%">' + _data.mb_datetime + '</td></tr>\
																<tr><td width="40%">KPI</td><td width="60%"><span class="badge badge-' + _badge + '">' + _kpi + '</span></td></tr>\
															</table>',
                                                    placement: 'left',
                                                    html: true,
                                                    trigger: 'focus'
                                                })
                                                .popover('show');
                                    }, 250);
                                }
                        ).fail(function() {
                            alert('An error occured. Please try again later.');
                        });
                    });
        },
        initComplete: function(settings, json) {

            if ('from_search' in window && window.from_search)
                $(settings.nTable).DataTable().search($.trim($('input#top-search').val())).draw();
        }
    }),
            emp_table_api = emp_table.api();
    $('input#main-check')
            .prop('checked', false)
            .change(function() {

                $(this).next().removeClass('indeterminate');
                $('input.multi-check', emp_table_api.rows({search: 'applied'}).nodes())
                        .prop('checked', $(this).prop('checked'))
                        .change()
                        .parents('tr')
                        .toggleClass('tr-selected', $(this).prop('checked'));
                if (!$(this).prop('checked'))
                    $('table#emp-table thead th:last').empty();
            });
    $(document).on('change', 'input.multi-check', function() {

        var n = emp_table_api.rows().nodes(),
                s = $(':checked', n).length;
        $('input#main-check').next().toggleClass('indeterminate', s && emp_table_api.rows()[0].length != s);
        $('button#emp-add-user').html(
                s > 1 ?
                '\
				<i class="ace-icon fa fa-edit"></i>\
				Multiple edit\
			'
                :
                '\
				<i class="ace-icon fa fa-plus"></i>\
				Add\
			'
                );
        if (s > 1) {

            if (!$('table#emp-table thead th:last a').length) {

                $('table#emp-table thead th:last').html('<a title="Enable selected" class="green" href="#"><i class="ace-icon fa fa-power-off bigger-130"></i></a>&nbsp;|&nbsp;<a title="Disable selected" class="red" href="#"><i class="ace-icon fa fa-power-off bigger-130"></i></a>');
                $('table#emp-table thead th:last a').tooltip();
            }
        }
        else
            $('table#emp-table thead th:last').empty();
        // if(s)
        // $('div#emp-table_wrapper').parent().block({
        // message: '<div id="multiple-edit-menu">' + s + ' out of ' + n.length + ' selected</div>',
        // css: {
        // border: 'none',
        // backgroundColor: 'transparent',
        // top: '25%'
        // },
        // showOverlay: false
        // });

    });
    $(document).on('click', 'table#emp-table thead th:last a', function(e) {

        e.preventDefault();
        $('div#emp-modal').data('mode', 'update');
        $('button#emp-save').data('mode', $(this).hasClass('green') ? 'enable' : 'disable');
        $('div#emp-modal div.modal-dialog').removeClass('modal-lg');
        $('div#emp-modal div.modal-header h4').text(($(this).hasClass('green') ? 'Enable' : 'Disable') + ' accounts');
        $('div#emp-modal div.row.emp-resign').removeClass('hidden');
        if ($(this).hasClass('green'))
            $('div#emp-modal div.row.emp-resign').addClass('hidden');
        $('div#emp-modal div.emp-update').hide();
        $('div#emp-modal').modal('show');
    });
    $('input#emp-display-inactive').change(function() {

        if ($('input#emp-display-expat').is(':checked') && $('input#emp-display-expat').is(':visible')) {

            var expat_extended_table_api = $('div#expat-extended-table').handsontable('getInstance');
            $.getJSON(
                    base_url + 'employees/getAllExpats_extended/' + ($('input#emp-display-inactive').prop('checked') ? '1' : '0') + '/' + ($('button#emp-filter-dept-btn').data('id') ? $('button#emp-filter-dept-btn').data('id') : 0),
                    function(data) {

                        if (data.count) {

                            var opts = '';
                            for (var i = 0; i < Math.ceil(data.count / $('div.expat-extended-table-header select').val()); i++)
                                opts += '<option>' + (i + 1) + '</option>';
                            if (opts)
                                $('select#emp-display-expat-page')
                                        .html(opts)
                                        .show();
                            else
                                $('select#emp-display-expat-page').hide();
                            updateexpat = new Array();
                            expat_extended_table_api.loadData(data.data);
                            $('input#main-check')
                                    .prop('checked', false)
                                    .change()
                                    .next()
                                    .removeClass('indeterminate');
                        }
                    }
            );
        }
        else
            emp_table_api.ajax.url(base_url + 'employees/getAll/' + ($('input#emp-display-inactive').prop('checked') ? '1' : '0') + '/' + $('button#emp-filter-nation-btn').data('id') + '/' + ($('button#emp-filter-dept-btn').data('id') ? $('button#emp-filter-dept-btn').data('id') : 0)).load(function() {

                $('input#main-check')
                        .prop('checked', false)
                        .change()
                        .next()
                        .removeClass('indeterminate');
            });
    });
    emp_table
            .on('search.dt', function() {

                $('tbody td:not(.no-highlight)', this)
                        .removeHighlight()
                        .highlight(emp_table_api.search());
            })
            .on('page.dt', function() {

                var that = this;
                if (!$.trim(emp_table_api.search()).length)
                    setTimeout(function() {

                        $('tbody td:not(.no-highlight)', that).removeHighlight();
                    }, 1);
            });
    $('ul#emp-filter-nation a').click(function(e) {

        e.preventDefault();
        $('ul#emp-filter-nation li').removeClass('active');
        $(this).parent().addClass('active');
        $('button#emp-filter-nation-btn').html('\
			<i class="ace-icon fa fa-filter"></i> Nationality: ' + $(this).text() + '\
			<i class="ace-icon fa fa-chevron-down icon-on-right"></i>\
		');
        if ($(this).data('id')) {

            $('button#emp-filter-nation-btn').data('id', $(this).data('id'));
            $('div#expat-switch').toggleClass('hidden', $(this).data('id') == 2);
            if ($('div#expat-switch').hasClass('hidden')) {

                $('div#expat-extended-table, div.expat-extended-table-header').hide();
                $('div#emp-table_wrapper').show();
            }

            if ($(this).data('id') == 2 || !$('div#expat-extended-table').is(':visible')) {

                $('input#emp-display-expat, input#expat-auto-save').prop('checked', false);
                $('select#emp-display-expat-page')[0].selectedIndex = 0;
                $('div#expat-page, div#expat-auto-save-container, div#expat-save').addClass('hidden');
            }
        }
        else {

            $('button#emp-filter-nation-btn').removeData('id');
            $('div#expat-switch').addClass('hidden');
            $('div#expat-extended-table, div.expat-extended-table-header').hide();
            $('input#emp-display-expat, input#expat-auto-save').prop('checked', false);
            $('select#emp-display-expat-page')[0].selectedIndex = 0;
            $('div#expat-page, div#expat-auto-save-container, div#expat-save').addClass('hidden');
            $('div#emp-table_wrapper').show();
        }

        emp_table_api.ajax.url(base_url + 'employees/getAll/' + ($('input#emp-display-inactive').prop('checked') ? '1' : '0') + '/' + $('button#emp-filter-nation-btn').data('id') + '/' + ($('button#emp-filter-dept-btn').data('id') ? $('button#emp-filter-dept-btn').data('id') : 0)).load(function() {

            $('input#main-check')
                    .prop('checked', false)
                    .change()
                    .next()
                    .removeClass('indeterminate');
        });
    });
    $('ul#emp-filter-dept a').click(function(e) {

        e.preventDefault();
        $('ul#emp-filter-dept li').removeClass('active');
        $(this).parent().addClass('active');
        $('button#emp-filter-dept-btn').html('\
			<i class="ace-icon fa fa-filter"></i> Department: ' + $(this).text() + '\
			<i class="ace-icon fa fa-chevron-down icon-on-right"></i>\
		');
        if ($(this).data('id'))
            $('button#emp-filter-dept-btn').data('id', $(this).data('id'));
        else
            $('button#emp-filter-dept-btn').removeData('id');
        if ($('input#emp-display-expat').is(':checked') && $('input#emp-display-expat').is(':visible')) {

            var expat_extended_table_api = $('div#expat-extended-table').handsontable('getInstance');
            $.getJSON(
                    base_url + 'employees/getAllExpats_extended/' + ($('input#emp-display-inactive').prop('checked') ? '1' : '0') + '/' + ($('button#emp-filter-dept-btn').data('id') ? $('button#emp-filter-dept-btn').data('id') : 0),
                    function(data) {
                        updateexpat = new Array();
                        var opts = '';
                        for (var i = 0; i < Math.ceil(data.count / $('div.expat-extended-table-header select').val()); i++)
                            opts += '<option>' + (i + 1) + '</option>';
                        if (opts)
                            $('select#emp-display-expat-page')
                                    .html(opts)
                                    .show();
                        else
                            $('select#emp-display-expat-page').hide();
                        expat_extended_table_api.loadData(data.data);
                        $('input#main-check')
                                .prop('checked', false)
                                .change()
                                .next()
                                .removeClass('indeterminate');
                    }
            );
        }
        else
            emp_table_api.ajax.url(base_url + 'employees/getAll/' + ($('input#emp-display-inactive').prop('checked') ? '1' : '0') + '/' + $('button#emp-filter-nation-btn').data('id') + '/' + ($('button#emp-filter-dept-btn').data('id') ? $('button#emp-filter-dept-btn').data('id') : 0)).load(function() {

                $('input#main-check')
                        .prop('checked', false)
                        .change()
                        .next()
                        .removeClass('indeterminate');
            });
    });
    $('select#emp-status').change(function() {
        $("div#reg-status").addClass("hidden");
        if ($(this).val() == '2' && !confirm('Saving an employee status as "Regular" is permanent.\n\nDo you want to continue?'))
            this.selectedIndex = $('button#emp-save').data('mode') == 'multi' ? -1 : 0;
        if ($(this).val() == '2')
            $("div#reg-status").removeClass("hidden");
    });
    $('div#emp-modal')
            .modal({
                backdrop: 'static',
                show: false
            })
            .on('shown.bs.modal', function() {

                if($('div.row.emp-resign.hidden').length)$('input:visible:first', this).focus();
                
            })
            .on('show.bs.modal', function(e) {

                if (e.target == this) {

                    $('ul#emp-tab a:first').tab('show');
                    $('div.alert-success, div.alert-danger').addClass('hidden');
                    $('input, select, button', this).removeAttr('disabled');
                    $('input:text', this).val('');
                    $('select', this)[0].selectedIndex = 0;
                    $('.hidables')
                            .css('opacity', 1)
                            .unblock()
                            .find('select, input')
                            .removeAttr('disabled');
                    if ($(this).data('mode') == 'update') {

                        // $('input#emp-id, input#emp-username')
                        // .attr('readonly', 'readonly');
                        // .disableSelection();

                        $('select#emp-status option:eq(0)').show();
                        window.empTimeout = setTimeout(function() {

                            $('div.emp-update').block({
                                message: $(new Spinner({color: '#438EB9'}).spin().el),
                                css: {
                                    border: 'none',
                                    backgroundColor: 'transparent'
                                },
                                overlayCSS: {
                                    backgroundColor: '#fff'
                                },
                                centerY: false,
                                ignoreIfBlocked: true,
                                onBlock: function() {


                                }
                            });
                        }, 500);
                        $.getJSON(
                                base_url + 'employees/get/' + $('button#emp-save').data('id'),
                                function(data) {

                                    clearTimeout(window.empTimeout);
                                    $('#reg-status').addClass("hidden");
                                    $('button#emp-save').data('old', data);
                                    $('input#emp-id').val(data.mb_id);
                                    $('input#emp-fname').val(data.mb_fname);
                                    $('input#emp-nick').val(data.mb_nick);
                                    $('input#emp-mname').val(data.mb_mname);
                                    $('input#emp-lname').val(data.mb_lname);
                                    $('select#emp-sex')[0].selectedIndex = data.mb_sex == 'M' ? 0 : 1;
                                    $('select#emp-dept')[0].selectedIndex = $('select#emp-dept option[value=' + data.mb_deptno + ']').index('select#emp-dept option');
                                    $('select#emp-condo')[0].selectedIndex = $('select#emp-condo option[value=' + data.condo_id + ']').index('select#emp-condo option');
                                    $('input#emp-job-title').val(data.mb_2);
                                    $('select#emp-ethnicity')[0].selectedIndex = data.mb_3 == 'Expat' ? 1 : 0;
                                    $('select#emp-group-ot')[0].selectedIndex = $('select#emp-group-ot option[value=' + data.mb_ot_app_grp_id + ']').index('select#emp-group-ot option');
                                    $('select#emp-group-leave')[0].selectedIndex = $('select#emp-group-leave option[value=' + data.mb_lv_app_grp_id + ']').index('select#emp-group-leave option');
                                    $('select#emp-group-obt')[0].selectedIndex = $('select#emp-group-obt option[value=' + data.mb_obt_app_grp_id + ']').index('select#emp-group-obt option');
                                    $('select#emp-group-cws')[0].selectedIndex = $('select#emp-group-cws option[value=' + data.mb_cws_app_grp_id + ']').index('select#emp-group-cws option');
                                    $('select#emp-sched-group')[0].selectedIndex = $('select#emp-sched-group option[value=' + data.mb_sched_grp_id + ']').index('select#emp-sched-group option');
                                    $('input#emp-username').val(data.mb_username);
                                    $('input#emp-email').val(data.mb_email);
                                    $('img#avatar')[0].src = base_url + 'assets/uploads/avatars/' + data.mb_no + '.jpg';
                                    $('select#emp-civil')[0].selectedIndex = data.mb_civil ? $('select#emp-civil option[value=' + data.mb_civil + ']').index('select#emp-civil option') : 0;
                                    $('input#emp-bday')
                                            .val(data.mb_birth)
                                            .datepicker('setDate', data.mb_birth);
                                    $('input#emp-doc-start')
                                            .val(data.mb_commencement)
                                            .datepicker('setDate', data.mb_commencement);
                                    if (moment($.trim(data.mb_resign_date), 'YYYY-MM-DD').isValid())
                                        $('input#emp-doc-end, input#emp-effective-date')
                                                .val(data.mb_resign_date)
                                                .datepicker('setDate', data.mb_resign_date);
                                    if (data.mb_confirmation !== null) {
                                        $('#reg-status').removeClass("hidden");
                                        $('input#reg-status-txt')
                                                .val(data.mb_confirmation)
                                                .datepicker('setDate', data.mb_confirmation)
                                                .prop("disabled", true);
                                    } else {
                                        $('input#reg-status-txt').val("").prop("disabled", false);
                                    }
                                    var e_status = data.mb_employment_status ? $('select#emp-status option[value=' + data.mb_employment_status + ']').index('select#emp-status option') : 0;
                                    $('select#emp-status')[0].selectedIndex = e_status;
                                    if (e_status == 1)
                                        $('select#emp-status').attr('disabled', 'disabled');
                                    else
                                        $('select#emp-status').removeAttr('disabled');
                                    // $('input#expat-tin-application').val(data.tin_application_date ? moment(data.tin_application_date, 'YYYY-MM-DD').format('MM-DD-YYYY') : '');
                                    // $('input#expat-tin-release').val(data.tin_release_date ? moment(data.tin_release_date, 'YYYY-MM-DD').format('MM-DD-YYYY') : '');
                                    // $('input#expat-tin-no').val(data.tin_no);

                                    // $('input#expat-aep-application').val(data.aep_application_date ? moment(data.aep_application_date, 'YYYY-MM-DD').format('MM-DD-YYYY') : '');
                                    // $('input#expat-aep-approval').val(data.aep_approval_date ? moment(data.aep_approval_date, 'YYYY-MM-DD').format('MM-DD-YYYY') : '');
                                    // $('input#expat-aep-issue').val(data.aep_issue_date ? moment(data.aep_issue_date, 'YYYY-MM-DD').format('MM-DD-YYYY') : '');

                                    // $('input#expat-cwv-application').val(data.cwv_application_date ? moment(data.cwv_application_date, 'YYYY-MM-DD').format('MM-DD-YYYY') : '');
                                    // $('input#expat-cwv-approval').val(data.cwv_approval_date ? moment(data.cwv_approval_date, 'YYYY-MM-DD').format('MM-DD-YYYY') : '');
                                    // $('input#expat-cwv-received').val(data.cwv_received_date ? moment(data.cwv_received_date, 'YYYY-MM-DD').format('MM-DD-YYYY') : '');

                                    // $('input#expat-remarks').val(data.remarks);

                                    //diplay access report

                                    if (data.reports.length > 0)
                                    {
                                        $("#no_report_module").remove();
                                        $("#reports_module_tbl").find(".report_list").remove();
                                        $.each(data.reports, function() {
                                            var table_dtl = "<tr class='report_list row-" + this.report_id + "-" + this.dept_no + "' >\
									  <td>\
										<input type='hidden' name='item_report[]' value='" + this.report_id + "' \>\
										\
										" + this.report_name + "\
									  </td>\
									  <td>\
										<input type='hidden' name='item_dept[]' value='" + this.dept_no + "' \>\
										" + this.dept_name + "\
									  </td>\
									  <td class='center'>\
										<span class='label label-success arrowed'>\
										  <i class='fa fa-check'></i>" + this.status_name + "\
										</span>\
									  </td>\
									  <td class='center'>\
										<a class='red approver-remove' href='#'>\
										  <i class='ace-icon fa fa-trash-o bigger-130'></i>\
										</a>\
									  </td>\
									</tr>";
                                            $("#reports_module_tbl").append(table_dtl);
                                        });
                                        //reports_module_tbl
                                    }
                                    else
                                    {
                                        $("#reports_module_tbl").html("<tr id=\"no_report_module\"><td colspan=4 class=\"center\">No Record Found</td></tr>");
                                        $("#reports_module_tbl").find(".report_list").remove();
                                    }

                                    $.getJSON(base_url+'groups/getAllowedGroup/' + $('button#emp-save').data('id')).done(function(setData) {
                                        $('#allowed-group').prop("checked", parseInt(setData));
                                    });
                                    $.getJSON(base_url+'timekeeping/holidaymem/select/' + $('button#emp-save').data('id')).done(function(setData) {

                                        $('#allowed-holiday').prop("checked", parseInt(setData.data.h_status));
                                    });
                                    $('div.emp-update').unblock();
                                    
                                }
                        );
                        $('input#emp-password, input#emp-password-confirm').removeAttr('placeholder');
                        
                    }
                    else if ($(this).data('mode') == 'add') {
                        $("#reports_module_tbl").html("<tr id=\"no_report_module\"><td colspan=4 class=\"center\">No Record Found</td></tr>");
                        $('select#emp-status option:eq(0)').show();
                        $('input#emp-id, input#emp-username').removeAttr('readonly');
                        $('input#emp-password, input#emp-password-confirm').attr('placeholder', 'Welcome1');
                        $("#reports_module_tbl").find(".report_list").remove();
                    }
                    else if ($(this).data('mode') == 'multi') {


                        $('select#emp-status option:eq(0)').hide();
                        $('input', this).val('');
                        $('select', this).prop('selectedIndex', -1);
                        $('.hidables')
                                .css('opacity', .5)
                                .block({
                                    message: null,
                                    overlayCSS: {
                                        backgroundColor: 'transparent',
                                        cursor: 'not-allowed'
                                    }
                                })
                                .find('select, input')
                                .attr('disabled', 'disabled');
                        $('input#emp-password, input#emp-password-confirm').removeAttr('placeholder');
                    }

                }


                else {

                    $('input#emp-id, input#emp-username')
                            .removeAttr('readonly');
                    // .enableSelection();
                }

                $('input:password', this).val('');
                $('button#emp-save').html('\
				<i class="ace-icon fa fa-check"></i>\
				Save\
			');
            });
    $('form#emp-password').submit(function(e) {

        e.preventDefault();
    });
    $('button#emp-save').click(function() {

        if ($("select#emp-status").val() == '2' && $("input#reg-status-txt").val() == "") {
            $('span#emp-err-msg').text('Please Input Confirmation date');
            $('div#emp-modal div.alert-danger').removeClass('hidden');
            return false;
        }

        var val_count = $.map($('input, select', $('div.emp-update')[0]), function(a) {

            var v = $.trim($(a).val());
            return v ? v : null;
        }).length,
                old_data = $(this).data('old');
        if ($.inArray($(this).data('mode'), ['add', 'edit']) != -1 && (!$.trim($('input#emp-id').val()) || !$.trim($('input#emp-lname').val()) || !$.trim($('input#emp-fname').val()) || !$.trim($('input#emp-username').val()) || !$.trim($('input#emp-email').val()) || !$.trim($('select#emp-sched-group').val()) || !$.trim($('input#emp-doc-start').val()))) {

            $('span#emp-err-msg').text('Please input at least an Employee ID, Last name, First name, D.O.C., Username, Approval Group, and E-mail.');
            $('div#emp-modal div.alert-danger').removeClass('hidden');
            return false;
        }
        else if ($(this).data('mode') == 'multi' && !val_count) {

            $('span#emp-err-msg').text('Please input at least 1 update.');
            $('div#emp-modal div.alert-danger').removeClass('hidden');
            return false;
        }

        var _data = {},
                pass_match = $.trim($('input#emp-password').val()) == $.trim($('input#emp-password-confirm').val());
        //access report   
        var emp_reports = new Array();
        $.each($("input[name='item_report[]']"), function(i) {
            emp_reports.push($(this).val());
        });
        _data.emp_reports = emp_reports;
        var emp_access_depts = new Array();
        $.each($("input[name='item_dept[]']"), function(i) {
            emp_access_depts.push($(this).val());
        });
        _data.emp_access_depts = emp_access_depts;
        if ($.inArray($(this).data('mode'), ['enable', 'disable']) != -1) {

            var n = emp_table_api.rows().nodes(),
                    c = $(':checked', n),
                    s = c.length;
            _data.mb_no = s > 1 ? $.map(c, function(a) {
                return $(a).val();
            }) : $(this).data('id');
            _data.mb_status = $(this).data('mode') == 'enable' ? 1 : 0;
        }

        if ($(this).data('mode') == 'edit') {

            _data.mb_no = $(this).data('id');
            _data.mb_id = $.trim($('input#emp-id').val());
            _data.mb_fname = $.trim($('input#emp-fname').val());
            _data.mb_nick = $.trim($('input#emp-nick').val());
            _data.mb_mname = $.trim($('input#emp-mname').val());
            _data.mb_lname = $.trim($('input#emp-lname').val());
            _data.mb_sex = $('select#emp-sex').val() == '1' ? 'M' : 'F';
            _data.mb_deptno = $.trim($('select#emp-dept').val());
            _data.mb_2 = $.trim($('input#emp-job-title').val());
            _data.mb_3 = $('select#emp-ethnicity').val();
            _data.mb_ot_app_grp_id = $('select#emp-group-ot').val();
            _data.mb_lv_app_grp_id = $('select#emp-group-leave').val();
            _data.mb_obt_app_grp_id = $('select#emp-group-obt').val();
            _data.mb_cws_app_grp_id = $('select#emp-group-cws').val();
            _data.mb_sched_grp_id = $('select#emp-sched-group').val();
            _data.mb_username = $.trim($('input#emp-username').val());
            _data.mb_email = $.trim($('input#emp-email').val());
            if ($('img#avatar')[0].src.startsWith('data:image/'))
                _data.photo = $('img#avatar')[0].src;
            _data.mb_civil = $('select#emp-civil').val();
            if (moment($.trim($('input#emp-bday').val()), 'YYYY-MM-DD').isValid())
                _data.mb_birth = $('input#emp-bday').val();
            if (moment($.trim($('input#emp-doc-start').val()), 'YYYY-MM-DD').isValid())
                _data.mb_commencement = $('input#emp-doc-start').val();
            if (moment($.trim($('input#emp-doc-end').val()), 'YYYY-MM-DD').isValid() || $('input#emp-doc-end').val().length == 0)
                _data.mb_resign_date = $('input#emp-doc-end').val();
            if (old_data.mb_employment_status != $('select#emp-status').val()) {
                _data.mb_employment_status = $('select#emp-status').val();
                if (moment($.trim($('input#reg-status-txt').val()), 'YYYY-MM-DD').isValid() && $('select#emp-status').val() == '2')
                    _data.mb_confirmation = $('input#reg-status-txt').val();
            }

            _data.condo_id = $('select#emp-condo').val() || 0;
            // if($('select#emp-ethnicity').val() == '2') {

            // _data.expat = {};

            // if(moment($.trim($('input#expat-tin-application').val()), 'MM-DD-YYYY').isValid())
            // _data.expat.tin_application_date = moment($.trim($('input#expat-tin-application').val()), 'MM-DD-YYYY').format('YYYY-MM-DD');

            // if(moment($.trim($('input#expat-tin-release').val()), 'MM-DD-YYYY').isValid())
            // _data.expat.tin_release_date = moment($.trim($('input#expat-tin-release').val()), 'MM-DD-YYYY').format('YYYY-MM-DD');

            // if($.trim($('input#expat-tin-no').val()))
            // _data.expat.tin_no = $.trim($('input#expat-tin-no').val());

            // if(moment($.trim($('input#expat-aep-application').val()), 'MM-DD-YYYY').isValid())
            // _data.expat.aep_application_date = moment($.trim($('input#expat-aep-application').val()), 'MM-DD-YYYY').format('YYYY-MM-DD');

            // if(moment($.trim($('input#expat-aep-approval').val()), 'MM-DD-YYYY').isValid())
            // _data.expat.aep_approval_date = moment($.trim($('input#expat-aep-approval').val()), 'MM-DD-YYYY').format('YYYY-MM-DD');

            // if(moment($.trim($('input#expat-aep-issue').val()), 'MM-DD-YYYY').isValid())
            // _data.expat.aep_issue_date = moment($.trim($('input#expat-aep-issue').val()), 'MM-DD-YYYY').format('YYYY-MM-DD');

            // if(moment($.trim($('input#expat-cwv-application').val()), 'MM-DD-YYYY').isValid())
            // _data.expat.cwv_application_date = moment($.trim($('input#expat-cwv-application').val()), 'MM-DD-YYYY').format('YYYY-MM-DD');

            // if(moment($.trim($('input#expat-expat-cwv-approval').val()), 'MM-DD-YYYY').isValid())
            // _data.expat.cwv_approval_date = moment($.trim($('input#expat-expat-cwv-approval').val()), 'MM-DD-YYYY').format('YYYY-MM-DD');

            // if(moment($.trim($('input#expat-cwv-received').val()), 'MM-DD-YYYY').isValid())
            // _data.expat.cwv_received_date = moment($.trim($('input#expat-cwv-received').val()), 'MM-DD-YYYY').format('YYYY-MM-DD');

            // if($.trim($('input#expat-remarks').val()))
            // _data.expat.remarks = $.trim($('input#expat-remarks').val());
            // }
        }
        else if ($(this).data('mode') == 'add') {

            _data.mb_id = $.trim($('input#emp-id').val());
            _data.mb_fname = $.trim($('input#emp-fname').val());
            _data.mb_nick = $.trim($('input#emp-nick').val());
            _data.mb_mname = $.trim($('input#emp-mname').val());
            _data.mb_lname = $.trim($('input#emp-lname').val());
            _data.mb_sex = $('select#emp-sex').val();
            _data.mb_deptno = $.trim($('select#emp-dept').val());
            _data.mb_2 = $.trim($('input#emp-job-title').val());
            _data.mb_3 = $('select#emp-ethnicity').val();
            _data.mb_ot_app_grp_id = $('select#emp-group-ot').val();
            _data.mb_lv_app_grp_id = $('select#emp-group-leave').val();
            _data.mb_obt_app_grp_id = $('select#emp-group-obt').val();
            _data.mb_cws_app_grp_id = $('select#emp-group-cws').val();
            _data.mb_sched_grp_id = $('select#emp-sched-group').val();
            _data.mb_username = $.trim($('input#emp-username').val());
            _data.mb_email = $.trim($('input#emp-email').val());
            if ($('img#avatar')[0].src.startsWith('data:image/'))
                _data.photo = $('img#avatar')[0].src;
            _data.mb_civil = $('select#emp-civil').val();
            if (moment($.trim($('input#emp-bday').val()), 'YYYY-MM-DD').isValid())
                _data.mb_birth = $('input#emp-bday').val();
            if (moment($.trim($('input#emp-doc-start').val()), 'YYYY-MM-DD').isValid())
                _data.mb_commencement = $('input#emp-doc-start').val();
            if (moment($.trim($('input#emp-doc-end').val()), 'YYYY-MM-DD').isValid())
                _data.mb_resign_date = $('input#emp-doc-end').val();
            _data.mb_employment_status = $('select#emp-status').val();
            _data.condo_id = $('select#emp-condo').val() || 0;
        }
        else if ($(this).data('mode') == 'multi') {

            var n = emp_table_api.rows().nodes(),
                    c = $(':checked', n),
                    s = c.length;
            _data.mb_no = $.map(c, function(a) {
                return $(a).val();
            });
            if ($('select#emp-sex').val())
                _data.mb_sex = $('select#emp-sex').val() == '1' ? 'M' : 'F';
            if ($('select#emp-dept').val())
                _data.mb_deptno = $.trim($('select#emp-dept').val());
            if ($.trim($('input#emp-job-title').val()))
                _data.mb_2 = $.trim($('input#emp-job-title').val());
            if ($('select#emp-ethnicity').val())
                _data.mb_3 = $('select#emp-ethnicity').val();
            if ($('select#emp-group-ot').val())
                _data.mb_ot_app_grp_id = $('select#emp-group-ot').val();
            if ($('select#emp-group-leave').val())
                _data.mb_lv_app_grp_id = $('select#emp-group-leave').val();
            if ($('select#emp-group-obt').val())
                _data.mb_obt_app_grp_id = $('select#emp-group-obt').val();
            if ($('select#emp-group-cws').val())
                _data.mb_cws_app_grp_id = $('select#emp-group-cws').val();
            if ($('select#emp-sched-group').val())
                _data.mb_sched_grp_id = $('select#emp-sched-group').val();
            if ($('select#emp-civil').val())
                _data.mb_civil = $('select#emp-civil').val();
            if (moment($.trim($('input#emp-doc-start').val()), 'YYYY-MM-DD').isValid())
                _data.mb_commencement = $('input#emp-doc-start').val();
            if (moment($.trim($('input#emp-doc-end').val()), 'YYYY-MM-DD').isValid() || $('input#emp-doc-end').val().length == 0)
                _data.mb_resign_date = $('input#emp-doc-end').val();
            if ($('select#emp-status').val())
                _data.mb_employment_status = $('select#emp-status').val();
        }
        else if ($(this).data('mode') == 'disable') {
            if (moment($.trim($('input#emp-effective-date').val()), 'YYYY-MM-DD').isValid()) {
                _data.mb_resign_date = $.trim($('input#emp-effective-date').val());
            } else {
                $('span#emp-err-msg').text("Please add effective resigned date");
                $('div.alert-success').addClass('hidden');
                $('div.alert-danger').removeClass('hidden');
                $('input#emp-effective-date').focus();
                return false;
            }
        }
        if ($.trim($('input#emp-password').val()) && pass_match)
            _data.mb_password = $.trim($('input#emp-password').val());
        if ((ace.sizeof(_data) > 8 && pass_match) || ($.inArray($(this).data('mode'), ['enable', 'disable']) != -1 && pass_match) || ($(this).data('mode') == 'multi' && val_count && pass_match)) {

            var that = this;
            $('div#emp-modal input, div#emp-modal button, div#emp-modal select').attr('disabled', 'disabled');
            $(this).html('Please wait&hellip;');
            $.post(
                    base_url + 'employees/' + ($(this).data('mode') == 'add' ? 'create' : 'update'),
                    _data,
                    function(data) {

                        if (data.success) {

                            //if($.trim($('select#allowed-group').val() > 0))
                            if ($(that).data('mode') != 'multi') {

                                $.post(base_url + 'groups/saveDept',
                                        {allowed_status: $('#allowed-group').prop("checked") ? 1 : 0,
                                            mb_no: _data.mb_no});
                                $.post(base_url + 'timekeeping/holidaymem/insert/' + (_data.mb_no) + '/' + ($('#allowed-holiday').prop("checked") ? 1 : 0));
                            }


                            $('div.alert-success').removeClass('hidden');
                            $('div.alert-danger').addClass('hidden');
                            emp_table_api.ajax.url(base_url + 'employees/getAll/' + ($('input#emp-display-inactive').prop('checked') ? '1' : '0') + '/' + $('button#emp-filter-nation-btn').data('id') + '/' + ($('button#emp-filter-dept-btn').data('id') ? $('button#emp-filter-dept-btn').data('id') : 0)).load(function() {

                                $('button#emp-add-user').html('\
								<i class="ace-icon fa fa-plus"></i>\
								Add\
							');
                                $('table#emp-table thead th:last').empty();
                                $('input#main-check')
                                        .prop('checked', false)
                                        .next()
                                        .removeClass('indeterminate');
                            }, false);
                            setTimeout(function() {

                                $('div#emp-modal').modal('hide');
                            }, 2000);
                        }
                        else {

                            var msg = 'An unknown error occured.';
                            switch (data.error) {
                                case 1:

                                    msg = 'Invalid request.';
                                    break;
                                case 2:

                                    msg = 'No changes made.';
                                    break;
                                case 3:

                                    msg = 'Incorrect password.';
                                    break;
                            }

                            $('span#emp-err-msg').text(msg);
                            $('div.alert-success').addClass('hidden');
                            $('div.alert-danger').removeClass('hidden');
                            $('div#emp-modal input, div#emp-modal button, div#emp-modal select').removeAttr('disabled');
                            $(that).html('\
							<i class="ace-icon fa fa-check"></i>\
							Save\
						');
                        }
                    },
                    'json'
                    );
        }
        else {

            $('span#emp-err-msg').text('Passwords must match.');
            $('div#emp-modal div.alert-danger').removeClass('hidden');
            $('ul#emp-tab a:eq(1)').tab('show');
        }

    });
    // $('select#emp-ethnicity').change(function () {

    // if($(this).val() == '1')
    // $('ul#emp-tab li:eq(2)').hide();
    // else
    // $('ul#emp-tab li:eq(2)').show();

    // $('ul#emp-tab a:first').tab('show');
    // });

    $('input#expat-tin-application, input#expat-tin-release, input#expat-aep-application, input#expat-aep-approval, input#expat-aep-issue, input#expat-cwv-application, input#expat-cwv-approval, input#expat-cwv-received')
            .datepicker({
                autoclose: true,
                todayHighlight: true
            })
            .next()
            .on('click', function() {

                $(this).prev().focus();
            });
    $('input#expat-tin-no').mask('999-999-999');
    $('input#emp-display-expat').change(function() {
        updateexpat = new Array();
        $('div#expat-page').toggleClass('hidden', !$(this).is(':checked'));
        $('div#expat-auto-save-container').toggleClass('hidden', !$(this).is(':checked'));
        $('div#expat-save').toggleClass('hidden', !$(this).is(':checked'));
        $('div.expat-extended-table-header select')[0].selectedIndex = 0;
        $('input#expat-search').val('');
        if ($(this).is(':checked')) {

            var expat_extended_table_api = $('div#expat-extended-table').handsontable('getInstance');
            $.getJSON(
                    base_url + 'employees/getAllExpats_extended/' + ($('input#emp-display-inactive').prop('checked') ? '1' : '0') + '/' + ($('button#emp-filter-dept-btn').data('id') ? $('button#emp-filter-dept-btn').data('id') : 0),
                    function(data) {

                        var opts = '';
                        for (var i = 0; i < Math.ceil(data.count / $('div.expat-extended-table-header select').val()); i++)
                            opts += '<option>' + (i + 1) + '</option>';
                        if (opts)
                            $('select#emp-display-expat-page')
                                    .html(opts)
                                    .show();
                        else
                            $('select#emp-display-expat-page').hide();
                        updateexpat = new Array();
                        expat_extended_table_api.loadData(data.data);
                    }
            );
            $('input#expat-auto-save').prop('checked', true);
            $('div#expat-save').addClass('hidden');
            $('div#expat-extended-table, div.expat-extended-table-header').show();
            $('div#emp-table_wrapper').hide();
        }
        else {

            $('div#expat-extended-table, div.expat-extended-table-header').hide();
            $('div#emp-table_wrapper').show();
        }

    });
    var dateValidator = function(value, callback) {

        if (value)
            callback(moment(value, 'YYYY-MM-DD', true).isValid());
        else
            callback(true);
    };
    var CivilEditor = Handsontable.editors.DropdownEditor.prototype.extend(),
            EmploymentEditor = Handsontable.editors.DropdownEditor.prototype.extend();
    CivilEditor.prototype.prepare = function(row, col, prop, td, originalValue, cellProperties) {
        switch (originalValue) {
            case "1":
                originalValue = 'Single';
                break;
            case "2":
                originalValue = 'Married';
                break;
            case "3":
                originalValue = 'Widowed';
                break;
            case "4":
                originalValue = 'Separated';
                break;
            case "5":
                originalValue = 'Divorced';
                break;
        }
        Handsontable.editors.DropdownEditor.prototype.prepare.apply(this, arguments);
    };
    EmploymentEditor.prototype.prepare = function(row, col, prop, td, originalValue, cellProperties) {
        switch (originalValue) {
            case '1':

                originalValue = 'Probational';
                break;
            case '2':

                originalValue = 'Regular';
                break;
        }

        Handsontable.editors.DropdownEditor.prototype.prepare.apply(this, arguments);
    };
    var columnSort = function(col, order) {

        var theads = $('thead', this.rootElement),
                thead,
                th,
                ths;
        for (var i = 0, theadsLength = theads.length; i < theadsLength; i++) {

            thead = theads[i];
            ths = $('th', theads[i]);
            for (var j = 0, thsLength = ths.length; j < thsLength; j++) {

                $(ths[j]).removeClass('sort_asc sort_desc');
            }
        }

        for (var i = 0, theadsLength = theads.length; i < theadsLength; i++) {

            th = $('th:nth-child(' + parseInt(col + 2, 10) + ')', theads[i]);
            if (th.length)
                th.addClass('sort_' + (order ? 'asc' : 'desc'));
        }

    };
    var expat_table = $('div#expat-extended-table')
            .handsontable({
                // stretchH: 'all',
                // minSpareRows: 1,
                currentRowClassName: 'currentRow',
                currentColClassName: 'currentCol',
                columnSorting: {
                    column: 1,
                    sortOrder: true
                },
                search: true,
                manualColumnResize: true,
                rowHeaders: function(index) {

                    return (($('div.expat-extended-table-header select').val() * $('select#emp-display-expat-page').val()) - $('div.expat-extended-table-header select').val()) + index + 1;
                },
                beforeColumnSort: columnSort,
                afterColumnSort: columnSort,
                // comments: true,
                // contextMenu: ['commentsAddEdit', 'commentsRemove'],
                // afterSelection : function (row, col, row2, col2) {

                // $('style#expat-style').html('\
                // .handsontableInput2 {\
                // min-width: ' + ($('td.current.highlight').width() + ((this.getColHeader().length - 1) == col ? 3 : 10)) + 'px !important;\
                // max-width: ' + ($('td.current.highlight').width() + ((this.getColHeader().length - 1) == col ? 3 : 10)) + 'px !important;\
                // height: ' + ($('td.current.highlight').height() + 2) + 'px !important;\
                // }\
                // ');

                // $('textarea.handsontableInput').addClass('handsontableInput2');
                // },
                // afterLoadData: function () {

                // var theads = $('thead', this.rootElement),
                // thead,
                // th,
                // ths;

                // for(var i = 0, theadsLength = theads.length; i < theadsLength; i++) {

                // thead = theads[i];

                // ths = $('th', theads[i]);

                // for(var j = 0, thsLength = ths.length; j < thsLength; j++) {

                // $(ths[j]).removeClass('sort_asc sort_desc');
                // }
                // }

                // for (var i = 0, theadsLength = theads.length; i < theadsLength; i++) {

                // th = $('th:nth-child(' + parseInt(this.sortColumn + 2, 10) + ')', theads[i]);

                // if(th)
                // $(th).addClass('sort_' + (this.sortOrder ? 'asc' : 'desc'));
                // }
                // },
                cells: function(row, col, prop) {

                    var s = this.instance.getSourceDataAtRow(row);
                    if (s[38] == 0)
                        this.renderer = function(instance, td, row, col, prop, value, cellProperties) {

                            Handsontable.renderers.TextRenderer.apply(this, arguments);
                            $(td).css('color', '#F89406');
                        };
                    if (col == 8 || col == 9)
                        this.renderer = function(instance, td, row, col, prop, value, cellProperties) {

                            Handsontable.renderers.TextRenderer.apply(this, arguments);
                            $(td).css('background-color', '#E8F5E2');
                        };
                    else if (col == 28 || col == 29)
                        this.renderer = function(instance, td, row, col, prop, value, cellProperties) {

                            Handsontable.renderers.TextRenderer.apply(this, arguments);
                            $(td).css('background-color', '#E2F4F5');
                        };
                    else if (col == 6)
                        this.renderer = function(instance, td, row, col, prop, value, cellProperties) {
                            switch (arguments[5]) {
                                case '1':
                                    arguments[5] = 'Single';
                                    break;
                                case '2':
                                    arguments[5] = 'Married';
                                    break;
                                case '3':
                                    arguments[5] = 'Widowed';
                                    break;
                                case '4':
                                    arguments[5] = 'Separated';
                                    break;
                                case '5':
                                    arguments[5] = 'Divorced';
                                    break;
                            }

                            Handsontable.renderers.AutocompleteRenderer.apply(this, arguments);
                        };
                    else if (col == 17)
                        this.renderer = function(instance, td, row, col, prop, value, cellProperties) {

                            switch (arguments[5]) {
                                case '1':

                                    arguments[5] = 'Probational';
                                    break;
                                case '2':

                                    arguments[5] = 'Regular';
                                    break;
                            }

                            Handsontable.renderers.AutocompleteRenderer.apply(this, arguments);
                        };
                    if (col > 31 && col < 34)
                        this.renderer = function(instance, td, row, col, prop, value, cellProperties) {

                            cellProperties.readOnly = false;
                            if (s[32] == null)
                                cellProperties.readOnly = true;
                            Handsontable.renderers.AutocompleteRenderer.apply(this, arguments);
                            if (col == 32 || col == 33) {
                                Handsontable.renderers.TextRenderer.apply(this, arguments);
                                $(td).css('background-color', '#E2F4F5');
                            }
                        };
                },
                colHeaders: [
                    'Employee ID',
                    'Family name',
                    'First name',
                    'Middle name',
                    'Nickname',
                    'Gender',
                    'Civil status',
                    'Nationality',
                    'Passport no.',
                    'Date of issue',
                    'Validity date',
                    'Birthdate',
                    'Job title',
                    'Department',
                    'LEC designation',
                    'DOC',
                    'Resigned date',
                    'Employment status',
                    'PSI mail',
                    'Personal email add',
                    'Blood type',
                    'Height',
                    'Weight',
                    'Phil add',
                    'Hometown add',
                    // 'Phil no.',
                    'Hometown no.',
                    'TIN no.',
                    'AEP no.',
                    'Validity date (from)',
                    'Validity date (to)',
                    'I card',
                    'CWV no',
                    'Validity date (from)',
                    'Validity date (to)',
                    'CWV Total',
                    'Remarks'
                ],
                columns: [
                    {
                        readOnly: true,
                        data: 1 //empid
                    },
                    {
                        data: 2 //familyname
                    },
                    {
                        data: 3 //fname
                    },
                    {
                        data: 4 //mname
                    },
                    {
                        data: 5 //nickname
                    },
                    {
                        type: 'dropdown',
                        source: ['M', 'F'],
                        allowInvalid: false,
                        data: 6 //gender
                    },
                    {
                        type: 'dropdown',
                        source: $.map($('select#emp-civil option'), function(a) {
                            return $(a).text();
                        }),
                        allowInvalid: false,
                        editor: CivilEditor,
                        data: 7 //civilstatus
                    },
                    {
                        type: 'autocomplete',
                        source: function(query, process) {
                            $.getJSON(
                                    base_url + 'employees/getExpatNationalities',
                                    {
                                        q: query
                                    },
                            function(data) {
                                process(data);
                            }
                            );
                        },
                        data: 8 //nationality
                    },
                    {
                        data: 9 //passport no.
                    },
                    {
                        type: 'date',
                        validator: dateValidator,
                        allowInvalid: false,
                        data: 10 //passport_validity
                    },
                    {
                        type: 'date',
                        validator: dateValidator,
                        allowInvalid: false,
                        data: 11 //passport_validity
                    },
                    {
                        type: 'date',
                        validator: dateValidator,
                        allowInvalid: false,
                        data: 12 //birthday
                    },
                    {
                        type: 'autocomplete',
                        source: function(query, process) {
                            $.getJSON(
                                    base_url + 'employees/getJobTitles',
                                    {
                                        q: query
                                    },
                            function(data) {
                                process(data);
                            }
                            );
                        },
                        data: 13 //job title
                    },
                    {
                        type: 'dropdown',
                        source: $.map($('ul#emp-filter-dept a:not(:last)'), function(a) {
                            return $(a).text();
                        }),
                        allowInvalid: false,
                        data: 14 //department
                    },
                    {
                        data: 15 //lec_designation
                    },
                    {
                        type: 'date',
                        validator: dateValidator,
                        allowInvalid: false,
                        data: 16 //commencement
                    },
                    {
                        type: 'date',
                        validator: dateValidator,
                        allowInvalid: false,
                        data: 17 //resign_date
                    },
                    {
                        type: 'dropdown',
                        source: ['Probational', 'Regular'],
                        allowInvalid: false,
                        editor: EmploymentEditor,
                        data: 18 //empstatus
                    },
                    {
                        data: 19 //psi_email
                    },
                    {
                        data: 20 //personal_email
                    },
                    {
                        data: 21 //bloodtype
                    },
                    {
                        data: 22 //height
                    },
                    {
                        data: 23 //weight
                    },
                    {
                        data: 24 //phil_add
                    },
                    {
                        data: 25 //hometown_add
                    },
                    {
                        data: 26 //hometown_no
                    },
                    {
                        data: 27 //tin
                    },
                    {
                        data: 28 //aep_no
                    },
                    {
                        type: 'date',
                        validator: dateValidator,
                        allowInvalid: false,
                        data: 29 //aep_validity
                    },
                    {
                        type: 'date',
                        validator: dateValidator,
                        allowInvalid: false,
                        data: 30 //aep_expired
                    },
                    {
                        data: 31 //i_card
                    },
                    {
                        data: 32 //cwv_no
                    },
                    {
                        type: 'date',
                        validator: dateValidator,
                        allowInvalid: false,
                        data: 33 //cwv_validity
                    },
                    {
                        type: 'date',
                        validator: dateValidator,
                        allowInvalid: false,
                        data: 34 //cwv_expired
                    },
                    {
                        type: 'numeric',
                        readOnly: true,
                        data: 35
                    },
                    {
                        data: 36 // remarks
                    }
                ],
                afterChange: function(changes, source) {

                    if (changes && source != 'loadData') {

                        var date_rows = [10, 11, 12, 16, 17, 29, 30, 33, 34];
                        if (jQuery.inArray(changes[0][1], date_rows) !== -1)
                            if (!moment($.trim(changes[0][3]), 'YYYY-MM-DD').isValid())
                                return false;
                        if (changes[0][1] == 32 && changes[0][2] !== null && changes[0][2].length && changes[0][3].length && !confirm("Press OK to Edit and Cancel to Create new CWS"))
                        {
                            changes[0][1] = 50;
                            expat_table_api.updateSettings({
                                cells: function(row, col, prop) {
                                    var s = this.instance.getSourceDataAtRow(row);
                                    if (col > 31 && col < 35)
                                        this.renderer = function(instance, td, row, col, prop, value, cellProperties) {
                                            cellProperties.readOnly = false;
                                            if (s[31] == null)
                                                cellProperties.readOnly = true;
                                            Handsontable.renderers.AutocompleteRenderer.apply(this, arguments);
                                        };
                                }
                            });
                        }
                        if (!$('input#expat-auto-save').prop('checked') && changes.length) {
                            var that = this,
                                    _data = {
                                        changes: new Array()
                                    };
                            if (updateexpat.length !== 0) {
                                var chck_chgs = false;
                                $.each(updateexpat, function(key, val) {
                                    if (val[1] == changes[0][0] && (parseInt(val[2]) + 1) == changes[0][1]) {
                                        updateexpat[key][4] = changes[0][3];
                                        chck_chgs = true;
                                    }
                                });
                                if (chck_chgs == false)
                                    $.each(changes, function(i, val) {

                                        var _val = val.slice();
                                        var s = that.getSourceDataAtRow(_val[0]);
                                        _val[1] = that.propToCol(_val[1]);
                                        _val.unshift(s[0]);
                                        updateexpat[updateexpat.length] = _val;
                                    });
                            }
                            if (updateexpat.length == 0) {

                                $.each(changes, function(i, val) {

                                    var _val = val.slice();
                                    var s = that.getSourceDataAtRow(_val[0]);
                                    _val[1] = that.propToCol(_val[1]);
                                    _val.unshift(s[0]);
                                    updateexpat[updateexpat.length] = _val;
                                });
                            }
                            //console.log(updateexpat);
                        }
                    }


                    if (changes && source != 'loadData' && $('input#expat-auto-save').prop('checked')) {

                        var that = this,
                                _data = {
                                    changes: new Array()
                                };
                        $.each(changes, function(i, val) {
                            var _val = val.slice();
                            var s = that.getSourceDataAtRow(_val[0]);
                            _val[1] = that.propToCol(_val[1]);
                            _val.unshift(s[0]);
                            _data.changes[_data.changes.length] = _val;
                        });
                        $('span.auto-save-message').html('Saving&hellip;');
                        $.ajaxq('expatqueue', {
                            url: base_url + 'employees/updateExpat',
                            dataType: 'json',
                            type: 'POST',
                            data: _data
                        }).done(function(data) {
                            var expat_extended_table_api = $('div#expat-extended-table').handsontable('getInstance');
                            $.getJSON(
                                    base_url + 'employees/getAllExpats_extended/' + ($('input#emp-display-inactive').prop('checked') ? '1' : '0') + '/' + ($('button#emp-filter-dept-btn').data('id') ? $('button#emp-filter-dept-btn').data('id') : 0 + '/' + $('select#emp-display-expat-page').val()),
                                    function(data) {

                                        var opts = '';
                                        for (var i = 0; i < Math.ceil(data.count / $('div.expat-extended-table-header select').val()); i++)
                                            opts += '<option>' + (i + 1) + '</option>';
                                        if (opts)
                                            $('select#emp-display-expat-page')
                                                    .html(opts)
                                                    .show();
                                        else
                                            $('select#emp-display-expat-page').hide();
                                        $('select#emp-display-expat-page').val(data.page);
                                        updateexpat = new Array();
                                        expat_extended_table_api.loadData(data.data);
                                    }
                            );
                            $('span.auto-save-message').text('Auto save (Last updated: ' + moment(data.updated, 'X').format('h:mm a') + ')');
                        });
                    }
                },
                beforeChange: function(changes, source) {

                    $.each(changes, function(i) {

                        if ($.trim(changes[i][2]) == $.trim(changes[i][3]))
                            changes.splice(i, 1);
                    });
                }
            })
            .hide();
    var expat_table_api = expat_table.handsontable('getInstance');
    // expat_table.handsontable('getInstance').addHook('afterInit', function () {

    // alert('')

    // });
    $("div#expat-save button").off("click").click(function(e) {
        e.preventDefault();
        $.ajaxq('expatqueue', {
            url: base_url + 'employees/updateExpat',
            dataType: 'json',
            type: 'POST',
            data: {changes: updateexpat}
        }).done(function(data) {
            var expat_extended_table_api = $('div#expat-extended-table').handsontable('getInstance');
            $.getJSON(
                    base_url + 'employees/getAllExpats_extended/' + ($('input#emp-display-inactive').prop('checked') ? '1' : '0') + '/' + ($('button#emp-filter-dept-btn').data('id') ? $('button#emp-filter-dept-btn').data('id') : 0),
                    function(data) {
                        var opts = '';
                        for (var i = 0; i < Math.ceil(data.count / $('div.expat-extended-table-header select').val()); i++)
                            opts += '<option>' + (i + 1) + '</option>';
                        if (opts)
                            $('select#emp-display-expat-page')
                                    .html(opts)
                                    .show();
                        else
                            $('select#emp-display-expat-page').hide();
                        updateexpat = new Array();
                        expat_extended_table_api.loadData(data.data);
                    }
            );
            $('span.auto-save-message').text('Successfully saved (Last updated: ' + moment(data.updated, 'X').format('h:mm a') + ')');
        });
    });
    $('input#expat-auto-save').change(function() {

        $('div#expat-save').toggleClass('hidden', $(this).is(':checked'));
    });
    $('button#emp-add-user').click(function() {

        var n = emp_table_api.rows().nodes(),
                s = $(':checked', n).length;
        $('div#emp-modal').data('mode', s > 1 ? 'multi' : 'add');
        $('button#emp-save').data('mode', s > 1 ? 'multi' : 'add');
        $('div#emp-modal div.modal-dialog').addClass('modal-lg');
        $('div#emp-modal div.modal-header h4').text(s > 1 ? 'Multiple edit' : 'Create account');
        $('div#emp-modal form').each(function() {

            this.reset();
        });
        $('div#emp-modal div.emp-update').show();
        $('img#avatar')[0].src = base_url + 'assets/avatars/default-avatar-male.jpg';
        $('div#emp-modal').modal('show');
    });
    window.onbeforeunload = function() {

        if ($.ajaxq.isRunning('expatqueue'))
            return 'Expat records are still being saved. Continue?';
    };
    $.fn.editable.defaults.mode = 'inline';
    $.fn.editableform.loading = "<div class='editableform-loading'><i class='ace-icon fa fa-spinner fa-spin fa-2x light-blue'></i></div>";
    $.fn.editableform.buttons = '<button type="button" class="btn btn-success" id="webcam" onclick="alert(\'Not yet implemented.\');"><i class="ace-icon fa fa-camera"></i></button>' +
            '<button type="submit" class="btn btn-info editable-submit"><i class="ace-icon fa fa-check"></i></button>' +
            '<button type="button" class="btn editable-cancel"><i class="ace-icon fa fa-times"></i></button>';
    // *** editable avatar *** //
    try {//ie8 throws some harmless exceptions, so let's catch'em

        //first let's add a fake appendChild method for Image element for browsers that have a problem with this
        //because editable plugin calls appendChild, and it causes errors on IE at unpredicted points
        try {
            document.createElement('IMG').appendChild(document.createElement('B'));
        } catch (e) {
            Image.prototype.appendChild = function(el) {
            }
        }

        var last_gritter
        $('img#avatar').editable({
            type: 'image',
            name: 'avatar',
            value: null,
            image: {
                //specify ace file input plugin's options here
                btn_choose: 'Change Photo',
                droppable: true,
                maxSize: 5000000,
                //and a few extra ones here
                name: 'avatar', //put the field name here as well, will be used inside the custom plugin
                on_error: function(error_type) {//on_error function will be called when the selected file has a problem
                    if (last_gritter)
                        $.gritter.remove(last_gritter);
                    if (error_type == 1) {//file format error
                        last_gritter = $.gritter.add({
                            title: 'File is not an image!',
                            text: 'Please choose a jpg|gif|png image!',
                            class_name: 'gritter-error gritter-center'
                        });
                    } else if (error_type == 2) {//file size rror
                        last_gritter = $.gritter.add({
                            title: 'File too big!',
                            text: 'Image size should not exceed 100Kb!',
                            class_name: 'gritter-error gritter-center'
                        });
                    }
                    else {//other error
                    }
                },
                on_success: function() {
                    $.gritter.removeAll();
                }
            },
            url: function(params) {
                // ***UPDATE AVATAR HERE*** //
                //for a working upload example you can replace the contents of this function with
                //examples/profile-avatar-update.js

                var deferred = new $.Deferred

                var value = $('#avatar').next().find('input[type=hidden]:eq(0)').val();
                if (!value || value.length == 0) {
                    deferred.resolve();
                    return deferred.promise();
                }


                //dummy upload
                setTimeout(function() {
                    if ("FileReader" in window) {
                        //for browsers that have a thumbnail of selected image
                        var thumb = $('#avatar').next().find('img').data('thumb');
                        if (thumb)
                            $('#avatar').get(0).src = thumb;
                    }

                    deferred.resolve({'status': 'OK'});
                    if (last_gritter)
                        $.gritter.remove(last_gritter);
                    last_gritter = $.gritter.add({
                        title: 'Photo Updated!',
                        text: 'You can now click Save to create the account, or go back to complete the other fields.',
                        class_name: 'gritter-info gritter-center'
                    });
                }, parseInt(Math.random() * 800 + 800))

                return deferred.promise();
                // ***END OF UPDATE AVATAR HERE*** //
            },
            success: function(response, newValue) {
            }
        })
    } catch (e) {
    }

    $('select#emp-sex').change(function() {

        if ($('img#avatar')[0].src.endsWith('default-avatar-male.jpg') || $('img#avatar')[0].src.endsWith('default-avatar-female.jpg'))
            $('img#avatar')[0].src = base_url + 'assets/avatars/default-avatar-' + ($(this).val() == '1' ? '' : 'fe') + 'male.jpg';
    });
    $('input#emp-bday')
            .datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'yyyy-mm-dd'
            })
            .next()
            .on('click', function() {

                $(this).prev().focus();
            });
    $('input#emp-doc-start, input#emp-doc-end, input#emp-effective-date').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: 'yyyy-mm-dd'
    });
    $('input#reg-status-txt').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: 'yyyy-mm-dd'
    });
    $('input#expat-search').keyup($.debounce(250, function() {

        expat_table_api.search.query(this.value);
        expat_table_api.render();
    }));
    $('select#emp-display-expat-page').change(function() {

        $('select#expat-search').val('');
        if ('expat_loading_timeout' in window)
            clearTimeout(window.expat_loading_timeout);
        window.expat_loading_timeout = setTimeout(function() {

            expat_table.block({
                message: $(new Spinner({color: '#438EB9'}).spin().el),
                css: {
                    border: 'none',
                    backgroundColor: 'transparent'
                },
                overlayCSS: {
                    backgroundColor: '#fff'
                },
                centerY: false,
                ignoreIfBlocked: true
            });
        }, 500);
        $.getJSON(
                base_url + 'employees/getAllExpats_extended/' + ($('input#emp-display-inactive').prop('checked') ? '1' : '0') + '/' + ($('button#emp-filter-dept-btn').data('id') ? $('button#emp-filter-dept-btn').data('id') : 0) + '/' + $(this).val() + '/' + $('div.expat-extended-table-header select').val(),
                function(data) {

                    clearTimeout(window.expat_loading_timeout);
                    expat_table.unblock();
                    if (data.count) {
                        updateexpat = new Array();
                        expat_table_api.loadData(data.data);
                    }
                }
        );
    });
    $('div.expat-extended-table-header select').change(function() {

        var that = this;
        $('select#expat-search').val('');
        if ('expat_loading_timeout' in window)
            clearTimeout(window.expat_loading_timeout);
        window.expat_loading_timeout = setTimeout(function() {

            expat_table.block({
                message: $(new Spinner({color: '#438EB9'}).spin().el),
                css: {
                    border: 'none',
                    backgroundColor: 'transparent'
                },
                overlayCSS: {
                    backgroundColor: '#fff'
                },
                centerY: false,
                ignoreIfBlocked: true
            });
        }, 500);
        $.getJSON(
                base_url + 'employees/getAllExpats_extended/' + ($('input#emp-display-inactive').prop('checked') ? '1' : '0') + '/' + ($('button#emp-filter-dept-btn').data('id') ? $('button#emp-filter-dept-btn').data('id') : 0) + '/' + $('select#emp-display-expat-page').val() + '/' + $(this).val(),
                function(data) {

                    clearTimeout(window.expat_loading_timeout);
                    if (data.count) {

                        var opts = '';
                        for (var i = 0; i < Math.ceil(data.count / $(that).val()); i++)
                            opts += '<option>' + (i + 1) + '</option>';
                        if (opts)
                            $('select#emp-display-expat-page')
                                    .html(opts)
                                    .show();
                        expat_table_api.loadData(data.data);
                    }
                }
        );
    });
    $("#report-access-add", 'div#emp-modal').click(function(e) {
        var error = 0;
        e.preventDefault();
        if ($("#emp_report", 'div#emp-modal').val() == "") {
            $("#emp_report", 'div#emp-modal').parent().addClass("has-error");
            error++;
        }

        if ($("#emp_access_dept", 'div#emp-modal').val() == "") {
            $("#emp_access_dept", 'div#emp-modal').parent().addClass("has-error");
            error++;
        }

        if ($("#emp_access_dept", 'div#emp-modal').val() == "") {
            $("#emp_access_dept", 'div#emp-modal').parent().addClass("has-error");
            error++;
        }


        if (error > 0)
            return false;
        var class_new = "row-" + $("#emp_report").val() + "-" + $("#emp_access_dept").val();
        if ($("." + class_new).length > 0) {
            $("#emp_report", 'div#emp-modal').parent().addClass("has-error");
            $("#emp_access_dept", 'div#emp-modal').parent().addClass("has-error");
            return false;
        }

        $("#emp_report", 'div#emp-modal').parent().removeClass("has-error");
        $("#emp_access_dept", 'div#emp-modal').parent().removeClass("has-error");
        var table_dtl = "<tr class='report_list row-" + $("#emp_report").val() + "-" + $("#emp_access_dept").val() + "' >\
						  <td>\
						    <input type='hidden' name='item_report[]' value='" + $("#emp_report").val() + "' \>\
							\
						    " + $("#emp_report option:selected").text() + "\
						  </td>\
						  <td>\
							<input type='hidden' name='item_dept[]' value='" + $("#emp_access_dept").val() + "' \>\
						    " + $("#emp_access_dept option:selected").text() + "\
						  </td>\
						  <td class='center'>\
							<span class='label label-warning'>\
							  <i class='fa fa-flag-o'></i>  Pending for Saving\
							</span>\
						  </td>\
						  <td class='center'>\
							<a class='red approver-remove' href='#'>\
							  <i class='ace-icon fa fa-trash-o bigger-130'></i>\
							</a>\
						  </td>\
						</tr>";
        if ($("#no_report_module", "#reports_module_tbl").html() == undefined)
        {
            $("#reports_module_tbl", 'div#emp-modal').append(table_dtl);
        }
        else
        {
            $("#reports_module_tbl", 'div#emp-modal').html(table_dtl);
        }

//$("#emp_report option[value='"+$("#emp_report").val()+"']","div#emp-modal").attr("disabled",true);
        $("#emp_report", 'div#emp-modal').val("");
        //$("#emp_access_dept option[value='"+$("#emp_access_dept").val()+"']","div#emp-modal").attr("disabled",true);
        $("#emp_access_dept", 'div#emp-modal').val("");
        //$("#approver_lvl",'div#emp-modal').val("1");

        $("#emp_report", 'div#emp-modal').trigger("chosen:updated");
        $("#emp_access_dept", 'div#emp-modal').trigger("chosen:updated");
    });
    $(document).off("click", "a.approver-remove").on("click", "a.approver-remove", function(e) {
        e.preventDefault();
        if (typeof $(this).data("id") !== "undefined")
            if ($.inArray($(this).data("id"), approver_del_arr) == -1)
                approver_del_arr.push($(this).data("id"));
        $("#emp_report option[value='" + $(this).data("id") + "']", "div#emp-modal").removeAttr("disabled");
        $("#emp_access_dept option[value='" + $(this).data("id") + "']", "div#emp-modal").removeAttr("disabled");
        $(this).parent().parent().remove();
        if ($("#reports_module_tbl").html() == "")
            $("#reports_module_tbl").html("<tr id='no_report_module'><td colspan='4' class='center'>No Record Found</td></tr>");
        $("#emp_report", 'div#emp-modal').trigger("chosen:updated");
        $("#emp_access_dept", 'div#emp-modal').trigger("chosen:updated");
    });
    $('.chosen-select').chosen({allow_single_deselect: true, width: "100%"});
    //$('.chosen-select','div#apprv-grps-modal').trigger("chosen:updated");



});
function setExpatexpiration() {

    $('ul#emp-filter-nation li').removeClass('active');
    $('ul#emp-filter-nation li:eq(2)').addClass('active');
    //$('button#emp-filter-nation-btn').data('id', 1);
    $('div#expat-switch').removeClass('hidden');
    $('button#emp-filter-nation-btn').html('\
			<i class="ace-icon fa fa-filter"></i> Nationality: ' + 'Expat' + '\
			<i class="ace-icon fa fa-chevron-down icon-on-right"></i>\
		');
    updateexpat = new Array();
    $('div#expat-page').toggleClass('hidden', false);
    $('div#expat-auto-save-container').toggleClass('hidden', false);
    $('div.expat-extended-table-header select')[0].selectedIndex = 0;
    $('input#expat-search').val('');
    $('input#emp-display-expat').prop('checked', true);
    var expat_extended_table_api = $('div#expat-extended-table').handsontable('getInstance');
    $.getJSON(
            base_url + 'employees/getAllExpats_expired/',
            function(data) {

                if (data.count > 0) {
                    var opts = '';
                    for (var i = 0; i < Math.ceil(data.count / $('div.expat-extended-table-header select').val()); i++)
                        opts += '<option>' + (i + 1) + '</option>';
                    if (opts)
                        $('select#emp-display-expat-page')
                                .html(opts)
                                .show();
                    expat_extended_table_api.loadData(data.data);
                }
            }
    );
    $('input#expat-auto-save').prop('checked', true);
    $('div#expat-extended-table, div.expat-extended-table-header').show();
    $('div#emp-table_wrapper').hide();
}