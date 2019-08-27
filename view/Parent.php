<?php

namespace Stanford\ParentChild;

/** @var \Stanford\ParentChild\ParentChild $this */

?>

    <input type="hidden" id="get-child-records-url" name="get-child-records-url"
           value="<?php echo $this->getUrl('ajax/child_records.php', false,
               true) ?>">
    <link rel="stylesheet" href="<?php echo $this->getUrl('assets/css/parent.css') ?>">
    <script src="<?php echo $this->getUrl('assets/js/parent.js') ?>"></script>
    <?php

if ($this->isInjectElement()) {
    ?>
    <script>
        ParentObject.recordId = <?php echo filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);  ?>;
        <?php
        $urls = array();
        foreach ($this->getChildrenArms() as $child){
        /* @var $child ChildArm */
        $arr = array(
            "url" => trim(APP_PATH_WEBROOT_FULL, "/") . $child->getUrl() . "&parent=" . filter_var($_GET['id'],
                    FILTER_SANITIZE_NUMBER_INT),
            "label" => $child->getInstrumentLabel(),
            "childInstrument" => $child->getInstrument(),
            "childEvent" => $child->getEventId(),
            "foreignKey" => $child->getRelation()->getForeignKey()
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

        window.addEventListener("load", ParentObject.inject(urls), true);
    </script>
    <?php
}
require_once "modal.php";