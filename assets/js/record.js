Record = {
    inject: function () {
        var instrument = jQuery("#record_instrument_name").val();
        var event = jQuery("#record_event_id").val();
        var id = jQuery("#record_id").val();
        var topParentId = (jQuery("#record_id").val() != null ? null : jQuery("#record_id").val());
        var text = jQuery(this).data('text');
        var $elem = jQuery(this);
        Record.buildChildrenTree('', event, instrument, id, text, topParentId);
        /*jQuery.ajax({
            'url': jQuery("#record-home-children-url").val(),
            'type': 'GET',
            'success': function (data) {
                console.log(data)
                jQuery(data).insertAfter("#event_grid_table")
            },
            'error': function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });*/
    },
    buildChildrenTree: function ($elem, event, instrument, id, text, topParentId) {
        jQuery.ajax({
            url: jQuery("#children-tree-url").val(),
            data: {
                event: event,
                instrument: instrument,
                id: id,
                topParentId: topParentId,
                text: text,
                redcap_csrf_token: jQuery("#redcap_csrf_token").val()
            },
            type: 'POST',
            success: function (data) {
                var label = jQuery("#record_instrument_label").val();
                var container = "<h4>Related " + label + " records</h4><div class='row'><div id='list-container' class='mt-2 col-5'>" + data + "</div><div class='col-5'><div id='record-container'></div></div></div>"
                jQuery(container).insertAfter("#event_grid_table")
                //$elem.find("i").removeClass("fa-chevron-right");
                //$elem.find("i").addClass("fa-chevron-down");

            },
            error: function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });
    },
    removeTree: function ($elem) {
        $elem.closest("li").find(".row").remove();
        $elem.find("i").removeClass("fa-chevron-down");
        $elem.find("i").addClass("fa-chevron-right");
    },
};
