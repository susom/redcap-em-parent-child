<?php

namespace Stanford\ParentChild;


/**
 * Class ChildArm
 * @package Stanford\ParentChild
 * @property int $eventId
 */
class ChildArm
{

    private $eventId;

    public function __construct($eventId)
    {
        try {
            $this->setEventId($eventId);
        } catch (\LogicException $e) {
            echo $e->getMessage();
        }
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