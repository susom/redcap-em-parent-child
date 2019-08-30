<?php

namespace Stanford\CorrelatedReport;

/** @var \Stanford\ParentChild\ParentChild $module */

use \REDCap;
use Stanford\ParentChild\RelationalReport;

$event = $module->getFirstEventId();
$children = $module->getParentEventRelation($event);
$module->setRelationalReport(new RelationalReport($module->getProjectId(), $event, $children));
?>
<div class="accordion" id="accordionExample">
    <input type="hidden" id="get-event-children-url"
           value="<?php echo $module->getUrl("ajax/report/get_event_children.php", false, true) ?>">
    <h2>Search Criteria</h2>
    <?php

    foreach ($module->getRelationalReport()->getInstruments() as $key => $instrument) {
        $module->getRelationalReport()->getEventCard($instrument, $key, $event);
    }
    ?>
</div>