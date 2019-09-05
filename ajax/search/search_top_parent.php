<?php

namespace Stanford\ParentChild;

/** @var \Stanford\ParentChild\ParentChild $module */

try {
    $term = filter_var($_POST['term'], FILTER_SANITIZE_STRING);
    $parentLabel = $module->getProjectSetting(TOP_PARENT_DISPLAY_LABEL);
    //init search object with parent
    $module->setSearchRelation(new SearchRelation($module->getFirstEventId(), $module->getProjectId(), $parentLabel));

    //set search term for later use
    $module->getSearchRelation()->setSearchTerm($term);

    /**
     * set top parents instruments fields so we can loop over them and search from them.
     */
    $fields = \REDCap::getFieldNames($module->getSearchRelation()->getTopParentArm()->getInstrument());
    if (empty($fields)) {
        throw new \LogicException("No fields in " . $module->getSearchRelation()->getTopParentArm()->getInstrument());
    } else {
        $module->getSearchRelation()->getTopParentArm()->setFields($fields);
        $module->getSearchRelation()->searchTopParent();
    }

    if (!empty($module->getSearchRelation()->getRecordsList())) {
        ?>
        <ul class="list-group" style="width: 100%">
            <?php
            foreach ($module->getSearchRelation()->getRecordsList() as $item) {
                ?>
                <li class="list-group-item" data-id="<?php echo $item[\REDCap::getRecordIdField()] ?>"
                    data-instrument="<?php echo $module->getSearchRelation()->getTopParentArm()->getInstrument() ?>"
                    data-event="<?php echo $module->getSearchRelation()->getEventId() ?>"><?php echo Main::replaceRecordLabels($parentLabel,
                        $item) ?>
                    <div class="float-right children-tree" data-id="<?php echo $item[\REDCap::getRecordIdField()] ?>"
                         data-instrument="<?php echo $module->getSearchRelation()->getTopParentArm()->getInstrument() ?>"
                         data-event="<?php echo $module->getSearchRelation()->getEventId() ?>"><i
                                class="fas fa-chevron-right "></i></div>
                </li>
                <?php
            }
            ?>
        </ul>
        <?php
    } else {
        echo "<div class='alert-info'>No records found</div>";
    }
} catch (\LogicException $e) {
    echo "<div class='alert-danger'>" . $e->getMessage() . "</div>";
}