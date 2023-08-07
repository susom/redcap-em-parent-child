<?php
//test comment
namespace Stanford\ParentChild;

ini_set("memory_limit", "-1");
set_time_limit(0);

use function Aws\filter;
use REDCap;

include_once __DIR__ . "/Main.php";
include_once __DIR__ . "/ParentArm.php";
include_once __DIR__ . "/ChildArm.php";
include_once __DIR__ . "/Relation.php";
include_once __DIR__ . "/RelationalReport.php";
include_once __DIR__ . "/SearchRelation.php";
include_once __DIR__ . "/emLoggerTrait.php";

define("PARENT_EVENT", "parent_event");
define("CHILD_EVENT", "child_event");
define("CHILD_FOREIGN_KEY", "child_foreign_key");
define("PARENT_DISPLAY_LABEL", "parent_display_label");
define("CHILD_DISPLAY_LABEL", "child_display_label");
define("DISPLAY_CHILDREN_RECORDS", "display_children_records");
define("TOP_FOREIGN_KEY", "top_foreign_key");
define("TOP_PARENT_DISPLAY_LABEL", "top_parent_display_label");
define("RECORD_ID_PREFIX", "record_id_prefix");

/**
 * Class ParentChild
 * @package Stanford\ParentChild
 * @property ParentArm $parentArm
 * @property ParentArm $topParentArm
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
 * @property string $topParentRow
 * @property string $addRecordURL
 * @property string $recordPrefix
 * @property string $recordId
 * @property RelationalReport $relationalReport
 * @property SearchRelation $searchRelation
 * @property array $roles
 * @property \Project $project
 */
class ParentChild extends \ExternalModules\AbstractExternalModule
{

    use emLoggerTrait;
    //TODO add new way to define relation without using config.json

    /**
     * @var
     */
    private $parentArm;

    private $topParentArm;

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

    private $topParentRow;

    private $addRecordURL;

    private $relationalReport;

    private $searchRelation;

    private $roles;

    private $project;

    private $recordId;

    public function __construct()
    {
        try {
            parent::__construct();

            global $Proj;

            if ($_GET && $_GET['pid'] != null) {
                $this->setProjectId(filter_var($_GET['pid'], FILTER_SANITIZE_NUMBER_INT));

                $this->setInstances();

                # add allowed roles is defined
                $this->setRoles();

                $this->setProject($Proj);
            }
        } catch (\LogicException $e) {
            echo $e->getMessage();
        }
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
            $this->includeFile("view/parent.php");
        }

        //This event is child of another event set relation and parent
        $children = $this->getChildEventRelation($event_id);
        if (!empty($children)) {
            foreach ($children as $child) {
                $relation = new Relation($child);

                /**
                 * now see if foreign key is not available then fallback to parent
                 */
                $fallbackId = array();
                $fallback = array();
                //no value in record for foreign key then lets limit dropdown to parent record if exists
                if ($this->getRecord()[$record][$this->getEventId()][$relation->getForeignKey()] == "") {
                    if ($this->getRecord()[$record][$this->getEventId()][$relation->getTopForeignKey()] != "") {
                        $temp = $this->getChildEventRelation($relation->getParentEventId());
                        $fallback['record_id'] = $this->getRecord()[$record][$this->getEventId()][$relation->getTopForeignKey()];
                        $fallback['field'] = $temp[CHILD_FOREIGN_KEY];
                    }
                }

                $this->setParentArm(new ParentArm($child[PARENT_EVENT], $project_id, $relation->getParentDisplayLabel(),
                    $relation, $fallback, $child[RECORD_ID_PREFIX]));

                /**
                 * if parent id is passed load its record
                 */
                if (isset($_GET['parent'])) {
                    /**
                     * set passed record as temp so can be selected by dropdown
                     */
                    $this->getParentArm()->setTempRecordId(filter_var($_GET['parent'], FILTER_SANITIZE_STRING));

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

                    $this->emLog("Parent Record ID:" . $parentRecordId);
                    if ($parentRecordId != "") {
                        $this->getParentArm()->setRecord(Main::getRecords($this->getParentArm()->getEventId(),
                            $parentRecordId));
                    }

                    /**
                     * if not parent record this this record is orphan and we need to mark it as dirty
                     */
                    if (empty($this->getParentArm()->getRecord())) {
                        $this->setOrphan(true);

                        /**
                         * if case no direct parent exists then use top parent
                         */
                        $parentRecordId = $this->getRecord()[$record][$this->getEventId()][$child[TOP_FOREIGN_KEY]];

                        /**
                         * for temp only we will create parent object for top parent. and create temp relation between current event and top parent.
                         */
                        $instance = array(
                            PARENT_EVENT => $this->getFirstEventId(),
                            CHILD_EVENT => $this->getEventId(),
                            CHILD_FOREIGN_KEY => $this->getProjectSetting(TOP_PARENT_DISPLAY_LABEL)
                        );

                        $relation = new Relation($instance);

                        $this->setTopParentArm(new ParentArm($this->getFirstEventId(), $project_id,
                            $this->getProjectSetting(TOP_PARENT_DISPLAY_LABEL), $relation));
                        $this->getTopParentArm()->setRecord(Main::getRecords($this->getFirstEventId(), $parentRecordId));
                        $this->getTopParentArm()->setUrl($parentRecordId);
                        /**
                         * this will make sure record show up in correct position.
                         */
                        $this->getTopParentArm()->getRelation()->setTopForeignKey($child[TOP_FOREIGN_KEY]);

                        /**
                         * top parent row is not editable.
                         */
                        $this->setTopParentRow("<div id='parent-row' data-parent-id='" . $parentRecordId . "'><a href='" . $this->getTopParentArm()->getUrl() . "'>" . $this->getTopParentArm()->getDropDownList()[$parentRecordId] . "</a></div>");
                    } else {
                        if (!$this->getParentArm()->getTempRecordId()) {
                            /**
                             * set the URL for parent record
                             */
                            $this->getParentArm()->setUrl($parentRecordId);
                            $this->setParentRow("<div id='parent-record-id-" . $parentRecordId . "' data-parent-id='" . $parentRecordId . "'><a href='" . $this->getParentArm()->getUrl() . "'>" . $this->getParentArm()->getDropDownList()[$parentRecordId] . "</a><a class='float-right' href='javascript:;'><img data-parent-input-name='" . $this->getParentArm()->getRelation()->getForeignKey() . "' data-parent-id='" . $parentRecordId . "' class='show-list' alt='Edit Parent' src='" . APP_PATH_WEBROOT . "Resources/images/pencil.png'></a>$dropdown</div>");
                        }
                    }
                } else {
                    $this->setDirty(true);
                }

                $this->includeFile("view/child.php");
            }
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
    public function getChildEventRelation($eventId)
    {
        $results = array();
        foreach ($this->getInstances() as $instance) {
            if ($instance[CHILD_EVENT] == $eventId) {
                $results[] = $instance;
            }
        }
        return $results;
    }

    /**
     * @param string $path
     */
    public function includeFile($path)
    {
        require $path;
    }

    public function getChildRecords($event, $recordId, $foreignKey, $topParentRecordId = null)
    {
        if ($_POST && isset($_POST['instrument']) && isset($_POST['event'])) {

            $records = Main::searchRecords($event, $foreignKey, $recordId);

            /**
             * if no records found lets check the fall back parent if defined
             */
            if (empty($records)) {
                $instance = $this->searchInstances($event, $foreignKey, CHILD_EVENT);
                if ($instance[TOP_FOREIGN_KEY] != "" && $topParentRecordId != null) {
                    $foreignKey = $instance[TOP_FOREIGN_KEY];
                    return Main::searchRecords($event, $foreignKey, $topParentRecordId);
                }
            } else {
                return $records;
            }
        } else {
            throw new \LogicException("Data is missing");
        }
    }

    /**
     * this hook will force add record only for main using top parent
     * @param int $project_id
     * @param string $instrument
     * @param int $event_id
     */
    public function redcap_add_edit_records_page($project_id, $instrument, $event_id)
    {
        $this->setProjectId($project_id);
        $this->setEventId($this->getFirstEventId());

        $parent = $this->getParentEventRelation($this->getEventId());
        $instance = end($parent);

        $this->setTopParentArm(new ParentArm($this->getEventId(), $this->getProjectId(), '', null, null,
            $instance['record_id_prefix']));
        $this->getTopParentArm()->setUrl();
        $this->setInstrument($instrument);
        $this->setRecordId($this->getTopParentArm()->getRecordId());
        $this->includeFile("view/form.php");
    }

    public function limitInstrumentFieldsOnly($instrument, $item)
    {
        $id = $item[$this->getProject()->table_pk];
        $instrumentFields = $this->getProject()->forms[$instrument]['fields'];
        $temp = array();
        foreach ($instrumentFields as $key => $field) {
            $temp[$field] = $item[$key];
        }
        /**
         * make sure to get the primary record id
         */
        $temp[$this->getProject()->table_pk] = $id;
        return $temp;
    }

    private function searchInstances($eventId, $foreignKey, $type)
    {
        foreach ($this->getInstances() as $instance) {
            if ($instance[$type] == $eventId && $instance[CHILD_FOREIGN_KEY] == $foreignKey) {
                return $instance;
            }
        }
        return false;
    }

    public function redcap_every_page_top()
    {
        // in case we are loading record homepage load its the record children if existed
        if (strpos(PAGE, 'DataEntry/record_home') !== false && $this->isUserRoleAllowed()) {

            $this->setProjectId(filter_var($_GET['pid'], FILTER_SANITIZE_NUMBER_INT));

            $this->setEventId($this->getProject()->getFirstEventIdArm(filter_var($_GET['arm'],
                FILTER_SANITIZE_NUMBER_INT)));

            $this->setRecordId(filter_var($_GET['id'], FILTER_SANITIZE_STRING));
            //create search object by defining parent event arm
            $this->setSearchRelation(new SearchRelation($this->getEventId(), $this->getProjectId(), ''));

            $this->includeFile("view/record/home.php");
        } elseif (strpos(PAGE, 'DataEntry/record_status_dashboard') !== false) {

            $this->setProjectId(filter_var($_GET['pid'], FILTER_SANITIZE_NUMBER_INT));
            $this->setEventId($this->getFirstEventId());
            $this->setTopParentArm(new ParentArm($this->getEventId(), $this->getProjectId(), ''));
            $this->getTopParentArm()->setUrl();
            $this->includeFile("view/form.php");
        }
    }

    /**
     * @return bool
     */
    private function isUserRoleAllowed()
    {
        if ($this->getRoles() && !empty($this->getRoles())) {
            $users = \REDCap::getUserRights();
            $user = $users[USERID];
            if (in_array($user['role_id'], $this->getRoles())) {
                return true;
            }
            return false;
        }
        return true;
    }

    public function getEventRecordPrefix($eventId, $parentRecordId = '')
    {
        $parent = $this->getParentEventRelation($eventId);
        $instance = end($parent);
        if ($instance[RECORD_ID_PREFIX] != null) {
            //check if we want to append parent record id.
            if (strpos($instance[RECORD_ID_PREFIX], '[parent_record_id]') !== false && $parentRecordId != '') {
                return str_replace('[parent_record_id]', $parentRecordId, $instance[RECORD_ID_PREFIX]);
            } else {
                return $instance[RECORD_ID_PREFIX];
            }


        } else {
            return '';
        }
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles()
    {
        $roles = $this->getProjectSetting('allowed_roles', $this->getProjectId())?:[];
        $this->roles = array_filter($roles);
    }


    /**
     * @return SearchRelation
     */
    public function getSearchRelation()
    {
        return $this->searchRelation;
    }

    /**
     * @param SearchRelation $searchRelation
     */
    public function setSearchRelation($searchRelation)
    {
        $this->searchRelation = $searchRelation;
    }

    /**
     * @return string
     */
    public function getTopParentRow()
    {
        return $this->topParentRow;
    }

    /**
     * @param string $topParentRow
     */
    public function setTopParentRow($topParentRow)
    {
        $this->topParentRow = $topParentRow;
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
     * @return string
     */
    public function getAddRecordURL()
    {
        return $this->addRecordURL;
    }

    /**
     * @param string $addRecordURL
     */
    public function setAddRecordURL($addRecordURL)
    {
        $this->addRecordURL = $addRecordURL;
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
    public function getRecordId()
    {
        return $this->recordId;
    }

    /**
     * @param string $recordId
     */
    public function setRecordId()
    {
        $temp = func_get_args();
        $recordId = $temp[0];
        $this->recordId = $recordId;
    }

}