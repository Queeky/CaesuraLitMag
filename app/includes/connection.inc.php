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

    // This will use a safe mysql_fetch function (finish later)
    function readQuery($sql, $return = false) {
        if ($return) {
            $result = mysqli_query($this->conn, $sql);
            $array = mysqli_fetch_all($result, MYSQLI_ASSOC);

            mysqli_free_result($result);

            return $array;
        } else {
            return mysqli_query($this->conn, $sql) ? true : false;
        }
    }

    // Selects from WORKS or moves specific search results to SEARCH to be displayed
    function collectWorks($searchKey, $queryArray) {
        // If searchKey is false, using default select from WORKS
        if (!$searchKey) {
            if ((!isset($_GET["fs"])) && (!isset($_GET["ls"]))) {
                $_SESSION["firstSearchId"] = $_SESSION["firstWorkId"]; 
                $_SESSION["lastSearchId"] = $_SESSION["lastWorkId"]; 

                $_GET["ls"] = $_SESSION["lastWorkId"] + 1; 
            }

            if (isset($_GET["ls"])) {
                $sql = "SELECT WORK.WORK_ID, WORK.WORK_NAME, THUMBNAIL.THUMB_LINK, "; 
                $sql .= "THUMBNAIL.THUMB_DESCRIPT, ISSUE.ISS_NAME, ISSUE.ISS_DATE, "; 
                $sql .= "CONTRIBUTOR.CON_FNAME, CONTRIBUTOR.CON_LNAME ";  
                $sql .= "FROM WORK "; 
                $sql .= "JOIN THUMBNAIL ON WORK.THUMB_ID = THUMBNAIL.THUMB_ID "; 
                $sql .= "JOIN ISSUE ON WORK.ISS_ID = ISSUE.ISS_ID "; 
                $sql .= "JOIN CONTRIBUTOR ON WORK.CON_ID = CONTRIBUTOR.CON_ID "; 
                $sql .= "WHERE WORK.WORK_ID < $_GET[ls] "; 
                $sql .= "ORDER BY WORK.WORK_ID DESC "; 
                $sql .= "LIMIT 16";
            } else if (isset($_GET["fs"])) {
                $sql = "SELECT * FROM "; 
                $sql .= "(SELECT WORK.WORK_ID, WORK.WORK_NAME, THUMBNAIL.THUMB_LINK, "; 
                $sql .= "THUMBNAIL.THUMB_DESCRIPT, ISSUE.ISS_NAME, ISSUE.ISS_DATE, "; 
                $sql .= "CONTRIBUTOR.CON_FNAME, CONTRIBUTOR.CON_LNAME ";  
                $sql .= "FROM WORK "; 
                $sql .= "JOIN THUMBNAIL ON WORK.THUMB_ID = THUMBNAIL.THUMB_ID "; 
                $sql .= "JOIN ISSUE ON WORK.ISS_ID = ISSUE.ISS_ID "; 
                $sql .= "JOIN CONTRIBUTOR ON WORK.CON_ID = CONTRIBUTOR.CON_ID "; 
                $sql .= "WHERE WORK.WORK_ID > $_GET[fs] "; 
                $sql .= "ORDER BY WORK.WORK_ID ASC "; 
                $sql .= "LIMIT 16) WORK ";
                $sql .= "ORDER BY WORK_ID DESC"; 
            } else {
                echo "<p class='header-notif'>Oh no! The code! It's broken!</p>"; 
            }

            $result = mysqli_query($this->conn, $sql);
            $works = mysqli_fetch_all($result, MYSQLI_ASSOC);

            mysqli_free_result($result);

            return $works; 
        }

        // Uploading results to SEARCH -- initial insert
        if ((!isset($_GET["fs"])) && (!isset($_GET["ls"]))) {
            $this->cleanPriority(); 

            $sql = "SET @order := 0"; 
            mysqli_query($this->conn, $sql);

            switch($searchKey) {
                case "query": 
                    // Calling searchWorks procedure
                    foreach ($queryArray as $keyword) {
                        $sql = "CALL searchWorks('$keyword', '$_SESSION[sessionId]');"; 
    
                        mysqli_query($this->conn, $sql);
                    }
    
                    break; 
                case "issue": 
                    $sql = "INSERT INTO SEARCH (WORK_ID, WORK_PRIORITY, SESSION_ID, ORDER_ID) "; 
                    $sql .= "SELECT WORK.WORK_ID, 4, '$_SESSION[sessionId]', (@order := @order + 1) FROM WORK "; 
                    $sql .= "JOIN ISSUE ON WORK.ISS_ID = ISSUE.ISS_ID "; 
                    $sql .= "WHERE ISSUE.ISS_ID = $_GET[issue]"; 

                    mysqli_query($this->conn, $sql);

                    break; 
                case "media": 
                    $sql = "INSERT INTO SEARCH (WORK_ID, WORK_PRIORITY, SESSION_ID, ORDER_ID) "; 
                    $sql .= "SELECT WORK.WORK_ID, 3, '$_SESSION[sessionId]', (@order := @order + 1) FROM WORK "; 
                    $sql .= "JOIN MEDIA_TYPE ON WORK.MEDIA_ID = MEDIA_TYPE.MEDIA_ID "; 
                    $sql .= "WHERE MEDIA_TYPE.MEDIA_ID = $_GET[media]"; 

                    mysqli_query($this->conn, $sql);

                    break; 
            }

            $sql = "SELECT W1.ORDER_ID AS FIRST_SEARCH, W2.ORDER_ID AS LAST_SEARCH FROM"; 
            $sql .= "(SELECT ORDER_ID FROM SEARCH WHERE SESSION_ID = '$_SESSION[sessionId]' "; 
            $sql .= "ORDER BY ORDER_ID ASC LIMIT 1) AS W1, ";
            $sql .= "(SELECT ORDER_ID FROM SEARCH WHERE SESSION_ID = '$_SESSION[sessionId]' "; 
            $sql .= "ORDER BY ORDER_ID DESC LIMIT 1) AS W2"; 

            $result = mysqli_query($this->conn, $sql);
            $idRange = mysqli_fetch_all($result, MYSQLI_ASSOC);

            if ($idRange) {
                $_SESSION["firstSearchId"] = $idRange[0]["FIRST_SEARCH"]; 
                $_SESSION["lastSearchId"] = $idRange[0]["LAST_SEARCH"]; 

                // Setting $_GET["ls"]
                $_GET["ls"] = $_SESSION["lastSearchId"] + 1;
            }

            mysqli_free_result($result);
        }

        if (isset($_GET["ls"])) {
            $sql = "SELECT SEARCH.ORDER_ID, WORK.WORK_ID, WORK.WORK_NAME, "; 
            $sql .= "SEARCH.WORK_PRIORITY, THUMBNAIL.THUMB_LINK, ";
            $sql .= "THUMBNAIL.THUMB_DESCRIPT, ISSUE.ISS_NAME, ISSUE.ISS_DATE, "; 
            $sql .= "CONTRIBUTOR.CON_FNAME, CONTRIBUTOR.CON_LNAME ";  
            $sql .= "FROM SEARCH "; 
            $sql .= "JOIN WORK ON SEARCH.WORK_ID = WORK.WORK_ID "; 
            $sql .= "JOIN THUMBNAIL ON WORK.THUMB_ID = THUMBNAIL.THUMB_ID "; 
            $sql .= "JOIN ISSUE ON WORK.ISS_ID = ISSUE.ISS_ID "; 
            $sql .= "JOIN CONTRIBUTOR ON WORK.CON_ID = CONTRIBUTOR.CON_ID "; 
            $sql .= "WHERE SEARCH.SESSION_ID = '$_SESSION[sessionId]' ";
            $sql .= "AND SEARCH.ORDER_ID < $_GET[ls] "; 
            $sql .= "ORDER BY SEARCH.ORDER_ID DESC "; 
            $sql .= "LIMIT 16";
        } else if (isset($_GET["fs"])) {
            $sql = "SELECT * FROM "; 
            $sql .= "(SELECT SEARCH.ORDER_ID, WORK.WORK_ID, WORK.WORK_NAME, "; 
            $sql .= "SEARCH.WORK_PRIORITY, THUMBNAIL.THUMB_LINK, ";
            $sql .= "THUMBNAIL.THUMB_DESCRIPT, ISSUE.ISS_NAME, ISSUE.ISS_DATE, "; 
            $sql .= "CONTRIBUTOR.CON_FNAME, CONTRIBUTOR.CON_LNAME ";  
            $sql .= "FROM SEARCH "; 
            $sql .= "JOIN WORK ON SEARCH.WORK_ID = WORK.WORK_ID "; 
            $sql .= "JOIN THUMBNAIL ON WORK.THUMB_ID = THUMBNAIL.THUMB_ID "; 
            $sql .= "JOIN ISSUE ON WORK.ISS_ID = ISSUE.ISS_ID "; 
            $sql .= "JOIN CONTRIBUTOR ON WORK.CON_ID = CONTRIBUTOR.CON_ID "; 
            $sql .= "WHERE SEARCH.SESSION_ID = '$_SESSION[sessionId]' ";
            $sql .= "AND SEARCH.ORDER_ID > $_GET[fs] "; 
            $sql .= "ORDER BY WORK.WORK_ID ASC "; 
            $sql .= "LIMIT 16) SEARCH ";
            $sql .= "ORDER BY SEARCH.ORDER_ID DESC"; 
        } else {
            echo "<p class='header-notif'>Oh no! The code! It's broken!</p>"; 
        }

        $result = mysqli_query($this->conn, $sql);
        $works = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_free_result($result);

        return $works;
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

        return mysqli_query($this->conn, $sql) ? true : false;
    }

    function deleteValues($table, $wColumn, $wValue) {
        // Only takes one wColumn and wValue value because it is searching for the id
        $sql = "DELETE FROM $table "; 
        $sql .= "WHERE $wColumn = $wValue;"; 

        return mysqli_query($this->conn, $sql) ? true : false;
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

        return mysqli_query($this->conn, $sql) ? true : false; 
    }

    // Clears SEARCH table for specific session
    function cleanPriority() {
        $sql = "DELETE FROM SEARCH WHERE SESSION_ID = '$_SESSION[sessionId]';"; 

        return mysqli_query($this->conn, $sql) ? true : false;
    }

    // Runs through contributors and makes sure each person has at least 
    // one work to their name; if not, contributor removed
    function cleanContributor() {
        $sql = "DELETE FROM CONTRIBUTOR "; 
        $sql .= "WHERE NOT EXISTS(SELECT 1 FROM WORK WHERE WORK.CON_ID = CONTRIBUTOR.CON_ID);"; 

        return mysqli_query($this->conn, $sql) ? true : false;
    }

    // Checks if contributor/thumbnail already exists
    // If true, returns id; if false, creates new contributor/thumbnail
    function insertReturnId($input1, $input2, $tag) {
        if ($tag == "con") {
            $names = $this->sanitize([$input1, $input2]); 

            $sql = "CALL checkContributor('$names[0]', '$names[1]', @id);";
            $sql .= "SELECT @id;";
        } else if ($tag == "thumb") {
            $descript = $this->sanitize([$input2]); 

            $sql = "CALL checkThumbnail('$input1', '$descript[0]', @id);"; 
            $sql .= "SELECT @id;"; 
        }

        $result = mysqli_multi_query($this->conn, $sql); 
        $id = null; 

        do {
            // Store first result set
            if ($result = mysqli_store_result($this->conn)) {
              while ($row = mysqli_fetch_row($result)) {
                if (is_string($row[0])) {
                    $id = $row[0]; 
                }
              }
              mysqli_free_result($result);
            }
            
             //Prepare next result set
          } while (mysqli_next_result($this->conn));
   
        return $id; 
    }

    // Checks if image is being used by any other object
    function checkUsed($id) {
        $sql = "SELECT THUMB_ID FROM "; 
        $sql .= "(SELECT DISTINCT THUMBNAIL.THUMB_ID AS THUMB_ID FROM THUMBNAIL "; 
        $sql .= "JOIN WORK ON THUMBNAIL.THUMB_ID = WORK.THUMB_ID "; 
        $sql .= "UNION "; 
        $sql .= "SELECT DISTINCT THUMBNAIL.THUMB_ID AS THUMB_ID FROM THUMBNAIL "; 
        $sql .= "JOIN ISSUE ON THUMBNAIL.THUMB_ID = ISSUE.THUMB_ID) AS IDS "; 
        $sql .= "WHERE THUMB_ID = $id;"; 

        $result = mysqli_query($this->conn, $sql);
        $array = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_free_result($result);

        return (!$array) ? false : true; 
    }

    function selectCustom($table, $selected, $wColumn = [], $wValue = [], $wOperator = [], $wCond = "AND", $jTable = [], $jColumn1 = [], $jColumn2 = [], $order = null, $orderType = null, $limit = null) {
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
                    $sql = $sql . "OR $wColumn[$i] LIKE '$wValue[$i]%') "; 
                    break; 
                case ">": 
                    $sql .= "$wColumn[$i] > $wValue[$i] "; 
                    break; 
                case "<": 
                    $sql .= "$wColumn[$i] < $wValue[$i] "; 
                    break; 
            } 
        }

        if ($order) {
            $sql .= "ORDER BY $order $orderType "; 
        }

        if ($limit) {
            $sql .= "LIMIT $limit";  
        }

        $sql .= ";"; 

        // var_dump($sql); 

        $result = mysqli_query($this->conn, $sql);
        $array = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_free_result($result);

        return $array;
    }

    // function selectSearch($queryArray) {
    //     // Removing everything from SEARCH table
    //     $this->cleanPriority();  

    //     // Calling searchWorks procedure
    //     foreach ($queryArray as $keyword) {
    //         $sql = "CALL searchWorks('$keyword', '$_SESSION[sessionId]');"; 

    //         mysqli_query($this->conn, $sql);
    //     }
        
    //     $sql = "SELECT WORK.WORK_ID, WORK.WORK_NAME, THUMBNAIL.THUMB_LINK, THUMBNAIL.THUMB_DESCRIPT, SEARCH.WORK_PRIORITY, SEARCH.ORDER_ID, "; 
    //     $sql .= "ISSUE.ISS_NAME, ISSUE.ISS_DATE, CONTRIBUTOR.CON_FNAME, CONTRIBUTOR.CON_LNAME "; 
    //     $sql .= "FROM SEARCH "; 
    //     $sql .= "JOIN WORK ON SEARCH.WORK_ID = WORK.WORK_ID ";
    //     $sql .= "JOIN THUMBNAIL ON WORK.THUMB_ID = THUMBNAIL.THUMB_ID "; 
    //     $sql .= "JOIN ISSUE ON WORK.ISS_ID = ISSUE.ISS_ID "; 
    //     $sql .= "JOIN CONTRIBUTOR ON WORK.CON_ID = CONTRIBUTOR.CON_ID "; 
    //     $sql .= "WHERE SEARCH.SESSION_ID = '$_SESSION[sessionId]' "; 
    //     $sql .= "ORDER BY SEARCH.WORK_PRIORITY DESC, SEARCH.WORK_ID DESC;"; 

    //     // echo var_dump($sql); 

    //     $result = mysqli_query($this->conn, $sql);
    //     $array = mysqli_fetch_all($result, MYSQLI_ASSOC);

    //     // var_dump($array); 

    //     mysqli_free_result($result);

    //     return $array;
    // }

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