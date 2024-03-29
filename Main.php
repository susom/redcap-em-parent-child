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

    public static function getInstrumentFieldLabel($field, $projectId, $instrument = null)
    {
        if (!is_null($instrument)) {
            $sql = "SELECT element_label FROM redcap_metadata WHERE field_name = '$field' AND project_id = '$projectId' AND form_name = '$instrument'";
        } else {
            $sql = "SELECT element_label FROM redcap_metadata WHERE field_name = '$field' AND project_id = '$projectId'";
        }


        $q = db_query($sql);

        if (db_num_rows($q) == 1) {
            $row = db_fetch_assoc($q);
            return $row['element_label'];
        } else {
            return false;
        }
    }

    public static function getInstrumentNameViaEventId($eventId)
    {

        $sql = "SELECT * FROM redcap_events_forms JOIN redcap_metadata on redcap_metadata.form_name = redcap_events_forms.form_name WHERE event_id = $eventId limit 1 ";

        $q = db_query($sql);

        if (db_num_rows($q) == 1) {
            $row = db_fetch_assoc($q);
            return $row['form_name'];
        } else {
            $result = array();
            while ($row = db_fetch_assoc($q)) {
                $result[] = $row['form_name'];
            }
            return $result;
        }
    }

    public function getArmIdViaEventId($eventId)
    {
        $sql = "SELECT a.arm_num as arm_number FROM redcap_events_metadata m JOIN redcap_events_arms a on m.arm_id = a.arm_id WHERE event_id $eventId";

        $q = db_query($sql);

        if (db_num_rows($q) == 1) {
            $row = db_fetch_assoc($q);
            return $row['arm_number'];
        } else {
            return false;
        }
    }

    public static function getEventIdViaArmId($armNumber, $projectId)
    {
        $sql = "SELECT arm_id  FROM redcap_events_arms WHERE arm_num = '$armNumber' AND project_id ='$projectId'";

        $q = db_query($sql);

        if (db_num_rows($q) == 1) {
            $row = db_fetch_assoc($q);
            $sql = "SELECT event_id FROM redcap_events_metadata WHERE arm_id = $row[arm_id]";
            $q = db_query($sql);
            $row = db_fetch_assoc($q);
            return $row['event_id'];
        } else {
            return false;
        }
    }

    public static function getInstrumentMenuDescription($name, $projectId)
    {

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
    public static function getNextId($pid, $event_id = null, $prefix = '', $padding = false)
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
//        global $Proj;
//        $projectId = $Proj->project_id;
//        $primaryKey = $Proj->table_pk;
//
//        if (is_array($eventId)) {
//            $eventId = implode(",", $eventId);
//        }
//
//        $sql = "select * from redcap_data rd where project_id = $projectId and event_id IN ($eventId)";
//        $q = db_query($sql);
//        $recordId = '';
//        $result = array();
//        $record = array();
//        while ($row = db_fetch_assoc($q)) {
//            $e = $row['event_id'];
//            if ($recordId != $row['record']) {
//                if (!empty($record)) {
//                    $result[$recordId][$e] = $record;
//                    $record = array();
//                }
//                $record[$row['field_name']] = $row['value'];
//                $recordId = $record[$primaryKey] = $row['record'];
//            } else {
//                $record[$row['field_name']] = $row['value'];
//            }
//        }
//
//        $result[$recordId][$e] = $record;
//
//        if (!is_null($id)) {
//            foreach ($result as $key => $item) {
//                if ($id != $key) {
//                    unset($result[$key]);
//                }
//            }
//        }
//

        if (!is_null($id)) {
            $params = array(
                'return_format' => 'array',
                'events' => $eventId,
                'records' => [$id]
            );
        } else {
            $params = array(
                'return_format' => 'array',
                'events' => $eventId
            );
        }

        $records = REDCap::getData($params);
        if ($id == null) {
            return $records;
        } else {
            foreach ($records as $recordId => $record) {
                if ($recordId == $id) {
                    return array($id => $record);
                }
            }
        }
        return $records;
    }

    /**
     * @param $eventId
     * @param null $id
     * @return mixed
     */
    public static function searchRecords($eventId, $field, $value)
    {
        global $Proj;
        $projectId = $Proj->project_id;
        $primaryKey = $Proj->table_pk;
        $data_table = method_exists('\REDCap', 'getDataTable') ? \REDCap::getDataTable($projectId) : "redcap_data";

        $sql = "select * from $data_table rd where project_id = $projectId and event_id IN ($eventId)";
        $q = db_query($sql);
        $recordId = '';
        $result = array();
        $record = array();
        while ($row = db_fetch_assoc($q)) {
            if ($recordId != $row['record']) {
                if (!empty($record)) {
                    if ($record[$field] == $value) {
                        return array($recordId => $record);
                    }
                    $result[$recordId][$eventId] = $record;
                    $record = array();
                }
                $record[$row['field_name']] = $row['value'];
                $recordId = $record[$primaryKey] = $row['record'];
            } else {
                $record[$row['field_name']] = $row['value'];
            }
        }

//        $result = array();
//        /*$params = array(
//            'return_format' => 'array',
//            'events' => $eventId,
//            'filterLogic' => "[$field] = '$value'"
//        );*/
//        $params = array(
//            'return_format' => 'array',
//            'events' => $eventId
//        );
//        $records = REDCap::getData($params);
//        foreach ($records as $id => $record) {
//            if (isset($record[$eventId][$field]) && $record[$eventId][$field] == $value) {
//                $result[$id] = $record;
//            }
//        }
//        if (empty($result)) {
//            return false;
//        } else {
//            return $result;
//        }

    }
    /**
     * @param $recordId
     * @return string
     */
    protected function generateInsertRecordURL($projectId, $eventId, $name, $recordId)
    {
        return APP_PATH_WEBROOT . "DataEntry/index.php?pid=$projectId&page=$name&id=$recordId&event_id=$eventId";
    }

    public static function getRecordHomeURL($projectId, $page, $eventId, $recordId)
    {
        if(is_array($page)){
            $page = end($page);
        }
        return APP_PATH_WEBROOT . "DataEntry/index.php?pid=$projectId&page=$page&event_id=$eventId&id=$recordId";
    }

    public static function replaceRecordLabels($text, $row)
    {
        $origin = $text;
        preg_match_all("/\[(.*?)\]/", $text, $matches);
        foreach ($matches[1] as $match) {
            if (isset($row[$match])) {
                $text = str_replace($match, $row[$match], $text);
            }
        }
        if ($origin != $text) {
            $text = str_replace("]", "", $text);
            $text = str_replace("[", "", $text);
            return $text;
        } else {
            return false;
        }


    }

    public static function getRecordIdPrefix($instrument)
    {
        $recordPrefix = '';
        $parts = explode("_", $instrument);
        foreach ($parts as $part) {
            $recordPrefix .= $part[0];
        }
        return $recordPrefix . '-';
    }
}