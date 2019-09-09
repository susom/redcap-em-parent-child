FormObject = {
    addURL: '',
    //this function will force add form to only allow add record to main parent.
    updateAddURL: function () {
        console.log(FormObject.addURL);
        setTimeout(function () {
            jQuery(".fs13").attr("onclick", "window.location.href='" + FormObject.addURL + "'");
            jQuery(".fs13").html("<i class='fas fa-plus'></i> Add record for parent Arm Only");
        }, 100)

    }
};