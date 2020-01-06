<?php


namespace Stanford\ParentChild;

/** @var \Stanford\ParentChild\ParentChild $this */
$instrument = Main::getInstrumentNameViaEventId($this->getEventId());
?>
<script src="<?php echo $this->getUrl('assets/js/record.js') ?>"></script>
<script src="<?php echo $this->getUrl('assets/js/search.js') ?>"></script>
<input type="hidden" name="children-tree-url" id="children-tree-url"
       value="<?php echo $this->getUrl("ajax/search/children_tree.php") . '&pid=' . $this->getProjectId() ?>">
<input type="hidden" name="record_event_id" id="record_event_id" value="<?php echo $this->getEventId() ?>">
<input type="hidden" name="record_id" id="record_id" value="<?php echo $this->getRecordId() ?>">
<input type="hidden" name="record_instrument_name" id="record_instrument_name" value="<?php echo $instrument ?>">
<input type="hidden" name="show-record-url" id="show-record-url"
       value="<?php echo $this->getUrl("ajax/search/show_record.php") ?>">
<input type="hidden" name="redcap_csrf_token" id="redcap_csrf_token" value="<?php echo \System::getCsrfToken() ?>">
<input type="hidden" name="record_instrument_label" id="record_instrument_label"
       value="<?php echo Main::getInstrumentMenuDescription($instrument, $this->getProjectId()) ?>">
<script>
    Record.inject();
    SearchObject.init();
</script>
<div class="loader"><!-- Place at bottom of page --></div>