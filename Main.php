<?php

namespace Stanford\ParentChild;

use REDCap;

/**
 * Class Main
 * @package Stanford\ParentChild
 * @property int $projectId
 */
Abstract class Main
{

    private $projectId;

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


    protected function getInstrumentNameViaEventId($eventId)
    {

        $sql = "SELECT * FROM redcap_events_forms WHERE event_id = $eventId";

        $q = db_query($sql);

        if ($row = db_fetch_assoc($q)) {
            return $row['form_name'];
        }
    }

    protected function getInstrumentMenuDescription($name)
    {

        $projectId = $this->getProjectId();

        $sql = "SELECT form_menu_description FROM redcap_metadata WHERE project_id = $projectId AND form_name = '$name' AND form_menu_description IS NOT NULL";

        $q = db_query($sql);

        if ($row = db_fetch_assoc($q)) {
            return $row['form_menu_description'];
        }
    }

    /**
     * @param $pid
     * @param int $event_id : Pass NULL or '' if CLASSICAL
     * @param string $prefix
     * @param bool $padding
     * @return bool|int|string
     * @throws
     */
    protected function getNextId($pid, $event_id = null, $prefix = '', $padding = false)
    {
        //Get Project
        global $Proj;
        if (empty($Proj) || $Proj->project_id !== $pid) {
            $thisProj = new \Project($pid);
        } else {
            $thisProj = $Proj;
        }

        $id_field = $thisProj->table_pk;
        //If Classical no event or null is passed
        if (($event_id == '') OR ($event_id == null)) {
            throw new \LogicException("no event found");
        }
        $q = \REDCap::getData($pid, 'array', null, array($id_field), $event_id);
        //$this->emLog($q, "Found records in project $pid using $id_field");
        $i = 1;
        do {
            // Make a padded number
            if ($padding) {
                // make sure we haven't exceeded padding, pad of 2 means
                //$max = 10^$padding;
                $max = 10 ** $padding;
                if ($i >= $max) {
                    return false;
                }
                $id = str_pad($i, $padding, "0", STR_PAD_LEFT);
                //$this->emLog("Padded to $padding for $i is $id");
            } else {
                $id = $i;
            }
            // Add the prefix
            $id = $prefix . $id;
            //$this->emLog("Prefixed id for $i is $id for event_id $event_id and idfield $id_field");
            $i++;
        } while (!empty($q[$id][$event_id][$id_field]));
        return $id;
    }

    /**
     * @param $eventId
     * @param null $id
     * @return mixed
     */
    public static function getRecords($eventId, $id = null)
    {
        if (!is_null($id)) {
            $params = array(
                'return_format' => 'array',
                'events' => $eventId,
                'filterLogic' => "[id] = '$id'"
            );
        } else {
            $params = array(
                'return_format' => 'array',
                'events' => $eventId
            );
        }

        return REDCap::getData($params);
    }

    /**
     * @param $recordId
     * @return string
     */
    protected function generateInsertRecordURL($projectId, $eventId, $name, $recordId)
    {
        return APP_PATH_WEBROOT . "DataEntry/index.php?pid=$projectId&page=$name&id=$recordId&event_id=$eventId";
    }

    public static function replaceRecordLabels($text, $row)
    {
        preg_match_all("/\[(.*?)\]/", $text, $matches);
        foreach ($matches[1] as $match) {
            if (isset($row[$match])) {
                $text = str_replace($match, $row[$match], $text);
            }
        }
        $text = str_replace("]", "", $text);
        $text = str_replace("[", "", $text);

        return $text;
    }
}