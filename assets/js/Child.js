ChildObject = {
    dropDownList: '',
    parentInputName: '',
    $originalParent: '',
    init: function () {
        jQuery(document).on("click", ".show-list", function () {
            if (confirm("Are you sure you want to edit this record parent?")) {
                var parentRecordId = jQuery("#parent-row").data("parent-id");
                var select = ChildObject.generateDropDown(parentRecordId);
                jQuery("#parent-row").replaceWith(select);
            }
        });
    },
    generateDropDown: function (record) {
        var select = '<select name="' + this.parentInputName + '" required><option value="">Select ' + this.parentInputName + '</option>';
        for (var key in this.dropDownList) {
            if (record != undefined && record == key) {
                select += "<option value='" + key + "' selected>" + this.dropDownList[key] + "</option>";
            } else {
                select += "<option value='" + key + "'>" + this.dropDownList[key] + "</option>";
            }

        }
        select += "</select>";
        return select;
    },
    injectDropdown: function () {
        var $input = jQuery("[name='" + this.parentInputName + "']");
        select = ChildObject.generateDropDown();
        $input.replaceWith(select);
    },
    injectParentRow: function (content, name) {
        if (name == undefined) {
            name = this.parentInputName;
        }
        var $input = jQuery("[name='" + name + "']");
        $input.replaceWith(content);
    },
    moveParentToTop: function ($input) {
        var $element = $input.closest("tr").clone();
        ChildObject.$originalParent = $element;
        jQuery(".formtbody").prepend($element);
        $input.closest("tr").remove();
        return $element;
    },
    orphanNote: function () {
        var $element = jQuery("<tr><td colspan='2' class='alert-danger'>This Record has no parent and considered orphan</td></tr>")
        jQuery(".formtbody").prepend($element);
    }
}
ChildObject.init();