<?php

namespace Stanford\ParentChild;

/**
 * Class ParentArm
 * @package Stanford\ParentChild
 * @property int $eventId
 * @property int $armId
 * @property string $instrument
 * @property string $instrumentLabel
 * @property array $record
 * @property array $dropDownList
 * @property string $url
 * @property Relation $relation
 * @property Array $fields
 */
class ParentArm extends Main
{

    private $eventId;

    private $instrument;

    private $record;

    private $dropDownList;

    private $url;

    private $relation;

    private $armId;

    private $topForeignKey;

    private $fields;

    private $instrumentLabel;
    /**
     * ParentArm constructor.
     * @param int $eventId
     * @param int $projectId
     * @param string $parentDisplayLabel
     * @param Relation $relation
     */
    public function __construct($eventId, $projectId, $parentDisplayLabel, $relation = null, $fallback = null)
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
            $this->setInstrumentLabel($this->getInstrumentMenuDescription($this->getInstrument()));

            /**
             * Set Arm ID
             */
            $this->setArmId($this->getArmIdViaEventId($this->getEventId()));
            /**
             * set instrument label
             */
            $this->getInstrumentMenuDescription($this->getInstrument());

            if (!is_null($relation)) {
                /**
                 * Set this parent relation.
                 */
                $this->setRelation($relation);

                /**
                 * if top parent defined then use it to shorten the dropdown list.
                 */
                if (!is_null($fallback)) {
                    $records = Main::searchRecords($this->getEventId(), $fallback['field'], $fallback['record_id']);
                }
            }
            /**
             * create the dropdown list
             */
            if ($records == false) {
                $records = Main::getRecords($this->getEventId());
            }

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
     * @return string
     */
    public function getInstrumentLabel()
    {
        return $this->instrumentLabel;
    }

    /**
     * @param string $instrumentLabel
     */
    public function setInstrumentLabel($instrumentLabel)
    {
        $this->instrumentLabel = $instrumentLabel;
    }

    /**
     * @return Array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param Array $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return string
     */
    public function getTopForeignKey()
    {
        return $this->topForeignKey;
    }

    /**
     * @param string $topForeignKey
     */
    public function setTopForeignKey($topForeignKey)
    {
        $this->topForeignKey = $topForeignKey;
    }

    /**
     * @return int
     */
    public function getArmId()
    {
        return $this->armId;
    }

    /**
     * @param int $armId
     */
    public function setArmId($armId)
    {
        $this->armId = $armId;
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
    public function setUrl($recordId = null)
    {
        if (is_null($recordId)) {
            $recordId = $this->getNextId($this->getProjectId(), $this->getEventId());
        }
        $this->url = Main::getRecordHomeURL($this->getProjectId(), $this->getArmId(), $recordId);
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