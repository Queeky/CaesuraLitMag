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

    // Sanitizes data to encode html chars and prevent SQL injection
    function sanitize($data) {
        $cleanData = []; 

        foreach($data as $datum) {
            $datum = mysqli_real_escape_string($this->conn, $datum); 
            $datum = htmlspecialchars($datum); 

            array_push($cleanData, $datum); 
        }

        return $cleanData; 
    }

    // Add where condition later
    function insertValues($table, $selected = [], $values = []) {
        // Sanitizing input
        $values = $this->sanitize($values); 

        $items = ""; 

        foreach ($selected as $item) {
            $items = $items . $item; 

            if (next($selected) != null) {
                $items = $items . ", "; 
            }
        }

        $sql = "INSERT INTO $table ($items) "; 
        $sql .= "VALUES ("; 

        foreach ($values as $value) {
            $sql .= "'$value'"; 

            if (next($values) != null) {
                $sql .= ", "; 
            }
        }

        $sql .= ");"; 

        // var_dump($sql); 

        if (mysqli_query($this->conn, $sql) === true) {
            return true; 
        } else {
            return false; 
        }
    }

    function deleteValues($table, $wColumn, $wValue) {
        // Only takes one wColumn and wValue value because it is searching for the id
        $sql = "DELETE FROM $table "; 
        $sql .= "WHERE $wColumn = $wValue;"; 

        if (mysqli_query($this->conn, $sql) === true) {
            return true; 
        } else {
            return false; 
        }
    }

    function updateValues($table, $selected = [], $values = [], $wColumn = [], $wValue = [], $wCond = "AND") {
        // Sanitizing input
        $values = $this->sanitize($values); 

        $sql = "UPDATE $table SET "; 

        for ($i = 0; $i < count($selected); $i++) {
            $sql .= "$selected[$i] = '$values[$i]' "; 

            if (next($selected)) {
                $sql .= ", "; 
            }
        }

        for ($i = 0; $i < count($wColumn); $i++) {
            if ($i == 0) {
                $sql .= "WHERE "; 
            }

            $sql .= "$wColumn[$i] = $wValue[$i] "; 

            if (next($wColumn)) {
                $sql .= $wCond . " "; 
            }
        }

        $sql .= ";"; 

        if (mysqli_query($this->conn, $sql) === true) {
            return true; 
        } else {
            return false; 
        }
    }

    // Runs through WORK table and reverts priority to 0
    function cleanPriority() {
        $this->updateValues("WORK", ["WORK_PRIORITY"], [0]); 
    }

    // Runs through contributors and makes sure each person has at least 
    // one work to their name; if not, contributor removed
    function cleanContributor() {
        $conIds = $this->selectCustom("CONTRIBUTOR", ["CON_ID"]); 

        foreach ($conIds as $id) {
            $work = $this->selectCustom("WORK", ["WORK_ID"], ["CON_ID"], [$id["CON_ID"]], ["="]); 

            if (!$work) {
                $this->deleteValues("CONTRIBUTOR", "CON_ID", $id["CON_ID"]); 
            }
        }
    }

    // Checks if contributor already exists
    // If true, returns id; if false, creates new contributor
    function checkContributor($fName, $lName) {
        // Sanitizing input
        $names = $this->sanitize([$fName, $lName]); 
        $names[0] = $fName; 
        $names[1] = $lName; 

        $check = $this->selectCustom("CONTRIBUTOR", ["*"], ["CON_FNAME", "CON_LNAME"], [$fName, $lName], ["=", "="]);
        $conId = null; 

        if ($check) {
            foreach ($check as $con) {
                $conId = $con["CON_ID"]; 
            }

            return $conId; 
        } else {
            $result = $this->insertValues("CONTRIBUTOR", ["CON_FNAME", "CON_LNAME"], [$fName, $lName]); 

            if (!$result) {
                echo "<p class='header-notif'>Error pushing $fName $lName to database.</p>"; 
            } else {
                echo "<p class='header-notif'>$fName $lName successfully added.</p>"; 
                $newCon = $this->selectCustom("CONTRIBUTOR", ["MAX(CON_ID) AS CON_ID"]); 

                foreach ($newCon as $id) {
                    $conId = $id["CON_ID"]; 
                }
            }

            return $conId; 
        }
    }

    // Checks if image is being used by any other object
    function checkUsed($id) {
        $thumbs1 = $this->selectCustom("WORK", ["THUMB_ID"], ["THUMB_ID"], [$id], ["="]); 
        $thumbs2 = $this->selectCustom("ISSUE", ["THUMB_ID"], ["THUMB_ID"], [$id], ["="]); 
        $count = 0; 

        foreach ($thumbs1 as $item) {
            $count = $count + 1; 
        }

        foreach ($thumbs2 as $item) {
            $count = $count + 1; 
        }

        if ($count > 1) {
            return true; 
        } else {
            return false; 
        }
    }

    function selectCustom($table, $selected, $wColumn = [], $wValue = [], $wOperator = [], $wCond = "AND", $jTable = [], $jColumn1 = [], $jColumn2 = [], $order = null, $orderType = null) {
        // Sanitizing input
        if ($wValue) {
            $wValue = $this->sanitize($wValue); 
        }

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

        if ($order) {
            $sql .= "ORDER BY $order $orderType"; 
        }

        $sql .= ";"; 

        $result = mysqli_query($this->conn, $sql);
        $array = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_free_result($result);

        return $array;
    }

    function selectSearch($queryArray) {
        // Resets priority each time search is called
        $this->cleanPriority(); 

        $setup = "SELECT WORK.WORK_ID, WORK.WORK_NAME, WORK.WORK_PRIORITY, THUMBNAIL.THUMB_LINK, THUMBNAIL.THUMB_DESCRIPT, "; 
        $setup .= "ISSUE.ISS_NAME, ISSUE.ISS_DATE, CONTRIBUTOR.CON_FNAME, CONTRIBUTOR.CON_LNAME "; 
        $setup .= "FROM WORK "; 
        $setup .= "JOIN THUMBNAIL ON WORK.THUMB_ID = THUMBNAIL.THUMB_ID "; 
        $setup .= "JOIN ISSUE ON WORK.ISS_ID = ISSUE.ISS_ID "; 
        $setup .= "JOIN CONTRIBUTOR ON WORK.CON_ID = CONTRIBUTOR.CON_ID "; 
        $setup .= "JOIN MEDIA_TYPE ON WORK.MEDIA_ID = MEDIA_TYPE.MEDIA_ID "; 

        $sql = $setup; 

        $title = $fName = $lName = $issue = $media = $date = $keyword = null; 
        $titles = $fNames = $lNames = $issues = $medias = $dates = $keywords = $allElements = array(); 

        function orderNUpdate($database, $array, $push, $key, $priority, $string) {
            foreach ($array as $item) {
                $toAdd = null; 

                switch($string) {
                    case true: 
                        $toAdd = "'" . $item[$key] . "'"; 
                        break; 
                    case false: 
                        $toAdd = $item[$key];
                        break; 
                }
            
                array_push($push, $toAdd); 
                $database->updateValues("WORK", ["WORK_PRIORITY"], [$item["WORK_PRIORITY"] + $priority], ["WORK_ID"], [$item["WORK_ID"]]); 
            }

            return $push; 
        }

        foreach ($queryArray as $keyword) {
            $title = $this->selectCustom("WORK", ["WORK_ID, WORK_PRIORITY", "WORK_NAME"], ["WORK_NAME"], [$keyword], ["like"]); 
            $fName = $this->selectCustom("CONTRIBUTOR", ["WORK.WORK_ID", "WORK.WORK_PRIORITY", "CONTRIBUTOR.CON_ID", "CONTRIBUTOR.CON_FNAME"], ["CONTRIBUTOR.CON_FNAME"], [$keyword], ["="], "AND", ["WORK"], ["CONTRIBUTOR.CON_ID"], ["WORK.CON_ID"]); 
            $lName = $this->selectCustom("CONTRIBUTOR", ["WORK.WORK_ID", "WORK.WORK_PRIORITY", "CONTRIBUTOR.CON_ID", "CONTRIBUTOR.CON_LNAME"], ["CONTRIBUTOR.CON_LNAME"], [$keyword], ["="], "AND", ["WORK"], ["CONTRIBUTOR.CON_ID"], ["WORK.CON_ID"]);
            $issue = $this->selectCustom("ISSUE", ["WORK.WORK_ID", "WORK.WORK_PRIORITY", "ISSUE.ISS_ID", "ISSUE.ISS_NAME"], ["ISSUE.ISS_NAME"], [$keyword], ["like"], "AND", ["WORK"], ["ISSUE.ISS_ID"], ["WORK.ISS_ID"]); 
            $media = $this->selectCustom("MEDIA_TYPE", ["WORK.WORK_ID", "WORK.WORK_PRIORITY", "MEDIA_TYPE.MEDIA_ID", "MEDIA_TYPE.MEDIA_NAME"], ["MEDIA_TYPE.MEDIA_NAME"], [$keyword], ["="], "AND", ["WORK"], ["MEDIA_TYPE.MEDIA_ID"], ["WORK.MEDIA_ID"]); 
            $date = $this->selectCustom("ISSUE", ["WORK.WORK_ID", "WORK.WORK_PRIORITY", "ISSUE.ISS_ID", "YEAR(ISSUE.ISS_DATE) AS ISS_DATE"], ["YEAR(ISSUE.ISS_DATE)"], [$keyword], ["="], "AND", ["WORK"], ["ISSUE.ISS_ID"], ["WORK.ISS_ID"]); 
            $keyword = $this->selectCustom("WORK", ["WORK_ID", "WORK_PRIORITY"], ["WORK_CONTENT"], [$keyword], ["like"]); 

            if ($title) {
                $allElements["WORK.WORK_NAME"] = orderNUpdate($this, $title, $titles, "WORK_NAME", 6, true); 
            }

            if ($fName) {
                $allElements["CONTRIBUTOR.CON_FNAME"] = orderNUpdate($this, $fName, $fNames, "CON_FNAME", 5, true); 
            }

            if ($lName) {
                $allElements["CONTRIBUTOR.CON_LNAME"] = orderNUpdate($this, $lName, $lNames, "CON_LNAME", 5, true);
            }

            if ($issue) {
                $allElements["ISSUE.ISS_NAME"] = orderNUpdate($this, $issue, $issues, "ISS_NAME", 4, true); 
            }

            if ($media) {
                $allElements["MEDIA_TYPE.MEDIA_NAME"] = orderNUpdate($this, $media, $medias, "MEDIA_NAME", 3, true);
            }

            if ($date) {
                $allElements["YEAR(ISSUE.ISS_DATE)"] = orderNUpdate($this, $date, $dates, "ISS_DATE", 2, true);
            }

            if ($keyword) {
                $allElements["WORK.WORK_ID"] = orderNUpdate($this, $keyword, $keywords, "WORK_ID", 1, false); 
            }
        }

        $count = 0; 

        foreach ($allElements as $key => $ids) {
            if ($count == 0) {
                $sql .= "WHERE "; 
            }

            foreach ($ids as $id) {
                if ($count > 0) {
                    $sql .= "AND "; 
                }

                $sql .= "$key = $id "; 

                $count++; 
            }
        }


        $sql .= "UNION "; 
        $sql .= $setup; 

        $count = 0; 

        foreach ($allElements as $key => $ids) {
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

        $sql .= "ORDER BY WORK_PRIORITY DESC;"; 

        // var_dump($sql); 

        $result = mysqli_query($this->conn, $sql);
        $array = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_free_result($result);

        return $array;
        
    }

    // Eventually optimize for the separate page, as well
    function selectAllIssues() {
        // $sql = "SELECT ISS_ID, ISS_NAME, YEAR(ISS_DATE) AS ISS_DATE FROM ISSUE"; 
        $sql = "SELECT ISSUE.ISS_ID, ISSUE.ISS_NAME, YEAR(ISSUE.ISS_DATE) AS ISS_DATE, THUMBNAIL.THUMB_LINK, "; 
        $sql .= "ISSUE.ISS_DESCRIPT, "; 
        $sql .= "THUMBNAIL.THUMB_DESCRIPT FROM ISSUE "; 
        $sql .= "JOIN THUMBNAIL ON ISSUE.THUMB_ID = THUMBNAIL.THUMB_ID ";
        $sql .= "ORDER BY ISSUE.ISS_DATE DESC"; 

        $result = mysqli_query($this->conn, $sql);
        $array = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_free_result($result);

        return $array;
    }

    // Getting the most recent issue by searching max ISS_ID
    // OR order by desc but only receiving one; then I can use selectCustom
    function selectRecentIssue() {
        $sql = "SELECT ISSUE.ISS_ID, ISSUE.ISS_NAME, YEAR(ISSUE.ISS_DATE) AS ISS_DATE, THUMBNAIL.THUMB_LINK, "; 
        $sql .= "ISS_DESCRIPT, "; 
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