FormObject = {
    addURL: '',
    recordId: '',
    //this function will force add form to only allow add record to main parent.
    updateAddURL: function () {
        setTimeout(function () {
            if (jQuery("#arm_name_newid") != undefined) {
                jQuery("#arm_name_newid").attr('disabled', 'disabled');
            } else {
                jQuery(".fs13").attr("onclick", "window.location.href='" + FormObject.addURL + "'");
                jQuery(".fs13").html("<i class='fas fa-plus'></i> Add record for parent Arm Only");
            }

            if (jQuery("#inputString") != undefined) {
                jQuery("#inputString").val(FormObject.recordId).attr('disabled', 'disabled');
                var url = "<a href=" + FormObject.addURL + " class=\"btn btn-success\" type=\"submit\">Create</a>"
                jQuery("#inputString").after(url);
            }
        }, 100)

    }
};