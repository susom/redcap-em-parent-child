SearchObject = {
    init: function () {
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

        jQuery(document).on("click", ".children-tree", function () {
            var instrument = jQuery(this).data('instrument');
            var event = jQuery(this).data('event');
            var id = jQuery(this).data('id');
            var $elem = jQuery(this);
            SearchObject.buildChildrenTree($elem, event, instrument, id);
        });
    },
    buildChildrenTree: function ($elem, event, instrument, id) {
        jQuery.ajax({
            url: $("#children-tree-url").val(),
            data: {event: event, instrument: instrument, id: id},
            type: 'POST',
            success: function (data) {
                $elem.parent().append(data);
            },
            error: function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });
    },
    searchTopParent: function (term) {
        jQuery.ajax({
            url: $("#search-top-parent-url").val(),
            data: {term: term},
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