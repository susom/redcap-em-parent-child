<?php

namespace Stanford\ParentChild;

/** @var \Stanford\ParentChild\ParentChild $module */

try {
    $id = filter_var($_POST["id"], FILTER_SANITIZE_STRING);
    $topParentId = filter_var($_POST["topParentId"], FILTER_SANITIZE_STRING);
    $instrument = filter_var($_POST["instrument"], FILTER_SANITIZE_STRING);
    $text = filter_var($_POST["text"], FILTER_SANITIZE_STRING);
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
            $records = $module->getChildRecords($child[CHILD_EVENT], $id, $child[CHILD_FOREIGN_KEY], $topParentId);

            //we need to know this event label when its a parent
            $childAsParent = $module->getParentEventRelation($child[CHILD_EVENT]);
                ?>
                <div class="col-12">
                    <div class="row"><strong><?php echo $tempChild->getInstrumentLabel() ?>
                            (<?php echo count($records) ?>)</strong></div>
                    <div class="row">
                        <?php
                        if ($records) {
                            ?>
                            <ul class="list-group list-group-flush" style="width: 100%">
                                <?php
                                foreach ($records as $record) {
                                    $item = $record[$tempChild->getEventId()];
                                    if ($childAsParent != false) {
                                        ?>
                                        <li class="list-group-item"><span class="show-record"
                                                                          data-id="<?php echo $item[\REDCap::getRecordIdField()] ?>"
                                                                          data-instrument="<?php echo $tempChild->getInstrument() ?>"
                                                                          data-event="<?php echo $tempChild->getEventId() ?>">
                                        <?php echo Main::replaceRecordLabels($childAsParent[0][PARENT_DISPLAY_LABEL],
                                            $item) ?>
                                    </span>
                                            <div class="float-right children-tree"
                                                 data-id="<?php echo $item[\REDCap::getRecordIdField()] ?>"
                                                 data-top-parent-id="<?php echo $item[$tempChild->getRelation()->getForeignKey()] ?>"
                                                 data-text="<?php echo Main::replaceRecordLabels($childAsParent[0][PARENT_DISPLAY_LABEL],
                                                     $item) ?>"
                                                 data-instrument="<?php echo $tempChild->getInstrument() ?>"
                                                 data-event="<?php echo $tempChild->getEventId() ?>"><i
                                                        class="fas fa-chevron-right"></i></div>
                                        </li>
                                        <?php
                                    } else {
                                        //if this event has no children and we do not know how to display its record
                                        $item = $module->limitInstrumentFieldsOnly($tempChild->getInstrument(), $item);
                                        ?>
                                        <li class="list-group-item"><span class="show-record"
                                                                          data-id="<?php echo $item[\REDCap::getRecordIdField()] ?>"
                                                                          data-top-parent-id="<?php echo $item[$tempChild->getRelation()->getForeignKey()] ?>"
                                                                          data-instrument="<?php echo $tempChild->getInstrument() ?>"
                                                                          data-event="<?php echo $tempChild->getEventId() ?>">
                                        <?php echo implode(", ", $item) ?>
                                    </span>
                                            <div class="float-right children-tree"
                                                 data-id="<?php echo $item[\REDCap::getRecordIdField()] ?>"
                                                 data-text="<?php echo implode(", ", $item) ?>"
                                                 data-instrument="<?php echo $tempChild->getInstrument() ?>"
                                                 data-event="<?php echo $tempChild->getEventId() ?>"><i
                                                        class="fas fa-chevron-right"></i></div>
                                        </li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <?php

        }
    } else {
        echo "<div class='alert-danger'>No defined children for $instrument</div>";
    }
} catch (\LogicException $e) {
    echo "<div class='alert-danger'>" . $e->getMessage() . "</div>";
}