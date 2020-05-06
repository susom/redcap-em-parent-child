Record = {
    childTreeURL: '',
    formChildTreeURL: '',
    eventId: '',
    instrument: '',
    id: '',
    topParentId: '',
    text: '',
    redcapToken: '',
    instrumentLabel: '',
    recordsDIV: 'label-__SUBMITBUTTONS__',
    inject: function () {
        Record.buildChildrenTree();
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
    formInject: function () {
        Record.buildFormChildrenTree()
    },
    buildFormChildrenTree: function () {
        jQuery.ajax({
            url: Record.formChildTreeURL,
            data: {
                event: Record.eventId,
                instrument: Record.instrument,
                id: Record.id,
                topParentId: Record.topParentId,
                text: Record.text,
                redcap_csrf_token: Record.redcapToken
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
                    console.log(children == undefined)
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
                        console.log($("#" + Record.recordsDIV));
                        console.log(html);
                        $("#" + Record.recordsDIV).html(html);
                    }
                }
                var label = Record.instrumentLabel;
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
    buildChildrenTree: function () {
        jQuery.ajax({
            url: Record.childTreeURL,
            data: {
                event: Record.eventId,
                instrument: Record.instrument,
                id: Record.id,
                topParentId: Record.topParentId,
                text: Record.text,
                redcap_csrf_token: Record.redcapToken
            },
            type: 'POST',
            success: function (data) {
                var label = Record.instrumentLabel;
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
