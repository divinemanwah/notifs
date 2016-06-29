$(function() {

    $.post(
            base_url + "employees/setdocs",
            function(response) {
                $("select#notifier").val(response[0].mb_no);
                
                
                $.each(response[0].aep.split(","), function(i, e) {
                    $("select#aep option[value=" + e + "]").prop("selected", true);
                });
                $.each(response[0].cwv.split(","), function(i, e) {
                    $("select#cwv option[value=" + e + "]").prop("selected", true);
                });
                $.each(response[0].passport.split(","), function(i, e) {
                    $("select#passport option[value=" + e + "]").prop("selected", true);
                });
                
                $('.chosen-select').trigger('chosen:updated');
            },
            "json");

    $(".chosen-select").chosen({max_selected_options: 2});

    $('select#emp').chosen({allow_single_deselect: true, search_contains: true});

    $('button#docs-save').off("click").click(function(e) {
        e.preventDefault();
        $.post(
                base_url + "employees/insertdocuments",
                {mb_no: $("select#notifier").val(),
                    passport: $("select#passport").val().join(),
                    aep: $("select#aep").val().join(),
                    cwv: $("select#cwv").val().join()
                },
        function(response) {
            if (response.success) {
                $(".success-msg", "#employee-setting").html("Successfully updated");
                $("div.alert-success", "#employee-setting").removeClass("hidden");

                setTimeout(function() {
                    $("div.alert-success", "#employee-setting").addClass("hidden");
                }, 1500);
            }
            else {
                $(".err-msg", "#employee-setting").html("Error on Saving");
                $("div.alert-danger", "#employee-setting").removeClass("hidden");
                setTimeout(function() {
                    $("div.alert-danger", "#employee-setting").addClass("hidden");
                }, 1500);
            }
        },
                "json");
    });

});