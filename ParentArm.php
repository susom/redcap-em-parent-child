<?php

namespace Stanford\ParentChild;

/**
 * Class ParentArm
 * @package Stanford\ParentChild
 * @property int $eventId
 * @property int $armId
 * @property int $tempRecordId
 * @property string $instrument
 * @property string $instrumentLabel
 * @property array $record
 * @property string $recordId
 * @property array $dropDownList
 * @property string $url
 * @property string $recordPrefix
 * @property Relation $relation
 * @property array $fields
 * @property \Project $project
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

    private $tempRecordId;

    private $project;

    private $recordPrefix;

    private $recordId;

    /**
     * ParentArm constructor.
     * @param int $eventId
     * @param int $projectId
     * @param string $parentDisplayLabel
     * @param Relation $relation
     */
    public function __construct(
        $eventId,
        $projectId,
        $parentDisplayLabel,
        $relation = null,
        $fallback = null,
        $recordIdPrefix = ''
    ) {
        try {
            global $Proj;

            $this->setProject($Proj);

            $this->setEventId($eventId);

            /**
             * defined in main class
             */
            $this->setProjectId($projectId);

            /**
             * set instrument unique name
             */
            $this->setInstrument($this->getProject()->eventsForms[$this->getEventId()][0]);

            /**
             * set instrument record prefix
             */
            $this->setRecordPrefix($recordIdPrefix);

            /**
             * set instrument label
             */
            $this->setInstrumentLabel($this->getProject()->forms[$this->getInstrument()]['menu']);

            /**
             * Set Arm ID
             */
            $this->setArmId(Main::getArmIdViaEventId($this->getEventId()));

            if (!is_null($relation)) {
                /**
                 * Set this parent relation.
                 */
                $this->setRelation($relation);

                /**
                 * if top parent defined then use it to shorten the dropdown list.
                 */
                if (!is_null($fallback) && !empty($fallback)) {
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
     * @return int
     */
    public function getTempRecordId()
    {
        return $this->tempRecordId;
    }

    /**
     * @param int $tempRecordId
     */
    public function setTempRecordId($tempRecordId)
    {
        $this->tempRecordId = $tempRecordId;
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
            $this->setRecordId($this->getNextId($this->getProjectId(), $this->getEventId(), $this->getRecordPrefix()));
        } else {
            $this->setRecordId($recordId);
        }
        $this->url = Main::getRecordHomeURL($this->getProjectId(), $this->getInstrument(), $this->getEventId(),
            $this->getRecordId());
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

    /**
     * @return \Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param \Project $project
     */
    public function setProject(\Project $project)
    {
        $this->project = $project;
    }

    /**
     * @return string
     */
    public function getRecordPrefix()
    {
        return $this->recordPrefix;
    }

    /**
     * @param string $recordPrefix
     */
    public function setRecordPrefix($recordIdPrefix)
    {
        if ($recordIdPrefix != '') {
            $this->recordPrefix = $recordIdPrefix;
        } else {
            $this->recordPrefix = Main::getRecordIdPrefix($this->getInstrument());
        }
    }

    /**
     * @return string
     */
    public function getRecordId()
    {
        return $this->recordId;
    }

    /**
     * @param string $recordId
     */
    public function setRecordId(string $recordId)
    {
        $this->recordId = $recordId;
    }


}