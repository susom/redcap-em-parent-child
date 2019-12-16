ParentObject = {
    recordId: '',
    submissionDiv: "__SUBMITBUTTONS__-div",
    init: function () {
        jQuery(document).on("click", ".clickable-row", function () {
            window.location = jQuery(this).data("url");
        });
    },
    inject: function (urls) {
        var html = '';
        for (var i = 0; i < urls.length; i++) {
            var url = "'" + urls[i]['url'] + "'";
            var instrument = "'" + urls[i]['childInstrument'] + "'";
            var event = "'" + urls[i]['childEvent'] + "'";
            var foreignKey = "'" + urls[i]['foreignKey'] + "'";
            var topParentRecordId = "'" + urls[i]['topParentRecordId'] + "'";
            html += '<div class="btn-group nowrap"><button class="btn btn-primaryrc" id="submit-btn-saverecord" name="submit-btn-saverecord" onclick="dataEntrySubmit(this);setTimeout(function() {' +
                ' ParentObject.redirect(' + url + ');  }, 100)" style="margin-bottom:2px;font-size:13px !important;padding:6px 8px;" tabindex="0">Save & Add ' + urls[i]['label'] + '</button><button id="submit-btn-dropdown" title="More save options" class="btn btn-primaryrc btn-savedropdown dropdown-toggle" style="margin-bottom:2px;font-size:13px !important;padding:6px 8px;" tabindex="0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" onclick="ParentObject.openChildDropdown(' + instrument + ');return false;">\n' +
                '\t\t\t\t\t\t\t\t<span class="sr-only"></span>\n' +
                '\t\t\t\t\t\t\t</button>';
            html += '<div class="dropdown-menu-child" id="' + urls[i]['childInstrument'] + '"><a class="dropdown-item" href="javascript:;" id="submit-btn-savecontinue" onclick="ParentObject.getAllChildRecords(' + instrument + ', ' + event + ', ' + foreignKey + ', ' + topParentRecordId + ');return false;">Show All ' + urls[i]['label'] + ' Records</a></div></div>';
        }
        jQuery("#" + this.submissionDiv).append(html);
    },
    redirect: function (url) {
        window.location.replace(url);
    },
    openChildDropdown: function (instrument) {

        //show the instrument we are looking for.
        jQuery("#" + instrument).toggleClass("show");

    },
    getAllChildRecords: function (instrument, event, foreignKey, topParentRecordId) {
        jQuery.ajax({
            'url': jQuery("#get-child-records-url").val(),
            'data': {
                instrument: instrument,
                event: event,
                foreignKey: foreignKey,
                recordId: this.recordId,
                topParentRecordId: topParentRecordId
            },
            'type': 'POST',
            'success': function (data) {
                if (!$('#instancesTablePopup').length) {
                    $('body').append('<div id="instancesTablePopup"><div id="instancesTablePopupSub"> </div></div>');
                }
                $('#instancesTablePopupSub').html(data);
                $('#instancesTablePopupSub .btnAddRptEv').removeClass('opacity50');
                var instancesTablePopup = $('#instancesTablePopup');
                instancesTablePopup.show();

                instancesTablePopup.css("top", "50%");
                instancesTablePopup.css("left", "50%");
                instancesTablePopup.css("z-index", "99999");

            },
            'error': function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });
    }
}
ParentObject.init();

