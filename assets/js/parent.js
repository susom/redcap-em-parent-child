ParentObject = {
    recordId: '',
    childRecordsURL: '',
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
            var action = "submit-btn-savecontinue"
            html += '<div class="btn-group nowrap"><button class="btn btn-primaryrc" id="submit-btn-savecontinue" name="submit-btn-savecontinue" onclick="return setTimeout(function() {' +
                ' ParentObject.redirect(' + url + ');  }, 100)" style="margin-bottom:2px;font-size:13px !important;padding:6px 8px;" tabindex="0">Add ' + urls[i]['label'] + '</button>';
            html += '</div><br>';
        }

        /**
         * wrapping this to hide when new parent not saved at least one time.
         * @type {string}
         */
        html = "<div id='children-buttons'>"+html+"</div>";
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
            'url': ParentObject.childRecordsURL,
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

