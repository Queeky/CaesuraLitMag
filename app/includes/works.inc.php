<?php 
    $queryArray = explode(" ", $query); // Breaking up keywords w/ " " delimiter
    $database = new Database(); // Fix this later

    // Use switch statement to choose between searchKeys
    switch($searchKey) {
        case "query": 
            $_GET["urlKeypair"] = "query=$_GET[query]&"; 

            // Uploading query results to SEARCH to be accessed page by page
            if ((!isset($_GET["fs"])) && (!isset($_GET["ls"]))) {
                $database->cleanPriority(); 

                $sql = "SET @order := 0"; 
                mysqli_query($database->conn, $sql);

                // Calling searchWorks procedure
                foreach ($queryArray as $keyword) {
                    $sql = "CALL searchWorks('$keyword', '$_SESSION[sessionId]');"; 

                    mysqli_query($database->conn, $sql);
                }

                $sql = "SELECT W1.ORDER_ID AS FIRST_SEARCH, W2.ORDER_ID AS LAST_SEARCH FROM"; 
                $sql .= "(SELECT ORDER_ID FROM SEARCH WHERE SESSION_ID = '$_SESSION[sessionId]' "; 
                $sql .= "ORDER BY ORDER_ID ASC LIMIT 1) AS W1, ";
                $sql .= "(SELECT ORDER_ID FROM SEARCH WHERE SESSION_ID = '$_SESSION[sessionId]' "; 
                $sql .= "ORDER BY ORDER_ID DESC LIMIT 1) AS W2"; 

                $result = mysqli_query($database->conn, $sql);
                $idRange = mysqli_fetch_all($result, MYSQLI_ASSOC);

                $_SESSION["firstSearchId"] = $idRange[0]["FIRST_SEARCH"]; 
                $_SESSION["lastSearchId"] = $idRange[0]["LAST_SEARCH"]; 

                mysqli_free_result($result);

                // Setting $_GET["ls"]
                $_GET["ls"] = $_SESSION["lastSearchId"] + 1;
            }
        
            if (isset($_GET["ls"])) {
                $sql = "SELECT SEARCH.ORDER_ID, WORK.WORK_ID, WORK.WORK_NAME, THUMBNAIL.THUMB_LINK, "; 
                $sql .= "THUMBNAIL.THUMB_DESCRIPT, ISSUE.ISS_NAME, ISSUE.ISS_DATE, "; 
                $sql .= "CONTRIBUTOR.CON_FNAME, CONTRIBUTOR.CON_LNAME ";  
                $sql .= "FROM SEARCH "; 
                $sql .= "JOIN WORK ON SEARCH.WORK_ID = WORK.WORK_ID "; 
                $sql .= "JOIN THUMBNAIL ON WORK.THUMB_ID = THUMBNAIL.THUMB_ID "; 
                $sql .= "JOIN ISSUE ON WORK.ISS_ID = ISSUE.ISS_ID "; 
                $sql .= "JOIN CONTRIBUTOR ON WORK.CON_ID = CONTRIBUTOR.CON_ID "; 
                $sql .= "WHERE SEARCH.SESSION_ID = '$_SESSION[sessionId]' ";
                $sql .= "AND SEARCH.ORDER_ID < $_GET[ls] "; 
                $sql .= "ORDER BY SEARCH.WORK_PRIORITY DESC, SEARCH.WORK_ID DESC "; 
                $sql .= "LIMIT 16";
            } else if (isset($_GET["fs"])) {
                $sql = "SELECT * FROM "; 
                $sql .= "(SELECT SEARCH.ORDER_ID, WORK.WORK_ID, WORK.WORK_NAME, THUMBNAIL.THUMB_LINK, "; 
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
                $sql .= "ORDER BY WORK_PRIORITY DESC, WORK_ID DESC"; 
            } 

            $result = mysqli_query($database->conn, $sql);
            $works = mysqli_fetch_all($result, MYSQLI_ASSOC);

            mysqli_free_result($result);
            break; 
        case "issue":  
            $_GET["urlKeypair"] = "issue=$_GET[issue]&"; 
 
            // Uploading issue results to SEARCH to be accessed page by page
            if ((!isset($_GET["fs"])) && (!isset($_GET["ls"]))) {
                $database->cleanPriority();

                $sql = "SET @order := 0"; 
                mysqli_query($database->conn, $sql);

                $sql = "INSERT INTO SEARCH (WORK_ID, WORK_PRIORITY, SESSION_ID, ORDER_ID) "; 
                $sql .= "SELECT WORK.WORK_ID, 4, '$_SESSION[sessionId]', (@order := @order + 1) FROM WORK "; 
                $sql .= "JOIN ISSUE ON WORK.ISS_ID = ISSUE.ISS_ID "; 
                $sql .= "WHERE ISSUE.ISS_ID = $_GET[issue]"; 

                mysqli_query($database->conn, $sql);

                $sql = "SELECT W1.ORDER_ID AS FIRST_SEARCH, W2.ORDER_ID AS LAST_SEARCH FROM"; 
                $sql .= "(SELECT ORDER_ID FROM SEARCH WHERE SESSION_ID = '$_SESSION[sessionId]' "; 
                $sql .= "ORDER BY ORDER_ID ASC LIMIT 1) AS W1, ";
                $sql .= "(SELECT ORDER_ID FROM SEARCH WHERE SESSION_ID = '$_SESSION[sessionId]' "; 
                $sql .= "ORDER BY ORDER_ID DESC LIMIT 1) AS W2"; 

                $result = mysqli_query($database->conn, $sql);
                $idRange = mysqli_fetch_all($result, MYSQLI_ASSOC);

                $_SESSION["firstSearchId"] = $idRange[0]["FIRST_SEARCH"]; 
                $_SESSION["lastSearchId"] = $idRange[0]["LAST_SEARCH"]; 

                mysqli_free_result($result);

                // Setting $_GET["ls"]
                $_GET["ls"] = $_SESSION["lastSearchId"] + 1; 
            }

            if (isset($_GET["ls"])) {
                $sql = "SELECT SEARCH.ORDER_ID, WORK.WORK_ID, WORK.WORK_NAME, THUMBNAIL.THUMB_LINK, "; 
                $sql .= "THUMBNAIL.THUMB_DESCRIPT, ISSUE.ISS_NAME, ISSUE.ISS_DATE, "; 
                $sql .= "CONTRIBUTOR.CON_FNAME, CONTRIBUTOR.CON_LNAME ";  
                $sql .= "FROM SEARCH "; 
                $sql .= "JOIN WORK ON SEARCH.WORK_ID = WORK.WORK_ID "; 
                $sql .= "JOIN THUMBNAIL ON WORK.THUMB_ID = THUMBNAIL.THUMB_ID "; 
                $sql .= "JOIN ISSUE ON WORK.ISS_ID = ISSUE.ISS_ID "; 
                $sql .= "JOIN CONTRIBUTOR ON WORK.CON_ID = CONTRIBUTOR.CON_ID "; 
                $sql .= "WHERE SEARCH.SESSION_ID = '$_SESSION[sessionId]' ";
                $sql .= "AND SEARCH.ORDER_ID < $_GET[ls] "; 
                $sql .= "ORDER BY WORK.WORK_ID DESC "; 
                $sql .= "LIMIT 16";
            } else if (isset($_GET["fs"])) {
                $sql = "SELECT * FROM "; 
                $sql .= "(SELECT SEARCH.ORDER_ID, WORK.WORK_ID, WORK.WORK_NAME, THUMBNAIL.THUMB_LINK, "; 
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
                $sql .= "ORDER BY WORK_ID DESC"; 
            } 

            $result = mysqli_query($database->conn, $sql);
            $works = mysqli_fetch_all($result, MYSQLI_ASSOC);

            mysqli_free_result($result);
            break; 
        case "media": 
            $_GET["urlKeypair"] = "media=$_GET[media]&";

            // Uploading media results to SEARCH to be accessed page by page
            if ((!isset($_GET["fs"])) && (!isset($_GET["ls"]))) {
                $database->cleanPriority();

                $sql = "SET @order := 0"; 
                mysqli_query($database->conn, $sql);

                $sql = "INSERT INTO SEARCH (WORK_ID, WORK_PRIORITY, SESSION_ID, ORDER_ID) "; 
                $sql .= "SELECT WORK.WORK_ID, 3, '$_SESSION[sessionId]', (@order := @order + 1) FROM WORK "; 
                $sql .= "JOIN MEDIA_TYPE ON WORK.MEDIA_ID = MEDIA_TYPE.MEDIA_ID "; 
                $sql .= "WHERE MEDIA_TYPE.MEDIA_ID = $_GET[media]"; 

                mysqli_query($database->conn, $sql);

                $sql = "SELECT W1.ORDER_ID AS FIRST_SEARCH, W2.ORDER_ID AS LAST_SEARCH FROM"; 
                $sql .= "(SELECT ORDER_ID FROM SEARCH WHERE SESSION_ID = '$_SESSION[sessionId]' "; 
                $sql .= "ORDER BY ORDER_ID ASC LIMIT 1) AS W1, ";
                $sql .= "(SELECT ORDER_ID FROM SEARCH WHERE SESSION_ID = '$_SESSION[sessionId]' "; 
                $sql .= "ORDER BY ORDER_ID DESC LIMIT 1) AS W2"; 

                $result = mysqli_query($database->conn, $sql);
                $idRange = mysqli_fetch_all($result, MYSQLI_ASSOC);

                $_SESSION["firstSearchId"] = $idRange[0]["FIRST_SEARCH"]; 
                $_SESSION["lastSearchId"] = $idRange[0]["LAST_SEARCH"]; 

                mysqli_free_result($result);

                // Setting $_GET["ls"]
                $_GET["ls"] = $_SESSION["lastSearchId"] + 1; 
            }

            if (isset($_GET["ls"])) {
                $sql = "SELECT SEARCH.ORDER_ID, WORK.WORK_ID, WORK.WORK_NAME, THUMBNAIL.THUMB_LINK, "; 
                $sql .= "THUMBNAIL.THUMB_DESCRIPT, ISSUE.ISS_NAME, ISSUE.ISS_DATE, "; 
                $sql .= "CONTRIBUTOR.CON_FNAME, CONTRIBUTOR.CON_LNAME ";  
                $sql .= "FROM SEARCH "; 
                $sql .= "JOIN WORK ON SEARCH.WORK_ID = WORK.WORK_ID "; 
                $sql .= "JOIN THUMBNAIL ON WORK.THUMB_ID = THUMBNAIL.THUMB_ID "; 
                $sql .= "JOIN ISSUE ON WORK.ISS_ID = ISSUE.ISS_ID "; 
                $sql .= "JOIN CONTRIBUTOR ON WORK.CON_ID = CONTRIBUTOR.CON_ID "; 
                $sql .= "WHERE SEARCH.SESSION_ID = '$_SESSION[sessionId]' ";
                $sql .= "AND SEARCH.ORDER_ID < $_GET[ls] "; 
                $sql .= "ORDER BY WORK.WORK_ID DESC "; 
                $sql .= "LIMIT 16";
            } else if (isset($_GET["fs"])) {
                $sql = "SELECT * FROM "; 
                $sql .= "(SELECT SEARCH.ORDER_ID, WORK.WORK_ID, WORK.WORK_NAME, THUMBNAIL.THUMB_LINK, "; 
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
                $sql .= "ORDER BY WORK_ID DESC"; 
            } 

            $result = mysqli_query($database->conn, $sql);
            $works = mysqli_fetch_all($result, MYSQLI_ASSOC);

            mysqli_free_result($result);
            break;
        default: 
            $_GET["urlKeypair"] = ""; 

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
            } 

            var_dump($sql); 

            $result = mysqli_query($database->conn, $sql);
            $works = mysqli_fetch_all($result, MYSQLI_ASSOC);

            mysqli_free_result($result);
            break;  
    }

    function displayWorks($works, $query) {
        if ($works) {
            // echo "<br><br><br>" . var_dump($works); 

            // Checking if a search is in action; otherwise, WORK_PRIORITY does 
            // not exist in results
            $query ? $priority = intval($works[0]["WORK_PRIORITY"]) : $priority = null; 

            // If default, WORK_ID; otherwise ORDER_ID
            (isset($_GET["issue"]) || isset($_GET["media"]) || isset($_GET["query"])) ? $firstShownId = $works[0]["ORDER_ID"] : $firstShownId = $works[0]["WORK_ID"]; 
            (isset($_GET["issue"]) || isset($_GET["media"]) || isset($_GET["query"])) ? $lastShownId = $works[count($works) - 1]["ORDER_ID"] : $lastShownId = $works[count($works) - 1]["WORK_ID"]; 

            echo "<br>" . "FIRST LOADED ID: " . $firstShownId . "<br>"; 
            echo "<br>" . "LAST LOADED ID: " . $lastShownId . "<br>";

            echo "<div class='grid'>"; 

            foreach ($works as $work) {
                if ($priority && ($priority > 1 && intval($work["WORK_PRIORITY"]) <= 1)) {
                    echo "</div>"; 

                    echo "<div class='similar-results'>"; 
                    echo "<p>Results similar to '$query'</p>"; 
                    echo "</div>"; 

                    echo "<div class='grid'>"; 
                }

                // Setting new priority
                $priority ? $priority = intval($work["WORK_PRIORITY"]) : $priority = null; 

                echo "<div class='archive-item'>"; 
                echo "<a href='highlight.php?id=$work[WORK_ID]'><img loading='lazy' src='$work[THUMB_LINK]' alt='$work[THUMB_DESCRIPT]'></a>"; 
                echo "<div class='archive-info'>"; 
                echo "<p>$work[WORK_NAME]</p>"; 
                echo "<p>$work[CON_FNAME] $work[CON_LNAME]</p>";
                echo "</div>"; 
                echo "</div>"; 
            }

            echo "</div>"; 

            echo "<div class='next-page'>";

            if ($firstShownId == $_SESSION["lastSearchId"]) {
                echo "<p style='color:grey; text-decoration:none;'>Previous Page</p>"; 
            } else {
                echo "<a href='archives.php?$_GET[urlKeypair]fs=$firstShownId'>Previous Page</a>"; 
            }

            echo "<p class='line-break'> | </p>"; 

            if ($lastShownId == $_SESSION["firstSearchId"]) {
                echo "<p style='color:grey; text-decoration:none;'>Next Page</p>"; 
            } else {
                echo "<a href='archives.php?$_GET[urlKeypair]ls=$lastShownId'>Next Page</a>"; 
            }

            echo "</div>"; 
        } else if (!$works && $query) {
            echo "<div class='empty-message large'>"; 
            echo "<p>No results for '$query.'</p>"; 
            echo "</div>";
        } else {
            echo "<div class='empty-message large'>"; 
            echo "<p>Nothing's here at the moment!</p>"; 
            echo "</div>";
        }
    }

    function displayAdmWorks($works, $database) {
        $issues = $database->selectAllIssues(); 
        $media = $database->selectCustom("MEDIA_TYPE", ["MEDIA_ID", "MEDIA_NAME"]); 

        echo "<div class='add-form archive'>"; 
        echo "<form action='archives.php' method='POST' enctype='multipart/form-data'>"; 
        echo "<h3>ADD NEW WORK:</h3>"; 

        echo "<div class='text-input'>"; 
        echo "<input type='text' name='title' placeholder='Enter work title'>";
        echo "<input type='text' name='fname' placeholder='Enter contributor first name'>";  
        echo "<input type='text' name='lname' placeholder='Enter contributor last name'>";  
        echo "</div>"; 

        echo "<div class='select-input'>"; 
        echo "<label for='issue'>CHOOSE ISSUE:</label>"; 
        echo "<select name='issue'>"; 

        foreach ($issues as $issue) {
            echo "<option value='$issue[ISS_ID]'>$issue[ISS_DATE] | $issue[ISS_NAME]</option>"; 
        }

        echo "</select>"; 

        echo "<label for='media'>CHOOSE MEDIA TYPE:</label>"; 
        echo "<select name='media'>"; 

        foreach ($media as $medium) {
            echo "<option value='$medium[MEDIA_ID]'>$medium[MEDIA_NAME]</option>";
        }

        echo "</select>"; 
        echo "</div>"; 

        echo "<div class='upload-type'>"; 
        echo "<label>IS THIS WORK LITERATURE OR ART?</label>"; 
        echo "<div>"; 
        echo "<button class='submit-btn upload-type-lit' type='button' name='upload-type'>Literature</button>"; 
        echo "<button class='submit-btn upload-type-art' type='button' name='upload-type'>Art</button>";
        echo "</div>"; 
        echo "</div>"; 

        echo "<div class='upload-input'>"; 
        echo "<div id='lit'>"; 
        echo "<label for='doc'>UPLOAD DOCUMENT:</label>"; 
        echo "<input type='file' name='doc'>"; 
        echo "<label for='doc'>UPLOAD THUMBNAIL (OPTIONAL):</label>"; 
        echo "<input type='file' name='thumb'>";  
        echo "<input type='text' name='thumbDescript' placeholder='Enter thumbnail description'>";
        echo "</div>"; 

        echo "<div id='art'>"; 
        echo "<label for='doc'>UPLOAD THUMBNAIL:</label>"; 
        echo "<input type='file' name='thumb'>";  
        echo "<input type='text' name='thumbDescript' placeholder='Enter thumbnail description'>";
        echo "</div>"; 
    
        echo "</div>"; 

        echo "<button class='submit-btn' type='submit' name='add'>Submit</button>"; 

        echo "</form>"; 
        echo "</div>"; 

        if ($works) {
            echo "<div class='grid'>"; 

            foreach ($works as $work) {
                echo "<div class='archive-item'>"; 
                echo "<a href='highlight.php?id=$work[WORK_ID]'><img loading='lazy' src='$work[THUMB_LINK]' alt='$work[THUMB_DESCRIPT]'></a>"; 
                echo "<div class='archive-info'>"; 
                echo "<p>$work[WORK_NAME]</p>"; 
                echo "<p>$work[CON_FNAME] $work[CON_LNAME]</p>";
                echo "</div>"; 

                echo "<div class='controls'>"; 
                echo "<form action='archives.php' method='POST' enctype='multipart/form-data'>"; 
                echo "<label for='thumb'>UPDATE THUMBNAIL: </label>"; 
                echo "<input type='file' name='thumb'>";
                echo "<input type='text' name='descript' placeholder='Enter thumbnail description ***'>"; 
                echo "<div>"; 
                echo "<button class='submit-btn' type='submit' name='update' value='$work[WORK_ID]'>Update</button>"; 
                echo "<button class='submit-btn' type='submit' name='remove' value='$work[WORK_ID]'>Remove</button>"; 
                echo "</div>";
                echo "</form>";  
                echo "</div>"; 
                echo "</div>"; 
            }

            echo "</div>"; 
        } else {
            echo "<div class='empty-message large>"; 
            echo "<p>Nothing's here at the moment!</p>"; 
            echo "</div>";
        }
    }

    if (isset($_SESSION["admName"])) {
        displayAdmWorks($works, $database); 
    } else {
        displayWorks($works, $query); 
    }
?>