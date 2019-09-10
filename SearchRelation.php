<?php

namespace Stanford\ParentChild;

/**
 * Class SearchRelation
 * @package Stanford\ParentChild
 * @property int $eventId
 * @property ParentArm $topParentArm
 * @property string $searchTerm
 * @property array $recordsList
 * @property array $record
 */
class SearchRelation extends Main
{
    /**
     * @var int
     */
    private $eventId;

    /**
     * @var ParentArm
     */
    private $topParentArm;

    /**
     * @var string
     */
    private $searchTerm;

    /**
     * @var array
     */
    private $recordsList;

    /**
     * @var array
     */
    private $record;

    /**
     * SearchRelation constructor.
     * @param int $eventId
     * @param int $projectId
     * @param string $parentDisplayLabel
     */
    public function __construct($eventId, $projectId, $parentDisplayLabel)
    {
        try {
            $this->setEventId($eventId);

            $this->setProjectId($projectId);

            $this->setTopParentArm(new ParentArm($this->getEventId(), $this->getProjectId(), $parentDisplayLabel));
        } catch (\LogicException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @return array
     */
    public function getRecord()
    {
        return $this->record;
    }

    /**
     * @param array $record
     */
    public function setRecord($record)
    {
        $this->record = $record;
    }

    /**
     * @return array
     */
    public function getRecordsList()
    {
        return $this->recordsList;
    }

    /**
     * @param array $recordsList
     */
    public function setRecordsList($recordsList)
    {
        $this->recordsList = $recordsList;
    }

    /**
     * @return string
     */
    public function getSearchTerm()
    {
        return $this->searchTerm;
    }

    /**
     * @param string $searchTerm
     */
    public function setSearchTerm($searchTerm)
    {
        $this->searchTerm = $searchTerm;
    }

    /**
     * @return ParentArm
     */
    public function getTopParentArm()
    {
        return $this->topParentArm;
    }

    /**
     * @param ParentArm $topParentArm
     */
    public function setTopParentArm($topParentArm)
    {
        $this->topParentArm = $topParentArm;
    }

    /**
     * @return int
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * @param int $eventId
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;
    }

    public function searchTopParent()
    {
        $records = $result = Main::getRecords($this->getEventId());
        foreach ($records as $recordId => $record) {
            $result = array_search($this->getSearchTerm(), $record[$this->getEventId()]);

            if ($result) {
                //check if record already found via different field
                if (!in_array($result, $this->getRecordsList())) {
                    $row = $record[$this->getEventId()];
                    $list = $this->getRecordsList();
                    $list[$result] = $row;
                    $this->setRecordsList($list);
                }

            }
        }
    }

    /**
     * @param array $children
     * @param int $recordsId
     * @param int $topParentId
     * @return array
     */
    public function getChildrenRecords($children, $recordsId, $topParentId)
    {
        $result = array();
        //pull all records for all events one time.
        $events = array();
        foreach ($children as $child) {
            $events[] = $child[CHILD_EVENT];
        }
        $records = Main::getRecords($events);
        foreach ($records as $id => $record) {
            foreach ($children as $child) {
                if ($record[$child[CHILD_EVENT]][$child[CHILD_FOREIGN_KEY]] == $recordsId) {
                    $result[$child[CHILD_EVENT]][$id] = $record;
                } elseif ($record[$child[CHILD_EVENT]][$child[TOP_FOREIGN_KEY]] == $topParentId) {
                    $result[$child[CHILD_EVENT]][$id] = $record;
                }
            }
        }

        return $result;
    }
}