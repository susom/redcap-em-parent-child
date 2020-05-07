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
        SearchObject.buildChildrenTree(undefined, Record.eventId, Record.instrument, Record.id, Record.text, Record.topParentId);
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
        SearchObject.buildFormChildrenTree(undefined, Record.eventId, Record.instrument, Record.id, Record.text, Record.topParentId)
    }
};
