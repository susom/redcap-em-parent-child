<?php

namespace Stanford\ParentChild;


/**
 * Class ChildArm
 * @package Stanford\ParentChild
 * @property int $eventId
 * @property int $armId
 * @property int $recordId
 * @property string $button
 * @property string $instrument
 * @property string $instrumentLabel
 * @property string $url
 * @property array $record
 * @property Relation $relation
 */
class ChildArm extends Main
{

    private $eventId;

    private $button;

    private $instrument;

    private $instrumentLabel;

    private $url;

    private $record;

    private $relation;

    private $recordId;

    private $armId;
    /**
     * ChildArm constructor.
     * @param int $eventId
     * @param int $projectId
     * @param Relation $relation
     */
    public function __construct($eventId, $projectId, $relation = null)
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
            $this->setInstrumentLabel($this->getInstrumentMenuDescription($this->getInstrument(),
                $this->getProjectId()));


            $this->setRecordId($this->getNextId($this->getProjectId(), $this->getEventId()));


            if (!is_null($relation)) {
                /**
                 * Set this child relation.
                 */
                $this->setRelation($relation);
            }
        } catch (\LogicException $e) {
            echo $e->getMessage();
        }
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
     * @return int
     */
    public function getRecordId()
    {
        return $this->recordId;
    }

    /**
     * @param int $recordId
     */
    public function setRecordId($recordId)
    {
        $this->recordId = $recordId;

        /**
         * set url to insert child record
         */
        $this->setUrl($this->generateInsertRecordURL($this->getProjectId(), $this->getEventId(), $this->getInstrument(),
            $recordId));
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
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
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

    /**
     * @return string
     */
    public function getButton()
    {
        return $this->button;
    }

    /**
     * @param string $button
     */
    public function setButton($button)
    {
        $this->button = $button;
    }
}