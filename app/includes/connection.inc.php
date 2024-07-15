<?php
class Database {
    public $conn; 

    function __construct() {
        $this->conn = mysqli_connect($_SERVER["DB_HOST"], $_SERVER["DB_USER"], $_SERVER["DB_PASS"], $_SERVER["DB_NAME"]); 

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

    // NOTE: Why is this here? Why would it need to return the id?
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
                    $sql = $sql . "($wColumn[$i] LIKE '%$wValue[$i]%' "; 
                    $sql = $sql . "OR $wColumn[$i] LIKE '%$wValue[$i]' "; 
                    $sql = $sql . "OR $wColumn[$i] LIKE '%$wValue[$i]%') "; 
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

        // var_dump($sql); 

        return $array;
    }

    function selectSearch($queryArray) {
        // Generating a random $_SESSION id # to help identify searches
        if (!isset($_SESSION["sessionId"])) $_SESSION["sessionId"] = uniqid(); 

        // Calling searchWorks procedure
        foreach ($queryArray as $keyword) {
            $sql = "CALL searchWorks('$keyword', '$_SESSION[sessionId]');"; 

            mysqli_query($this->conn, $sql);
        }
        
        $sql = "SELECT WORK.WORK_ID, WORK.WORK_NAME, THUMBNAIL.THUMB_LINK, THUMBNAIL.THUMB_DESCRIPT, SEARCH.WORK_PRIORITY, "; 
        $sql .= "ISSUE.ISS_NAME, ISSUE.ISS_DATE, CONTRIBUTOR.CON_FNAME, CONTRIBUTOR.CON_LNAME "; 
        $sql .= "FROM SEARCH "; 
        $sql .= "JOIN WORK ON SEARCH.WORK_ID = WORK.WORK_ID ";
        $sql .= "JOIN THUMBNAIL ON WORK.THUMB_ID = THUMBNAIL.THUMB_ID "; 
        $sql .= "JOIN ISSUE ON WORK.ISS_ID = ISSUE.ISS_ID "; 
        $sql .= "JOIN CONTRIBUTOR ON WORK.CON_ID = CONTRIBUTOR.CON_ID "; 
        $sql .= "JOIN MEDIA_TYPE ON WORK.MEDIA_ID = MEDIA_TYPE.MEDIA_ID ";
        $sql .= "WHERE SEARCH.SESSION_ID = '$_SESSION[sessionId]' "; 
        $sql .= "ORDER BY SEARCH.WORK_PRIORITY DESC;"; 

        // var_dump($sql); 

        $result = mysqli_query($this->conn, $sql);
        $array = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_free_result($result);

        // // Removing everything from SEARCH table
        $this->cleanPriority(); 

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