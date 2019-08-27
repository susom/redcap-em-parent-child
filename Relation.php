<?php

namespace Stanford\ParentChild;


/**
 * Class Relation
 * @package Stanford\ParentChild
 * @property int $parentEventId
 * @property int $childEventId
 * @property int $foreignKey
 * @property string $parentDisplayLabel
 */
class Relation extends Main
{
    private $parentEventId;

    private $childEventId;

    private $foreignKey;

    private $parentDisplayLabel;

    /**
     * Relation constructor.
     * @param array $instance
     */
    public function __construct($instance)
    {
        try {
            $this->setChildEventId($instance[CHILD_EVENT]);

            $this->setForeignKey($instance[CHILD_FOREIGN_KEY]);

            $this->setParentEventId($instance[PARENT_EVENT]);

            $this->setParentDisplayLabel($instance[PARENT_DISPLAY_LABEL]);
        } catch (\LogicException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @return string
     */
    public function getParentDisplayLabel()
    {
        return $this->parentDisplayLabel;
    }

    /**
     * @param string $parentDisplayLabel
     */
    public function setParentDisplayLabel($parentDisplayLabel)
    {
        $this->parentDisplayLabel = $parentDisplayLabel;
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
