<?php


namespace Stanford\ParentChild;

/** @var \Stanford\ParentChild\ParentChild $this */
$instrument = $this->getProject()->eventsForms[$this->getEventId()][0];
?>
<script src="<?php echo $this->getUrl('assets/js/record.js') ?>"></script>
<script src="<?php echo $this->getUrl('assets/js/search.js') ?>"></script>
<script>
    Record.eventId = "<?php echo $this->getEventId() ?>";
    Record.id = "<?php echo $this->getRecordId() ?>"
    Record.instrument = "<?php echo $instrument ?>"
    Record.redcapToken = "<?php echo \System::getCsrfToken() ?>"
    Record.instrumentLabel = "<?php echo $this->getProject()->forms[$instrument]['menu'] ?>"
    Record.childTreeURL = "<?php echo $this->getUrl('ajax/search/children_tree.php') . '&pid=' . $this->getProjectId()?>"
    SearchObject.childTreeURL = "<?php echo $this->getUrl('ajax/search/children_tree.php') . '&pid=' . $this->getProjectId()?>"
    Record.inject();
    SearchObject.showRecordURL = "<?php echo $this->getUrl('ajax/search/show_record.php') ?>"
    SearchObject.init();
</script>
<div class="loader"><!-- Place at bottom of page --></div>