$(function () {

    var hrmisPoint = $('#max_hrmis');
    var departmentPoint = $('#max_department');
    var hrRecordPoint = $('#max_hr_records');
    var saveBtn = $("#saveBtn");

    hrmisPoint.ace_spinner({value: 0, min: 0, max: 100, step: 5, btn_up_class: 'btn-success', btn_down_class: 'btn-danger'})
            .closest('.ace-spinner')
            .on('changed.fu.spinbox', function () {
            	
                calculateTotal();
            });

    departmentPoint.ace_spinner({value: 0, min: 0, max: 100, step: 5, btn_up_class: 'btn-success', btn_down_class: 'btn-danger'})
            .closest('.ace-spinner')
            .on('changed.fu.spinbox', function () {
                calculateTotal();
            });

    hrRecordPoint.ace_spinner({value: 0, min: 0, max: 100, step: 5, btn_up_class: 'btn-success', btn_down_class: 'btn-danger'})
            .closest('.ace-spinner')
            .on('changed.fu.spinbox', function () {
                calculateTotal();
            });

    function calculateTotal() {
        var total = parseInt(hrmisPoint.val()) + parseInt(departmentPoint.val()) + parseInt(hrRecordPoint.val());
        
        if (total > 100 || total < 100 || isNaN(total)) {
            saveBtn.prop("disabled", true);
            
        } else {
            saveBtn.prop("disabled", false);
        }
        console.log(total)
    }

    $("#saveBtn").off("click").click(function (e) {
        e.preventDefault();

        $.ajax({
            url: base_url + "employees/updateKpiPercentage",
            data: {
                hrmisKpi: hrmisPoint.val(),
                departmentKpi: departmentPoint.val(),
                hrRecordKpi: hrRecordPoint.val()
            },
            cache: false,
            type: "post",
            success: function () {
//                alert("Percentage already udpated!");
                $.gritter.add({
                    title: 'Update Percentage',
                    text: 'Percentage already udpated!',
                    sticky: false,
                    time: '2000',
                    class_name: 'gritter-success'
                });

                return false;
            }
        });
    });

});