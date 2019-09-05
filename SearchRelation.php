<?php

namespace Stanford\ParentChild;

/**
 * Class SearchRelation
 * @package Stanford\ParentChild
 * @property int $eventId
 * @property ParentArm $topParentArm
 * @property string $searchTerm
 * @property array $recordsList
 */
class SearchRelation extends Main
{

    private $eventId;

    private $topParentArm;

    private $searchTerm;

    private $recordsList;

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
        foreach ($this->getTopParentArm()->getFields() as $field) {
            $result = Main::searchRecords($this->getEventId(), $field, $this->getSearchTerm());
            if (!empty($result)) {
                foreach ($result as $id => $row) {
                    //check if record already found via different field
                    if (!in_array($id, $this->getRecordsList())) {
                        $record = $row[$this->getEventId()];
                        $records = $this->getRecordsList();
                        $records[$id] = $record;
                        $this->setRecordsList($records);
                    }
                }

            }
        }
    }
}