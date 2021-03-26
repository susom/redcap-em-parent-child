ChildObject = {
    dropDownList: '',
    parentInputName: '',
    $originalParent: '',
    tempRecordId: '',
    submissionDiv: "__SUBMITBUTTONS__-div",
    init: function () {
        jQuery(document).on("click", ".show-list", function (e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            if (confirm("Are you sure you want to edit this record parent?")) {
                var parentRecordId = jQuery(this).data("parent-id");
                var $input = jQuery("[name='" + jQuery(this).data("parent-input-name") + "']");
                var dropdown = $input.data('parent-dropdown');
                var select = ChildObject.generateDropDown(jQuery(this).data("parent-input-name"), dropdown);
                jQuery("#parent-record-id-" + parentRecordId).replaceWith(select);
            }
        });

    },
    generateDropDown: function (parentInputName, dropDownList) {

        var select = '<select name="' + parentInputName + '" required><option value="">Select ' + parentInputName + '</option>';

        for (var key in dropDownList) {
            if (ChildObject.tempRecordId != '' && ChildObject.tempRecordId == key) {
                select += "<option value='" + key + "' selected>" + dropDownList[key] + "</option>";
            } else {
                select += "<option value='" + key + "'>" + dropDownList[key] + "</option>";
            }

        }
        select += "</select>";
        return select;
    },
    injectDropdown: function () {
        var $input = jQuery("[name='" + this.parentInputName + "']");
        select = ChildObject.generateDropDown(ChildObject.parentInputName, ChildObject.dropDownList);
        $input.replaceWith(select);
    },
    hideChildButtons: function(){
        jQuery("#children-buttons").hide();
    },
    injectParentRow: function (content, name) {
        if (name == undefined) {
            name = this.parentInputName;
        }
        var $input = jQuery("[name='" + name + "']");
        $input.attr('type', 'hidden');

        // hack to pass the dropdown list to the element
        $input.attr('data-parent-dropdown', JSON.stringify(ChildObject.dropDownList))

        $input.before(content);
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