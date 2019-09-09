SearchObject = {
    init: function () {

        $body = $("body");
        /**
         * display loader
         */
        jQuery(document).on({
            ajaxStart: function () {
                $body.addClass("loading");
            },
            ajaxStop: function () {
                $body.removeClass("loading");
            }
        });

        /**
         * Event for search top parent.
         */
        jQuery("#search-top-parent").on("click", function () {
            var term = jQuery("#top-parent-field").val();

            if (term == "") {
                alert("You must add search term");
                return false;
            } else {
                SearchObject.searchTopParent(term);
            }
        });

        /**
         * show parent tree
         */
        jQuery(document).on("click", ".children-tree", function () {
            var instrument = jQuery(this).data('instrument');
            var event = jQuery(this).data('event');
            var id = jQuery(this).data('id');
            var topParentId = jQuery(this).data('top-parent-id');
            var text = jQuery(this).data('text');
            var $elem = jQuery(this);
            if ($elem.find("i").hasClass("fa-chevron-down")) {
                SearchObject.removeTree($elem);
            } else {
                SearchObject.buildChildrenTree($elem, event, instrument, id, text, topParentId);
            }
        });

        /**
         * show record
         */
        jQuery(document).on("click", ".show-record", function () {
            var instrument = jQuery(this).data('instrument');
            var event = jQuery(this).data('event');
            var id = jQuery(this).data('id');
            var $elem = jQuery(this);
            SearchObject.showRecord($elem, event, instrument, id);
        });
    },
    showRecord: function ($elem, event, instrument, id) {
        jQuery.ajax({
            url: jQuery("#show-record-url").val(),
            data: {event: event, instrument: instrument, id: id, redcap_csrf_token: jQuery("#redcap_csrf_token").val()},
            type: 'POST',
            success: function (data) {
                jQuery("#record-container").html(data);

                jQuery("#record-table").dataTable();
            },
            error: function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });
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
                $elem.closest("li").append(data);
                $elem.find("i").removeClass("fa-chevron-right");
                $elem.find("i").addClass("fa-chevron-down");

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
    searchTopParent: function (term) {
        jQuery.ajax({
            url: jQuery("#search-top-parent-url").val(),
            data: {term: term, redcap_csrf_token: jQuery("#redcap_csrf_token").val()},
            type: 'POST',
            success: function (data) {
                jQuery("#list-container").html(data);
            },
            error: function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });
    }
};


SearchObject.init();