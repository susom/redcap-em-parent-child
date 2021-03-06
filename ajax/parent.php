<?php

namespace Stanford\ParentChild;

/** @var \Stanford\ParentChild\ParentChild $this */

?>

    <link rel="stylesheet" href="<?php echo $this->getUrl('assets/css/parent.css') ?>">
    <script src="<?php echo $this->getUrl('assets/js/parent.js') ?>"></script>
    <?php

if ($this->isInjectElement()) {
    ?>
    <script>
        ParentObject.childRecordsURL = "<?php echo $this->getUrl('ajax/child_records.php', false,
            true) ?>";
        ParentObject.recordId = "<?php echo filter_var($_GET['id'], FILTER_SANITIZE_STRING);  ?>";
        <?php
        $urls = array();
        foreach ($this->getChildrenArms() as $child){

        $record = $this->getRecord();
        $record = $record[filter_var($_GET['id'], FILTER_SANITIZE_STRING)][$this->getEventId()];
        /* @var $child ChildArm */
        $arr = array(
            "url" => trim(APP_PATH_WEBROOT_FULL, "/") . $child->getUrl() . "&parent=" . filter_var($_GET['id'],
                    FILTER_SANITIZE_STRING),
            "label" => $child->getInstrumentLabel(),
            "childInstrument" => $child->getInstrument(),
            "childEvent" => $child->getEventId(),
            "foreignKey" => $child->getRelation()->getForeignKey(),
            "topParentForeignKey" => $child->getRelation()->getTopForeignKey(),
            "topParentRecordId" => $record[$child->getRelation()->getTopForeignKey()],
        );
        $urls[] = $arr;
        ?>
        <?php
        }
        ?>
        var urls = <?php echo json_encode($urls)  ?>;

        /**
         * once everything is loaded trigger this function
         */
        //TODO move the children section to button of parent form
        window.addEventListener("load", ParentObject.inject(urls), true);
    </script>
    <?php
}
require_once "../view/modal.php";