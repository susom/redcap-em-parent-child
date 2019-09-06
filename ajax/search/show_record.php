<?php

namespace Stanford\ParentChild;

/** @var \Stanford\ParentChild\ParentChild $module */

try {
    $id = filter_var($_POST["id"], FILTER_SANITIZE_STRING);
    $instrument = filter_var($_POST["instrument"], FILTER_SANITIZE_STRING);
    $event = filter_var($_POST["event"], FILTER_SANITIZE_STRING);


    //create search object by defining parent event arm
    $module->setSearchRelation(new SearchRelation($event, $module->getProjectId(), ''));

    $instrumentFields = \REDCap::getFieldNames($module->getSearchRelation()->getTopParentArm()->getInstrument());
    /**
     * pull record information. and save it into searchRelation Object.
     */
    $module->getSearchRelation()->setRecord(Main::getRecords($event, $id));

    /**
     * set the record URL using parent object
     */
    $module->getSearchRelation()->getTopParentArm()->setUrl($id);
    if (empty($module->getSearchRelation()->getRecord())) {
        throw new \LogicException("Cant find record");
    }

    ?>
    <div class="row">
        <div class="ml-3">
            <h5><?php echo(is_array($module->getSearchRelation()->getTopParentArm()->getInstrument()) ? implode(", ",
                    $module->getSearchRelation()->getTopParentArm()->getInstrument()) : $module->getSearchRelation()->getTopParentArm()->getInstrument()) ?></h5>
        </div>
    </div>
    <table class="table" id="record-table">
        <thead class="thead-dark">
        <tr>
            <th>Field</th>
            <th>Value</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $item = $module->getSearchRelation()->getRecord();
        $item = $item[$id][$module->getSearchRelation()->getEventId()];
        foreach ($item as $key => $field) {
            if (!in_array($key, $instrumentFields)) {
                continue;
            }
            ?>
            <tr>
                <td><?php echo Main::getInstrumentFieldLabel($key, $module->getProjectId(),
                        !is_array($module->getSearchRelation()->getTopParentArm()->getInstrument()) ? $module->getSearchRelation()->getTopParentArm()->getInstrument() : null) ?></td>
                <td><?php echo $field ?></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    <div class="row">
        <a class="btn btn-primary"
           href="<?php echo Main::getRecordHomeURL($module->getProjectId(),
               $module->getSearchRelation()->getTopParentArm()->getArmId(), $id); ?>">View
            Record</a>
    </div>
    <?php
} catch (\LogicException $e) {
    echo "<div class='alert-danger'>" . $e->getMessage() . "</div>";
}