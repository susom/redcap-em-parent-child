<?php

namespace Stanford\ParentChild;

/**
 * Class ParentArm
 * @package Stanford\ParentChild
 * @property int $eventId
 * @property string $instrument
 * @property array $record
 * @property array $dropDownList
 * @property string $url
 * @property Relation $relation
 */
class ParentArm extends Main
{

    private $eventId;

    private $instrument;

    private $record;

    private $dropDownList;

    private $url;

    private $relation;

    /**
     * ParentArm constructor.
     * @param int $eventId
     * @param int $projectId
     * @param string $parentDisplayLabel
     * @param Relation $relation
     */
    public function __construct($eventId, $projectId, $parentDisplayLabel, $relation)
    {
        try {
            $this->setEventId($eventId);

            /**
             * defined in main class
             */
            $this->setProjectId($projectId);

            /**
             * set instrument unique name
             */
            $this->setInstrument($this->getInstrumentNameViaEventId($this->getEventId()));

            /**
             * set instrument label
             */
            $this->getInstrumentMenuDescription($this->getInstrument());

            /**
             * Set this parent relation.
             */
            $this->setRelation($relation);
            /**
             * create the dropdown list
             */
            $records = Main::getRecords($this->getEventId());
            $list = array();
            foreach ($records as $id => $record) {
                $row = $record[$this->getEventId()];
                $list[$id] = $id . ' - ' . Main::replaceRecordLabels($parentDisplayLabel, $row);
            }

            if (!empty($list)) {
                $this->setDropDownList($list);
            }
        } catch (\LogicException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @return Relation
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * @param Relation $relation
     */
    public function setRelation($relation)
    {
        $this->relation = $relation;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $recordId
     */
    public function setUrl($recordId)
    {
        $this->url = $this->generateInsertRecordURL($this->getProjectId(), $this->getEventId(), $this->getInstrument(),
            $recordId);
    }

    /**
     * @return mixed
     */
    public function getDropDownList()
    {
        return $this->dropDownList;
    }

    /**
     * @param mixed $dropDownList
     */
    public function setDropDownList($dropDownList)
    {
        $this->dropDownList = $dropDownList;
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
     * @return string
     */
    public function getInstrument()
    {
        return $this->instrument;
    }

    /**
     * @param string $instrument
     */
    public function setInstrument($instrument)
    {
        $this->instrument = $instrument;
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


}