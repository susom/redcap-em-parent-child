<?php

namespace Stanford\ParentChild;

/** @var \Stanford\ParentChild\ParentChild $module */

try {
    $id = filter_var($_POST["id"], FILTER_SANITIZE_STRING);
    if ($id) {
        $topParentId = filter_var($_POST["topParentId"], FILTER_SANITIZE_STRING);
        $eventId = filter_var($_POST["event"], FILTER_SANITIZE_STRING);

        if (is_null($eventId) && isset($_GET['arm'])) {
            $arm = filter_var($_GET['arm'], FILTER_SANITIZE_NUMBER_INT);
        }

        if (is_null($eventId)) {
            throw new \LogicException("Cant build the tree for this record");
        }
        //create search object by defining parent event arm
        $module->setSearchRelation(new SearchRelation($eventId, $module->getProjectId(), ''));
        //check if there is any children for this event
        $children = $module->getParentEventRelation($eventId);


        $childrenRecords = $module->getSearchRelation()->getChildrenRecords($children, $id, $topParentId);
        $result = array();
        $primaryKey = $module->getProject()->table_pk;
        if ($children != false) {
            foreach ($children as $child) {

                $relation = new Relation($child);


                //temp child object to get some info from it.
                $tempChild = new ChildArm($child[ParentChild::$CHILD_EVENT], $module->getProjectId(), $relation,
                    $module->getEventRecordPrefix($child[ParentChild::$CHILD_EVENT], $id));

                //get child records related to parent id
                if (!empty($childrenRecords) && isset($childrenRecords[$child[ParentChild::$CHILD_EVENT]])) {
                    $records = $childrenRecords[$child[ParentChild::$CHILD_EVENT]];
                } else {
                    $records = false;
                }

                //we need to know this event label when its a parent
                $childAsParent = $module->getParentEventRelation($child[ParentChild::$CHILD_EVENT]);


                $record = $module->getRecord();
                $record = $record[filter_var($_GET['id'], FILTER_SANITIZE_STRING)][$module->getEventId()];
                /* @var $child ChildArm */
                $arr = array(
                    "url" => trim(APP_PATH_WEBROOT_FULL, "/") . $tempChild->getUrl() . "&parent=" . $id,
                    "label" => $tempChild->getInstrumentLabel(),
                    "childInstrument" => $tempChild->getInstrument(),
                    "childEvent" => $tempChild->getEventId(),
                    "foreignKey" => $tempChild->getRelation()->getForeignKey(),
                    "topParentForeignKey" => $tempChild->getRelation()->getTopForeignKey(),
                    "topParentRecordId" => $record[$tempChild->getRelation()->getTopForeignKey()],
                );
                $result['urls'][] = $arr;
                $result['children'][$child[ParentChild::$CHILD_EVENT]] = array(
                    'label' => $tempChild->getInstrumentLabel(),
                    'count' => ($records ? count($records) : 0),
                    'childAsParent' => $childAsParent,
                );
                $tempRecords = array();
                if ($records) {
                    foreach ($records as $record) {
                        $item = $record[$tempChild->getEventId()];
                        $label = Main::replaceRecordLabels($child[ParentChild::$CHILD_DISPLAY_LABEL], $item);
                        if ($label == false) {
                            $label = $module->limitInstrumentFieldsOnly($tempChild->getInstrument(), $item);
                        }

                        $tempRecords[] = array(
                            'id' => $item[$primaryKey],
                            'instrument' => $tempChild->getInstrument(),
                            'event_id' => $tempChild->getEventId(),
                            'label' => !is_array($label) ? $label : implode(", ", $label),
                            'topParentId' => $item[$tempChild->getRelation()->getForeignKey()],
                            'url' => Main::getRecordHomeURL($module->getProjectId(), $tempChild->getInstrument(),
                                $tempChild->getEventId(), $item[$primaryKey])
                        );

                    }
                    $result['children'][$child[ParentChild::$CHILD_EVENT]]['records'] = $tempRecords;
                }
            }
        }
        echo json_encode(array('status' => 'success', 'data' => $result));
    } else {
        throw new \LogicException("no record ID passed");
    }
} catch (\LogicException $e) {
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}