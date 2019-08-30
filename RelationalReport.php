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
 */
class RelationalReport extends Main
{

    private $childrenArms;

    private $eventId;

    private $projectId;

    private $instruments;

    public function __construct($projectId, $eventId, $children)
    {
        try {
            $this->setEventId($eventId);
            $this->setProjectId($projectId);
            $this->setInstruments($this->getInstrumentNameViaEventId($this->getEventId()));

            if ($children != false) {
                foreach ($children as $child) {
                    $relation = new Relation($child);

                    $child = new ChildArm($child[CHILD_EVENT], $this->getProjectId(), $relation);

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
            $temp[$instruments] = $this->getInstrumentMenuDescription($instruments);
            $instruments = $temp;
        } else {
            $temp = array();
            foreach ($instruments as $instrument) {
                $temp[$instrument] = $this->getInstrumentMenuDescription($instruments);
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
}