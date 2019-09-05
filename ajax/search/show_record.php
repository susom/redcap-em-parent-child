<?php

namespace Stanford\ParentChild;

/** @var \Stanford\ParentChild\ParentChild $module */

try {
    $id = filter_var($_POST["id"], FILTER_SANITIZE_NUMBER_INT);
    $instrument = filter_var($_POST["instrument"], FILTER_SANITIZE_STRING);
    $event = filter_var($_POST["event"], FILTER_SANITIZE_STRING);

    $instrumentFields = \REDCap::getFieldNames($instrument);
    //create search object by defining parent event arm
    $module->setSearchRelation(new SearchRelation($event, $module->getProjectId(), ''));

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
    <table class="table">
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
                <td><?php echo Main::getInstrumentFieldLabel($key, $instrument) ?></td>
                <td><?php echo $field ?></td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td colspan="2"><a class="btn btn-primary"
                               href="<?php echo $module->getSearchRelation()->getTopParentArm()->getUrl(); ?>">Edit
                    Record</a></td>
        </tr>
        </tbody>
    </table>
    <?php
} catch (\LogicException $e) {
    echo "<div class='alert-danger'>" . $e->getMessage() . "</div>";
}