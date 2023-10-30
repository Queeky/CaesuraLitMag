<?php

// Default variables
$host = "localhost"; 
$user = "root"; 
$password = ""; 
$db = "caesuralitmag_data"; 


class Database {
    public $conn; 

    function __construct() {
        $this->conn = mysqli_connect($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['password'], $GLOBALS['db']); 

        if(!$this->conn) {
            die("Could not connect to database");
        }
    }

    function selectCustom($table, $selected, $wColumn = [], $wValue = [], $wOperator = [], $wCond = "AND", $jTable = [], $jColumn1 = [], $jColumn2 = []) {
        // Checking if selectCustom will successfully run
        if (count($wValue) != count($wColumn) || count($wOperator) != count($wColumn)) {
            print("wColumn, wValue, and wOperator must be the same length."); 
            return; 
        }

        if (count($jTable) != count($jColumn1) || count($jTable) != count($jColumn2)) {
            print("jTable, jColumn1, and jColumn2 must be the same length."); 
            return; 
        }

        $items = ""; 

        foreach ($selected as $item) {
            $items = $items . $item; 

            if (next($selected) != null) {
                $items = $items . ", "; 
            }
        }

        $sql = "SELECT $items FROM $table "; 

        // Joining tables
        for ($i = 0; $i < count($jTable); $i++) {
            $sql .= "JOIN $jTable[$i] ON $jColumn1[$i] = $jColumn2[$i] "; 
        }

        // Adding WHERE statements
        for ($i = 0; $i < count($wColumn); $i++) {
            switch ($i) {
                default: 
                    $sql = $sql . $wCond . " ";
                    break; 
                case 0: 
                    $sql = $sql . "WHERE "; 
                    break; 
            }

            switch ($wOperator[$i]) {
                case "=": 
                    $sql = $sql . "$wColumn[$i] = '$wValue[$i]' "; 
                    break; 
                case "like": 
                    $sql = $sql . "$wColumn[$i] LIKE '%$wValue[$i]%' "; 
                    break; 
            } 
        }

        $sql .= ";"; 

        //var_dump($sql); 

        $result = mysqli_query($this->conn, $sql);
        $array = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_free_result($result);

        return $array;
    }

    // Selecting IDs using search keywords
    function selectSearch($allIds) {
        $sql = "SELECT WORK.WORK_ID, WORK.WORK_NAME, THUMBNAIL.THUMB_LINK, THUMBNAIL.THUMB_DESCRIPT, "; 
        $sql .= "ISSUE.ISS_NAME, ISSUE.ISS_DATE, CONTRIBUTOR.CON_FNAME, CONTRIBUTOR.CON_LNAME "; 
        $sql .= "FROM WORK "; 
        $sql .= "JOIN THUMBNAIL ON WORK.THUMB_ID = THUMBNAIL.THUMB_ID "; 
        $sql .= "JOIN ISSUE ON WORK.ISS_ID = ISSUE.ISS_ID "; 
        $sql .= "JOIN CONTRIBUTOR ON WORK.CON_ID = CONTRIBUTOR.CON_ID "; 
        $sql .= "JOIN MEDIA_TYPE ON WORK.MEDIA_ID = MEDIA_TYPE.MEDIA_ID "; 

        $count = 0; 

        // Yo dawg, I heard you like foreach loops, so I put a foreach in a foreach
        // in a foreach in a foreach

        // This is currently doing (WORK.WORK_ID = 9 AND ISSUE.ISS_ID = 6) 
        // and (ISSUE.ISS_ID = 6 AND WORK.WORK_ID = 9), which is unnecessary

        foreach ($allIds as $key => $ids) {
            if ($count == 0) {
                $sql .= "WHERE "; 
            }

            foreach ($ids as $id) {
                if ($count > 0) {
                    $sql .= "OR "; 
                }

                $sql .= "$key = $id "; 

                foreach ($allIds as $key2 => $ids2) {
                    if ($key2 != $key) {
                        foreach ($ids2 as $id2) {
                            $sql .= "AND $key2 = $id2 "; 
                        }
                    }
                }

                $count++; 
            }
        }

        $sql .= "UNION "; 
        $sql .= "SELECT WORK.WORK_ID, WORK.WORK_NAME, THUMBNAIL.THUMB_LINK, THUMBNAIL.THUMB_DESCRIPT, "; 
        $sql .= "ISSUE.ISS_NAME, ISSUE.ISS_DATE, CONTRIBUTOR.CON_FNAME, CONTRIBUTOR.CON_LNAME "; 
        $sql .= "FROM WORK "; 
        $sql .= "JOIN THUMBNAIL ON WORK.THUMB_ID = THUMBNAIL.THUMB_ID "; 
        $sql .= "JOIN ISSUE ON WORK.ISS_ID = ISSUE.ISS_ID "; 
        $sql .= "JOIN CONTRIBUTOR ON WORK.CON_ID = CONTRIBUTOR.CON_ID "; 
        $sql .= "JOIN MEDIA_TYPE ON WORK.MEDIA_ID = MEDIA_TYPE.MEDIA_ID "; 

        $count = 0; 

        foreach ($allIds as $key => $ids) {
            if ($count == 0) {
                $sql .= "WHERE "; 
            }

            foreach ($ids as $id) {
                if ($count > 0) {
                    $sql .= "OR "; 
                }

                $sql .= "$key = $id "; 

                $count++; 
            }
        }

        $sql .= ";"; 

        //var_dump($sql); 

        $result = mysqli_query($this->conn, $sql);
        $array = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_free_result($result);

        return $array;
    }

    // Eventually optimize for the separate page, as well
    function selectAllIssues() {
        // $sql = "SELECT ISS_ID, ISS_NAME, YEAR(ISS_DATE) AS ISS_DATE FROM ISSUE"; 
        $sql = "SELECT ISSUE.ISS_ID, ISSUE.ISS_NAME, YEAR(ISSUE.ISS_DATE) AS ISS_DATE, THUMBNAIL.THUMB_LINK, "; 
        $sql .= "LEFT(ISSUE.ISS_DESCRIPT, 150) AS ISS_DESCRIPT, "; 
        $sql .= "THUMBNAIL.THUMB_DESCRIPT FROM ISSUE "; 
        $sql .= "JOIN THUMBNAIL ON ISSUE.THUMB_ID = THUMBNAIL.THUMB_ID ";
        $sql .= "ORDER BY ISSUE.ISS_DATE DESC"; 

        $result = mysqli_query($this->conn, $sql);
        $array = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_free_result($result);

        return $array;
    }

    // Getting the most recent issue by searching max ISS_ID
    function selectRecentIssue() {
        $sql = "SELECT ISSUE.ISS_ID, ISSUE.ISS_NAME, YEAR(ISSUE.ISS_DATE) AS ISS_DATE, THUMBNAIL.THUMB_LINK, "; 
        $sql .= "LEFT(ISSUE.ISS_DESCRIPT, 150) AS ISS_DESCRIPT, "; 
        $sql .= "THUMBNAIL.THUMB_DESCRIPT FROM ISSUE "; 
        $sql .= "JOIN THUMBNAIL ON ISSUE.THUMB_ID = THUMBNAIL.THUMB_ID "; 
        $sql .= "WHERE ISS_DATE = ( SELECT MAX(ISS_DATE) FROM ISSUE )"; 

        $result = mysqli_query($this->conn, $sql);
        $array = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_free_result($result);

        return $array;
    }

    function selectAllContacts() {
        $sql = "SELECT CONTACT.CONTACT_TITLE, CONTACT.CONTACT_FNAME, CONTACT.CONTACT_LNAME, ";
        $sql .= "CONTACT.CONTACT_EMAIL, CONTACT.CONTACT_PHONE, THUMBNAIL.THUMB_LINK, THUMBNAIL.THUMB_DESCRIPT "; 
        $sql .= "FROM CONTACT "; 
        $sql .= "JOIN THUMBNAIL ON CONTACT.THUMB_ID = THUMBNAIL.THUMB_ID"; 

        $result = mysqli_query($this->conn, $sql);
        $array = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_free_result($result);

        return $array;
    }
}

$database = new Database(); 
?>