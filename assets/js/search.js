SearchObject = {
    childTreeURL: '',
    showRecordURL: '',
    redcapToken: '',
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
        jQuery(document).unbind('click').on("click", ".children-tree", function () {
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
            return false;
        });

        /**
         * show record
         */
        jQuery(document).on("click", ".show-record", function (e) {
            e.stopPropagation();
            e.preventDefault();
            e.stopImmediatePropagation();
            var instrument = jQuery(this).data('instrument');
            var event = jQuery(this).data('event');
            var id = jQuery(this).data('id');
            var $elem = jQuery(this);
            SearchObject.showRecord($elem, event, instrument, id);
        });
    },
    showRecord: function ($elem, event, instrument, id) {
        jQuery.ajax({
            url: SearchObject.showRecordURL,
            data: {event: event, instrument: instrument, id: id, redcap_csrf_token: SearchObject.redcapToken},
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
    buildFormChildrenTree: function ($elem, event, instrument, id, text, topParentId) {
        jQuery.ajax({
            url: Record.childTreeURL,
            data: {
                event: event,
                instrument: instrument,
                id: id,
                topParentId: topParentId,
                text: text,
                redcap_csrf_token: SearchObject.redcapToken
            },
            type: 'POST',
            success: function (response) {
                response = JSON.parse(response);
                if (response.status == 'success') {
                    var data = response.data
                    if (data.urls.length > 0) {
                        ParentObject.inject(data.urls)
                    }
                    var children = data.children;
                    if (children != undefined) {
                        var html = ''
                        for (var key in children) {
                            html += children[key]['label'] + '(' + children[key]['count'] + ' records)';
                            html += "<ul>";
                            if (children[key]['records'] != undefined) {
                                for (var j = 0; j < children[key]['records'].length; j++) {
                                    html += "<li><a href='" + children[key]['records'][j]['url'] + "'>" + children[key]['records'][j]['label'] + "</a></li>";
                                }
                            }
                            html += "</ul>";
                        }
                        $("#" + Record.recordsDIV).html(html);
                    }
                }

            },
            error: function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });
    },
    buildChildrenTree: function ($elem, event, instrument, id, text, topParentId) {
        jQuery.ajax({
            url: SearchObject.childTreeURL,
            data: {
                event: event,
                instrument: instrument,
                id: id,
                topParentId: topParentId,
                text: text,
                redcap_csrf_token: jQuery("#redcap_csrf_token").val()
            },
            type: 'POST',
            success: function (response) {
                response = JSON.parse(response);
                if (response.status == 'success') {
                    var data = response.data
                    if (data.urls != undefined) {
                        ParentObject.inject(data.urls)
                    }
                    var children = data.children;
                    if (children != undefined) {
                        var html = '<div class="col-12">';
                        for (var key in children) {
                            html += '<div class="row"><strong>' + children[key]['label'] + '(' + children[key]['count'] + ' record/s)</strong></div>';
                            html += '<ul class="list-group list-group-flush" style="width: 100%">';
                            if (children[key]['records'] != undefined) {
                                for (var j = 0; j < children[key]['records'].length; j++) {
                                    html += "<li class=\"list-group-item\" ><span class='show-record' data-id='" + children[key]['records'][j]['id'] + "' data-instrument='" + children[key]['records'][j]['instrument'] + "' data-event='" + children[key]['records'][j]['event_id'] + "'>" + children[key]['records'][j]['label'] + "</span><div class=\"float-right children-tree\" data-id='" + children[key]['records'][j]['id'] + "' data-instrument='" + children[key]['records'][j]['instrument'] + "' data-event='" + children[key]['records'][j]['event_id'] + "' data-text='" + children[key]['records'][j]['label'] + "'><i\n" +
                                        "                                                        class=\"fas fa-chevron-right\"></i></div></li>";
                                }
                            }
                            html += "</ul>";
                        }
                        html += '</div>';
                        var label = Record.instrumentLabel;
                        var container = "<h4>Related " + label + " records</h4><div class='row'><div id='list-container' class='mt-2 col-5'>" + html + "</div><div class='col-5'><div id='record-container'></div></div></div>"

                        jQuery(container).insertAfter("#event_grid_table")
                    } else {
                        if ($elem != undefined) {
                            $elem.closest("li").append('<div class="row">No child records found</div>');
                        }

                    }
                }
                if ($elem != undefined) {
                    $elem.find("i").removeClass("fa-chevron-right");
                    $elem.find("i").addClass("fa-chevron-down");
                }

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