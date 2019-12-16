<?php

namespace Stanford\ParentChild;

/** @var \Stanford\ParentChild\ParentChild $this */

?>
    <script src="<?php echo $this->getUrl('assets/js/child.js') ?>"></script>
    <script>
        /**
         * define main variables.
         */
        ChildObject.dropDownList = <?php echo json_encode($this->getParentArm()->getDropDownList())  ?>;
        ChildObject.parentInputName = "<?php echo $this->getParentArm()->getRelation()->getForeignKey()  ?>";
    </script>
    <?php
// in case we passed parent id but not saved yet. selected that record in the dropdown
if ($this->getParentArm()->getTempRecordId()) {
    ?>
    <script>
        ChildObject.tempRecordId = "<?php echo $this->getParentArm()->getTempRecordId()  ?>";
    </script>
    <?php
}
if ($this->getParentRow()) {
    ?>
    <script>
        var content = "<?php echo $this->getParentRow()  ?>";
        /**
         * once everything is loaded trigger this function
         */
        window.addEventListener("load", ChildObject.injectParentRow(content), true);
    </script>
    <?php
} else {
    ?>
    <script>

        /**
         * once everything is loaded trigger this function
         */
        window.addEventListener("load", ChildObject.injectDropdown(), true);
    </script>
    <?php
}
if ($this->isOrphan()) {
    ?>
    <script>

        /**
         * once everything is loaded trigger this function
         */
        window.addEventListener("load", ChildObject.orphanNote(), true);
        var content = "<?php echo $this->getTopParentRow()  ?>";
        /**
         * once everything is loaded trigger this function
         */
        window.addEventListener("load", ChildObject.injectParentRow(content, "<?php echo $this->getParentArm()->getRelation()->getTopForeignKey()  ?>"), true);
    </script>
    <?php
}

require_once "modal.php";