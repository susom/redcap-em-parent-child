<?php

namespace Stanford\ParentChild;


/**
 * Class Relation
 * @package Stanford\ParentChild
 * @property int $parentEventId
 * @property int $childEventId
 * @property int $foreignKey
 * @property string $parentDisplayLabel
 * @property boolean $displayChildren
 * @property string $topForeignKey
 * @property string $prefix
 */
class Relation extends Main
{
    private $parentEventId;

    private $childEventId;

    private $foreignKey;

    private $parentDisplayLabel;

    private $displayChildren;

    private $topForeignKey;

    private $prefix;

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

            $this->setDisplayChildren($instance[DISPLAY_CHILDREN_RECORDS]);

            $this->setTopForeignKey($instance[TOP_FOREIGN_KEY]);
        } catch (\LogicException $e) {
            echo $e->getMessage();
        }
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
     * @return bool
     */
    public function isDisplayChildren()
    {
        return $this->displayChildren;
    }

    /**
     * @param bool $displayChildren
     */
    public function setDisplayChildren($displayChildren)
    {
        $this->displayChildren = $displayChildren;
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

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;
    }


}
