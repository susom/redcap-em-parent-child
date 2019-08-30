<?php

namespace Stanford\ParentChild;

use REDCap;

include_once "Main.php";
include_once "ParentArm.php";
include_once "ChildArm.php";
include_once "Relation.php";
include_once "RelationalReport.php";


define("PARENT_EVENT", "parent_event");
define("CHILD_EVENT", "child_event");
define("CHILD_FOREIGN_KEY", "child_foreign_key");
define("PARENT_DISPLAY_LABEL", "parent_display_label");

/**
 * Class ParentChild
 * @package Stanford\ParentChild
 * @property ParentArm $parentArm
 * @property array $childrenArms
 * @property array $parentRelation
 * @property Relation $childRelation
 * @property array $instances
 * @property int $projectId
 * @property int $eventId
 * @property string $instrument
 * @property boolean $injectElement
 * @property boolean $orphan
 * @property boolean $dirty
 * @property array $record
 * @property string $parentRow
 * @property RelationalReport $relationalReport
 */
class ParentChild extends \ExternalModules\AbstractExternalModule
{

    /**
     * @var
     */
    private $parentArm;

    /**
     * @var
     */
    private $childrenArms;

    /**
     * @var
     */
    private $parentRelation;

    /**
     * @var
     */
    private $childRelation;

    /**
     * @var
     */
    private $instances;

    /**
     * @var
     */
    private $projectId;

    /**
     * @var
     */
    private $eventId;

    /**
     * @var
     */
    private $instrument;

    /**
     * @var
     */
    private $injectElement;

    /**
     * @var
     */
    private $orphan;

    /**
     * @var
     */
    private $record;

    /**
     * @var
     */
    private $dirty;

    /**
     * @var
     */
    private $parentRow;

    private $relationalReport;
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
     * @return RelationalReport
     */
    public function getRelationalReport()
    {
        return $this->relationalReport;
    }

    /**
     * @param RelationalReport $relationalReport
     */
    public function setRelationalReport($relationalReport)
    {
        $this->relationalReport = $relationalReport;
    }

    /**
     * @return string
     */
    public function getParentRow()
    {
        return $this->parentRow;
    }

    /**
     * @param string $parentRow
     */
    public function setParentRow($parentRow)
    {
        $this->parentRow = $parentRow;
    }

    /**
     * @return bool
     */
    public function isDirty()
    {
        return $this->dirty;
    }

    /**
     * @param bool $dirty
     */
    public function setDirty($dirty)
    {
        $this->dirty = $dirty;
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
     * @return bool
     */
    public function isOrphan()
    {
        return $this->orphan;
    }

    /**
     * @param bool $orphan
     */
    public function setOrphan($orphan)
    {
        $this->orphan = $orphan;
    }


    /**
     * @return bool
     */
    public function isInjectElement()
    {
        return $this->injectElement;
    }

    /**
     * @param bool $injectElement
     */
    public function setInjectElement($injectElement)
    {
        $this->injectElement = $injectElement;
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
     * @return array
     */
    public function getChildrenArms()
    {
        return $this->childrenArms;
    }

    /**
     * @param ChildArm $childrenArms
     */
    public function setChildrenArms($childrenArms)
    {
        $this->childrenArms[] = $childrenArms;
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

    public function setInstances()
    {
        $this->instances = $this->getSubSettings('instance', $this->getProjectId());;
    }

    /**
     * @return array
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
        $this->parentRelation[] = $parentRelation;
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

        $this->setRecord(Main::getRecords($this->getEventId(), $record));


        //This event is parent of other children set the relation and init the these child object
        $parent = $this->getParentEventRelation($event_id);
        if ($parent !== false) {
            foreach ($parent as $p) {

                $relation = new Relation($p);

                $child = new ChildArm($p[CHILD_EVENT], $project_id, $relation);

                $this->setChildrenArms($child);


            }

            $this->setInjectElement(true);

            $this->includeFile("view/parent.php");
        }

        //This event is child of another event set relation and parent
        $child = $this->getChildEventRelation($event_id);
        if ($child !== false) {
            $relation = new Relation($child);

            $this->setParentArm(new ParentArm($child[PARENT_EVENT], $project_id, $relation->getParentDisplayLabel(),
                $relation));

            /**
             * if parent id is passed load its record
             */
            if (isset($_GET['parent'])) {
                $this->getParentArm()->setRecord(Main::getRecords($this->getParentArm()->getEventId(),
                    filter_var($_GET['parent'], FILTER_SANITIZE_NUMBER_INT)));
                /**
                 * if not parent record this this record is orphan and we need to mark it as dirty
                 */
                if (empty($this->getParentArm()->getRecord())) {
                    $this->setOrphan(true);
                }
            }
            /**
             * in case we are editing child record directly them pull its parent
             */
            if (!empty($this->getRecord())) {
                $parentRecordId = $this->getRecord()[$record][$this->getEventId()][$child[CHILD_FOREIGN_KEY]];

                $this->getParentArm()->setRecord(Main::getRecords($this->getParentArm()->getEventId(),
                    $parentRecordId));

                /**
                 * if not parent record this this record is orphan and we need to mark it as dirty
                 */
                if (empty($this->getParentArm()->getRecord())) {
                    $this->setOrphan(true);
                } else {
                    /**
                     * set the URL for parent record
                     */
                    $this->getParentArm()->setUrl($parentRecordId);

                    $this->setParentRow("<div id='parent-row' data-parent-id='" . $parentRecordId . "'><a href='" . $this->getParentArm()->getUrl() . "'>Parent Record for this record is " . $this->getParentArm()->getDropDownList()[$parentRecordId] . "</a><a class='float-right' href='javascript:;'><img class='show-list' alt='Edit Parent' src='/redcap_v9.2.5/Resources/images/pencil.png'></a></div>");
                }
            } else {
                $this->setDirty(true);
            }


            $this->includeFile("view/Child.php");
        }
    }

    /**
     * Event could have multiple children
     * @param int $eventId
     * @param string $type
     * @return bool|array
     */
    public function getParentEventRelation($eventId)
    {
        $result = array();
        foreach ($this->getInstances() as $instance) {
            if ($instance[PARENT_EVENT] == $eventId) {
                $result[] = $instance;
            }
        }
        if (!empty($result)) {
            return $result;
        }
        return false;
    }


    /**
     *
     * @param int $eventId
     * @param string $type
     * @return bool|array
     */
    private function getChildEventRelation($eventId)
    {
        foreach ($this->getInstances() as $instance) {
            if ($instance[CHILD_EVENT] == $eventId) {
                return $instance;
            }
        }
        return false;
    }

    public function redcap_every_page_top()
    {
        //TODO represent parent record in the display page for child record.
        //TODO add ajax call to pull list of children and allow user to navigate to them
    }

    /**
     * @param string $path
     */
    private function includeFile($path)
    {
        include_once $path;
    }

    public function getChildRecords($event, $recordId, $foreignKey)
    {
        if ($_POST && isset($_POST['instrument']) && isset($_POST['event'])) {
            $params = array(
                'return_format' => 'array',
                'events' => $event,
                'filterLogic' => "[$foreignKey] = '$recordId'"
            );
            return REDCap::getData($params);
        } else {
            throw new \LogicException("Data is missing");
        }
    }
}