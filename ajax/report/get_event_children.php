<?php

namespace Stanford\CorrelatedReport;

/** @var \Stanford\ParentChild\ParentChild $module */

use \REDCap;
use Stanford\ParentChild\RelationalReport;

$event = filter_var($_POST['eventId'], FILTER_SANITIZE_NUMBER_INT);
$children = $module->getParentEventRelation($event);
$module->setRelationalReport(new RelationalReport($module->getProjectId(), $event, $children));

/**
 * now we need to get children event information instead of first event information
 */
foreach ($module->getRelationalReport()->getChildrenArms() as $child) {
    /** @var \Stanford\ParentChild\ChildArm $child */
    $key = $child->getInstrument();
    $instrument = $child->getInstrumentLabel();
    $event = $child->getEventId();
    $module->getRelationalReport()->getEventCard($instrument, $key, $event);
}