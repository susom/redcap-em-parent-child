<?php

namespace Stanford\ParentChild;

include_once "ParentChild.php";
include_once "ChildArm.php";
include_once "Relation.php";

use REDCap;

define("PARENT_EVENT", "parent_event");
define("CHILD_EVENT", "child_event");
define("CHILD_FOREIGN_KEY", "child_foreign_key");

/**
 * Class ParentChild
 * @package Stanford\ParentChild
 * @property \Stanford\ParentChild\ParentArm $parentArm
 * @property \Stanford\ParentChild\ChildArm $childArm
 * @property \Stanford\ParentChild\Relation $parentRelation
 * @property \Stanford\ParentChild\Relation $childRelation
 * @property array $instances
 * @property int $projectId
 * @property int $eventId
 * @property string $instrument
 */
class ParentChild extends \ExternalModules\AbstractExternalModule
{

    /**
     * @var \Stanford\ParentChild\ParentArm
     */
    private $parentArm;

    /**
     * @var \Stanford\ParentChild\ChildArm
     */
    private $childArm;

    /**
     * @var \Stanford\ParentChild\Relation
     */
    private $parentRelation;

    /**
     * @var \Stanford\ParentChild\Relation
     */
    private $childRelation;

    /**
     * @var array
     */
    private $instances;

    /**
     * @var int
     */
    private $projectId;

    /**
     * @var int
     */
    private $eventId;

    /**
     * @var string
     */
    private $instrument;


    public function __construct()
    {
        try {
            parent::__construct();


            if ($_GET && $_GET['pid'] != null) {
                $this->setProjectId(filter_var($_GET['pid'], FILTER_SANITIZE_NUMBER_INT));

                $this->setInstances();
            }
        } catch (\LogicException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @return \Stanford\ParentChild\ParentArm
     */
    public function getParentArm()
    {
        return $this->parentArm;
    }

    /**
     * @param \Stanford\ParentChild\ParentArm $parentArm
     */
    public function setParentArm($parentArm)
    {
        $this->parentArm = $parentArm;
    }

    /**
     * @return int
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * @param int $projectId
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;
    }

    /**
     * @return ChildArm
     */
    public function getChildArm()
    {
        return $this->childArm;
    }

    /**
     * @param ChildArm $childArm
     */
    public function setChildArm($childArm)
    {
        $this->childArm = $childArm;
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
     * @return array
     */
    public function getInstances()
    {
        return $this->instances;
    }

    /**
     * @param array $instance
     */
    public function setInstances()
    {
        $this->instances = $this->getSubSettings('instance', $this->getProjectId());;
    }

    /**
     * @return Relation
     */
    public function getParentRelation()
    {
        return $this->parentRelation;
    }

    /**
     * @param Relation $parentRelation
     */
    public function setParentRelation($parentRelation)
    {
        $this->parentRelation = $parentRelation;
    }

    /**
     * @return Relation
     */
    public function getChildRelation()
    {
        return $this->childRelation;
    }

    /**
     * @param Relation $childRelation
     */
    public function setChildRelation($childRelation)
    {
        $this->childRelation = $childRelation;
    }


    /**
     * @param int $project_id
     * @param null|int $record
     * @param int $instrument
     * @param int $event_id
     * @param null|int $group_id
     * @param int $repeat_instance
     */
    public function redcap_data_entry_form(
        $project_id,
        $record = null,
        $instrument,
        $event_id,
        $group_id = null,
        $repeat_instance = 1
    ) {

        //define my event and instrument;
        $this->setEventId($event_id);
        $this->setInstrument($instrument);

        //Check if this instrument is parent
        $parent = $this->getEventRelation($event_id, PARENT_EVENT);
        if ($parent !== false) {
            $this->setParentRelation(new Relation($parent[PARENT_EVENT], $parent[CHILD_EVENT],
                $parent[CHILD_FOREIGN_KEY]));

            $this->setChildArm(new ChildArm($parent[CHILD_EVENT]));

            //TODO inject button to add child record in UI
        }

        //check if this instrument is child
        $child = $instance = $this->getEventRelation($event_id, CHILD_EVENT);
        if ($child !== false) {
            $this->setChildRelation(new Relation($child[PARENT_EVENT], $child[CHILD_EVENT], $child[CHILD_FOREIGN_KEY]));

            $this->setParentArm(new ParentArm($child[PARENT_EVENT]));

            //TODO update parent list and make it drop down
        }
    }

    /**
     * @param int $eventId
     * @param string $type
     * @return bool|array
     */
    private function getEventRelation($eventId, $type)
    {
        foreach ($this->getInstances() as $instance) {
            if ($instance[$type] == $eventId) {
                return $instance;
            }
        }
        return false;
    }
}