<?php

namespace Stanford\ParentChild;


/**
 * Class Relation
 * @package Stanford\ParentChild
 * @property int $parentEventId
 * @property int $childEventId
 * @property int $foreignKey
 */
class Relation
{

    private $parentEventId;

    private $childEventId;

    private $foreignKey;

    public function __construct($parentEventId, $childEventId, $foreignKey)
    {
        try {
            $this->setChildEventId($childEventId);

            $this->setForeignKey($foreignKey);

            $this->setParentEventId($parentEventId);
        } catch (\LogicException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @return int
     */
    public function getChildEventId()
    {
        return $this->childEventId;
    }

    /**
     * @param int $childEventId
     */
    public function setChildEventId($childEventId)
    {
        $this->childEventId = $childEventId;
    }

    /**
     * @return int
     */
    public function getParentEventId()
    {
        return $this->parentEventId;
    }

    /**
     * @param int $parentEventId
     */
    public function setParentEventId($parentEventId)
    {
        $this->parentEventId = $parentEventId;
    }

    /**
     * @return int
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    /**
     * @param int $foreignKey
     */
    public function setForeignKey($foreignKey)
    {
        $this->foreignKey = $foreignKey;
    }
}
