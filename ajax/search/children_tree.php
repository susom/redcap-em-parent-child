<?php

namespace Stanford\ParentChild;

/** @var \Stanford\ParentChild\ParentChild $module */

try {
    $id = filter_var($_POST["id"], FILTER_SANITIZE_NUMBER_INT);
    $instrument = filter_var($_POST["id"], FILTER_SANITIZE_STRING);
    $event = filter_var($_POST["event"], FILTER_SANITIZE_STRING);

    //create search object by defining parent event arm
    $module->setSearchRelation(new SearchRelation($event, $module->getProjectId(), ''));
    //check if there is any children for this event
    $children = $module->getParentEventRelation($event);

    if ($children != false) {
        foreach ($children as $child) {

            $relation = new Relation($child);

            //temp child object to get some info from it.
            $tempChild = new ChildArm($child[CHILD_EVENT], $module->getProjectId(), $relation);

            //get child records related to parent id
            $records = $module->getChildRecords($child[CHILD_EVENT], $id, $child[CHILD_FOREIGN_KEY]);

            //we need to know this event label when its a parent
            $childAsParent = $module->getParentEventRelation($child[CHILD_EVENT]);
            if ($records) {
                ?>
                <strong><?php echo $tempChild->getInstrumentLabel() ?></strong>
                <ul class="list-group" style="width: 100%">
                    <?php
                    foreach ($records as $record) {
                        $item = $record[$tempChild->getEventId()];
                        if ($childAsParent != false) {
                            ?>
                            <li class="list-group-item" data-id="<?php echo $item[\REDCap::getRecordIdField()] ?>"
                                data-instrument="<?php echo $tempChild->getInstrument() ?>"
                                data-event="<?php echo $tempChild->getEventId() ?>"><?php echo Main::replaceRecordLabels($childAsParent[0][PARENT_DISPLAY_LABEL],
                                    $item) ?>
                                <div class="float-right children-tree"
                                     data-id="<?php echo $item[\REDCap::getRecordIdField()] ?>"
                                     data-instrument="<?php echo $tempChild->getInstrument() ?>"
                                     data-event="<?php echo $tempChild->getEventId() ?>"><i
                                            class="fas fa-chevron-right "></i></div>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </ul>
                <?php
            } else {
                echo "<div class='alert-danger'>No records for " . $id . " in " . $tempChild->getInstrument() . "</div>";
            }

        }
    } else {
        echo "<div class='alert-danger'>No defined children for this record</div>";
    }
} catch (\LogicException $e) {
    echo "<div class='alert-danger'>" . $e->getMessage() . "</div>";
}