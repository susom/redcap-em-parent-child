ReportObject = {
    init: function () {

        ReportObject.initSortable();

        jQuery(document).on("click", ".get-children", function () {
            var eventId = jQuery(this).data('event-id');
            ReportObject.getEventChildren(eventId);
        });

        /**
         * Delete filter
         */
        jQuery(document).on('click', '.delete-criteria', function () {
            jQuery(this).closest('.list-group-item').remove();
        });

        /**
         * Add field to view panel
         */
        jQuery(document).on('click', '.include-field', function () {
            var field = jQuery(this).data('field');
            jQuery("#instruments-fields").append("<div>\n" +
                "                    <label for=\"" + field + "_field\">\n" +
                "                        <input style=\"vertical-align:middle;\" checked type=\"checkbox\" id=\"" + field + "_field\"\n" +
                "                               name=\"" + field + "_field\">&nbsp;&nbsp;<span\n" +
                "                                style=\"word-break: break-all\">" + field + "</span>\n" +
                "                    </label>\n" +
                "                </div>");
        });

    },
    getEventChildren(eventId) {
        jQuery.ajax({
            url: $("#get-event-children-url").val(),
            data: {eventId: eventId},
            type: 'POST',
            success: function (data) {
                jQuery("#accordionExample").append(data);
            },
            error: function (request, error) {
                alert("Request: " + JSON.stringify(request));
            },
            complete: function () {
                ReportObject.initSortable();
            }
        });
    },
    appendInputs: function (element) {
        $.ajax({
            url: $("#base-url").val(),
            data: {field_name: element.data('field'), redcap_csrf_token: $("#redcap_csrf_token").val()},
            type: 'POST',
            success: function (data) {
                data = ' ' + data + ReportObject.appendContactInput();
                element.append(data);
                //TODO APPEND DELETE BUTTON
            },
            error: function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });
    },
    appendContactInput: function () {
        return '<select name="limiter_connector[]"><option value="AND">AND</option><option value="OR">OR</option></select><button type="button" class="delete-criteria close" aria-label="Close">\n' +
            '  <span aria-hidden="true">&times;</span>\n' +
            '</button>'
    },
    initSortable: function () {

        /**
         * track drag and drop to append field filter inputs
         */
        jQuery(".instruments-fields").sortable({
            connectWith: ".connectedSortable",
            stop: function (event, ui) {
                var $element = $(ui.item[0]);
            },
            remove: function (event, ui) {
                var $newELem = ui.item.clone();
                ReportObject.appendInputs($newELem);
                $newELem.appendTo('.filters-fields');
                $(this).sortable('cancel');
            }
        });

        /**
         * remove input if we drag field to main list
         */
        jQuery(".filters-fields").sortable({
            connectWith: ".connectedSortable",
            stop: function (event, ui) {
                //TODO strip LI from inputs
            }
        });
    },
};

ReportObject.init();