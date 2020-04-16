<?php

namespace Stanford\ParentChild;

/** @var \Stanford\ParentChild\ParentChild $module */

try {

    $event = filter_var($_POST['event'], FILTER_SANITIZE_NUMBER_INT);

    $instrument = filter_var($_POST['instrument'], FILTER_SANITIZE_STRING);
    $recordId = filter_var($_POST['recordId'], FILTER_SANITIZE_STRING);
    $foreignKey = filter_var($_POST['foreignKey'], FILTER_SANITIZE_STRING);
    $topParentRecordId = filter_var($_POST['topParentRecordId'], FILTER_SANITIZE_STRING);
    $fields = \REDCap::getFieldNames($instrument);
    $records = $module->getChildRecords($event, $recordId, $foreignKey, $topParentRecordId);
    $childArm = new ChildArm($event, $module->getProjectId());
    $data = array();
    if (!empty($records)) {
        $headers = end($records);
        $headers = array_keys($headers[$event]);
        ?>
        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th colspan="<?php echo count($headers) ?>"><?php echo $instrument ?> Records <a class="float-right"
                                                                                                 href="javascript:;"
                                                                                                 onclick="$('#instancesTablePopup').hide();"><img
                                src="<?php echo APP_PATH_WEBROOT ?>Resources/images/delete_box.gif">
                    </a></th>
            </tr>
            <tr>
                <?php
                foreach ($headers as $header) {
                    if (!in_array($header, $fields)) {
                        continue;
                    }
                    ?>
                    <th scope="col"><?php echo $header ?></th>
                    <?php
                }
                ?>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($records as $id => $record) {
                $childArm->setRecordId($id);
                ?>
                <tr class="clickable-row" data-url="<?php echo $childArm->getUrl(); ?>">
                    <?php
                    foreach ($record[$event] as $key => $row) {
                        if (!in_array($key, $fields)) {
                            continue;
                        }
                        ?>
                        <td><?php echo $row ?></td>
                        <?php
                    }
                    ?>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php
    } else {
        echo "<div class='alert-info'>No Child record for " . $instrument . "</div>";
    }
} catch (\LogicException $e) {
    echo "<div class='alert-danger'>" . $e->getMessage() . "</div>";
}
?>