<?php

namespace Stanford\ParentChild;

/** @var \Stanford\ParentChild\ParentChild $module */

try {
    if (!isset($_GET['event_id'])) {
        throw new \LogicException("no event id found");
    }
    $module->setEventId(filter_var($_GET['event_id'], FILTER_SANITIZE_NUMBER_INT));
    if (!isset($_GET['pid'])) {
        throw new \LogicException("no project id found");
    }
    $module->setProjectId(filter_var($_GET['pid'], FILTER_SANITIZE_NUMBER_INT));
    if (!isset($_GET['id'])) {
        throw new \LogicException("no record id found");
    }
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    //init search object with parent
    $module->setSearchRelation(new SearchRelation($module->getEventId(), $module->getProjectId(), ''));

    //we are using id as search term
    $module->getSearchRelation()->setSearchTerm($id);

    $module->includeFile("view/search/index.php");
} catch (\LogicException $e) {
    echo "<div class='alert-danger'>" . $e->getMessage() . "</div>";
}