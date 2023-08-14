<?php

namespace Stanford\ParentChild;

//TODO this report need to be saved in the database for future use!

/**
 * Class RelationalReport
 * @package Stanford\ParentChild
 * @property array $childrenArms
 * @property int $eventId
 * @property int $projectId
 * @property array $instruments
 * @property \Project $project;
 */
class RelationalReport extends Main
{

    private $childrenArms;

    private $eventId;

    private $projectId;

    private $instruments;

    private $project;

    public function __construct($projectId, $eventId, $children)
    {
        try {
            global $Proj;
            $this->setProject($Proj);
            $this->setEventId($eventId);
            $this->setProjectId($projectId);
            $this->setInstruments($this->getProject()->eventsForms[$this->getEventId()][0]);

            if ($children != false) {
                foreach ($children as $child) {
                    $relation = new Relation($child);

                    $child = new ChildArm($child[ParentChild::CHILD_EVENT], $this->getProjectId(), $relation);

                    $this->setChildrenArms($child);
                }
            }
        } catch (\LogicException $e) {
            echo $e->getMessage();

        }
    }

    /**
     * @return array
     */
    public function getInstruments()
    {
        return $this->instruments;
    }

    /**
     * @param array $instruments
     */
    public function setInstruments($instruments)
    {
        if (!is_array($instruments)) {
            $temp = array();
            $temp[$instruments] = $this->getProject()->forms[$instruments]['menu'];
            $instruments = $temp;
        } else {
            $temp = array();
            foreach ($instruments as $instrument) {
                $temp[$instrument] = $this->getProject()->forms[$instrument]['menu'];
            }
            $instruments = $temp;
        }
        $this->instruments = $instruments;
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
     * @return mixed
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * @param mixed $eventId
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;
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

    public function getEventCard($instrument, $key, $event)
    {
        require "view/report/card.php";
    }

    /**
     * @return \Project
     */
    public function getProject(): \Project
    {
        return $this->project;
    }

    /**
     * @param \Project $project
     */
    public function setProject(\Project $project): void
    {
        $this->project = $project;
    }

}