var inputfile = "";
$(function() {
    var attendance_tbl = $("#tk-attendance-table");
    var limit_default = 15;
    var loading = false;
    var current_page = 1;

    $("#tk-search-btn").off("click").click(function(e) {
        e.preventDefault();
        getSchedules(1);
    });

    $('.chosen-select').chosen({allow_single_deselect: true, width: "100%"});
    $('.chosen-select').trigger("chosen:updated");

    $('ul#tk-filter-dept a').off("click").click(function(e) {
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

    $('.input-daterange').datepicker({autoclose: true, format: "yyyy-mm-dd"});

    $('ul#tk-filter-le a').off("click").click(function(e) {
        e.preventDefault();
        $('ul#tk-filter-le li').removeClass('active');
        $(this).parent().addClass('active');

        $('button#tk-filter-le-btn').html('\
	  <i class="ace-icon fa fa-filter"></i> Type: ' + $(this).text() + '\
	  <i class="ace-icon fa fa-chevron-down icon-on-right"></i>\
	');

        if ($(this).data('id'))
            $('button#tk-filter-le-btn').data('id', $(this).data('id'));
        else
            $('button#tk-filter-le-btn').removeData('id');
    });

    $(document).off("click", "div#tk-attendance-pager ul.pagination li a").on("click", "div#tk-attendance-pager ul.pagination li a", function(e) {
        e.preventDefault();
        getSchedules($(this).data("page"));
    });

    getSchedules(1);

    $(document).off("submit", "#exportForm").on("submit", "#exportForm", function(e) {
        $("input[name='export-dept']").val($('button#tk-filter-dept-btn').data('id'));
        $("input[name='export-emp']").val($('select#tk-filter-emp').val());
        $("input[name='export-from']").val($('#date-from').val());
        $("input[name='export-to']").val($('#date-to').val());
        $("input[name='export-type']").val($('button#tk-filter-le-btn').data('id'));
    });
    var editablerow = function(instance, td, row, col, prop, value, cellProperties) {
        Handsontable.renderers.TextRenderer.apply(this, arguments);
        td.style.color = "blue";
    }
    function getSchedules(page_num) {
        if (loading) {
            return false;
        }
        $("#tk-table-loader").removeClass("hidden");
        $("#tk-table-no-record").addClass("hidden");
        attendance_tbl.addClass("hidden");
        loading = true;
        current_page = page_num;
        $.ajax({
            url: base_url + "attendance/getAllAttendance",
            data: {limit: limit_default, page: page_num, department: $('button#tk-filter-dept-btn').data('id'), emp: $('select#tk-filter-emp').val(), dateFrom: $('#date-from').val(), dateTo: $('#date-to').val(), type: $('button#tk-filter-le-btn').data('id')},
            cache: false,
            dataType: "json",
            type: "post",
            success: function(response) {
                $("#tk-table-loader").addClass("hidden");
                if (response.total_count) {
                    $("#tk-table-no-record").addClass("hidden");
                    attendance_tbl.removeClass("hidden");
                    attendance_tbl.handsontable({
                        data: response.data,
                        width: "100%",
                        colHeaders: response.header,
                        colWidths: response.width,
                        fixedColumnsLeft: 3,
                        columns: [{}, {}, {}, {}, {}, {}, {}, {}, {}, {}, {}, {}, {}, {}, {}, {
                                renderer: editRenderer
                            }],
                        cells: function(row, col, prop) {
                            var cellProperties = {};
                            cellProperties.readOnly = true;
                            if (col > 1)
                                cellProperties.className = "htCenter";
                            return cellProperties;
                        }
                    });
                }
                else {

                    $("#tk-table-no-record").removeClass("hidden");
                    attendance_tbl.addClass("hidden");
                }
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
                $("#tk-attendance-pager").html(pagination_lbl);
                loading = false;
            }
        });
    }
    function strip_tags(input, allowed) {
        var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
                commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;

        // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
        allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');

        return input.replace(commentsAndPhpTags, '').replace(tags, function($0, $1) {
            return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
        });
    }

    function editRenderer(instance, td, row, col, prop, value, cellProperties) {
        if (value !== "") {
            var s = instance.getSourceDataAtRow(row);
            var escaped = Handsontable.helper.stringify(value);
            var nodata = (escaped == "0") ? ' data-mb_id="' + s[0] + '" data-att_date="' + s[3] + '" ' : '';
            var tagging = "";
            $.post(
                    base_url + 'attendance/AttendanceDetails/',
                    {'updateAttendance': escaped, rowdata: s},
            function(db) {
                if (db.chg !== undefined && db.chg.length > 0) {
                    $.each(db.chg, function(key, val) {
                        if (val.att_status == "0") {
                            tagging += '<a data-att_id="' + val.id + '"  class="red attendance-show" href="#"><i class="ace-icon fa fa-search bigger-130"></i></a> &nbsp;';
                            if (db.chg.length == 1)
                                tagging += '<a data-att_id="' + escaped + '" ' + nodata + ' class="green attendance-edit" href="#"><i class="ace-icon fa fa-pencil-square-o bigger-130"></i></a>';
                        }
                        else if (val.att_status == "1") {
                            tagging += '<a data-att_id="' + val.id + '" class="green attendance-show" href="#"><i class="ace-icon fa fa-search bigger-130"></i></a>';
                            if (db.chg.length == 1 && (db.att.actual_in == "" || db.att.actual_out == ""))
                                tagging += '<a data-att_id="' + escaped + '" class="green attendance-edit" href="#"><i class="ace-icon fa fa-pencil-square-o bigger-130"></i></a>';
                        }
                        else if (val.att_status == "2") {
                            tagging += '<a data-att_id="' + val.id + '" class="primary attendance-show" href="#"><i class="ace-icon fa fa-search bigger-130"></i></a>';
                            if (db.chg.length == 1 && (db.att.actual_in == "" || db.att.actual_out == ""))
                                tagging += '<a data-att_id="' + escaped + '" class="green attendance-edit" href="#"><i class="ace-icon fa fa-pencil-square-o bigger-130"></i></a>';
                        }
                        else {
                            tagging += '<a data-att_id="' + val.id + '" class="primary attendance-show" href="#"><i class="ace-icon fa fa-search bigger-130"></i></a>';
                        }
                    });
                } else {

                    tagging += '<a data-att_id="' + escaped + '" ' + nodata + ' class="green attendance-edit" href="#"><i class="ace-icon fa fa-pencil-square-o bigger-130"></i></a>';
                }
                tagging = strip_tags(tagging, '<a><i>'); //be sure you only allow certain HTML tags to avoid XSS threats (you should also remove unwanted HTML attributes)
                td.innerHTML = tagging;
                td.style.textAlign = "center";
                return td;
            },
                    'json');
        } else {
            Handsontable.renderers.TextRenderer.apply(this, arguments);
        }
    }

    $(document).off("click", ".attendance-edit ,.attendance-show").on("click", ".attendance-edit ,.attendance-show", function(e) {
        e.preventDefault();
        clearmodal();
        $('#fine-uploader-validation').fineUploader('reset');
        $('button#add-att-save', "div#add-att-modal").data("att_id");
        $('.qq-uploader').css('overflow', 'auto');
        $(".success-msg", "#add-att-modal").html("");
        $("div.alert-success", "#add-att-modal").addClass("hidden");
        $(".err-msg", "#add-att-modal").html("");
        $("div.alert-danger", "#add-att-modal").addClass("hidden");
        $("#reject-field").addClass("hidden");
        $(".success-msg", this).html("");
        $('div.alert-success', this).addClass('hidden');
        $(".err-msg", this).html("");
        $('div.alert-danger', this).addClass('hidden');
        $('div#add-att-modal').modal('show');
        $("#imgtbl").html("");
        $("input[name='qqfile']").css({"position": "relative", "height": "auto", "width": "100%"});
        $("#fine-uploader-validation").show();
        if ($(this).data('att_id') >= 0) {
            var edit = ($(this).attr('class').search('attendance-show') == -1) ? 0 : 1;
            var reject = ($(this).attr('class').search('red attendance-show') == -1) ? 0 : 1;
            var atpost = {'updateAttendance': $(this).data('att_id'), 'limit': edit, 'reject': reject};
            if ($(this).data('att_id') == 0)
                atpost = {'updateAttendance': $(this).data('att_id'), 'limit': edit, 'reject': reject, 'rowdata': Array($(this).data('mb_id'), 0, 0, $(this).data('att_date'))};
            if (edit == 1)
                atpost = {'limit': edit, 'reject': reject, 'checkid': $(this).data('att_id')};
            $.post(
                    base_url + 'attendance/AttendanceDetails/',
                    atpost,
                    function(response) {

                        $("#att_id").val(response.attdetails.att_id);

                        if (response.attdetails.att_date !== "") {
                            $("#att_date").val(response.attdetails.att_date);
                            $("#mb_id").val(response.attdetails.mb_id);
                        }
                        //console.log($(this).data());
                        var att_date = (response.att.length == 0) ? response.attdetails.att_date : response.att.att_date;
                        $("#att_name").html(response.emp.mb_nick + " " + response.emp.mb_lname + "  - " + att_date);
                        $("#add-actual-in").val(response.att.actual_in);
                        $("#add-actual-out").val(response.att.actual_out);
                        $("#add-att-save").show();
                        $('#cancel-req').addClass('hidden');
                        if (response.chg && response.chg.remarks !== "" && response.chg.remarks !== undefined && edit == 1) {
                            $("#add-actual-in").val(response.chg.actual_in);
                            $("#add-actual-out").val(response.chg.actual_out);
                            $("#add-new-in").val(response.chg.new_in).prop('readonly', true);
                            $("#add-new-out").val(response.chg.new_out).prop('readonly', true);
                            $("#add-remarks").val(response.chg.remarks).prop('readonly', true);

                            var fname = response.chg.image_file;
                            fname = fname.split(":");
                            $("#fine-uploader-validation").hide();
                            if (response.chg.att_status == "1") {
                                $('#cancel-req').removeClass('hidden');
                                $('#cancel-req').data('id', response.chg.id);
                            }

                            if (response.chg.reject_remarks !== "" && response.chg.reject_remarks !== null) {
                                $("#reject-field").removeClass("hidden");
                                $("#reject-remarks").val(response.chg.reject_remarks).prop('readonly', true);
                            }
                            var thumb2 = '</div>';
                            for (var i = 0; i < fname.length; i++) {
                                var thumb = '<li class="media"><a target="_blank" href="' + base_url + "/uploads/att_/" + fname[i] + '" ><div class="media-left">';
                                var thumbname = '<div class="media-body"><h3 class="media-heading">' + fname[i].substring(33, fname[i].length) + '</h3></div>';
                                $("#imgtbl").append(thumb + "<img class='media-object' src='" + base_url + "/uploads/att_/" + fname[i] + "' style='width:64px; height:64px;' >" + thumb2 + thumbname + "</a></li>");
                            }
                            $("#add-att-save").hide();
                        }
                    },
                    'json'
                    );
        }
        else {

        }

    });


    function clearmodal() {
        $("#att_id").val("");
        $("#att_date").val("");
        $("#att_mb_id").val("");
        $("#att_name").html("");
        $("#add-actual-in").val("");
        $("#add-actual-out").val("");
        $("#add-new-in").val("").removeAttr('readonly');
        $("#add-new-out").val("").removeAttr('readonly');
        $("#add-remarks").val("").removeAttr('readonly');
        $("#add-file").val("");
        inputfile = "";
    }


    function getBase64FromImageUrl(url) {
        var img = new Image();

        img.onload = function() {
            var canvas = document.createElement("canvas");
            canvas.width = this.width;
            canvas.height = this.height;

            var ctx = canvas.getContext("2d");
            ctx.drawImage(this, 0, 0);

            var dataURL = canvas.toDataURL("image/png");

            //alert(dataURL.replace(/^data:image\/(png|jpg);base64,/, ""));
        };

        img.src = url;
    }
    var fineuploader = $('#fine-uploader-validation').fineUploader({
        template: 'qq-template-validation',
        request: {
            endpoint: base_url + "attendance/fineupload"
        },
        autoUpload: false,
        text: {
            uploadButton: "Drop image here"
        },
        validation: {
            allowedExtensions: ['jpeg', 'jpg', 'png'],
            itemLimit: 2,
            sizeLimit: (10000 * 1024) // 1100 kB = 1100 * 1024 bytes
        },
        /*
         deleteFile: {
         enabled: true, // defaults to false
         endpoint: base_url+"attendance/fineuploaddelete"
         },
         */
        callbacks: {
            onDelete: function(id) {

            },
            onError: function(id, name, errorReason, xhr) {
                $("input[name='qqfile']").css({"position": "relative", "height": "auto"});
                return false;
            },
            onCancel: function(id, name) {
                var submittedFileCount = this.getUploads({status: qq.status.SUBMITTED}).length;
                if (submittedFileCount == 1)
                    this.reset();
            },
            onComplete: function(id, name, responseJSON, xhr) {

                if (inputfile == "" && responseJSON.filename !== undefined) {
                    $("#add-file").val(responseJSON.filename);
                    inputfile = responseJSON.filename;
                }
                else if (responseJSON.filename === undefined) {
                    inputfile = "";
                    $(".err-msg", "#add-att-modal").html("File Restricted");
                    $("div.alert-danger", "#add-att-modal").removeClass("hidden");
                } else {
                    $("#add-file").val($("#add-file").val() + ":" + responseJSON.filename);
                    inputfile = inputfile + ":" + responseJSON.filename;
                }

            },
            onAllComplete: function(succeeded, failed) {
                if (inputfile.length > 0 && succeeded !== undefined) {
                    var datapost = {"att_id": $("#att_id").val(),
                        "new_in": $("#add-new-in").val(),
                        "new_out": $("#add-new-out").val(),
                        "remarks": $("#add-remarks").val(),
                        "addfile": inputfile
                    };

                    if ($("#att_id").val() == "0")
                        datapost = {"att_id": $("#att_id").val(),
                            "mb_id": $("#mb_id").val(),
                            "att_date": $("#att_date").val(),
                            "new_in": $("#add-new-in").val(),
                            "new_out": $("#add-new-out").val(),
                            "remarks": $("#add-remarks").val(),
                            "addfile": inputfile
                        };
                    $.post(base_url + 'attendance/setAttendanceChg/',
                            datapost,
                            function(d) {
                                if (d.success == 1) {
                                    $(".success-msg", "#add-att-modal").html("Successfully Added");
                                    $("div.alert-success", "#add-att-modal").removeClass("hidden");

                                    setTimeout(function() {
                                        $("div.alert-success", "#add-att-modal").addClass("hidden");
                                        $('div#add-att-modal').modal('hide');
                                        if (!$(this).data("page"))
                                            getSchedules(1);
                                        elsegetSchedules($(this).data("page"));
                                    }, 1500);

                                } else {
                                    $(".err-msg", "#add-att-modal").html(d.msg);
                                    $("div.alert-danger", "#add-att-modal").removeClass("hidden");
                                }
                            },
                            'json'
                            );
                }
            },
            onSubmit: function(id, name) {
                $(".qq-upload-button-selector span.glyphicon-open-file").hide();
                $("input[name='qqfile']").css({"position": "relative", "height": "auto"});
            },
            onStatusChange: function(id, oldStatus, newStatus) {

                if (qq.status.UPLOADING) {
                    $("input[name='qqfile']").css({"position": "relative", "height": "auto"});
                    $('.qq-total-progress-bar-container').css("position", "relative");
                    $('.qq-upload-cancel').css("z-index", 9999999);
                }
                if (qq.status.UPLOAD_SUCCESSFUL) {
                    $("input[name='qqfile']").css({"position": "relative", "height": "auto"});
                    $('.qq-total-progress-bar-container').css({"position": "absolute", "z-index": "-1"});

                }
            }
        }
    });

    $("button#cancel-req").off("click").click(function(e) {
        e.preventDefault();
        $.post(base_url + 'attendance/cancelrequest/',
                {rejectid:$(this).data('id')},
                function(d) {
                    if (d.success == 1) {
                        $(".success-msg", "#add-att-modal").html("Request Canceled");
                        $("div.alert-success", "#add-att-modal").removeClass("hidden");

                        setTimeout(function() {
                            $("div.alert-success", "#add-att-modal").addClass("hidden");
                            $('div#add-att-modal').modal('hide');
                            if (!$(this).data("page"))
                                getSchedules(1);
                            elsegetSchedules($(this).data("page"));
                        }, 1500);

                    } else {
                        $(".err-msg", "#add-att-modal").html(d.msg);
                        $("div.alert-danger", "#add-att-modal").removeClass("hidden");
                    }
                },
                'json'
                );
    });

    $("button#add-att-save").off("click").click(function(e) {
        e.preventDefault();
        //console.log(fineuploader);
        $("div.alert-danger").addClass("hidden");
        $("div.alert-success").addClass("hidden");

        if (($("#add-new-in").val() == "" || $("#add-new-out").val() == "") && $("#add-remarks").val() == "") {
            $(".err-msg", "#add-att-modal").html("Need to input your In/Out and Remarks");
            $("div.alert-danger", "#add-att-modal").removeClass("hidden");
            return false;
        }

        var submittedFileCount = fineuploader.fineUploader('getUploads', {status: qq.status.SUBMITTED}).length;

        if (submittedFileCount > 0) {
            fineuploader.fineUploader('uploadStoredFiles');
        }
        else {
            $(".err-msg", "#add-att-modal").html("Please Upload your ScreenShot");
            $("div.alert-danger", "#add-att-modal").removeClass("hidden");
            return false;
        }

    });

    $("#add-new-in,#add-new-out").keydown(function(e) {
        TimeStamp(e);
        if ($(this).val().length == 1 && $(this).val() > 2 && e.keyCode !== 8)
            $(this).val("0" + parseInt($(this).val()));
        if ($(this).val().length == 2 && parseInt($(this).val()) > 24 && e.keyCode !== 8)
            $(this).val("02:" + $(this).val().replace("2", ""));
        if ($(this).val().length == 2 && e.keyCode !== 8)
            $(this).val($(this).val() + ":");
        if ($(this).val().length > 4 && e.keyCode !== 8)
            return false;
    });

    function TimeStamp(e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                // Allow: Ctrl+A, Command+A
                        (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                        // Allow: home, end, left, right, down, up
                                (e.keyCode >= 35 && e.keyCode <= 40)) {
                    // let it happen, don't do anything
                    return;
                }
                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            }

        });