$(function() {
    var processing = false;
    var cws_processing = false;
    var approval_table = $('table#approval-table').dataTable({
        serverSide: true,
        ajax: {
            url: base_url + 'timekeeping/getAllForApproval',
            type: "POST",
            data: function(d) {
                d.status = $('button#tk-sched-filter-status-btn').data('id');
            }
        },
        deferRender: true,
        autoWidth: false,
        method: "post",
        columns: [
            {
                orderable: false,
                data: "approval_id",
                render: function(d) {
                    return '<label class="position-relative"><input type="checkbox" class="ace" value="' + d + '" /><span class="lbl"></span></label>';
                },
                className: 'center'
            },
            {data: "group_code"},
            {data: "period"},
            {data: "created_datetime"},
            {data: "sender"},
            {data: "approver"},
            {
                data: "status_lbl",
                render: function(d, t, r, m) {
                    var label = "";
                    switch (d) {
                        case "Pending":
                            label = '<span class="label label-info arrowed">' + d + '</span>';
                            break;
                        case "Submitted":
                            label = '<span class="label label-warning arrowed">For Approval</span>';
                            break;
                        case "Rejected":
                            label = '<span class="label label-danger arrowed">' + d + '</span>';
                            break;
                        case "Approved":
                            label = '<span class="label label-success arrowed">' + d + '</span>';
                            break;
                    }
                    return label;
                }
            },
            {
                orderable: false,
                data: "approval_id",
                render: function(d, t, r, m) {
                    return '\
			  <div class="hidden-sm hidden-xs action-buttons">\
			    <a class="blue download-file" href="' + base_url + r.file_path + '" download="' + r.org_file + '" title="Download">\
				  <i class="ace-icon fa fa-download bigger-130"></i>\
				</a>\
				' + ((r.status == 1 && r.approved_level == r.user_level) ? '<a class="green upload-approve" href="" title="Approve"><i class="ace-icon fa fa-check bigger-130"></i></a>' : '') + '\
				' + ((r.status == 1 && r.approved_level == r.user_level) ? '<a class="red upload-reject" href="#" title="Reject"><i class="ace-icon fa fa-remove bigger-130"></i></a>' : '') + '\
				' + ((r.status > 0) ? '<a class="blue upload-view-hist" href="#" title="View History"><i class="ace-icon fa fa-search bigger-130"></i></a>' : '') + '\
			  </div>\
			  <div class="hidden-md hidden-lg">\
				<div class="inline position-relative">\
				  <button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown" data-position="auto">\
				    <i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>\
				  </button>\
				  <ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">\
				    <li>\
					  <a href="#" class="tooltip-info download-file" data-rel="tooltip" href="' + base_url + r.file_path + '" download="' + r.org_file + '" title="Download">\
					    <span class="blue">\
						  <i class="ace-icon fa fa-download bigger-120"></i>\
						</span>\
					  </a>\
					</li>\
					' + ((r.status == 1 && r.approved_level == r.user_level) ? '\
				    <li>\
					  <a href="#" class="tooltip-success upload-approve" data-rel="tooltip" title="Approve">\
					    <span class="green">\
						  <i class="ace-icon fa fa-check bigger-120"></i>\
						</span>\
					  </a>\
					</li>\
					\ ' : '') + '\
					' + ((r.status == 1 && r.approved_level == r.user_level) ? '\
					<li>\
					  <a href="#" class="tooltip-error upload-reject" data-rel="tooltip" title="Reject">\
						<span class="red">\
						  <i class="ace-icon fa fa-remove bigger-120"></i>\
						</span>\
					  </a>\
					</li>\
					\ ' : '') + '\
					' + ((r.status > 0) ? '\
					<li>\
					  <a href="#" class="tooltip-info upload-view-hist" data-rel="tooltip" title="View History">\
						<span class="blue">\
						  <i class="ace-icon fa fa-search bigger-120"></i>\
						</span>\
					  </a>\
					</li>\
					\ ' : '') + '\
				  </ul>\
				</div>\
			  </div>\
		    ';
                },
                className: 'center no-highlight'
            }
        ],
        order: [[1, 'asc']],
        rowCallback: function(r, d) {
            $('a.upload-approve', r).click(function(e) {
                e.preventDefault();
                $('button.modal-action-btn', "div#approval-modal").data({level: d["approved_level"], id: d["approval_id"], grp_id: d["apprv_grp_id"], upload: d["upload_id"], label: "[" + d["group_code"] + "] " + d["period"], action: "approve"});
                $("p#approval-remarks_lbl", "div#approval-modal").hide();
                $('textarea#approval-remarks', "div#approval-modal").hide();
                $('div#approval-modal').modal('show');
            });
            $('a.upload-reject', r).click(function(e) {
                e.preventDefault();
                $('button.modal-action-btn', "div#approval-modal").data({id: d["approval_id"], grp_id: d["apprv_grp_id"], upload: d["upload_id"], label: "[" + d["group_code"] + "] " + d["period"], action: "reject"});
                $("p#approval-remarks_lbl", "div#approval-modal").show();
                $('textarea#approval-remarks', "div#approval-modal").show();
                $('div#approval-modal').modal('show');
            });
            $('a.upload-view-hist', r).click(function(e) {
                e.preventDefault();
                $.post(
                        base_url + "timekeeping/getUploadSchedHistory",
                        {id: d["upload_id"]},
                function(response) {
                    if (response.remarks.length) {
                        $("#approval_list").html("");
                        $.each(response.remarks, function(i, v) {
                            var escaped = $("<span/>").text(v.remarks).html();
                            v.remarks = escaped;
                            switch (v.status * 1) {
                                case 1:
                                    $("#approval_list").append("<div class='well well-sm alert-warning'><b><i>" + v.created_datetime + "</i></b><br/>" + (v.remarks.length ? v.remarks + " - " : "") + v.mb_nick + "</div>");
                                    break;
                                case 2:
                                    $("#approval_list").append("<div class='well well-sm alert-danger'><b><i>" + v.created_datetime + "</i></b><br/>" + (v.remarks.length ? v.remarks + " - " : "") + v.mb_nick + "</div>");
                                    break;
                                case 3:
                                    $("#approval_list").append("<div class='well well-sm alert-success'><b><i>" + v.created_datetime + "</i></b><br/>" + (v.remarks.length ? v.remarks + " - " : "") + v.mb_nick + "</div>");
                                    break;
                                case 4:
                                    $("#approval_list").append("<div class='well well-sm'><b><i>" + v.created_datetime + "</i></b><br/>" + (v.remarks.length ? v.remarks + " - " : "") + v.mb_nick + "</div>");
                                    break;
                            }
                        });
                    }
                    else {
                        $("#approval_list").html("None");
                    }
                },
                        'json'
                        );
                $('div#history-modal').modal('show');
            });
        }
    }),
            approval_table_api = approval_table.api();

    $('div#approval-modal')
            .modal({
                backdrop: 'static',
                show: false
            })
            .off('show.bs.modal')
            .on('show.bs.modal', function() {
                $("#modal-target-lbl").html($('button.modal-action-btn', "div#approval-modal").data("label"));
                $("#modal-action").html($('button.modal-action-btn', "div#approval-modal").data("action"));
            });

    $('button.modal-action-btn', "div#approval-modal").off("click").click(function(e) {
        e.preventDefault();
        if (processing)
            return false;
        processing = true;

        $("#approval-modal button.btn-success").hide();
        $("#approval-modal button.btn-danger").hide();

        if ($(this).data("action") == "reject") {
            $.post(
                    base_url + "timekeeping/rejectSchedule",
                    {upload_id: $('button.modal-action-btn', "div#approval-modal").data("upload"), remarks: $('textarea#approval-remarks', "div#approval-modal").val()},
            function(response) {
                processing = false;
                $("#approval-modal button.btn-success").show();
                $("#approval-modal button.btn-danger").show();
                $("div.alert-success", "div.approval-list-widget").addClass("hidden");
                $("#approval-list-success-msg", "div.approval-list-widget").html("");
                $("div.alert-danger", "div.approval-list-widget").addClass("hidden");
                $("#approval-list-err-msg", "div.approval-list-widget").html("");

                if (response.success) {
                    $("div.alert-success", "div.approval-list-widget").removeClass("hidden");
                    $("#approval-list-success-msg", "div.approval-list-widget").html(response.msg);
                    setTimeout(function() {
                        $("div.alert-success", "div.approval-list-widget").addClass("hidden");
                    }, 3000);
                }
                else {
                    $("div.alert-danger", "div.approval-list-widget").removeClass("hidden");
                    $("#approval-list-err-msg", "div.approval-list-widget").html(response.msg);
                    setTimeout(function() {
                        $("div.alert-danger", "div.approval-list-widget").addClass("hidden");
                    }, 3000);
                }
                $('div#approval-modal').modal('hide');
                approval_table_api.ajax.reload(null, false);
            },
                    "json"
                    );
        }
        else if ($(this).data("action") == "approve") {
            $.post(
                    base_url + "timekeeping/approveSchedule",
                    {
                        upload_id: $('button.modal-action-btn', "div#approval-modal").data("upload"),
                        approval_id: $('button.modal-action-btn', "div#approval-modal").data("id"),
                        approval_level: $('button.modal-action-btn', "div#approval-modal").data("level"),
                        grp_id: $('button.modal-action-btn', "div#approval-modal").data("grp_id")
                    },
            function(response) {
                processing = false;
                $("#approval-modal button.btn-success").show();
                $("#approval-modal button.btn-danger").show();
                $("div.alert-success", "div.approval-list-widget").addClass("hidden");
                $("#approval-list-success-msg", "div.approval-list-widget").html("");
                $("div.alert-danger", "div.approval-list-widget").addClass("hidden");
                $("#approval-list-err-msg", "div.approval-list-widget").html("");

                if (response.success) {
                    $("div.alert-success", "div.approval-list-widget").removeClass("hidden");
                    $("#approval-list-success-msg", "div.approval-list-widget").html(response.msg);
                    setTimeout(function() {
                        $("div.alert-success", "div.approval-list-widget").addClass("hidden");
                    }, 3000);
                }
                else {
                    $("div.alert-danger", "div.approval-list-widget").removeClass("hidden");
                    $("#approval-list-err-msg", "div.approval-list-widget").html(response.msg);
                    setTimeout(function() {
                        $("div.alert-danger", "div.approval-list-widget").addClass("hidden");
                    }, 3000);
                }
                $('div#approval-modal').modal('hide');
                approval_table_api.ajax.reload(null, false);
            },
                    "json"
                    );
        }
    });

    var change_sched_table = $('table#change-sched-table').dataTable({
        serverSide: true,
        ajax: {
            url: base_url + 'timekeeping/getAllChangeScheduleForApproval',
            type: "POST",
            data: function(d) {
                d.status = $('button#tk-cws-filter-status-btn').data('id');
            }
        },
        deferRender: true,
        autoWidth: false,
        method: "post",
        columns: [
            {
                orderable: false,
                data: "approval_id",
                render: function(d) {
                    return '<label class="position-relative"><input type="checkbox" class="ace" value="' + d + '" /><span class="lbl"></span></label>';
                },
                className: 'center'
            },
            {data: "mb_id"},
            {data: "date_from"},
            {data: "date_to"},
            {data: "sender"},
            {data: "approver"},
            {
                data: "status_lbl",
                render: function(d, t, r, m) {
                    var label = "";
                    switch (d) {
                        case "Pending":
                            label = '<span class="label label-info arrowed">' + d + '</span>';
                            break;
                        case "Submitted":
                            label = '<span class="label label-warning arrowed">For Approval</span>';
                            break;
                        case "Rejected":
                            label = '<span class="label label-danger arrowed">' + d + '</span>';
                            break;
                        case "Approved":
                            label = '<span class="label label-success arrowed">' + d + '</span>';
                            break;
                    }
                    return label;
                }
            },
            {
                orderable: false,
                data: "approval_id",
                render: function(d, t, r, m) {
                    return '\
			  <div class="hidden-sm hidden-xs action-buttons">\
			    <a class="blue request-view" href="#" title="View"><i class="ace-icon fa fa-search bigger-130"></i></a>\
				' + ((r.status == 1 && r.approved_level == r.user_level) ? '<a class="green change-approve" href="#" title="Approve"><i class="ace-icon fa fa-check bigger-130"></i></a>' : '') + '\
				' + ((r.status == 1 && r.approved_level == r.user_level) ? '<a class="red change-reject" href="#" title="Reject"><i class="ace-icon fa fa-remove bigger-130"></i></a>' : '') + '\
			  </div>\
			  <div class="hidden-md hidden-lg">\
				<div class="inline position-relative">\
				  <button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown" data-position="auto">\
				    <i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>\
				  </button>\
				  <ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">\
				    <li>\
					  <a href="#" class="tooltip-info request-view" data-rel="tooltip" title="View">\
					    <span class="blue">\
						  <i class="ace-icon fa fa-search bigger-120"></i>\
						</span>\
					  </a>\
					</li>\
					' + ((r.status == 1 && r.approved_level == r.user_level) ? '\
				    <li>\
					  <a href="#" class="tooltip-success change-approve" data-rel="tooltip" title="Approve">\
					    <span class="green">\
						  <i class="ace-icon fa fa-check bigger-120"></i>\
						</span>\
					  </a>\
					</li>\
					\ ' : '') + '\
					' + ((r.status == 1 && r.approved_level == r.user_level) ? '\
					<li>\
					  <a href="#" class="tooltip-error change-reject" data-rel="tooltip" title="Reject">\
						<span class="red">\
						  <i class="ace-icon fa fa-remove bigger-120"></i>\
						</span>\
					  </a>\
					</li>\
					\ ' : '') + '\
				  </ul>\
				</div>\
			  </div>\
		    ';
                },
                className: 'center no-highlight'
            }
        ],
        order: [[1, 'asc']],
        rowCallback: function(r, d) {
            $(document).off("click", ".request-view");
            $('a.request-view', r).click(function(e) {
                e.preventDefault();
                $("input, textarea, select", 'div#request-modal').val("").prop("disabled", true);
                $("#shift-str", 'div#request-modal').html("[N/A] - N/A");

                $("#cws-requester").html("");
                $("#cws-approve").removeData().hide();
                $("#cws-reject").removeData().hide();

                $.post(
                        base_url + "timekeeping/getChangeShift",
                        {request_id: d["cs_req_id"]},
                function(response) {
                    $("#request-id").val(response.data[0].cs_req_id);
                    $("#att-date-from").val(response.data[0].att_date_from);
                    $("#att-date-to").val(response.data[0].att_date_to);
                    $("#shift-str").html(response.data[0].orig_shift);
                    $("#orig-shift-str").val(response.data[0].orig_shift);
                    $("#orig-shift-ids-str").val(response.data[0].orig_shift_ids);
                    $("#new-shift").val(response.data[0].proposed_shift_id);
                    $("#reason").val(response.data[0].reason);
                    $("#cws-requester").html(d["sender"]);
                    if (response.data[0].status == 1 && d["approved_level"] == d["user_level"]) {
                        $("#cws-approve").data({level: d["approved_level"], id: d["approval_id"], grp_id: d["apprv_grp_id"], change: d["cs_req_id"], label: "[" + d["sender"] + "] CWS on " + d["date_from"] + (d["date_from"] != d["date_to"] ? " up to " + d["date_to"] : ""), action: "approve"}).show();
                        $("#cws-reject").data({id: d["approval_id"], grp_id: d["apprv_grp_id"], change: d["cs_req_id"], label: "[" + d["sender"] + "] CWS on " + d["date_from"] + (d["date_from"] != d["date_to"] ? " up to " + d["date_to"] : ""), action: "reject"}).show();
                    }
                    if (response.remarks.length) {
                        $("#cws_approval_list").html("");
                        $.each(response.remarks, function(i, v) {
                            $.each(response.remarks, function(i, v) {
                                var escaped = $("<span/>").text(v.remarks).html();
                                v.remarks = escaped;
                                switch (v.status * 1) {
                                    case 1:
                                        $("#cws_approval_list").append("<div class='well well-sm alert-warning'><b><i>" + v.created_datetime + "</i></b><br/>" + (v.remarks.length ? v.remarks + " - " : "") + v.mb_nick + "</div>");
                                        break;
                                    case 2:
                                        $("#cws_approval_list").append("<div class='well well-sm alert-danger'><b><i>" + v.created_datetime + "</i></b><br/>" + (v.remarks.length ? v.remarks + " - " : "") + v.mb_nick + "</div>");
                                        break;
                                    case 3:
                                        $("#cws_approval_list").append("<div class='well well-sm alert-success'><b><i>" + v.created_datetime + "</i></b><br/>" + (v.remarks.length ? v.remarks + " - " : "") + v.mb_nick + "</div>");
                                        break;
                                    case 4:
                                        $("#cws_approval_list").append("<div class='well well-sm'><b><i>" + v.created_datetime + "</i></b><br/>" + (v.remarks.length ? v.remarks + " - " : "") + v.mb_nick + "</div>");
                                        break;
                                }
                            });
                        });
                    }
                    else {
                        $("#cws_approval_list").html("None");
                    }
                },
                        'json'
                        );
                $('div#request-modal').modal('show');
            });
            $('a.change-approve', r).click(function(e) {
                e.preventDefault();
                $('button.change-modal-action-btn', "div#change-approval-modal").data({level: d["approved_level"], id: d["approval_id"], grp_id: d["apprv_grp_id"], change: d["cs_req_id"], label: "[" + d["sender"] + "] CWS on " + d["date_from"] + (d["date_from"] != d["date_to"] ? " up to " + d["date_to"] : ""), action: "approve"});
                $("p#remarks_lbl", "div#change-approval-modal").hide();
                $('textarea#change-remarks', "div#change-approval-modal").val("").hide();
                $('div#change-approval-modal').modal('show');
            });
            $('a.change-reject', r).click(function(e) {
                e.preventDefault();
                $('button.change-modal-action-btn', "div#change-approval-modal").data({id: d["approval_id"], grp_id: d["apprv_grp_id"], change: d["cs_req_id"], label: "[" + d["sender"] + "] CWS on " + d["date_from"] + (d["date_from"] != d["date_to"] ? " up to " + d["date_to"] : ""), action: "reject"});
                $("p#remarks_lbl", "div#change-approval-modal").show();
                $('textarea#change-remarks', "div#change-approval-modal").val("").show();
                $('div#change-approval-modal').modal('show');
            });
        }
    }),
            change_sched_table_api = change_sched_table.api();

    $('div#change-approval-modal')
            .modal({
                backdrop: 'static',
                show: false
            })
            .off('show.bs.modal')
            .on('show.bs.modal', function() {
                $("#change-modal-target-lbl").html($('button.change-modal-action-btn', "div#change-approval-modal").data("label"));
                $("#change-modal-action").html($('button.change-modal-action-btn', "div#change-approval-modal").data("action"));
            });

    $('button.change-modal-action-btn', "div#change-approval-modal").off("click").click(function(e) {
        e.preventDefault();
        if (cws_processing)
            return false;
        cws_processing = true;

        $("#change-approval-modal button.btn-success").hide();
        $("#change-approval-modal button.btn-danger").hide();

        if ($(this).data("action") == "reject") {
            $.post(
                    base_url + "timekeeping/rejectChangeSchedule",
                    {request_id: $('button.change-modal-action-btn', "div#change-approval-modal").data("change"), remarks: $('textarea#change-remarks', "div#change-approval-modal").val()},
            function(response) {
                cws_processing = false;
                $("#change-approval-modal button.btn-success").show();
                $("#change-approval-modal button.btn-danger").show();
                $("div.alert-success", "div.change-sched-list-widget").addClass("hidden");
                $("#change-sched-list-success-msg", "div.change-sched-list-widget").html("");
                $("div.alert-danger", "div.change-sched-list-widget").addClass("hidden");
                $("#change-sched-list-err-msg", "div.change-sched-list-widget").html("");

                if (response.success) {
                    $("div.alert-success", "div.change-sched-list-widget").removeClass("hidden");
                    $("#change-sched-list-success-msg", "div.change-sched-list-widget").html(response.msg);
                    setTimeout(function() {
                        $("div.alert-success", "div.change-sched-list-widget").addClass("hidden");
                    }, 3000);
                }
                else {
                    $("div.alert-danger", "div.change-sched-list-widget").removeClass("hidden");
                    $("#change-sched-list-err-msg", "div.change-sched-list-widget").html(response.msg);
                    setTimeout(function() {
                        $("div.alert-danger", "div.change-sched-list-widget").addClass("hidden");
                    }, 3000);
                }
                $('div#change-approval-modal').modal('hide');
                change_sched_table_api.ajax.reload(null, false);
            },
                    "json"
                    );
        }
        else if ($(this).data("action") == "approve") {
            $.post(
                    base_url + "timekeeping/approveChangeSchedule",
                    {
                        request_id: $('button.change-modal-action-btn', "div#change-approval-modal").data("change"),
                        approval_id: $('button.change-modal-action-btn', "div#change-approval-modal").data("id"),
                        approval_level: $('button.change-modal-action-btn', "div#change-approval-modal").data("level"),
                        grp_id: $('button.change-modal-action-btn', "div#change-approval-modal").data("grp_id")
                    },
            function(response) {
                cws_processing = false;
                $("#change-approval-modal button.btn-success").show();
                $("#change-approval-modal button.btn-danger").show();
                $("div.alert-success", "div.change-sched-list-widget").addClass("hidden");
                $("#change-sched-list-success-msg", "div.change-sched-list-widget").html("");
                $("div.alert-danger", "div.change-sched-list-widget").addClass("hidden");
                $("#change-sched-list-err-msg", "div.change-sched-list-widget").html("");

                if (response.success) {
                    $("div.alert-success", "div.change-sched-list-widget").removeClass("hidden");
                    $("#change-sched-list-success-msg", "div.change-sched-list-widget").html(response.msg);
                    setTimeout(function() {
                        $("div.alert-success", "div.change-sched-list-widget").addClass("hidden");
                    }, 3000);
                }
                else {
                    $("div.alert-danger", "div.change-sched-list-widget").removeClass("hidden");
                    $("#change-sched-list-err-msg", "div.change-sched-list-widget").html(response.msg);
                    setTimeout(function() {
                        $("div.alert-danger", "div.change-sched-list-widget").addClass("hidden");
                    }, 3000);
                }
                $('div#change-approval-modal').modal('hide');
                change_sched_table_api.ajax.reload(null, false);
            },
                    "json"
                    );
        }
    });

    $('div#history-modal')
            .modal({
                backdrop: 'static',
                show: false
            })
            .off('show.bs.modal')
            .on('show.bs.modal', function() {
            });

    $('ul#tk-cws-filter-status a').off("click").click(function(e) {
        e.preventDefault();
        $('ul#tk-cws-filter-status li').removeClass('active');
        $(this).parent().addClass('active');

        $('button#tk-cws-filter-status-btn').html('\
	    <i class="ace-icon fa fa-filter"></i> Status: ' + $(this).text() + '\
	    <i class="ace-icon fa fa-chevron-down icon-on-right"></i>\
	  ');

        if ($(this).data('id'))
            $('button#tk-cws-filter-status-btn').data('id', $(this).data('id'));
        else
            $('button#tk-cws-filter-status-btn').data('id', 0);
    });

    $('button#tk-cws-search-btn').off("click").click(function(e) {
        change_sched_table_api.ajax.reload(null, true);
    });

    $('ul#tk-sched-filter-status a').off("click").click(function(e) {
        e.preventDefault();
        $('ul#tk-sched-filter-status li').removeClass('active');
        $(this).parent().addClass('active');

        $('button#tk-sched-filter-status-btn').html('\
	    <i class="ace-icon fa fa-filter"></i> Status: ' + $(this).text() + '\
	    <i class="ace-icon fa fa-chevron-down icon-on-right"></i>\
	  ');

        if ($(this).data('id'))
            $('button#tk-sched-filter-status-btn').data('id', $(this).data('id'));
        else
            $('button#tk-sched-filter-status-btn').data('id', 0);
    });

    $('button#tk-sched-search-btn').off("click").click(function(e) {
        approval_table_api.ajax.reload(null, true);
    });

    $("#cws-approve").off("click").click(function(e) {
        e.preventDefault();
        $('div#request-modal').modal('hide');
        $('button.change-modal-action-btn', "div#change-approval-modal").data($(this).data());
        $("p#remarks_lbl", "div#change-approval-modal").hide();
        $('textarea#change-remarks', "div#change-approval-modal").val("").hide();
        $('div#change-approval-modal').modal('show');
    });

    $("#cws-reject").off("click").click(function(e) {
        e.preventDefault();
        $('div#request-modal').modal('hide');
        $('button.change-modal-action-btn', "div#change-approval-modal").data($(this).data());
        $("p#remarks_lbl", "div#change-approval-modal").show();
        $('textarea#change-remarks', "div#change-approval-modal").val("").show();
        $('div#change-approval-modal').modal('show');
    });

});